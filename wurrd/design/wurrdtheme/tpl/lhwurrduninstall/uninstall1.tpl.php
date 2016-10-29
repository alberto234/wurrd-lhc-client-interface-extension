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

<h1>Wurrd Client Interface Uninstallation Step 1</h1>

<form action="<?php echo erLhcoreClassDesign::baseurl('wurrd/uninstall')?>/1" method="POST">

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<h3>Sorry to see you go! Hope you will try us again</h3>

<table class="table">
    <tr>
        <td><h4>Installed version</h4></td>
        <td><h4><?php echo $wci_installed_ver;?></h4></td>
    </tr>
</table>
<br>

<input type="submit" class="btn btn-default" value="Next" name="Next">
<br /><br />

</form>