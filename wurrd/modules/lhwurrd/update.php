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

// - We need to check our config file here instead of the standard config.
// - Given that we have have registered our class paths in bootstrap.php, we may
// 	 be able to use our config here.

$tpl = new erLhcoreClassTemplate( 'lhwurrdupdate/update1.tpl.php');

$wciConfig = Config::getInstance();

if (!$wciConfig->isValid())
{
    header('Location: ' .erLhcoreClassDesign::baseurldirect('wurrd/install') );
    exit;
}

$instance = erLhcoreClassSystem::instance();

if (substr($instance->RequestURI, 0, strlen('/wurrd/update')) !== '/wurrd/update') {
    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
    exit;
}

// Get last installed WCI version
$newWCIVersion = Constants::WCI_VERSION;
$installedWCIVersion = '1.0.0';	// Assume version that didn't have db entry
{
	$db = ezcDbInstance::get();
	$results = $db->query("SELECT * FROM `lh_chat_config` 
							WHERE `identifier` = '" . Constants::WCI_VERSION_KEY ."'")->fetchAll();
	if (count($results) > 0) {
		// Should be only 1 result
		$installedWCIVersion = $results[0]['value'];
	}
}

// We currently do not support downgrades.
if (version_compare($installedWCIVersion, $newWCIVersion) >= 0)
{
	$tpl->set('wci_installed_ver', $installedWCIVersion);
    $tpl->setFile('lhwurrdupdate/update3.tpl.php');
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'install';
	$Result['path'] = array(array('title' => 'Wurrd client interface installation'));
	return $Result;

    exit;
}

switch ((int)$Params['user_parameters']['step_id']) {

	case '1':
		// We run the update here.
		$Errors = array();
				
		$versionProgress = $installedWCIVersion;
		if (version_compare($installedWCIVersion, '1.0.2') < 0) {
			// Update schema
			
			$versionProgress = '1.0.2';
		}
		
		
		
		// After all updates, save the latest version in the database. 
		// At this point $versionProgress should be the WCI version.
		// Sanity check
		if (version_compare($versionProgress, $newWCIVersion) == 0) {
			// Create or update the version info in the lh_chat_config table
			$db = ezcDbInstance::get();
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
		} else {
			 $Errors[] = "A version mismatch occurred during the upgrade. New version = $newWCIVersion, script result = $versionProgress";
		}
		

		$tpl->set('wci_installed_ver', $installedWCIVersion);
		$tpl->set('wci_new_version', $newWCIVersion);

       	if (count($Errors) == 0){
          $tpl->setFile('lhwurrdupdate/update2.tpl.php');
       	} else {
	       $tpl->set('errors', $Errors);
	       $tpl->setFile('lhwurrdupdate/update1.tpl.php');
       	}
	  	break;

	default:

		$tpl->set('wci_installed_ver', $installedWCIVersion);
		$tpl->set('wci_new_version', $newWCIVersion);
		
	    $tpl->setFile('lhwurrdupdate/update1.tpl.php');
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