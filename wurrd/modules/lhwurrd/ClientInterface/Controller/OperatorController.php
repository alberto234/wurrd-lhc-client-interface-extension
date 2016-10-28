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
use Wurrd\ClientInterface\Classes\AccessManagerAPI;
use Wurrd\ClientInterface\Classes\OperatorUtil;
use Wurrd\ClientInterface\Classes\ServerUtil;
use Wurrd\ClientInterface\Classes\UrlGeneratorUtil;
use Wurrd\ClientInterface\Constants;
use Wurrd\ClientInterface\Model\Device;
use Wurrd\ClientInterface\Model\Authorization;
use Wurrd\Http\Exception;

 /**
  * Controller that handles operator interactions
  * 
  * This controller returns JSON encoded output. The output format can 
  * be abstracted such that there is an output factory that will return
  * the results in the requested format.
  */
class OperatorController extends AbstractController
{
    /**
     * Authorizes the user of a client to the system,
	 * and returns access and refresh tokens.
     *
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function loginAction(Request $request)
	{
		$httpStatus = Response::HTTP_OK;
		$message = Constants::MSG_SUCCESS;
		$arrayOut = array();
		
		$requestPayload = $request->getContent();
		$authRequest = json_decode($requestPayload, true);
        $json_error_code = json_last_error();
        if ($json_error_code != JSON_ERROR_NONE) {
            // Not valid JSON
            $message = Constants::MSG_INVALID_JSON;
			$arrayOut['requestpayload'] = $requestPayload;
            $httpStatus = Response::HTTP_BAD_REQUEST;
		} else {
			try {
				// We need to validate the client id here.
				$clientID = $authRequest[Constants::CLIENTID_KEY];
				if ($clientID != 'AABBB-CCCCC-DEFGHIJ') {
					throw new Exception\AccessDeniedException(Constants::MSG_INVALID_CLIENTID);
				}

				$authorization = AccessManagerAPI::requestAccess($authRequest);
				$authArray = array(
							'accesstoken' => $authorization->accesstoken,
							'accessexpire' => $authorization->dtmaccessexpires,
							'accesscreated' => $authorization->dtmaccesscreated,
							'refreshtoken' => $authorization->refreshtoken,
							'refreshexpire' => $authorization->dtmrefreshexpires,
							'refreshcreated' => $authorization->dtmrefreshcreated,
							);
							
				$operatorArray = OperatorUtil::getInfo(array('accesstoken' => $authorization->accesstoken,
															 'deviceuuid' => $authRequest[Constants::DEVICEUUID_KEY],
															 ));
				
				// Fix the operator's avatar by getting the URL from the relative path
				if ($operatorArray['avatarurl'] != null && strlen($operatorArray['avatarurl']) > 0) {
					$operatorArray['avatarurl'] = UrlGeneratorUtil::getFullURL($request, $operatorArray['avatarurl']);
				}
				
				$serverInfoArray = ServerUtil::getDetailedInfo(array('accesstoken' => $authorization->accesstoken,
															 'deviceuuid' => $authRequest[Constants::DEVICEUUID_KEY],
															 ));
				
				// Fix the logo url if necessary
				$serverInfoArray['logourl'] = UrlGeneratorUtil::getFullURL($request, $serverInfoArray['logourl']);
				
				$arrayOut['authorization'] = $authArray;
				$arrayOut['operator'] = $operatorArray;
				$arrayOut['server'] = $serverInfoArray;
			} catch(Exception\HttpException $e) {
				$httpStatus = $e->getStatusCode();
				$message = $e->getMessage();
			}
		}

		$arrayOut['message'] = $message;
		$response = new Response(json_encode($arrayOut),
								$httpStatus,
								array('content-type' => 'application/json'));
		return $response;
  
    }


    /**
     * Refresh the access token - Used to refresh an expired token
     * 
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function refreshAccessAction(Request $request)
	{
		$httpStatus = Response::HTTP_OK;
		$message = Constants::MSG_SUCCESS;
		$arrayOut = array();
		
		try {
			$xAuthToken = $this->getXAuthToken($request);
			$accessToken = $xAuthToken['accesstoken'];
			$deviceuuid = $xAuthToken['deviceuuid'];

			$data = json_decode($request->getContent(), true);
	        $json_error_code = json_last_error();
	        if ($json_error_code != JSON_ERROR_NONE) {
	            // Not valid JSON
	            throw new Exception\HttpException(
								Response::HTTP_BAD_REQUEST,
								Constants::MSG_INVALID_JSON);
			}

			$refreshToken = $data['refreshtoken'];
			
			$authorization = AccessManagerAPI::refreshAccess($accessToken, $refreshToken, $deviceuuid);
			$arrayOut = array(
						'accesstoken' => $authorization->accesstoken,
						'accessexpire' => $authorization->dtmaccessexpires,
						'accesscreated' => $authorization->dtmaccesscreated,
						'refreshtoken' => $authorization->refreshtoken,
						'refreshexpire' => $authorization->dtmrefreshexpires,
						'refreshcreated' => $authorization->dtmrefreshcreated,
						);
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


    /**
     * Log the operator out of the system.
     *
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function logoutAction(Request $request)
	{
		$httpStatus = Response::HTTP_OK;
		$message = Constants::MSG_SUCCESS;
		
		try {
			$xAuthToken = $this->getXAuthToken($request);
			$accessToken = $xAuthToken['accesstoken'];
			$deviceuuid = $xAuthToken['deviceuuid'];
			
			if (AccessManagerAPI::dropAccess($accessToken, $deviceuuid)) {
				// Do nothing, $message has already been set
			}
		} catch(Exception\HttpException $e) {
			$httpStatus = $e->getStatusCode();
			if ($httpStatus == 400) {
				$message = Constants::MSG_INVALID_ACCESS_TOKEN;
			} else {
				$message = $e->getMessage();
			}
		}
		
		$response = new Response(json_encode(array('accesstoken' => $accessToken,
													'message' => $message)),
								$httpStatus,
								array('content-type' => 'application/json'));
		return $response;
    }

    /**
     * Retrieves the operator's detals
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
		
			$arrayOut = OperatorUtil::getInfo($args);

			// Fix the operator's avatar by getting the URL from the relative path
			if ($arrayOut['avatarurl'] != null && strlen($arrayOut['avatarurl']) > 0) {
				$arrayOut['avatarurl'] = UrlGeneratorUtil::getFullURL($request, $arrayOut['avatarurl']);
			}
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

