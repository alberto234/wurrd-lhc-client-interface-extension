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

/**
 * This is a utility class that backs the ServerController
 * 
 */
class ServerUtil
{
	/**
     * Retrieves detailed server information available only after
	 * authentication
	 * 
	 * @param array $args - An array containing the arguments needed for the
	 * 					    access token to be generated. The arguments are
	 * 						defined in Constants.php are.
	 * 
	 * @return array|bool  An array with the server details or false if a failure
	 */
	 public static function getDetailedInfo($args) {

        $accessToken = $args[Constants::ACCESSTOKEN_KEY];
		$deviceuuid = $args[Constants::DEVICEUUID_KEY];
		
		if (AccessManagerAPI::isAuthorized($accessToken, $deviceuuid)) {

			$globalConfigs = \erConfigClassLhConfig::getInstance();
			$configs = Config::getInstance();
			
			// If it is not valid, use the site_admin_email
			$contactEmail = $configs->getSetting('general', 'contact_email');
			if (!\ezcMailTools::validateEmailAddress($contactEmail)) {
				$contactEmail = $globalConfigs->getSetting('site', 'site_admin_email');
			}
			
			$installationid = $configs->getSetting('general', Constants::WCI_INSTALLATION_ID_KEY);
			// If this is not valid, throw an exception requesting that the user install the extension.
			// We should actually catch this way before this, in the entry clientinterface.php file
			
			return array(
						'chatplatform' => Constants::WCI_CHAT_PLATFORM,
						'chatplatformversion' => \erLhcoreClassUpdate::LHC_RELEASE / 100,
						'interfaceversion' => Constants::WCI_VERSION,
						'apiversion' => Constants::WCI_API_VERSION,
						'authapiversion' => AccessManagerAPI::getAuthAPIPluginVersion(),
						'installationid' => $installationid,
						'name' => $globalConfigs->getSetting('site', 'title'),
						'logourl' => '', // We will sort this later. Logos are set by theme, however we are looking for admin logo
						'usepost' => ServerUtil::usePost(),
						'email' => $contactEmail,
					);
		} else {
			// This shouldn't get here as an exception will be thrown if access is not valid
			return false;
		}
	 }

	/**
	 * Determine if we should use POST for all 'input' requests
	 */
	public static function usePost() {
		return filter_var(Config::getInstance()->getSetting('general', 'use_http_post', false), 
			FILTER_VALIDATE_BOOLEAN);
	}
	
	
}


 