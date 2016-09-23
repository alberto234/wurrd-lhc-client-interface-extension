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
use Wurrd\ClientInterface\Model\Revision;


/**
 * This is an event handler for chat events
 * 
 * @author Eyong N <eyongn@scalior.com>		Sept 20, 2016
 */
class ChatEventHandler
{
	/**
	 * Handle chat.chat_started event
	 * @param Array $args - Arguments of this event
	 */
	public static function chatStarted($args) {
		$chat = $args['chat']; 		// Chat object
		if ($chat == null) {
			return;
		}
		
		// If the thread doesn't already exist, create it.
		// It is expected that the thread shouldn't exist
		$chatExtension = ChatExtension::fecthByChatId($chat->id);
		$nextRevision = Revision::getNextRevision();
		if ($chatExtension === false) {
			$chatExtension = ChatExtension::createChatExtension($chat->id, $nextRevision);
		} else {
			$chatExtension->revision = $nextRevision;
		}
		$chatExtension->saveThis();
	}


	/**
	 * Handle chat.accepted event
	 * @param Array $args - Arguments of this event
	 */
	public static function chatAccepted($args) {
		$chat = $args['chat']; 		// Chat object
		if ($chat == null) {
			return;
		}

		self::updateChatRevision($chat);
	}


	/**
	 * Handle chat.closed and chat.desktop_client_closed events
	 * @param Array $args - Arguments of this event
	 */
	public static function chatClosed($args) {
		$chat = $args['chat']; 		// Chat object
		if ($chat == null) {
			return;
		}

		self::updateChatRevision($chat);
	}


	/**
	 * Handle chat.data_changed event
	 * @param Array $args - Arguments of this event
	 */
	public static function chatDataChanged($args) {
		$chat = $args['chat']; 		// Chat object
		if ($chat == null) {
			return;
		}

		self::updateChatRevision($chat);
	}


	/**
	 * Helper method to update the chat revision
	 */	
	private static function updateChatRevision($chat) {
		$chatExtension = ChatExtension::fecthByChatId($chat->id);
		if ($chatExtension === false) {
			// error_log('Existing chat extension not found: NOT creating a new row');
		} else {
			$nextRevision = Revision::getNextRevision();
			// error_log('Existing chat extension found: Updating revision to ' . $nextRevision);
			$chatExtension->revision = $nextRevision;
			$chatExtension->saveThis();
		}
	}
}

