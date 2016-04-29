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


/**
 * This is a utility class that backs the OperatorController
 * 
 */
class OperatorUtil
{
	/**
     * Retrieves operator information
	 * 
	 * @param array $args - An array containing the arguments needed for the
	 * 					    access token to be generated. The arguments are
	 * 						defined in Constants.php are.
	 * 
	 * @return array|bool  An array with the server details or false if a failure
	 */
	 public static function getInfo($args) {

        $accessToken = $args[Constants::ACCESSTOKEN_KEY];
		$deviceuuid = $args[Constants::DEVICEUUID_KEY];
		
		if (AccessManagerAPI::isAuthorized($accessToken, $deviceuuid)) {
			$authorization = Authorization::fetchByAccessToken($accessToken);
			$operator = \erLhcoreClassModelUser::findOne(array(
				'filter' => array(
					'id' => $authorization->operatorid
				)
			));  
	
			return OperatorUtil::getInfoFromOperator($operator);
			
		} else {
			// This shouldn't get here as an exception will be thrown if access is not valid
			return false;
		}
	 }


	/**
     * Retrieves operator information
	 * 
	 * @param erLhcoreClassModelUser $operator - The operator
	 * 
	 * @return array|bool  An array with the server details or false if a failure
	 */
	 public static function getInfoFromOperator($operator) {
	 	if (is_object($operator)) {
			return array('operatorid' => $operator->id,
						 'username' => $operator->username,
						 'email' => $operator->email,
						 'commonname' => $operator->name_official,
						 'localename' => $operator->name_support,
						 'avatarurl' => $operator->has_photo ? $operator->photo_path : '',
					);
		} else {
			return false;
		}
	 }
}


 