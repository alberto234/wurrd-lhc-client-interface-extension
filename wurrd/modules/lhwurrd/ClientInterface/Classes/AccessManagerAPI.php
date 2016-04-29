<?php

/*
 * This file is a part of Wurrd extension for LiveHelperChat.
 *
 * Copyright 2016 Eyong N <eyongn@scalior.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


namespace Wurrd\ClientInterface\Classes;

use Wurrd\ClientInterface\Constants;
use Wurrd\ClientInterface\Model\Authorization;
use Wurrd\ClientInterface\Model\Device;
use Wurrd\Http\Exception;

/**
 * Interface to manage access to LHC from devices such as 
 * mobile apps or third-party webapps
 */
class AccessManagerAPI
{
	/**
	 * This method is used to grant access to a device/client. 
	 * This call causes previous access to be revoked.
	 * 
	 * @param array $args - An array containing the arguments needed for the
	 * 					    access token to be generated. The arguments are
	 * 						defined in constants.php are.
	 * 						- username
	 * 						- password
	 * 						- clientid
	 * 						- deviceuuid
	 * 						- type
	 * 						- devicename
	 * 						- platform
	 * 						- os
	 * 						- osversion
	 *
	 * @return Authorization - An instance of Authorization when successful
	 * 
	 * @throws	\Wurrd\Exception\Http\UnauthorizedException
	 *				With one of the following messages: 
	 * 					- Constants::MSG_BAD_USERNAME_PASSWORD 
	 * 			\Wurrd\Exception\Http\HttpException
	 */
	 public static function requestAccess($args) {
	 	
	 	// Step 1 - Get the operator and confirm access to the system
        $login = $args[Constants::USERNAME_KEY];
        $password = $args[Constants::PASSWORD_KEY];
        $deviceuuid = $args[Constants::DEVICEUUID_KEY];
        $platform = $args[Constants::PLATFORM_KEY];
        $type = $args[Constants::TYPE_KEY];
        $devicename = $args[Constants::DEVICENAME_KEY];
		$clientID = $args[Constants::CLIENTID_KEY];
		$os = $args[Constants::DEVICEOS_KEY];
		$osVersion = $args[Constants::DEVICEOSVERSION_KEY];

		$currentUser = \erLhcoreClassUser::instance();

		if (!$currentUser->authenticate($login, $password))
		{
		    throw new Exception\UnauthorizedException(Constants::MSG_BAD_USERNAME_PASSWORD);
		}
		
		 // Step 2 - Get/create the device
		 $newDevice = false;
		 $device = Device::fetchByUUID($deviceuuid, $platform);
		 if (!$device) {
		 	// The device is not found, add a new device
		 	$device = Device::createDevice($deviceuuid, $platform, $type, $devicename, $os, $osVersion);
			$device->saveThis();
			$newDevice = true;
		 }

		$authorization = null;
		if ($device !== false) {
		 	// Step 3 - Create authorization
		 	$authorization = AccessManagerAPI::createAuthorization($currentUser->getUserData(), $device, $clientID, $newDevice);
			if ($authorization !== false) {
				$authorization->saveThis();
			}
		 }
		
		if (is_null($authorization) || $authorization === false) {
			// This means we have a server-side issue, possibly database related
			throw new Exception\HttpException(Response::HTTP_INTERNAL_SERVER_ERROR,
												Constants::MSG_UNKNOWN_ERROR);
		}
		
		return $authorization;
	 }
	 

	/**
	 * Determines if the given access token is valid to access the system
	 * 
	 * @param string $accessToken	The access token to check
	 * @param string $deviceuuid	The unique id of the device associated with this token
	 * @return bool true if allowed. 
	 * @throws Http\Exception\AccessDeniedException
	 * 
	 * Exception codes are:
	 * 			1 = invalid token
	 * 			2 = expired token
	 * 			3 = new token generated
	 * 			4 = invalid device
	 */
	 public static function isAuthorized($accessToken, $deviceuuid) {
	 	$authorization = Authorization::fetchByAccessToken($accessToken);
		if ($authorization == false) {

			// If this request is using an old access token notify the caller that this 
			// access token has been superseded.
			$authorization = Authorization::fetchByPreviousAccessToken($accessToken);
			if ($authorization != false) {
				throw new Exception\AccessDeniedException(
						Constants::MSG_NEW_TOKEN_GENERATED,
						3);
			} else {
				throw new Exception\AccessDeniedException(Constants::MSG_INVALID_ACCESS_TOKEN, 1);
			}
		}
		
		$currTime = time();
		if ($currTime > $authorization->dtmaccessexpires) {
			throw new Exception\AccessDeniedException(Constants::MSG_EXPIRED_ACCESS_TOKEN, 2);
		}
		
		// Check the deviceuuid. 
		// How terrible is it to have another db query to get the deviceuuid?
		// We can create a column for the deviceuuid in the authorization table such that only
		// one query brings down the necessary data. Another option is to explore a DAO made up 
		// of a join query.
		$device = Device::fetch($authorization->deviceid);
		if ($device == false || $device->deviceuuid != $deviceuuid) {
			throw new Exception\AccessDeniedException(
					Constants::MSG_INVALID_DEVICE,
					4);
		}
				
		// If the previous access token is set, clear it. This constitutes the acknowledgement that the
		// new token was successfully received by the client.
		if ($authorization->previousaccesstoken != null) {
			$authorization->previousaccesstoken = null;
			$authorization->previousrefreshtoken = null;
			$authorization->saveThis();
		}
		
		// TODO: At a future iteration we will also check access scopes

		return true;
	 }	 

	/**
	 * Refreshes the tokens
	 * 
	 * @param string $accessToken	The access token to check
	 * @param string $refresToken	An unexpired refresh token is needed for this
	 * @param string $deviceuuid	The unique id of the device associated with this token
	 * @return Authorization - An instance of Authorization or false if a failure
	 * @throws \Mibew\Http\Exception\HttpException	-- and subclasses
	 * 
	 * Exception codes are:
	 * 			1 = invalid access token
	 * 			3 = invalid refresh token
	 * 			4 = expired refresh token
	 * 			5 = couldn't retrieve the operator
	 * 			6 = couldn't retrieve the device
	 */
	 public static function refreshAccess($accessToken, $refreshToken, $deviceuuid)
	 {
	 	// TODO: What does it mean for a refresh token to expire? 
		//		 The current algorithm is that the refresh is on a rolling interval
		//		 of Constants::REFRESH_DURATION with each refresh.
		//		 If the client doesn't access the system within that duration, they 
		//		 will need to login again. REFRESH_DURATION can be made configurable
		
	 	$authorization = Authorization::fetchByAccessToken($accessToken);
		if ($authorization == false) {

			$authorization = Authorization::fetchByPreviousAccessToken($accessToken);
			if ($authorization == false) {
				throw new Exception\AccessDeniedException(
						Constants::MSG_INVALID_ACCESS_TOKEN,
						1);
			}

			// Here, the request is using an old access token. Re-send the new tokens if they
			// have not yet expired.
			$currTime = time();
			if ($currTime < $authorization->dtmaccessexpires) {
				return $authorization;
			}
			
			// Access token has already expired. Replace the new refresh token with the old one and 
			// let the code below proceed to refresh the tokens
			$authorization->refreshtoken = $authorization->previousrefreshtoken;
		}
		
		if ($authorization->refreshtoken != $refreshToken) {
			throw new Exception\AccessDeniedException(
					Constants::MSG_INVALID_REFRESH_TOKEN,
					3);
		} else if (time() > $authorization->dtmrefreshexpires) {
			throw new Exception\AccessDeniedException(
					Constants::MSG_EXPIRED_REFRESH_TOKEN,
					4);
		}
				

		$operator = \erLhcoreClassModelUser::findOne(array(
			'filter' => array(
				'id' => $authorization->operatorid
			)
		));  

		// TODO: In addition, we need to check if the user is still allowed to 
		// access the system.
		// It is here that we can also implement settings such as forcing all sessions
		// to expire if the password changes.
		if ($operator == null) {
			throw new Exception\HttpException(
					Response::HTTP_INTERNAL_SERVER_ERROR,
					Constants::MSG_INVALID_OPERATOR,
					null,
					5);
		}
		
		$device = Device::fetch($authorization->deviceid);
		if ($device == false || $device->deviceuuid != $deviceuuid) {
			throw new Exception\HttpException(
					Response::HTTP_INTERNAL_SERVER_ERROR,
					Constants::MSG_INVALID_DEVICE,
					null,
					6);
		}

		$newAccessToken = AccessManagerAPI::generateAccessToken($operator->username);
		$newRefreshToken = AccessManagerAPI::generateRefreshToken($device->deviceuuid);
		
		// Update the authorization
		$authorization->previousaccesstoken = $authorization->accesstoken;
		$authorization->accesstoken = $newAccessToken['accesstoken'];
		$authorization->dtmaccessexpires = $newAccessToken['expiretime'];
		$authorization->dtmaccesscreated = $newAccessToken['created'];
		
		$authorization->previousrefreshtoken = $authorization->refreshtoken;
		$authorization->refreshtoken = $newRefreshToken['refreshtoken'];
		$authorization->dtmrefreshexpires = $newRefreshToken['expiretime'];
		$authorization->dtmrefreshcreated = $newRefreshToken['created'];
		
		$authorization->saveThis();
		
		
		return $authorization;
	 }	 


	/**
	 * Drop access from the system -- revoke tokens
	 * 
	 * @param string $accessToken	The access token to drop
	 * @param string $deviceuuid	The unique id of the device associated with this token
	 * @return bool true if successful. 
	 * @throws \Mibew\Exception\AccessDeniedException
	 * 
	 * Exception codes are:
	 * 			1 = invalid token
	 * 			2 = invalid device
	 */
	 public static function dropAccess($accessToken, $deviceuuid) {
	 	$authorization = Authorization::fetchByAccessToken($accessToken);
		if ($authorization == false) {
			throw new Exception\AccessDeniedException(Constants::MSG_INVALID_ACCESS_TOKEN, 1);
		}
		
		$device = Device::fetch($authorization->deviceid);
		if ($device == false) {
			throw new Exception\HttpException(
					Response::HTTP_INTERNAL_SERVER_ERROR,
					Constants::MSG_INVALID_DEVICE,
					null,
					2);
		}
		
		// This uses the deviceuuid from the device object rather than the authorization object
		// to emphasize that this is a destructive operation and we want to make sure we are 
		// accessing the right device.
		// Not sure why both values should be different, but just in case.
		if ($device->deviceuuid != $deviceuuid) {
			throw new Exception\AccessDeniedException(Constants::MSG_INVALID_DEVICE, 2);
		}

		// Given that there currently is a one-to-one relationship between the device and authorization,
		// i.e., only one access can be given to a particular device, we also have to remove the device
		// from the database.
		$authorization->delete();
		$device->delete();
		
		return true;
	 }	 

	/**
	 * Returns the version of this plugin
	 * 
	 * @return string plugin version. 
	 */
	public static function getAuthAPIPluginVersion()
	{
		return Constants::WCI_VERSION;
	}



	 // *********************************************
	 //  PRIVATE HELPER METHODS
	 // *********************************************
	 
	/**
	 * This method is used to create a new authorization record.
	 * Note: 	We want to ensure that only one user is logged in per device/client.
	 * 			If a device is being re-used, delete previous authorizations. 
	 * 
	 * @param erLhcoreClassModelUser 	$operator	The operator associated with the access token
	 * @param Device 					$device		The device associated with the access token
	 * @param string 					$clientID	The client ID of the app
	 * @param boolean 					$newDevice	Indicates if this is a new device being added
	 * 
	 * @return Authorization|bool	An Authorization instance. 
	 */
	 private static function createAuthorization($operator, $device, $clientID, $newDevice) {
	 	
		// Check if we need to delete previous authorizations.
		if (!$newDevice) {
			$prevAuths = Authorization::fecthAllByDevice($device->id);
			foreach($prevAuths as $auth) {
				$auth->delete();
			} 
		}

		$newAccessToken = AccessManagerAPI::generateAccessToken($operator->username);
		$newRefreshToken = AccessManagerAPI::generateRefreshToken($device->deviceuuid);
		
		$authorization = Authorization::createNewAuhtorization(
							$newAccessToken['accesstoken'],
							$newAccessToken['expiretime'],
							$newAccessToken['created'],
							$newRefreshToken['refreshtoken'],
							$newRefreshToken['expiretime'],
							$newRefreshToken['created'],
							$operator->id, 
							$device->id,
							$clientID);

		return $authorization;
	 }

	private static function generateAccessToken($login) 
	{
		$currTime = time();
		$expireTime = $currTime + Constants::ACCESS_DURATION;
		
		// Create access token: sha256 of operator login + time
		$tmp = Constants::TOKEN_VERSION . "\x0" . $expireTime . "\x0";
		$tmp .= hash("sha256", $login . $currTime, true);
		$accesstoken = strtr(base64_encode($tmp), '+/=', '-_,');
		
		return array('accesstoken' => $accesstoken,
					 'expiretime' => $expireTime,
					 'created' => $currTime);
	}
	
	
	private static function generateRefreshToken($deviceuuid) 
	{
		$currTime = time();
		$expireTime = $currTime + Constants::REFRESH_DURATION;
		
		// Create refresh token: sha256 of deviceuuid + time
		// We need to hash the entire tmp string such that the tokens are randomized.
		// Without this, all tokens will begin with the same string sequence
		$tmp = Constants::TOKEN_VERSION . "\x0" . $expireTime . "\x0";
		$tmp .= hash("sha256", $deviceuuid . $currTime, true);
		$refreshtoken = strtr(base64_encode($tmp), '+/=', '-_,');
		
		return array('refreshtoken' => $refreshtoken,
					 'expiretime' => $expireTime,
					 'created' => $currTime);
	}
	 
    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
 

