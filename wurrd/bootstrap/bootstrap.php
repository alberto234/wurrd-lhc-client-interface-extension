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
 
 
// Do we have access to items in the Wurrd namespace? 
// If not we need to find a way to autoload from here.


// Register Wurrd classes
$wurrdExtFSRoot = dirname(dirname(__FILE__));

$loader = require_once($wurrdExtFSRoot . '/vendor/autoload.php');
$loader->addPsr4('', $wurrdExtFSRoot . '/lib/classes/', true);
$loader->addPsr4('Wurrd\\ClientInterface\\', $wurrdExtFSRoot . '/modules/lhwurrd/ClientInterface/', true);


use Wurrd\ClientInterface\Classes\ChatEventHandler;
// use Wurrd\ClientInterface\Model\ChatExtension;

class erLhcoreClassExtensionWurrd {

	public function __construct() {
		
	}
	
	public function run(){
		
		// Not yet sure how database installs and updates are done.	
		// Update 9/20/16:
		//		Databae installs are done in install.php
		//		Not sure how updates are done
		
		$dispatcher = erLhcoreClassChatEventDispatcher::getInstance();
		
		
		// Attatch event listeners

		// Chat Events
		$dispatcher->listen('chat.chat_started', array($this,'chatStarted'));
		$dispatcher->listen('chat.accept', array($this,'chatAccepted'));
		$dispatcher->listen('chat.close',array($this,'chatClosed'));
		$dispatcher->listen('chat.addmsguser', array($this,'chatNewMessage'));		

		// We are not yet sure of these		
		// $dispatcher->listen('chat.data_changed',array($this,'chatDataChanged'));	
		// $dispatcher->listen('chat.desktop_client_closed',array($this,'chatClosed'));	 // 'chat.close' is also called
						
	}
	
	
	public function chatStarted($args) {
		ChatEventHandler::chatStarted($args);
	}

	public function chatAccepted($args) {
		ChatEventHandler::chatAccepted($args);
	}

	public function chatDataChanged($args) {
		ChatEventHandler::chatDataChanged($args);
	}

	public function chatClosed($args) {
		ChatEventHandler::chatClosed($args);
	}

	public function chatNewMessage($args) {
		// Do nothing
	}
}


