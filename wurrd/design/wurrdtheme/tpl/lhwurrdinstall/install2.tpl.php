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
?>

<img src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="Live Helper Chat" title="Live Helper Chat" />

<h1>Wurrd Client Interface Installation Step 2</h1>


<?php
// The form below should be a read-only form. We are going to extract the db info from the settings and present them here
?>

<form action="<?php echo erLhcoreClassDesign::baseurl('wurrd/install')?>/2" method="POST" autocomplete="off">

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<h2>Database settings</h2>

<div class="panel">
  <p>Click next to confirm settings below</p>
</div>

<table class="table">
    <tr>
        <td>Username</td>
        <td><input class="form-control" type="text" name="DatabaseUsername" value="<?php echo isset($db_username) ? htmlspecialchars($db_username) : ''?>" disabled /></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><input class="form-control" type="password" name="DatabasePassword" value="<?php echo isset($db_password) ? htmlspecialchars($db_password) : ''?>" disabled /></td>
    </tr>
    <tr>
        <td>Host</td>
        <td><input class="form-control" type="text" name="DatabaseHost" value="<?php echo isset($db_host) ? htmlspecialchars($db_host) : '127.0.0.1' ?>" disabled /> </td>
    </tr>
    <tr>
        <td>Port</td>
        <td><input class="form-control" type="text" name="DatabasePort" value="<?php echo isset($db_port) ? htmlspecialchars($db_port) : '3306'?>" disabled /></td>
    </tr>
    <tr>
        <td>Database name</td>
        <td><input class="form-control" type="text" name="DatabaseDatabaseName" value="<?php echo isset($db_name) ? htmlspecialchars($db_name) : ''?>" disabled /></td>
    </tr>
</table>
<br>

<input type="submit" value="Next" class="btn btn-default" name="Install">
<br /><br />

</form>