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
		error_log('ChatEventHandler::chatStarted - 1');
		$chat = $args['chat']; 		// Chat object
		if ($chat == null) {
			return;
		}
		
		// If the thread doesn't already exist, create it.
		// It is expected that the thread shouldn't exist
		error_log('ChatEventHandler::chatStarted - 2');
		$chatExtension = ChatExtension::fecthByChatId($chat->id);
		error_log('ChatEventHandler::chatStarted - 3');
		$nextRevision = Revision::getNextRevision();
		error_log('ChatEventHandler::chatStarted - 4');
		if ($chatExtension === false) {
			error_log('Existing chat extension not found: Creating a new row');
			$chatExtension = ChatExtension::createChatExtension($chat->id, $nextRevision);
		} else {
			error_log('Existing chat extension found: Updating revision');
			$chatExtension->revision = $nextRevision;
		}
		
		error_log('ChatEventHandler::chatStarted - 5');
		$chatExtension->saveThis();
		error_log('ChatEventHandler::chatStarted - 10');
	}
}

