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
	    $this->conf = include($this->settingsFile);
	    	  	
		/**
		 * At this stage let's not worry about the default settings file.
		 * Once we figure out installation of extensions using a GUI, we 
		 * shall impement the default.
		 * We have removed the '@' in front of the include above such that 
		 * we get an error if this file is not present.
         if ( !is_array($this->conf) ) {
		    	$this->conf = include(__DIR__ . '/../settings.default.ini.php');
         } */
    }

    public function getSetting($section, $key, $throwException = true)
    {
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
        return isset($this->conf['settings'][$section][$key]);
    }

    public function setSetting($section, $key, $value)
    {
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
        file_put_contents($this->settingsFile, "<?php\n return ".var_export($this->conf,true).";\n?>");
    }
}


?>