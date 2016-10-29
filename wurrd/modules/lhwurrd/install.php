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
 
// Define our variables here. Necessary constants will be passed to templates as needed.
// Directories
$WCI_SETTINGS_DIR = "extension/wurrd/modules/lhwurrd/ClientInterface";
$WCI_CACHE_DIR = "extension/wurrd/modules/lhwurrd/cache";

$WCI_SETTINGS_FILE = __DIR__ . '/ClientInterface/settings.ini.php';

use Wurrd\ClientInterface\Classes\Config;
use Wurrd\ClientInterface\Constants;

try {

$tpl = new erLhcoreClassTemplate( 'lhwurrdinstall/install1.tpl.php');

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged()){    	
    $tpl->setFile('lhwurrdinstall/notloggedin.tpl.php');
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'install';
	$Result['path'] = array(array('title' => 'Wurrd client interface installation'));
	return $Result;

    exit;
}
$wciConfig = Config::getInstance();

if ($wciConfig->isValid())
{
    $tpl->setFile('lhwurrdinstall/install5.tpl.php');
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'install';
	$Result['path'] = array(array('title' => 'Wurrd client interface installation'));
	return $Result;

    exit;
}

$instance = erLhcoreClassSystem::instance();

if (substr($instance->RequestURI, 0, strlen('/wurrd/install')) !== '/wurrd/install') {
    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
    exit;
}



switch ((int)$Params['user_parameters']['step_id']) {

	case '1':
		$Errors = array();
		if (!is_writable($WCI_SETTINGS_DIR))
	       $Errors[] = "$WCI_SETTINGS_DIR is not writable";

       if (count($Errors) == 0){
       	  // Pass along database settings
       	  // We assume that the core has been successfully installed so we take the config values as gospel
	      $cfgSite = erConfigClassLhConfig::getInstance();
		  $tpl->set('db_username', $cfgSite->getSetting('db', 'user'));
          $tpl->set('db_password', $cfgSite->getSetting('db', 'password'));
          $tpl->set('db_host', $cfgSite->getSetting('db', 'host'));
          $tpl->set('db_port', $cfgSite->getSetting('db', 'port'));
          $tpl->set('db_name', $cfgSite->getSetting('db', 'database'));

          $tpl->setFile('lhwurrdinstall/install2.tpl.php');
       }
	  break;

	  case '2':
		// No need for error checking. Nothing changed.
		$cfgSite = erConfigClassLhConfig::getInstance();
		$tpl->set('admin_email', $cfgSite->getSetting('site', 'site_admin_email'));
		
	    $tpl->setFile('lhwurrdinstall/install3.tpl.php');
		 		 
		 
	  break;

	case '3':
	    $Errors = array();

	    if ($_SERVER['REQUEST_METHOD'] == 'POST')
	    {
    		$definition = array(
                'AdminEmail' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
                ),
            );

    	    $form = new ezcInputForm( INPUT_POST, $definition );

    	    if ( !$form->hasValidData( 'AdminEmail' ) )
            {
                $Errors[] = 'Wrong email address';
            }

            if (count($Errors) == 0) {

               $adminEmail = '';
               if ( $form->hasValidData( 'AdminEmail' ) ) {
               		$tpl->set('admin_email',$form->AdminEmail);
               		$adminEmail = $form->AdminEmail;
               }
			   
			   $usePost = false;
			   if (isset($_POST['UsePost'])) {
			 		$usePost = true;
			   }

				// Create the installation key
				$installationId = strtr(base64_encode(hash("sha256", time(), true)), '/', '-');
								
				// Create the wurrd client interface config array
				$wciConfigArray = array (
						  'settings' => 
						  array (
						    'general' => 
						    array (
						      'contact_email' => $adminEmail,
						      'wurrd_ci_installation_id' => $installationId,
						      'use_http_post' => $usePost,
						    ),
						  ),
						);
						
				$wciConfig->setConfig($wciConfigArray);
			    $success = $wciConfig->save();
				if ($success == true) {
					
	    	       /*DATABASE TABLES SETUP*/
	    	       // Note that $db below is a PDO object
	    	       
	    	       // Fresh install for this version
	    	       // On upgrade we have to handle database upgrades.
	    	       $db = ezcDbInstance::get();
				   $db->beginTransaction();
				   try {
		        	  $db->query("CREATE TABLE IF NOT EXISTS `waa_device` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `deviceuuid` varchar(1024) NOT NULL,
						  `platform` varchar(64),
						  `type` varchar(64),
						  `name` varchar(256),
						  `os` varchar(64),
						  `osversion` varchar(32),
						  `dtmcreated` int NOT NULL DEFAULT 0,
						  `dtmmodified` int NOT NULL DEFAULT 0,
						  PRIMARY KEY (`id`),
						  KEY `idx_device` (`deviceuuid`(256),`platform`)
						) DEFAULT CHARSET=utf8;");
		
		        	   $db->query("CREATE TABLE IF NOT EXISTS `waa_authorization` (
		                  `authid` int(11) NOT NULL AUTO_INCREMENT,
		                  `operatorid` int(11) NOT NULL,
		                  `deviceid` int(11) NOT NULL,
		                  `clientid` varchar(256),
		                  `dtmcreated` int(11) NOT NULL DEFAULT 0,
		                  `dtmmodified` int(11) NOT NULL DEFAULT 0,
		                  `accesstoken` varchar(256),
		                  `dtmaccesscreated` int(11) NOT NULL,
		                  `dtmaccessexpires` int(11) NOT NULL,
		                  `refreshtoken` varchar(256),
		                  `dtmrefreshcreated` int(11) NOT NULL,
		                  `dtmrefreshexpires` int(11) NOT NULL,
		                  `previousaccesstoken` varchar(256),
		                  `previousrefreshtoken` varchar(256),
		                  PRIMARY KEY (`authid`),
		                  KEY `idx_accesstoken` (`accesstoken`),
		                  KEY `idx_refreshtoken` (`refreshtoken`),
		                  KEY `idx_deviceid` (`deviceid`),
		                  KEY `idx_previousaccesstoken` (`previousaccesstoken`)
		                ) DEFAULT CHARSET=utf8;");
		
		                // The revision table
		        	   $db->query("CREATE TABLE IF NOT EXISTS `wci_revision` (
		                  `id` int(11) NOT NULL
		                ) DEFAULT CHARSET=utf8;");
		                
		               // chat_extension - A table to extend columns of the chat table without
		               // 				modifying the core
		        	   $db->query("CREATE TABLE IF NOT EXISTS `wci_chat_extension` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `chatid` int(11) NOT NULL,
						  `revision` int(11) NOT NULL,
						  PRIMARY KEY (`id`)
						) DEFAULT CHARSET=utf8;");
	
		                // Give the revision a default value of 0
		                $db->query("INSERT INTO `wci_revision` values (0);");
		                
											
						// Create or update the version info in the lh_chat_config table
						
						$results = $db->query("SELECT * FROM `lh_chat_config` 
												WHERE `identifier` = '" . Constants::WCI_VERSION_KEY ."'")->fetchAll();
						if (count($results) == 0) {
							$db->query("INSERT INTO `lh_chat_config` 
								(`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('" .
								Constants::WCI_VERSION_KEY . "','" .Constants::WCI_VERSION . "',0, 'Wurrd client interface vesion', 0)");
						} else {
							$db->query("UPDATE `lh_chat_config` SET `value` = '" . Constants::WCI_VERSION . "'
								WHERE `identifier` = '" . Constants::WCI_VERSION_KEY . "'");
						}
				   		$db->commit();
				   		
	    		       $tpl->setFile('lhwurrdinstall/install4.tpl.php');
				   } catch (Exception $ex) {
				   		$db->rollback();
					   $Errors[] = "An error occurred when creating the database tables: " . $ex->getMessage();
		    	       $tpl->set('errors',$Errors);
		    	       $tpl->setFile('lhwurrdinstall/install3.tpl.php');
				   }
				} else {
					$Errors[] = "Error saving the configuration file";
	    	       $tpl->set('errors',$Errors);
	    	       $tpl->setFile('lhwurrdinstall/install3.tpl.php');
				}
            } else {

               if ( $form->hasValidData( 'AdminEmail' ) ) $tpl->set('admin_email',$form->AdminEmail);

    	       $tpl->set('errors',$Errors);

    	       $tpl->setFile('lhwurrdinstall/install3.tpl.php');
            }
	    } else {
	        $tpl->setFile('lhwurrdinstall/install3.tpl.php');
	    }

	    break;

	case '4':
	    $tpl->setFile('lhwurrdinstall/install4.tpl.php');
	    break;

	default:
		$tpl->set('wci_settings_dir', $WCI_SETTINGS_DIR);
		$tpl->set('wci_cache_dir', $WCI_CACHE_DIR);
		
	    $tpl->setFile('lhwurrdinstall/install1.tpl.php');
		break;
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'install';
$Result['path'] = array(array('title' => 'Wurrd client interface installation'));

} catch (Exception $e) {
	echo "Make sure that &quot;cache/*&quot; is writable and then <a href=\"".erLhcoreClassDesign::baseurl('wurrd/install')."\">try again</a>";

	echo "<pre>";
	print_r($e);
	echo "</pre>";
	exit;
}
?>