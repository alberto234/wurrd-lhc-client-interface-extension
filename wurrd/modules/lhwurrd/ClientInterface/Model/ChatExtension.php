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
 * A class that represents ChatExtension entity.
 * 
 * Note: This class contains methods for persistence. Ideally persistence should be 
 * 		 moved to a persistence manager such that users of this class wouldn't be 
 * 		 able to inadvertently change its state in persistence.
 */
class ChatExtension
{
    /**
     * Unique ID.
     *
     * @var int
     */
    public $id = null;

    /**
     * A foreign key to the chat that this entry extends
     *
     * @var int
     */
    public $chatid = null;

    /**
     * This chat's revision number
     *
     * @var int
     */
    public $revision = null;

    public static function fetch($id)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        // Load chat extension info
        try {
			$chatExtension = PersistentSession::getInstance()->load( 'Wurrd\\ClientInterface\\Model\\ChatExtension', (int)$id );
		} catch (\Exception $ex) {
			// error_log("No device with id $id");
			return false;
		}
		
        // There is no chat extension with such id in database
        if (!$chatExtension) {
            return false;
        }

        return $device;
    }

    /**
     * Fetch device by chat id.
     *
     * @param int $chatId The chat id for which we want the extension
     * @return boolean|ChatExtension Returns a ChatExtension instance or boolean false on failure.
     */
    public static function fecthByChatId($chatId)
    {
        // Check $id
        if (empty($chatId)) {
            return false;
        }

        // Load chat extension info
	   	$q = PersistentSession::getInstance()->createFindQuery('Wurrd\\ClientInterface\\Model\\ChatExtension');
		$conditions = array();
		$conditions[] = $q->expr->eq('revision', $q->bindValue($chatId) );
		$q->where ($conditions);
		try {
			$chatExtensions = PersistentSession::getInstance()->find($q);
			
			if (!is_null($chatExtensions) && count($chatExtensions) > 0) {
				// There should only be one.
				foreach ($chatExtensions as $key => $chatExtension) {
					return $chatExtension;
				}
			}
		} catch (\Exception $ex) {
			// error_log('not found');
		}
		
		return false;
    }


    /**
     * Fetch chat extensions that have changed since the last revision.
     *
     * @param int $fromRevision The starting revision (not inclusive) for which we want extensions
     * @return Array Returns an array of ChatExtensions that meet the criteria.
     */
    public static function fecthUpdatedThreads($fromRevision)
    {
        // Check param
        if ($fromRevision == null) {
            return false;
        }

        // Load chat extension info
	   	$q = PersistentSession::getInstance()->createFindQuery('Wurrd\\ClientInterface\\Model\\ChatExtension');
		$conditions = array();
		$conditions[] = $q->expr->gt('revision', $q->bindValue($fromRevision) );
		$q->where ($conditions);
		try {
			$chatExtensions = PersistentSession::getInstance()->find($q);
			return $chatExtensions;
		} catch (\Exception $ex) {
			// error_log('not found');
		}
		
		return array();
    }


    /**
     * Creates a new chat extension object from parameters.
     *
     * @param int $chatId Chat Id
	 * @param int $revision The chat's current revision
     * @return boolean|ChatExtension Returns a ChatExtension instance or boolean false on failure.
     */
    public static function createChatExtension($chatId, $revision)
    {
        // Check parameters
        if (empty($chatId) || empty($revision)) {
            return false;
        }

		$now = time();
        $chatExtensionInfo = array('chatid' => $chatId,
        					 'revision' => $revision,
							 );
							 
        // Create and populate device object
        $chatExtension = new self();
        $chatExtension->setState($chatExtensionInfo);

        return $chatExtension;
    }

	/**
	 * Get the highest revision number so far.
	 * This comes in handy to rebase a client that is supplying a revision number
	 * higher than what the current revision is.
	 */
	 public static function getHighestChatRevision() {
        // Load chat extension info
	   	$q = PersistentSession::getInstance()->createFindQuery('Wurrd\\ClientInterface\\Model\\ChatExtension');
		$conditions = array();
		error_log('highestrevision - 1 ' . print_r($q, true));
		$conditions[] = $q->expr->max('revision');
		error_log('highestrevision - 2');
		$q->where ($conditions);
		error_log('highestrevision - 3');
		try {
		error_log('highestrevision - 4');
			$chatExtensions = PersistentSession::getInstance()->find($q);
			error_log('Experiment ' . print_r($chatExtensions, true));
			return $chatExtensions;
		} catch (\Exception $ex) {
			// error_log('not found');
			// error_log('highestrevision - exception: ' . print_r($ex, true));
		}
		
		return array();
	 }


    /**
     * Save the chat extension to the database.
     *
     */
    public function saveThis()
    {
   		PersistentSession::getInstance()->saveOrUpdate($this);
    }

    /**
     * Remove device from the database.
     *
     */
    public function delete()
    {
        if (!$this->id) {
            throw new \RuntimeException('You cannot delete a device without id');
        }

		PersistentSession::getInstance()->delete($this);
    }


 
   public function getState()
   {
       return array(
               'id'         	=> $this->id,
               'chatid'     	=> $this->chatid,
               'revision'       => $this->revision,
              );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }

}

?>