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



 /**
  * Controller that handles interactions with chat threads
  * 
  * This controller returns JSON encoded output. The output format can 
  * be abstracted such that there is an output factory that will return
  * the results in the requested format.
  */
class ThreadController extends AbstractController
{
    /**
     * Gets a list of messages for the thread
     *
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function bulkUpdateMessagesAction(Request $request)
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
				
				$threadArray = json_decode($request->getContent(), true);
		        $json_error_code = json_last_error();
		        if ($json_error_code != JSON_ERROR_NONE) {
		            // Not valid JSON
		            throw new Exception\HttpException(
									Response::HTTP_BAD_REQUEST,
									Constants::MSG_INVALID_JSON);
				}
				
				$requestedThreads = $threadArray['threads'];
				
				$arrayOut = ChatUtil::updateMessages($requestedThreads); 
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

    /**
     * Starts a chat session
     *
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function startChatAction(Request $request)
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
				
				$threadId = $request->attributes->getInt('threadid');
				ChatUtil::startChat($threadId);
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


    /**
     * Closes a chat session
     *
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function closeChatAction(Request $request)
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
				
				$threadId = $request->attributes->getInt('threadid');
				ChatUtil::closeChat($threadId);
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

    /**
     * Posts messages to potentially multiple chat sessions
     *
     * @param Request $request Incoming request.
     * @return Response Rendered page content.
     */
    public function postMessagesAction(Request $request)
	{
		/*
		 * An example of the format of messages is
			{"threadmessages":
			 	[{"threadid":4, "messages":
			 		[{"message":"This is message from operator: 4-1", "clientmessageid":55},
			 		 {"message":"This is message from operator: 4-2", "clientmessageid":56}
			 		]},
			 	 {"threadid":5, "messages":
			 		[{"message":"This is message from operator: 5-1", "clientmessageid":57},
			 		 {"message":"This is message from operator: 5-2", "clientmessageid":58}
			 		]}
			 	]}
		 * 
		 * Output is of the form
				{
				  "threadconfirmations": [
				    {
				      "threadid": 9,
				      "confirmations": [
				        {
				          "clientmessageid": 55,
				          "servermessageid": "176"
				        },
				        {
				          "clientmessageid": 56,
				          "servermessageid": "177"
				        }
				      ]
				    }
				  ],
				  "message": "Success"
				}
		 * 
		======================================================*/	
			 	
		$httpStatus = Response::HTTP_OK;
		$message = Constants::MSG_SUCCESS;
		$arrayOut = array();
		$threadConfirmations = array();
		try {
			$xAuthToken = $this->getXAuthToken($request);
			$accessToken = $xAuthToken['accesstoken'];
			$deviceuuid = $xAuthToken['deviceuuid'];

			if (AccessManagerAPI::isAuthorized($accessToken, $deviceuuid)) {
				$authorization = Authorization::fetchByAccessToken($accessToken);
				$currentUser = \erLhcoreClassUser::instance();
				$currentUser->setLoggedUser($authorization->operatorid);
		        $userData = $currentUser->getUserData();
		        $messageUserId = $userData->id;
		        $messageNameSupport = $userData->name_support;
				
				// $threadId = $request->attributes->getInt('threadid');
				// ChatUtil::closeChat($threadId);

				$data = json_decode($request->getContent(), true);
		        $json_error_code = json_last_error();
		        if ($json_error_code != JSON_ERROR_NONE) {
		            // Not valid JSON
		            throw new Exception\HttpException(
									Response::HTTP_BAD_REQUEST,
									Constants::MSG_INVALID_JSON);
				}
				
				// error_log("ThreadController::postMessagesAction - data: " . print_r($data, true));
				$threadMessages = $data['threadmessages'];
				foreach($threadMessages as $threadMessage) {
					$threadId = $threadMessage['threadid'];
					$postedMessages = $threadMessage['messages'];
					$confirmationsForThread = array();

					foreach($postedMessages as $clientMessage) {
						$chatMessage = $clientMessage['message'];
						$clientMessageId = $clientMessage['clientmessageid'];
						
						// Post the message using ChatUtil
						try {
							$postedId = ChatUtil::postMessageToChatId($threadId, $chatMessage, 
											$messageUserId, $messageNameSupport, true);
							
						} catch (\Exception $ex) {
							// Indicate that this has an error and move to the next
							$postedId = -1;
						}

						// If the message couldn't be posted, the returned id will be -1
						
						$confirmation = array();
						$confirmation['clientmessageid'] = $clientMessageId;
						$confirmation['servermessageid'] = $postedId;
						$confirmationsForThread[] = $confirmation;
					}
					$threadConfirmation['threadid'] = $threadId;
					$threadConfirmation['confirmations'] = $confirmationsForThread;
					$threadConfirmations[] = $threadConfirmation;
				}
				
				$arrayOut['threadconfirmations'] = $threadConfirmations;
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

