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

<h1>Wurrd Client Interface Uninstall Complete</h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div class="panel">

<p>Thank you for giving the Wurrd app a shot. We would like to know why the app didn't work out for you. Please drop us a note at 
	<a href="mailto:info@wurrdapp.com?subject=Feedback from LHC">info@wurrdapp.com</a></p>

<p>If you wish to install the extension again, click <a href="<?php echo erLhcoreClassDesign::baseurl('wurrd/install')?>">here.</a>
</p>
<br>
</div>
