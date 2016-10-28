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
use Wurrd\ClientInterface\Classes\ServerUtil;
use Wurrd\ClientInterface\Classes\UrlGeneratorUtil;
use Wurrd\ClientInterface\Constants;
use Wurrd\ClientInterface\Model\Device;
use Wurrd\ClientInterface\Model\Authorization;
use Wurrd\Http\Exception;
 
class ServerController extends AbstractController
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

		$arrayOut = array('message' => Constants::MSG_SUCCESS,
						  'apiversion' => Constants::WCI_API_VERSION,
						  'chatplatform' => Constants::WCI_CHAT_PLATFORM,
						  'usepost' => ServerUtil::usePost(),
						  );

		$response = new Response(json_encode($arrayOut),
								Response::HTTP_OK,
								array('content-type' => 'application/json'));
		return $response;
    }


    /**
     * Retrieves detailed server information available only after
	 * authentication
	 * 
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function detailInfoAction(Request $request)
	{
		$httpStatus = Response::HTTP_OK;
		$message = Constants::MSG_SUCCESS;
		$arrayOut = array();
		
		try {
			$xAuthToken = $this->getXAuthToken($request);
			$args = array(Constants::ACCESSTOKEN_KEY => $xAuthToken['accesstoken'],
					  	Constants::DEVICEUUID_KEY => $xAuthToken['deviceuuid']);
		
			$arrayOut = ServerUtil::getDetailedInfo($args);
			
			// Fix the logo url if necessary
			$arrayOut['logourl'] = UrlGeneratorUtil::getFullURL($request, $arrayOut['logourl']);
			
		} catch(Exception\HttpException $e) {
			$httpStatus = $e->getStatusCode();
			$message = $e->getMessage();
		}
		
		$arrayOut['message'] = $message;
		$response = new Response(json_encode($arrayOut),
								$httpStatus,
								array('content-type' => 'application/json'));
		return $response;
  
    }

}




 
 
 
 
 ?>