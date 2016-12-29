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
 
 // This is where all the processing for wurrd/clientinterface will happen
 
// define(WURRD_EXTENSION_FS_ROOT, dirname(dirname(dirname(__FILE__))));

// $loader = require_once(WURRD_EXTENSION_FS_ROOT . '/vendor/autoload.php');
//$loader->addPsr4('', WURRD_EXTENSION_FS_ROOT . '/lib/classes/', true);
//$loader->addPsr4('Wurrd\\ClientInterface\\', __DIR__ . '/ClientInterface/', true);


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wurrd\ClientInterface\Classes\ClientInterfaceRequestProcessor;
use Wurrd\Http\Exception\HttpException;

$response = null;
try {
	$request = Request::createFromGlobals();
	$requestProcessor = new ClientInterfaceRequestProcessor($request);

	$response = $requestProcessor->handleRequest($request);
} catch (HttpException $exception) {
	$response = new Response($exception->getMessage(),
		$exception->getStatusCode(),
		array('content-type' => 'text/plain'));
}

$response->send();

exit();
