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

<form action="<?php echo erLhcoreClassDesign::baseurl('wurrd/uninstall')?>/2" method="post" autocomplete="off" >
<h1>Wurrd Client Interface Uninstallation Step 2</h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<h2>Confirm uninistall</h2>

<p>This will remove all the entries in the database. You will have to manually disable the extension and remove wurrd from the extension directory</p>

<table class="table">
    <tr>
        <td>Do you really want to uninstall the extension?
        </td>
        <td><input class="form-control" type="checkbox" name="UninstalExtension" value="true"></td>
    </tr>
</table>
<br>
<input type="submit" class="btn btn-default" value="Uninstall" name="Uninstall">
<br /><br />

</form>