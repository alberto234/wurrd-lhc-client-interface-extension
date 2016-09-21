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
use Wurrd\ClientInterface\Model\ChatExtension;


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
			$oneUpdatedThread['state'] 			= $oneThread->status;
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
     * @param Array $requestThreads Threads that we are interested in.
     * @return Array of messages for each of the supplied threads
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

		// $currentUser = \erLhcoreClassUser::instance();

		//erLhcoreClassLog::write(print_r($_POST,true));
		//[chats] => 2|5,2,5,2;8|0,5,2,0,5,2
		//$_POST['chats']   = '6|5,1,4;8|0,5,2,0,5,2';
		
		
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

				$chatMessages = array();
				$chatMessages['threadid'] = (int)$chatId;
				$chatMessages['lastid'] = (int)$newLastId;
				$chatMessages['messages'] = $messagesArray;
				$threadMessages[] = $chatMessages;
			} else {
				// Log that chat wasn't found
			}
		}

		$arrayOut['threadmessages'] = $threadMessages;
		return $arrayOut;
    }
}

