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

/**
 * This is a utility class that backs the CannedResponsesController
 * 
 */
class CannedResponsesUtil
{
	/**
	 * Retrieves the canned responses for departments that the current operator belongs to.
	 * 
	 * @return array  An array containing the canned responses,
	 * 					or an empty array if none is available 
	 */
	 public static function getCannedResponses($currentUser) {
	 	$wurrdCannedResponses = array();
		
		$responses = self::getAllCannedMessages($currentUser);
		
		if (count($responses) > 0) {
			foreach($responses as $response) {
				$wurrdCannedResponse = array();
				$wurrdCannedResponse['id'] = (int)$response->id;
				$wurrdCannedResponse['locale'] = ''; 	// Not supported
				$wurrdCannedResponse['groupid'] = (int)$response->department_id;
				$wurrdCannedResponse['title'] = $response->title;
				$wurrdCannedResponse['response'] = $response->msg;
				
				$wurrdCannedResponses[] = $wurrdCannedResponse;
			}
		}

		return $wurrdCannedResponses;
	 }
	
    public static function getAllCannedMessages($currentUser)
    {
		// -------------
		// This logic query is based on erLhcoreClassModelCannedMsg::getCannedMessages()
		// We should watch out for when it is updated in the core.
		// -------------

		$departments = explode(',', $currentUser->getUserData()->departments_ids);
        $session = \erLhcoreClassChat::getSession();
        $q = $session->createFindQuery('erLhcoreClassModelCannedMsg');
        $q->where(
        	$q->expr->lOr(
        		$q->expr->in('department_id', $departments),
        		$q->expr->lAnd(
        			$q->expr->eq('department_id', $q->bindValue(0)),
        			$q->expr->eq('user_id', $q->bindValue(0))
				),
        		$q->expr->eq('user_id', $q->bindValue($currentUser->getUserData()->user_id))
			)
		);
        
        $q->limit(5000, 0);
        $q->orderBy('position ASC, title ASC');
        $items = $session->find($q);

        return $items;
    }

}


 