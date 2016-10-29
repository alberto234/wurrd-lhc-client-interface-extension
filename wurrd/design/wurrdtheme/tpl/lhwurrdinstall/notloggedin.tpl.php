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

<?php
	$errors = array();
	$errors[] = "You are not logged in!";
	include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));
?>

<div class="panel">
<p>First <a href="<?php echo erLhcoreClassDesign::baseurl('site_admin/user/login')?>" target="_blank">login here</a>, 
	then refresh this window to complete the operation.
</p>
<br>
</div>
