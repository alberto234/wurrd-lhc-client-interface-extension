<?php
/*
 * This file is a part of Wurrd Client Interface Extension for LiveHelperChat.
 *
 * Copyright 2016 Eyong N <eyongn@scalior.com>.
 *
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

namespace Wurrd\ClientInterface\Model;

use Wurrd\ClientInterface\Classes\PersistentSession;


/**
 * A class that represents an Authorization entity.
 * 
 * Note: This class contains methods for persistence. Ideally persistence should be 
 * 		 moved to a persistence manager such that users of this class wouldn't be 
 * 		 able to inadvertently change its state in persistence.
 */
class Authorization
{
    /**
     * Unique authorization ID.
     *
     * @var int
     */
    public $id;

    /**
     * The operator id associated with this authorization
     *
     * @var int
     */
    public $operatorid;

    /**
     * The device id associated with this authorization
     *
     * @var int
     */
    public $deviceid;

    /**
     * ID of the client application that is requesting the authorization.
     *
     * @var string
     */
    public $clientid;

    /**
     * The access token generated for this authorization
     *
     * @var string
     */
    public $accesstoken;

    /**
     * Unix timestamp of the moment when the access token was created.
     *
     * @var int
     */
    public $dtmaccesscreated;

    /**
     * When the access token expires.
     *
     * @var int
     */
    public $dtmaccessexpires;

    /**
     * The refresh token generated for this authorization
     *
     * @var string
     */
    public $refreshtoken;

    /**
     * Unix timestamp of the moment when the refresh token was created.
     *
     * @var int
     */
    public $dtmrefreshcreated;

    /**
     * When the refresh token expires.
     *
     * @var int
     */
    public $dtmrefreshexpires;

    /**
     * The previous access token generated for this authorization
     *
     * @var string
     */
    public $previousaccesstoken;

    /**
     * The previous refresh token generated for this authorization
     *
     * @var string
     */
    public $previousrefreshtoken;
	
    /**
     * Unix timestamp of the moment this record was created.
     * @var int
     */
    public $dtmcreated;

    /**
     * Unix timestamp of the moment this record was modified.
     * @var int
     */
    public $dtmmodified;

	// Constants
	const CLASS_NAME 				= 'Wurrd\\ClientInterface\\Model\\Authorization';


    /**
     * Loads authorization by its ID.
     *
     * @param int $id ID of the authorization to load
     * @return boolean|Authorization Returns an Authorization instance or boolean false on failure.
     */
    public static function fetch($id)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        // Load authorization info
        try {
			$authorization = PersistentSession::getInstance()->load(self::CLASS_NAME, (int)$id);
		} catch (\Exception $ex) {
	        // There is no authorization with such id in database
			// error_log("No authorization with id $id");
			return false;
		}

        return $authorization;
    }

    /**
     * Returns an array of authorizations for a given device.
     *
     * @param int	$deviceID 	ID of the device to query
     * @return array	Returns an array of Authorizations.
     */
    public static function fecthAllByDevice($deviceID) {
    	$authorizations = array();
		
	   	$q = PersistentSession::getInstance()->createFindQuery(self::CLASS_NAME);
		$conditions = array();
		$conditions[] = $q->expr->eq('deviceid', $q->bindValue($deviceID) );
		$q->where ($conditions);

		try {
			$authorizations = PersistentSession::getInstance()->find($q);
		} catch (\Exception $ex) {
			// error_log('No authorizations found for device ' . $deviceID);
		}

        return $authorizations;
	}
	
	
    /**
     * Fetch authorization by access token.
     *
     * @param string $accessToken The access token.
     * @return boolean|Authorization Returns an Authorization instance or boolean false on failure.
     */
    public static function fetchByAccessToken($accessToken)
    {
        // Check parameters
        if (empty($accessToken)) {
            return false;
        }

        // Load authorization info
	   	$q = PersistentSession::getInstance()->createFindQuery(self::CLASS_NAME);
		$conditions = array();
		$conditions[] = $q->expr->eq('accesstoken', $q->bindValue($accessToken));
		$q->where ($conditions);
		try {
			$authorizations = PersistentSession::getInstance()->find($q);
			if (!is_null($authorizations) && count($authorizations) > 0) {
				// There should only be one.
				foreach ($authorizations as $key => $authorization) {
					return $authorization;
				}
			}
		} catch (\Exception $ex) {
			//error_log('No authorizations found for device ' . $deviceID);
		}

        return false;
    }

    /**
     * Loads authorization by previous access token.
     *
     * @param string $previousAccessToken The previous access token.
     * @return boolean|Authorization Returns an Authorization instance or boolean false on failure.
     */
    public static function fetchByPreviousAccessToken($previousAccessToken)
    {
        // Check parameters
        if (empty($previousAccessToken)) {
            return false;
        }

        // Load authorization info
        // Load authorization info
	   	$q = PersistentSession::getInstance()->createFindQuery(self::CLASS_NAME);
		$conditions = array();
		$conditions[] = $q->expr->eq('accesstoken', $q->bindValue($previousAccessToken));
		$q->where ($conditions);
		try {
			$authorizations = PersistentSession::getInstance()->find($q);
			if (!is_null($authorizations) && count($authorizations) > 0) {
				// There should only be one.
				foreach ($authorizations as $key => $authorization) {
					return $authorization;
				}
			}
		} catch (\Exception $ex) {
			//error_log('No authorizations found for device ' . $deviceID);
		}

        return false;
    }

    /**
     * Create a new authorization
     *
     */
    public static function createNewAuhtorization($accessToken, $accessExpire, $accessCreated,
    	$refreshToken, $refreshExpire, $refreshCreated, $operatorid, $deviceid, $clientid)
    {
		$now = time();
    	$db_fields = array('operatorid' => (int)$operatorid,
    					   'deviceid' => (int)$deviceid,
    					   'clientid' => $clientid,
    					   'accesstoken' => $accessToken,
    					   'dtmaccesscreated' => $accessCreated,
    					   'dtmaccessexpires' => $accessExpire,
    					   'refreshtoken' => $refreshToken,
    					   'dtmrefreshcreated' => $refreshCreated,
    					   'dtmrefreshexpires' => $refreshExpire,
    					   'previousaccesstoken' => null,
    					   'previousrefreshtoken' => null,
						   'dtmcreated' => $now,
						   'dtmmodified' => $now,
    				);

        // Create and populate object
        $authorization = new self();
        $authorization->setState($db_fields);

        return $authorization;
    }

    /**
     * Save the authorization to the database.
     *
     */
    public function saveThis()
    {
   		PersistentSession::getInstance()->saveOrUpdate($this);
    }

    /**
     * Remove authorization from the database.
     *
     */
    public function delete()
    {
        if (!$this->id) {
            throw new \RuntimeException('You cannot delete an authorization without id');
        }

		PersistentSession::getInstance()->delete($this);
    }


   public function getState()
   {
       return array(
       			   	'id' 				=> $this->id,
				   	'operatorid'	 	=> $this->operatorid,
				   	'deviceid' 			=> $this->deviceid,
				   	'clientid' 			=> $this->clientid,
				   	'accesstoken' 		=> $this->accesstoken,
				   	'dtmaccesscreated' 	=> $this->dtmaccesscreated,
				   	'dtmaccessexpires' 	=> $this->dtmaccessexpires,
				   	'refreshtoken' 		=> $this->refreshtoken,
				   	'dtmrefreshcreated' => $this->dtmrefreshcreated,
				   	'dtmrefreshexpires' => $this->dtmrefreshexpires,
				   	'previousaccesstoken' => $this->previousaccesstoken,
				   	'previousrefreshtoken' => $this->previousrefreshtoken,
				   	'dtmcreated' 		=> $this->dtmcreated,
				   	'dtmmodified' 		=> $this->dtmmodified,
             );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

 }