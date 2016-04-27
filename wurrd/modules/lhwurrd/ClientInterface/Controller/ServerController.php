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
use Wurrd\ClientInterface\Model\Device;
use Wurrd\ClientInterface\Model\Authorization;

 
class ServerController 
{
    /**
     * Retrieves the server details that are available for 
	 * public consumption
	 * 
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function simpleInfoAction(Request $request)
	{
		$httpStatus = Response::HTTP_OK;

		// Determine if we should use POST for all 'input' requests
		/*$usePost = false;
		$configs = load_system_configs();
		if (!empty($configs['plugins']) &&
			!empty($configs['plugins']['Wurrd:ClientInterface'])) {
			$usePost = filter_var($configs['plugins']['Wurrd:ClientInterface']['use_http_post'], 
								FILTER_VALIDATE_BOOLEAN);
		}

		$arrayOut = array('message' => Constants::MSG_SUCCESS,
						  'apiversion' => Constants::WCI_API_VERSION,
						  'usepost' => $usePost,
						  );

		*/

		// $device = Device::fetch(3);		
		/*$device = Device::fetchByUUID('zend', 'droid');
		error_log(print_r($device, true));
		
		// Update the modified time.
		if ($device !== false) {
			// $device->dtmmodified = time();
			// $device->saveThis();
			$device->delete();
		}*/
		
		$authorizations = Authorization::fecthAllByDevice(2);
		foreach($authorizations as $auth) {
			error_log('delete auth id ' . $auth->id);
			$auth->delete();
		}

		$arrayOut = array('message' => Constants::MSG_SUCCESS,
						  'apiversion' => Constants::WCI_API_VERSION,
						  'chatplatform' => Constants::WCI_CHAT_PLATFORM,
						  'usepost' => false,
						  //'deviceuuid' => $device->deviceuuid,
						  //'platform' => $device->platform,
						  // 'device_id' => $device->id,
						  );

		$response = new Response(json_encode($arrayOut),
								Response::HTTP_OK,
								array('content-type' => 'application/json'));
		return $response;
    }
}




 
 
 
 
 ?>