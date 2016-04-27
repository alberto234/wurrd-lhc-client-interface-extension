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

namespace Wurrd\ClientInterface\Classes;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Wurrd\AbstractRequestProcessor;

 
 /**
 * Implements abstract class for request processing
 *
*/

class ClientInterfaceRequestProcessor extends AbstractRequestProcessor {
	
	public function __construct(Request $request) {
		$locator = new FileLocator(array(__DIR__ . '/..'));
		$requestContext = new RequestContext();
		$requestContext->fromRequest($request);
		
		$this->router = new Router(
		    new YamlFileLoader($locator),
		    'routing.yml',
		    array('cache_dir' => null),
		    $requestContext
		);
	}
	
	public static function now() {
		error_log('now');
	}
}




 
 
 
 
 ?>