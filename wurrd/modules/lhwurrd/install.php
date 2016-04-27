<?php
/*
 * NOT YET IMPLEMENTED!!!!!!!
 * 
 * 
 * */
try {

$cfgSite = erConfigClassLhConfig::getInstance();

if ($cfgSite->getSetting( 'site', 'installed' ) == true)
{
    $Params['module']['functions'] = array('install');
    include_once('modules/lhkernel/nopermission.php');

    $Result['pagelayout'] = 'install';
    $Result['path'] = array(array('title' => 'Live helper chat installation'));
    return $Result;

    exit;
}

$instance = erLhcoreClassSystem::instance();

if ($instance->SiteAccess != 'site_admin') {
    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
    exit;
}

$tpl = new erLhcoreClassTemplate( 'lhinstall/install1.tpl.php');

switch ((int)$Params['user_parameters']['step_id']) {

	case '1':
		$Errors = array();
		if (!is_writable("cache/cacheconfig"))
	       $Errors[] = "cache/cacheconfig is not writable";

	    if (!is_writable("settings/"))
	       $Errors[] = "settings/ is not writable";

		if (!is_writable("cache/translations"))
	       $Errors[] = "cache/translations is not writable";

		if (!is_writable("cache/userinfo"))
	       $Errors[] = "cache/userinfo is not writable";

		if (!is_writable("cache/compiledtemplates"))
	       $Errors[] = "cache/compiledtemplates is not writable";

		if (!is_writable("var/storage"))
	       $Errors[] = "var/storage is not writable";

		if (!is_writable("var/storageform"))
	       $Errors[] = "var/storageform is not writable";

		if (!is_writable("var/userphoto"))
	       $Errors[] = "var/userphoto is not writable";

		if (!is_writable("var/tmpfiles"))
	       $Errors[] = "var/tmpfiles is not writable";

		if (!is_writable("var/storagetheme"))
	       $Errors[] = "var/storagetheme is not writable";

		if (!is_writable("var/storageadmintheme"))
	       $Errors[] = "var/storageadmintheme is not writable";

		if (!extension_loaded ('pdo_mysql' ))
	       $Errors[] = "php-pdo extension not detected. Please install php extension";
		
		if (!extension_loaded('curl'))
			$Errors[] = "php_curl extension not detected. Please install php extension";	
		
		if (!extension_loaded('mbstring'))
			$Errors[] = "mbstring extension not detected. Please install php extension";	
		
		if (!extension_loaded('gd'))
			$Errors[] = "gd extension not detected. Please install php extension";	
		
		if (!function_exists('json_encode'))
			$Errors[] = "json support not detected. Please install php extension";	
		
		if (version_compare(PHP_VERSION, '5.4.0','<')) {
			$Errors[] = "Minimum 5.4.0 PHP version is required";	
		}
		
       if (count($Errors) == 0){
           $tpl->setFile('lhinstall/install2.tpl.php');
       }
	  break;

	  case '2':
		$Errors = array();

		$definition = array(
            'DatabaseUsername' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
            ),
            'DatabasePassword' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
            ),
            'DatabaseHost' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'string'
            ),
            'DatabasePort' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'int'
            ),
            'DatabaseDatabaseName' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'string'
            ),
        );

	   $form = new ezcInputForm( INPUT_POST, $definition );


	   if ( !$form->hasValidData( 'DatabaseUsername' ) )
       {
           $Errors[] = 'Please enter database username';
       }

	   if ( !$form->hasValidData( 'DatabasePassword' ) )
       {
           $Errors[] = 'Please enter database password';
       }

	   if ( !$form->hasValidData( 'DatabaseHost' ) || $form->DatabaseHost == '' )
       {
           $Errors[] = 'Please enter database host';
       }

	   if ( !$form->hasValidData( 'DatabasePort' ) || $form->DatabasePort == '' )
       {
           $Errors[] = 'Please enter database post';
       }

	   if ( !$form->hasValidData( 'DatabaseDatabaseName' ) || $form->DatabaseDatabaseName == '' )
       {
           $Errors[] = 'Please enter database name';
       }

       if (count($Errors) == 0)
       {
           try {
           	$db = ezcDbFactory::create( "mysql://{$form->DatabaseUsername}:{$form->DatabasePassword}@{$form->DatabaseHost}:{$form->DatabasePort}/{$form->DatabaseDatabaseName}" );
           } catch (Exception $e) {
                  $Errors[] = 'Cannot login with provided logins. Returned message: <br/>'.$e->getMessage();
           }
       }

	       if (count($Errors) == 0){

	           $cfgSite = erConfigClassLhConfig::getInstance();
	           $cfgSite->setSetting( 'db', 'host', $form->DatabaseHost);
	           $cfgSite->setSetting( 'db', 'user', $form->DatabaseUsername);
	           $cfgSite->setSetting( 'db', 'password', $form->DatabasePassword);
	           $cfgSite->setSetting( 'db', 'database', $form->DatabaseDatabaseName);
	           $cfgSite->setSetting( 'db', 'port', $form->DatabasePort);

	           $cfgSite->setSetting( 'site', 'secrethash', substr(md5(time() . ":" . mt_rand()),0,10));

	           $cfgSite->save();

	           $tpl->setFile('lhinstall/install3.tpl.php');
	       } else {

	          $tpl->set('db_username',$form->DatabaseUsername);
	          $tpl->set('db_password',$form->DatabasePassword);
	          $tpl->set('db_host',$form->DatabaseHost);
	          $tpl->set('db_port',$form->DatabasePort);
	          $tpl->set('db_name',$form->DatabaseDatabaseName);

	          $tpl->set('errors',$Errors);
	          $tpl->setFile('lhinstall/install2.tpl.php');
	       }
	  break;

	case '3':

	    $Errors = array();

	    if ($_SERVER['REQUEST_METHOD'] == 'POST')
	    {
    		$definition = array(
                'AdminUsername' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
                ),
                'AdminPassword' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
                ),
                'AdminPassword1' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
                ),
                'AdminEmail' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
                ),
                'AdminName' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                ),
                'AdminSurname' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
                ),
                'DefaultDepartament' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'string'
                )
            );

    	    $form = new ezcInputForm( INPUT_POST, $definition );


    	    if ( !$form->hasValidData( 'AdminUsername' ) || $form->AdminUsername == '')
            {
                $Errors[] = 'Please enter admin username';
            }

            if ($form->hasValidData( 'AdminUsername' ) && $form->AdminUsername != '' && strlen($form->AdminUsername) > 40)
            {
                $Errors[] = 'Maximum 40 characters for admin username';
            }

    	    if ( !$form->hasValidData( 'AdminPassword' ) || $form->AdminPassword == '')
            {
                $Errors[] = 'Please enter admin password';
            }

    	    if ($form->hasValidData( 'AdminPassword' ) && $form->AdminPassword != '' && strlen($form->AdminPassword) > 40)
            {
                $Errors[] = 'Maximum 40 characters for admin password';
            }

    	    if ($form->hasValidData( 'AdminPassword' ) && $form->AdminPassword != '' && strlen($form->AdminPassword) <= 40 && $form->AdminPassword1 != $form->AdminPassword)
            {
                $Errors[] = 'Passwords missmatch';
            }


    	    if ( !$form->hasValidData( 'AdminEmail' ) )
            {
                $Errors[] = 'Wrong email address';
            }


            if ( !$form->hasValidData( 'DefaultDepartament' ) || $form->DefaultDepartament == '')
            {
                $Errors[] = 'Please enter default department name';
            }

            if (count($Errors) == 0) {

               $tpl->set('admin_username',$form->AdminUsername);
               $adminEmail = '';
               if ( $form->hasValidData( 'AdminEmail' ) ) {
               		$tpl->set('admin_email',$form->AdminEmail);
               		$adminEmail = $form->AdminEmail;
               }
    	       $tpl->set('admin_name',$form->AdminName);
    	       $tpl->set('admin_surname',$form->AdminSurname);
    	       $tpl->set('admin_departament',$form->DefaultDepartament);

    	       /*DATABASE TABLES SETUP*/
    	       $db = ezcDbInstance::get();

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
				  KEY `idx_device` (`deviceuuid`,`platform`)
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

            } else {

               $tpl->set('admin_username',$form->AdminUsername);
               if ( $form->hasValidData( 'AdminEmail' ) ) $tpl->set('admin_email',$form->AdminEmail);
    	       $tpl->set('admin_name',$form->AdminName);
    	       $tpl->set('admin_surname',$form->AdminSurname);
    	       $tpl->set('admin_departament',$form->DefaultDepartament);

    	       $tpl->set('errors',$Errors);

    	       $tpl->setFile('lhinstall/install3.tpl.php');
            }
	    } else {
	        $tpl->setFile('lhinstall/install3.tpl.php');
	    }

	    break;

	case '4':
	    $tpl->setFile('lhinstall/install4.tpl.php');
	    break;

	default:
	    $tpl->setFile('lhinstall/install1.tpl.php');
		break;
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'install';
$Result['path'] = array(array('title' => 'Live helper chat installation'));

} catch (Exception $e) {
	echo "Make sure that &quot;cache/*&quot; is writable and then <a href=\"".erLhcoreClassDesign::baseurl('install/install')."\">try again</a>";

	echo "<pre>";
	print_r($e);
	echo "</pre>";
	exit;
}
?>