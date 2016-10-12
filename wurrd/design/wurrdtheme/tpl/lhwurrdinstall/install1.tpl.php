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

<h1>Wurrd Client Interface Installation</h1>

<form action="<?php echo erLhcoreClassDesign::baseurl('wurrd/install')?>/1" method="POST">

<div class="panel">
  <p>You will need to grant write permissions on any of the red-marked folders. You can do this by changing its username to your web server's username or by changing permissions with a CHMOD 777 on the displayed files/folders.</p>
</div>

<h2>Checking folders permission</h2>

<table class="table">
    <tr>
        <td>I can write to &quot;<?php echo $wci_settings_dir; ?>&quot; directory</td>
        <td><?php echo is_writable($wci_settings_dir) ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>'?></td>
    </tr>
    <tr>
        <td>I can write to &quot;<?php echo $wci_cache_dir; ?>&quot; directory</td>
        <td><?php echo is_writable($wci_cache_dir) ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>'?></td>
    </tr>
</table>
<br>

<input type="submit" class="btn btn-default" value="Next" name="Install">
<br /><br />

</form>