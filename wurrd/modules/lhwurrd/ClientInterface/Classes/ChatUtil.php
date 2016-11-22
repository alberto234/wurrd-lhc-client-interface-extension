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


use Symfony\Component\HttpFoundation\Response;
use Wurrd\ClientInterface\Constants;
use Wurrd\ClientInterface\Model\ChatExtension;
use Wurrd\Http\Exception;


/**
 * This is a utility class that extends the functions of the core chat class
 * 
 * @author Eyong N <eyongn@scalior.com>		Sept 15, 2016
 */
class ChatUtil extends  \erLhcoreClassChat
{
	/**
	 * Updated threads are defined as threads that this operator has not yet received updates for.
	 * For LiveHelperChat, these include pending, active, closed, and blocked threads that the
	 * client doesn't already know about.
	 */
	public static function getUpdatedThreads($currentUser, $requestRevision, &$newRevision) {
		// Check to see if there have been any changes since the last revision
		$newRevision = $requestRevision;
		$canditateChats = ChatExtension::fecthUpdatedThreads($requestRevision);
		if (count($canditateChats) == 0) {
			// (Not working) $newRevision = ChatExtension::getHighestChatRevision();
			return array();
		}
		
  	 	$limitation = self::getDepartmentLimitation();

    	// Does not have any assigned department
    	if ($limitation === false) {
			// (Not working) $newRevision = ChatExtension::getHighestChatRevision();
    		return array(); 
		}
    	

    	$filter = array();
    	// $filter['filter'] = array('status' => 1);

    	if ($limitation !== true) {
    		$filter['customfilter'][] = $limitation;
    		$filter['use_index'] = 'status_dep_id_id';
    	}

    	$filter['limit'] = 100;
    	$filter['offset'] = 0;
    	$filter['smart_select'] = true;
		
		if ($requestRevision == 0) {
			// Client doesn't have any record of previous chats. 
			// Filter out closed chats
			
			// In the array we are below defining, the keys are not used.
	    	$filter['filterin']['status'] = array('pending' => \erLhcoreClassModelChat::STATUS_PENDING_CHAT,
												  'active' 	=> \erLhcoreClassModelChat::STATUS_ACTIVE_CHAT);
		}
		
		// Add an "IN" filter for the candidate threads that we are looking at.
		$chatIds = array();
		$first = true;
		foreach($canditateChats as $oneCandidate) {
			$chatIds[] = $oneCandidate->chatid;
			if ($newRevision < $oneCandidate->revision) {
				$newRevision = $oneCandidate->revision;
			}
		}

		$filter['filterin']['id'] = $chatIds;

    	$tempThreads = self::getList($filter);
		
		// We now want to filter out unneeded columns.
		// Ideally we should accomplish this in the query
		$updatedThreads = array();
		foreach($tempThreads as $oneThread) {
			$oneUpdatedThread = array();
			$oneUpdatedThread['id'] 			= $oneThread->id;
			$oneUpdatedThread['userName'] 		= $oneThread->nick;
			$oneUpdatedThread['remote'] 		= $oneThread->ip;
			$oneUpdatedThread['agentId'] 		= $oneThread->user_id;
			$oneUpdatedThread['state'] 			= self::convertToWurrdChatState($oneThread->status);
			$oneUpdatedThread['totalTime'] 		= $oneThread->time;
			$oneUpdatedThread['locale']			= $oneThread->chat_locale;
			$oneUpdatedThread['groupid'] 		= $oneThread->dep_id;
			
			$updatedThreads[] = $oneUpdatedThread;
		}

		return $updatedThreads;
	}



    /**
     * Gets new messages for the threads supplied
     *
     * @param array $requestThreads Threads that we are interested in.
     * @return array of messages for each of the supplied threads
     */
    public static function updateMessages($requestThreads)
	{
		if (count($requestThreads) == 0) {
			return null;
		}
		
		$arrayOut = null;
		$threadMessages = array();

		// The logic from here has been lifted from chatssynchro.php
		// We need to ensure that as the core is upgraded this logic doesn't
		// get out of sync.

		foreach($requestThreads as $requestThread) {
			$chatId = $requestThread['threadid'];
			$minMessageId = $requestThread['lastid'];
			$chat = self::getSession()->load('erLhcoreClassModelChat', $chatId);
			if ($chat != null && self::hasAccessToRead($chat)) {
				// Get a list of chat messages
				$messages = self::getPendingMessages($chatId, $minMessageId);
				$messagesArray = array();
				$newLastId = $minMessageId;
				foreach ($messages as $message) {
					// Map from LHC to Wurrd
					$wurrdMessage = array();
					$wurrdMessage['id'] 		= (int)$message['id'];
					$wurrdMessage['message'] 	= $message['msg'];
					$wurrdMessage['created']	= (int)$message['time'];
					$wurrdMessage['agentid'] 	= (int)$message['user_id'];
					$wurrdMessage['name'] 		= $message['name_support'];
					
					// 'kind' is derived from user_id. In Wurrd, the following
					// message types are supported.
					// CHAT_MESSAGE_TYPE_USER		= 1;
					// CHAT_MESSAGE_TYPE_AGENT		= 2;
					// CHAT_MESSAGE_TYPE_FOR_AGENT	= 3;
					// CHAT_MESSAGE_TYPE_INFO		= 4;
					// CHAT_MESSAGE_TYPE_CONN		= 5;
					// 	CHAT_MESSAGE_TYPE_EVENTS	= 6;
					// 	CHAT_MESSAGE_TYPE_PLUGIN	= 7;
					$type = 1;
					if ($message['user_id'] == -1) {
						$type = 6;
					} else if ($message['user_id'] > 0) {
						$type = 2;
					}
					$wurrdMessage['kind'] 	= $type;
					
					if ($newLastId < $message['id']) {
						$newLastId = $message['id'];
					}
					
					$messagesArray[] = $wurrdMessage;
				}

				// Get any new footprint records...
				$newLastFootPrintId = null;
				$visitorFootPrint = self::getFootPrint($chat, $requestThread['lastfootprintid'], $newLastFootPrintId);


				$chatMessages = array();
				$chatMessages['threadid'] = (int)$chatId;
				$chatMessages['lastid'] = (int)$newLastId;
				$chatMessages['messages'] = $messagesArray;
				if ($visitorFootPrint !== null) {
					$chatMessages['lastfootprintid'] = (int)$newLastFootPrintId;
					$chatMessages['footprints'] = $visitorFootPrint;
				}

				$threadMessages[] = $chatMessages;
			} else {
				// Log that chat wasn't found
			}
		}

		$arrayOut['threadmessages'] = $threadMessages;
		return $arrayOut;
    }

	/**
	 * Start a chat session with a guest
	 */
	public static function startChat($chatId) {
		if ($chatId == null) {
	        throw new Exception\HttpException(Response::HTTP_BAD_REQUEST, 
	        									Constants::MSG_WRONG_THREAD);
		}
		
		try {
			$chat = self::getSession()->load( 'erLhcoreClassModelChat', $chatId);
			
		} catch (\ezcPersistentObjectNotFoundException $ex) {
	        throw new Exception\HttpException(Response::HTTP_BAD_REQUEST, 
	        									Constants::MSG_WRONG_THREAD);
		}

		if (!self::hasAccessToRead($chat)) {
			throw new Exception\AccessDeniedException(Constants::MSG_CANNOT_VIEW_THREADS);
		}

		// -------------
		// This logic has been lifted from xml/chatdata
		// We should watch out for when it is updated in the core.
		// Ideally the core should provide a method for this so that
		// this functionality is done only in one place.
		// -------------
			
		// If status is pending change status to active		
        $operatorAccepted = false;
        $chatDataChanged = false;
        
        if ($chat->user_id == 0) {
        	$currentUser =  \erLhcoreClassUser::instance();
        	$chat->user_id = $currentUser->getUserID();
        	$chatDataChanged = true;
        }
         
        // If status is pending change status to active
        if ($chat->status == \erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
        	$chat->status = \erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
        
        	if ($chat->wait_time == 0) {
        		$chat->wait_time = time() - $chat->time;
        	}
        
        	$chat->user_id = $currentUser->getUserID();
        	$operatorAccepted = true;
        	$chatDataChanged = true;
        }
         
        if ($chat->support_informed == 0 || $chat->has_unread_messages == 1 ||  $chat->unread_messages_informed == 1) {
        	$chatDataChanged = true;
        }
         
        $chat->support_informed = 1;
        $chat->has_unread_messages = 0;
        $chat->unread_messages_informed = 0;
        self::getSession()->update($chat);
                
	    if ($chatDataChanged == true) {
	    	\erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed',array('chat' => & $chat,'user' => $currentUser));
	    }
    	    
	    if ($operatorAccepted == true) {
	    	\erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.accept',array('chat' => & $chat,'user' => $currentUser));
	    	\erLhcoreClassChat::updateActiveChats($chat->user_id);
	    	\erLhcoreClassChatWorkflow::presendCannedMsg($chat);
	    	$options = $chat->department->inform_options_array;
	    	\erLhcoreClassChatWorkflow::chatAcceptedWorkflow(array('department' => $chat->department,'options' => $options),$chat);
	    };	    
	}




	/**
	 * Close a chat session
	 */
	public static function closeChat($chatId) {
		if ($chatId == null) {
	        throw new Exception\HttpException(Response::HTTP_BAD_REQUEST, 
	        									Constants::MSG_WRONG_THREAD);
		}
		
		try {
			$chat = self::getSession()->load( 'erLhcoreClassModelChat', $chatId);
			
		} catch (\ezcPersistentObjectNotFoundException $ex) {
	        throw new Exception\HttpException(Response::HTTP_BAD_REQUEST, 
	        									Constants::MSG_WRONG_THREAD);
		}

		// -------------
		// This logic has been lifted from xml/chatdata
		// We should watch out for when it is updated in the core.
		// Ideally the core should provide a method for this so that
		// this functionality is done only in one place.
		// -------------

		// Chat can be closed only by owner
		$currentUser = \erLhcoreClassUser::instance();
		if (!$currentUser->hasAccessTo('lhchat','allowcloseremote') &&
			$chat->user_id != $currentUser->getUserID()) {
			throw new Exception\AccessDeniedException(Constants::MSG_CANNOT_VIEW_THREADS);
		}

		
		if ($chat->status != \erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
		    $chat->status = \erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
		    $chat->chat_duration = self::getChatDurationToUpdateChatID($chat->id);
	
		    $userData = $currentUser->getUserData(true);
	
			// The business of posting a message should be a method that takes necessary parameters
			self::postMessage($chat, 
							  (string)$userData.' '.\erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has closed the chat!'),
							  -1,
							  null,
							  false);

		    self::updateActiveChats($chat->user_id);
		    
		    // Execute callback for close chat
		    self::closeChatCallback($chat,$userData);
		    
		    \erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.desktop_client_closed',array('chat' => & $chat));
		}							
	}

	/**
	 * Post message to a chat using its id.
	 * 
	 * The method throws an exception if the chat id is not valid
	 */
	public static function postMessageToChatId($chatId, $message, $userId, $nameSupport, $setLastMessageId) {
		$chat = self::getSession()->load( 'erLhcoreClassModelChat', $chatId);
		return self::postMessage($chat, $message, $userId, $nameSupport, $setLastMessageId);
	}
	
	/**
	 * Post a message to the specified thread.
	 * 
	 * @param erLhcoreClassModelChat $chat 
	 * 
	 * Prerequisite: $chat must be of type erLhcoreClassModelChat and valid. Not checks are made.
	 */
	public static function postMessage($chat, $message, $userId, $nameSupport, $setLastMessageId) {
	    $msg = new \erLhcoreClassModelmsg();
	    $msg->msg = $message;
	    $msg->chat_id = $chat->id;
	    $msg->user_id = $userId;
		if ($nameSupport != null) {
			$msg->name_support = $nameSupport;
		}

		// TODO: This is a questionable line.
	    $chat->last_user_msg_time = $msg->time = time();

	    self::getSession()->save($msg);

        // Set last message ID
        if ($setLastMessageId && $chat->last_msg_id < $msg->id) {
        	$chat->last_msg_id = $msg->id;
		}

	    $chat->updateThis();
		
		return $msg->id;
	}

	/**
	 * Ping the chat thread
	 * 
	 * @param int $chatId	The id of the chat to ping
	 * @param Boolean $operatorTyping	An indicator whether the operator is currently typing.
	 * @return Array 	giving feedback about the chat to the caller
	 */	
	 public static function pingChat($chatId, $operatorTyping) {
	 	$arrayOut = array();
		if ($chatId == null) {
	        throw new Exception\HttpException(Response::HTTP_BAD_REQUEST, 
	        									Constants::MSG_WRONG_THREAD);
		}
		
		try {
			$chat = self::getSession()->load( 'erLhcoreClassModelChat', $chatId);
			if (self::hasAccessToRead($chat) )
			{    	
				self::setOperatorTyping($chat, $operatorTyping);
				
				$arrayOut['usertyping'] = $chat->is_user_typing;
				$arrayOut['threadstate'] = self::convertToWurrdChatState($chat->status);
				$arrayOut['threadagentid'] = $chat->user_id;
				$arrayOut['canpost'] = true; 	// Assume true. The client should figure this out through other means
			}
		} catch (\ezcPersistentObjectNotFoundException $ex) {
	        throw new Exception\HttpException(Response::HTTP_BAD_REQUEST, 
	        									Constants::MSG_WRONG_THREAD);
		}

		return $arrayOut;	
	 }
	 
	/**
	 * Helper method to map LHC chat statuses to Wurrd chat statuses
	 * 
	 * These are the Wurrd chat statuses support
	 * 		WURRD									LHC
			int CHAT_STATE_QUEUE		= 0		<=	STATUS_PENDING_CHAT	= 0
			int CHAT_STATE_WAITING		= 1		-	N/A
			int CHAT_STATE_CHATTING		= 2		<=	STATUS_ACTIVE_CHAT 	= 1
			int CHAT_STATE_CLOSED		= 3		<=	STATUS_CLOSED_CHAT	= 2
			int CHAT_STATE_LOADING		= 4		-	N/A
			int CHAT_STATE_LEFT			= 5		-	N/A
			int CHAT_STATE_INVITED		= 6		-	N/A
	 *  
	 */
	 public static function convertToWurrdChatState($lhcChatStatus) {
	 	$wurrdChatStatus = 0;
	 	switch($lhcChatStatus) {
			case \erLhcoreClassModelChat::STATUS_PENDING_CHAT:
				$wurrdChatStatus = 0;
				break;
			case \erLhcoreClassModelChat::STATUS_ACTIVE_CHAT:
				$wurrdChatStatus = 2;
				break;
			case \erLhcoreClassModelChat::STATUS_CLOSED_CHAT:
				$wurrdChatStatus = 3;
				break;
			default:
				// We are not yet accounting for any other status. Mark others as closed
				$wurrdChatStatus = 3;
				break;
	 	}
		
		return $wurrdChatStatus;
	 }


	/**
	 * Helper method get the most recent footprint for the current visitor
	 *
	 * We assume that user has access to chat
	 */
	public static function getFootPrint($chat, $minFootPrintId, &$lastFootPrintId) {
		if ($minFootPrintId === null) {
			return null;
		}

		$visitorFootPrint = null;

		$trackFootPrint = \erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 &&
			\erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1;
		if ($trackFootPrint) {

			$filter = array();
			$filter['limit'] = 100;
			$filter['offset'] = 0;
			$filter['smart_select'] = true;
			$filter['filter'] = array('chat_id' => (int)$chat->id);
			$filter['filtergt'] = array('id' => (int)$minFootPrintId);

			$footPrintList = \erLhcoreClassModelChatOnlineUserFootprint::getList($filter);

			// vtime is a varchar, so how efficient is it to use in a query?
			// This filter will only exhibit itself when the $minFootPrintId is 0 for the first call.
			// Subsequent calls will have min id set correctly, so no need to filter

			$visitorFootPrint = array();
			$lastFootPrintId = $minFootPrintId;
			foreach($footPrintList as $footPrint) {
				if ((int)$footPrint->vtime >= $chat->time) {
					$visitorFootPrint[] = array('footprintid' => $footPrint->id,
												'page' => $footPrint->page,
												'time' => (int)$footPrint->vtime,
												);

					if ($footPrint->id > $lastFootPrintId) {
						$lastFootPrintId = $footPrint->id;
					}
				}
			}

			return $visitorFootPrint;
		}
	}

	 
	 /**
	  * Helper method to set the operator's typing status.
	  */
	 private static function setOperatorTyping($chat, $operatorTyping) {
	 	
		// -------------
		// This logic has been lifted from lhchat/operatortyping.php
		// We should watch out for when it is updated in the core.
		// Ideally the core should provide a method for this so that
		// this functionality is done only in one place.
		// -------------

		$currentUser = \erLhcoreClassUser::instance();

		// Rewritten in a more efficient way
		$db = \ezcDbInstance::get();
		$stmt = $db->prepare('UPDATE lh_chat SET operator_typing = :operator_typing, operator_typing_id = :operator_typing_id WHERE id = :id');
		$stmt->bindValue(':id',$chat->id,\PDO::PARAM_INT);
				
	    if ($operatorTyping) {
	    	$stmt->bindValue(':operator_typing',time(),\PDO::PARAM_INT);
	    	$stmt->bindValue(':operator_typing_id',$currentUser->getUserID(),\PDO::PARAM_INT); 
	    } else {
	    	// Do we need the else of this statement? 
	    	// The check for is_operator_typing factors in the age of the last time the chat
	    	// was marked that the operator was typing. This call can be made as frequently as
	    	// 2 seconds. Depending on how fast other clients read this field, some clients 
	    	// may miss that the operator has entered some keystrokes.
	    	$stmt->bindValue(':operator_typing',0,\PDO::PARAM_INT);
	    	$stmt->bindValue(':operator_typing_id',0,\PDO::PARAM_INT);  
	    }
	    
	    $stmt->execute();             
	 }
}

