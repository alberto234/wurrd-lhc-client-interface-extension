<?php
/*
 * This file is a part of Wurrd Client Interface Extension for LiveHelperChat.
 *
 * Copyright 2016 Eyong N <eyongn@scalior.com>.
 *
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

namespace Wurrd\ClientInterface\Model;

use Wurrd\ClientInterface\Classes\PersistentSession;

/**
 * A class that represents Revision entity.
 * 
 * Note: This class contains methods for persistence. Ideally persistence should be 
 * 		 moved to a persistence manager such that users of this class wouldn't be 
 * 		 able to inadvertently change its state in persistence.
 */
class Revision
{
    /**
     * Unique device ID.
     *
     * @var int
     */
    public $id = null;

	/**
	 * We return the next revision number (current revision + 1)
	 */
   public static function getNextRevision()
   {
   		// Increment the sequence
   		// This pattern is explained in http://dev.mysql.com/doc/refman/5.7/en/information-functions.html#function_last-insert-id

		$db = \ezcDbInstance::get();
		$db->query("UPDATE `wci_revision` SET id=LAST_INSERT_ID(id+1)");
		return $db->lastInsertId();
   }
}

?>