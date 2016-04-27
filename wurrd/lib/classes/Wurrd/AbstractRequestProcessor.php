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

namespace Wurrd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
 
 /**
 * Implements abstract class for request processing
 *
*/

abstract class AbstractRequestProcessor {

	// These are to be set by the implementing class
	protected $router; 
		
	public function handleRequest(Request $request) {
		// Find the appropriate controller.
		// Execute the controller
		
		try {
            // Get controller and perform its action to get a response.
            $controller = $this->getController($request);
            $response = call_user_func($controller, $request);
		} catch (Exception $e) {

		}
		
        return $response;
    }
	
	
    /**
     * Resolves controller by request.
     *
     * @param Request $request Incoming request.
     * @return callable
     * @throws \InvalidArgumentException If the controller cannot be resolved.
     */
    public function getController(Request $request)
    {
    	
		$parameters = $this->router->matchRequest($request);
        $request->attributes->add($parameters);
        $controller = $parameters['_controller'];
        if (!$controller) {
            //throw new \InvalidArgumentException('The "_controller" parameter is missed.');
        }

        // Build callable for specified controller
        $callable = $this->createController($controller);

        if (!is_callable($callable)) {
            /*throw new \InvalidArgumentException(sprintf(
                'Controller "%s" for URI "%s" is not callable.',
                $controller,
                $request->getPathInfo()
            ));*/
        }

        return $callable;
    }
	
    /**
     * Builds controller callable by its full name.
     *
     * @param string $controller Full controller name in "<Class>::<method>"
     *   format.
     * @return callable Controller callable
     * @throws \InvalidArgumentException
     */
    protected function createController($controller)
    {
        if (strpos($controller, '::') === false) {
            /*throw new \InvalidArgumentException(sprintf(
                'Unable to find controller "%s".',
                $controller
            ));*/
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            // throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
            error_log(sprintf('Class "%s" does not exist.', $class));
        }

        $object = new $class();
        return array($object, $method);
    }

}




 
 
 
 
 ?>