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

<h1>Wurrd Client Interface Update Step 1</h1>

<form action="<?php echo erLhcoreClassDesign::baseurl('wurrd/update')?>/1" method="POST">

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div class="panel">
  <p>You will be prompted to walk through the update steps if there are any required</p>
</div>

<h2>Checking versions</h2>

<table class="table">
    <tr>
        <td><h3>Installed version</h3></td>
        <td><h3><?php echo $wci_installed_ver;?></h3></td>
    </tr>
    <tr>
        <td><h3>Updating to version</h3></td>
        <td><h3><span class="success-color"><?php echo $wci_new_version;?></span></h3></td>
    </tr>
</table>
<br>

<input type="submit" class="btn btn-default" value="Update" name="Update">
<br /><br />

</form>