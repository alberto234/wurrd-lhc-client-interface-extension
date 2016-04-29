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

namespace Wurrd\ClientInterface\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wurrd\ClientInterface\Constants;
use Wurrd\Http\Exception;

/**
 * Implements abstract class for a controller.
 * 
 * This class contains common methods used by the controller
*/
abstract class AbstractController {

	/**
	 * Get X-Auth-Token.
	 * 		The token parameter should be supplied of the form <deviceuuid>:<accesstoken>
	 * 
	 * @param Request $request	The request to extract the token header from
	 * @return array holding 'accesstoken' and 'deviceuuid' elements. 
	 * 
	 * @throws \Wurrd\Http\Exception\AccessDeniedException
	 */
	protected function getXAuthToken(Request $request) {
		$xauthToken = array();
		
		$tmp = $request->headers->get('x-auth-token');
		$requestToken = explode(':', $tmp);
		if (count($requestToken) == 2) {
			$xauthToken['deviceuuid'] = $requestToken[0];
			$xauthToken['accesstoken'] = $requestToken[1];
			return $xauthToken;
		}
		
		// If we get here, something went wrong
		throw new Exception\AccessDeniedException(Constants::MSG_INVALID_ACCESS_TOKEN);
	}	
}

?>