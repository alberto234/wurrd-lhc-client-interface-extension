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
use Wurrd\ClientInterface\Classes\CannedResponsesUtil;
use Wurrd\ClientInterface\Constants;
use Wurrd\ClientInterface\Model\Device;
use Wurrd\ClientInterface\Model\Authorization;
use Wurrd\Http\Exception;
 
class CannedResponsesController extends AbstractController
{
    /**
	/**
	 * Retrieves the canned responses for the departments that this operator belongs to.
	 * 
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function getCannedResponsesAction(Request $request)
	{
		$httpStatus = Response::HTTP_OK;
		$message = Constants::MSG_SUCCESS;
		$arrayOut = array();

		try {
			$xAuthToken = $this->getXAuthToken($request);
			$accessToken = $xAuthToken['accesstoken'];
			$deviceuuid = $xAuthToken['deviceuuid'];

			if (AccessManagerAPI::isAuthorized($accessToken, $deviceuuid)) {
				$authorization = Authorization::fetchByAccessToken($accessToken);
				$currentUser = \erLhcoreClassUser::instance();
				$currentUser->setLoggedUser($authorization->operatorid);
				
				// returns id, [locale], groupid, title, response
				$arrayOut['cannedresponses'] = CannedResponsesUtil::getCannedResponses($currentUser);
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




 
 
 
 
 ?>