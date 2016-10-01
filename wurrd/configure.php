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
 
 
function showForm() {
	$config = readClientInterfaceSettings();

	$freshInstall = false;
	$installationId = getSetting($config, 'general', 'wurrd_ci_installation_id');
	if ($installationId === null) {
		$installationId = 'Fresh Install - Will be generated';
		$freshInstall = true;
	}

	$contactEmail = getSetting($config, 'general', 'contact_email');
	if ($contactEmail === null) {
		$contactEmail = '';
	}
	
	$usePost = filter_var(getSetting($config, 'general', 'use_http_post'), FILTER_VALIDATE_BOOLEAN);
	
?>
	<h3>Configure your installation of the Wurrd Client Interface for LiveHelperChat </h3>
	<ul>
		<li>Upon success, delete &lt;LiveHelperChat_install_dir&gt;/extension/wurrd/configure.php file to prevent unauthorized access.</li>
		<li>To change any of the settings after installation copy configure.php back and go to the link.</li>
		<li>In a later release installation and configuration will be improved</li>
	</ul>
	<p>&nbsp;</p>
	
	
	<form action="" method="post">
	  Installation ID: <input type="text" name="installation" size="100" disabled
	  									value="<?php echo $installationId; ?>"><br /><br />
	  Site Admin (Contact) Email: <input type="email" name="contactEmail" size="100" placeholder="Enter valid email" required
	  									value="<?php echo $contactEmail; ?>"><br /><br />
	  <i>Only enable this if you have a problem</i><br />
	  Force use of POST: <input type="checkbox" name="usePost" value="true" <?php if ($usePost) echo 'checked'; ?>> <br /><br />
		<input type="submit" value="<?php echo ($freshInstall ? 'Submit' : 'Update'); ?>">

		<!-- Hidden fields -->
		<input type="hidden" name="freshInstall" value="<?php echo $freshInstall; ?>">
		<input type="hidden" name="installationId" value="<?php echo $installationId; ?>">
	</form>
<?php
}


function readClientInterfaceSettings() {
	$settingsFile = __DIR__ . '/modules/lhwurrd/ClientInterface/settings.ini.php';
	$config = include($settingsFile);
	return $config;
}

function writeClientInterfaceSettings() {
	global $installationId;
	global $contactEmail;
	$freshInstall = filter_var($_POST['freshInstall'], FILTER_VALIDATE_BOOLEAN);
	$usePost = filter_var((isset($_POST['usePost']) ? $_POST['usePost'] : false), FILTER_VALIDATE_BOOLEAN);
	$settingsFile = __DIR__ . '/modules/lhwurrd/ClientInterface/settings.ini.php';

	$config = null;
	if ($freshInstall) {
		$config = createDefaultClientInterfaceSettings();
		$installationId = strtr(base64_encode(hash("sha256", time(), true)), '/', '-');
	} else {
		$config = include($settingsFile);
	}
	
	setSetting($config, 'general', 'wurrd_ci_installation_id', $installationId);
	setSetting($config, 'general', 'contact_email', $contactEmail);
	setSetting($config, 'general', 'use_http_post', $usePost);
	
	$success = file_put_contents($settingsFile, "<?php\n return ".var_export($config,true).";\n?>");
	
	if ($success === false) {
		return false;
	} else {
		return true;
	}
}
 
function createDefaultClientInterfaceSettings() {
	 return array (
	  'settings' => 
	  array (
	    'general' => 
	    array (
	      'contact_email' => '',
	      'wurrd_ci_installation_id' => '',
	      'use_http_post' => false,
	    ),
	  ),
	);
} 


function getSetting($config, $section, $key)
{
    if (isset($config['settings'][$section][$key])) {
        return $config['settings'][$section][$key];
    } else {
    	return null;
	}
}


function setSetting(&$config, $section, $key, $value)
{
    $config['settings'][$section][$key] = $value;
}

?>


<!--
	HTML begins here. The this is where the form to be displayed is generated.

-->


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Wurrd Client Interface Extension Configuration</title>
  <meta name="description" content="Basic configuration for Wurrd Client Interface Extension for LiveHelperChat">
  <meta name="author" content="Scalior">
  
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body>
<?php 

$installationId = isset($_POST['installationId']) ? $_POST['installationId'] : null;
$contactEmail = isset($_POST['contactEmail']) ? $_POST['contactEmail'] : null;
$written = null;

if (isset($installationId) && count($installationId) > 0 &&
	isset($contactEmail) && count($contactEmail) > 0 ) {
	$written = writeClientInterfaceSettings();
}

showForm(); 
 
 if (isset($written)) { ?>
	<h5><?php echo ($written ? 'Successfully saved!' : 'Failed to save the settings file. Do you have write permissions to the folder'); ?></h5><?php	
} 
 
?>
 
