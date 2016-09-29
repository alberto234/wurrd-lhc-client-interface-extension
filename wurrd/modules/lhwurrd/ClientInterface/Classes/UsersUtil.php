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
use Wurrd\ClientInterface\Classes\ChatUtil;
use Wurrd\ClientInterface\Model\Revision;


/*use Mibew\Controller\AbstractController;
use Mibew\Http\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wurrd\Mibew\Plugin\AuthAPI\Classes\AccessManagerAPI;
use Wurrd\Mibew\Plugin\AuthAPI\Model\Authorization;
use Wurrd\Mibew\Plugin\ClientInterface\Constants;
use Wurrd\Mibew\Plugin\ClientInterface\Classes\AuthenticationManager;
use Wurrd\Mibew\Plugin\ClientInterface\Classes\PackageUtil;
use Wurrd\Mibew\Plugin\ClientInterface\Classes\UsersProcessor;
*/

/**
 * This is a utility class that backs the UsersController
 * 
 * @author Eyong N <eyongn@scalior.com>		Sept 15, 2016
 */
class UsersUtil
{
    /**
     * Gets a list of active threads for a given user
     *
     */
    public static function getActiveThreads($currentUser, $requestRevision)
	{
		$arrayOut = null;
		$newRevision = 0;
		
		$activeChats = ChatUtil::getUpdatedThreads($currentUser, (int)$requestRevision, $newRevision);
		
		$arrayOut = array();
		if ($activeChats !== null) {
			$arrayOut['threads'] = $activeChats;
		}	
		$arrayOut['lastrevision'] = $newRevision;
		
		// Update the current user's last activity
		$currentUser->updateLastVisit();

		return $arrayOut;
	}
}

