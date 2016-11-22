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
use Wurrd\ClientInterface\Classes\ChatUtil;
use Wurrd\ClientInterface\Constants;
use Wurrd\ClientInterface\Model\Device;
use Wurrd\ClientInterface\Model\Authorization;
use Wurrd\Http\Exception;
 
class NotificationController extends AbstractController
{

    /**
     * Retrieves the server details that are available for 
	 * public consumption
	 * 
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function bulkCheckForUpdatesAction(Request $request)
	{
		$httpStatus = Response::HTTP_OK;
		$message = Constants::MSG_SUCCESS;
		$arrayOut = array();
		$notificationArray = array();


		try {
			$data = json_decode($request->getContent(), true);
	        $json_error_code = json_last_error();
	        if ($json_error_code != JSON_ERROR_NONE) {
	            // Not valid JSON
	            throw new Exception\HttpException(
								Response::HTTP_BAD_REQUEST,
								Constants::MSG_INVALID_JSON);
			}
			
			$clientArray = $data['clientstates'];

			foreach($clientArray as $client) {
				$clientNotification = array();
				$clientNotification['accesstoken'] = $client['accesstoken'];
	
				// We want to isolate each client and return an appropriate status message for the client
				// so we are surrounding each in a try-catch block
				try {
					$threadListUpdates = $this->checkForThreadListUpdate($client);
					if ($threadListUpdates != null) {
						$clientNotification['threadrevision'] = $threadListUpdates['lastrevision'];
						
						// We are here
						$activeThreadsUpdates = $this->checkForActiveThreadUpdates($client);
						
						// error_log("ActiveThreadsUpdates " . print_r($activeThreadsUpdates, true));
						
						if ($activeThreadsUpdates != null) {
							$clientNotification['activethreads'] = $activeThreadsUpdates['threadmessages'];
						}
						
						$clientNotification['message'] = Constants::MSG_SUCCESS;
					}
				} catch(Exception\HttpException $e) {
					$clientNotification['message'] = $e->getMessage();
				}
				
				$notificationArray[] = $clientNotification;
			}
				
			$arrayOut['notificationlist'] = $notificationArray;	
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


	private function checkForThreadListUpdate($client) {
		$arrayOut = array('lastrevision' => 0);
		if (AccessManagerAPI::isAuthorized($client['accesstoken'], null, false)) {
			$authorization = Authorization::fetchByAccessToken($client['accesstoken']);
			$currentUser = \erLhcoreClassUser::instance();
			$currentUser->setLoggedUser($authorization->operatorid);

			$newRevision = 0;
			$activeChats = ChatUtil::getUpdatedThreads($currentUser, (int)$client['threadrevision'], $newRevision);
			$arrayOut['lastrevision'] = $newRevision;
			
			// Update the current user's last activity
			$currentUser->updateLastVisit();
		}
		
		return $arrayOut;
	}
	

	private function checkForActiveThreadUpdates($client) {
		$arrayOut = array('threadmessages' => array());
		if (AccessManagerAPI::isAuthorized($client['accesstoken'], null, false)) {
			$authorization = Authorization::fetchByAccessToken($client['accesstoken']);
			$currentUser = \erLhcoreClassUser::instance();
			$currentUser->setLoggedUser($authorization->operatorid);

			$threadMessages =  ChatUtil::updateMessages($client['activethreads']);
			
			$output = array();
			foreach($threadMessages['threadmessages'] as $threadMessage) {
				$temp = array();
				$temp['threadid'] = $threadMessage['threadid'];
				$temp['lastid'] = $threadMessage['lastid'];
				if ($threadMessage['lastfootprintid'] != null) {
					$temp['lastfootprintid'] = $threadMessage['lastfootprintid'];
				}
				$output[] = $temp;
			}
			
			$arrayOut['threadmessages'] = $output;
		}
		
		return $arrayOut;
	}

}




 
 
 
 
 ?>