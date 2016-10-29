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

$tpl = new erLhcoreClassTemplate( 'lhwurrduninstall/uninstall1.tpl.php');

$wciConfig = Config::getInstance();

if (!$wciConfig->isValid())
{
    $tpl->setFile('lhwurrduninstall/uninstall3.tpl.php');
	$Result['content'] = $tpl->fetch();
	$Result['pagelayout'] = 'install';
	$Result['path'] = array(array('title' => 'Wurrd client interface installation'));
	return $Result;

    exit;
}

$instance = erLhcoreClassSystem::instance();

if (substr($instance->RequestURI, 0, strlen('/wurrd/uninstall')) !== '/wurrd/uninstall') {
    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
    exit;
}

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


switch ((int)$Params['user_parameters']['step_id']) {

	case '1':
		// Nothing to do here. Simply forward to the next page
       $tpl->setFile('lhwurrduninstall/uninstall2.tpl.php');
	  	break;

	case '2':
	    $Errors = array();

	    if ($_SERVER['REQUEST_METHOD'] == 'POST')
	    {
		   if (isset($_POST['UninstalExtension'])) {
				$db = ezcDbInstance::get();
			   	$db->beginTransaction();
				try {
					// Remove entries in chat_config
				   	$db->query("DELETE FROM `lh_chat_config` 
								WHERE `identifier` = '" . Constants::WCI_VERSION_KEY ."'");
					
			   		// Remove tables
			   		$db->query("DROP TABLE `wci_chat_extension`");
			   		$db->query("DROP TABLE `wci_revision`");
			   		$db->query("DROP TABLE `waa_authorization`");
			   		$db->query("DROP TABLE `waa_device`");
			   					   		
			   		$db->commit();
			   } catch (Exception $ex) {
			   		$db->rollback();
				   $Errors[] = "An error occurred when removing database entries: " . $ex->getMessage();
	    	       $tpl->set('errors',$Errors);
	    	       $tpl->setFile('lhwurrduninstall/uninstall2.tpl.php');
			   }

				// If we get here database calls didn't throw exception.
		   		// Remove wurrd config (settings.ini.php)
		   		if ($wciConfig->deleteFile()) {
	    	       $tpl->setFile('lhwurrduninstall/uninstall3.tpl.php');
		   		} else {
		   			$Errors[] = "Error removing settings file ($WCI_SETTINGS_FILE)! Database entry have been removed. " .
								"If you choose to keep the extension, manually delete the file and run through the installation again";
					// This is not fatal since the user will manually delete the extension directory. Proceed
	    	       $tpl->set('errors',$Errors);
	    	       $tpl->setFile('lhwurrduninstall/uninstall3.tpl.php');
		   		}
		   } else {
		   		$Errors[] = "In order to proceed with the uninstallation you must click the checkbox below";
		        $tpl->set('errors', $Errors);
		        $tpl->setFile('lhwurrduninstall/uninstall2.tpl.php');
		   }
	    } else {
	    	$Errors[] = "Unexpected error: Request method not POST";
	        $tpl->set('errors', $Errors);
	        $tpl->setFile('lhwurrduninstall/uninstall2.tpl.php');
	    }
		
		break;
	default:

		$tpl->set('wci_installed_ver', $installedWCIVersion);
		
	    $tpl->setFile('lhwurrduninstall/uninstall1.tpl.php');
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