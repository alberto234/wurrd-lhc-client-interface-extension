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
 * A class that represents Device entity.
 * 
 * Note: This class contains methods for persistence. Ideally persistence should be 
 * 		 moved to a persistence manager such that users of this class wouldn't be 
 * 		 able to inadvertently change its state in persistence.
 */
class Device
{
    /**
     * Unique device ID.
     *
     * @var int
     */
    public $id = null;

    /**
     * The unique identifier for this device by platform
     *
     * @var string
     */
    public $deviceuuid = '';

    /**
     * The device's platform, e.g, Android, iOS, Windows
     *
     * @var string
     */
    public $platform = null;

    /**
     * Device type (phone, tablet, etc).
     *
     * @var string
     */
    public $type = null;

    /**
     * Device name as provided by the manufacturer
     *
     * @var string
     */
    public $name = null;

    /**
     * Device operation system
     *
     * @var string
     */
    public $os = null;

    /**
     * Device operation system version
     *
     * @var string
     */
    public $osVersion = null;
	
    /**
     * Unix timestamp of the moment this record was created.
     * @var int
     */
    public $dtmcreated = 0;

    /**
     * Unix timestamp of the moment this record was modified.
     * @var int
     */
    public $dtmmodified = 0;


    /**
     * Fetch device by its ID.
     *
     * @param int $id ID of the device to load
     * @return boolean|Device Returns a Device instance or boolean false on failure.
     */
    public static function fetch($id)
    {
        // Check $id
        if (empty($id)) {
            return false;
        }

        // Load device info
        try {
			$device = PersistentSession::getInstance()->load( 'Wurrd\\ClientInterface\\Model\\Device', (int)$id );
		} catch (\Exception $ex) {
			// error_log("No device with id $id");
			return false;
		}
		
        // There is no device with such id in database
        if (!$device) {
            return false;
        }

        return $device;
    }

    /**
     * Fetch device by UUID and platform.
     *
     * @param string $uuid Platform unique identifier for device to load.
	 * @param string $platform Device's platform
     * @return boolean|Device Returns a Device instance or boolean false on failure.
     */
    public static function fetchByUUID($uuid, $platform)
    {
        // Check $id
        if (empty($uuid) || empty($platform)) {
            return false;
        }

        // Load device info
	   	$q = PersistentSession::getInstance()->createFindQuery('Wurrd\\ClientInterface\\Model\\Device');
		$conditions = array();
		$conditions[] = $q->expr->eq('deviceuuid', $q->bindValue($uuid) );
		$conditions[] = $q->expr->eq('platform', $q->bindValue($platform) );
		$q->where ($conditions);
		try {
			$devices = PersistentSession::getInstance()->find($q);
			
			if (!is_null($devices) && count($devices) > 0) {
				// There should only be one.
				foreach ($devices as $key => $device) {
					return $device;
				}
			}
		} catch (\Exception $ex) {
			// error_log('device not found');
		}
		
		return false;
    }

    /**
     * Creates a new device object from parameters.
     *
     * @param string $uuid Platform unique identifier
	 * @param string $platform Device's platform
	 * @param string $type Type of device
	 * @param string $name The device name
     * @return boolean|Device Returns a Device instance or boolean false on failure.
     */
    public static function createDevice($uuid, $platform, $type, $name, $os, $osVersion)
    {
        // Check parameters
        if (empty($uuid) || empty($platform) || empty($type) || empty($name) ||
        	empty($os) || empty($osVersion)) {
            return false;
        }

		$now = time();
        $device_info = array('deviceid' => false,
        					 'deviceuuid' => $uuid,
        					 'platform' => $platform,
        					 'type' => $type,
        					 'name' => $name,
							 'os' => $os,
							 'osversion' => $osVersion,
							 'dtmcreated' => $now,
							 'dtmmodified' => $now,
							 );
							 
        // Create and populate device object
        $device = new self();
        $device->setState($device_info);

        return $device;
    }

    /**
     * Save the device to the database.
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
               'deviceuuid'     => $this->deviceuuid,
               'platform'       => $this->platform,
               'type'    		=> $this->type,
               'name'    		=> $this->name,
               'os'   			=> $this->os,
               'osversion'   	=> $this->osversion,
               'dtmcreated'   	=> $this->dtmcreated,
               'dtmmodified'   	=> $this->dtmmodified,
              );
   }

   public function setState( array $properties )
   {
       foreach ( $properties as $key => $val )
       {
           $this->$key = $val;
       }
   }


	/** NOT SURE YET WHAT TO DO WITH THIS **/
   public static function getList($paramsSearch = array()) {
       if (!isset($paramsSearch['sort'])){
           $paramsSearch['sort'] = 'id ASC';
       };
       
       return erLhcoreClassChat::getList($paramsSearch,'Wurrd\\ClientInterface\\Model\\Device','waa_device');
   }

}

?>