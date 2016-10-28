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


/**
 * Config class for the ClientInterface extension.
 * 
 * This is borrowed from erConfigClassLhConfig
 */
class Config
{
    private static $instance = null;
    public $conf;
	private $settingsFile;

    public function __construct()
    {
		$this->settingsFile = __DIR__ . '/../settings.ini.php';
		$this->reload();
    }

	public function reload() {
		if (is_readable($this->settingsFile)) {
	    	$this->conf = include($this->settingsFile);
		} else {
			$this->conf = null;
		}
	}
	
	/**
	 * This overwrites the current config
	 */
	public function setConfig($config) {
		if (is_array($config)) {
			$this->conf = $config;
			return true;
		}
		
		return false;
	}
	
	public function isValid() {
		return is_array($this->conf);
	}
	
    public function getSetting($section, $key, $throwException = true)
    {
    	if (!$this->isValid()) {
    		return false;
    	}

        if (isset($this->conf['settings'][$section][$key])) {
            return $this->conf['settings'][$section][$key];
        } else {
        	if ($throwException === true) {
            	throw new Exception\HttpException(500, 'Setting with section {'.$section.'} value {'.$key.'}');
        	} else {
        		return false;
        	}
        }
    }

    public function hasSetting($section, $key)
    {
    	if (!$this->isValid()) {
    		return false;
    	}
    	
        return isset($this->conf['settings'][$section][$key]);
    }

    public function setSetting($section, $key, $value)
    {
    	if (!$this->isValid()) {
    		return false;
    	}

        $this->conf['settings'][$section][$key] = $value;
    }

    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function save()
    {
    	if (!$this->isValid()) {
    		return false;
    	}

        return file_put_contents($this->settingsFile, "<?php\n return ".var_export($this->conf,true).";\n?>");
    }
}


?>