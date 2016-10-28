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

use Symfony\Component\HttpFoundation\Request;

/**
 * This is a utility for URL generation
 * 
 */
class UrlGeneratorUtil
{
	/**
     * Generates the full URL based on the input URL including the scheme and host
	 * 
	 * @param Request $request - The Symfony request object
	 * @param string $urPath - The url path to be resolved
	 *  
	 * @return array|bool  An array with the server details or false if a failure
	 */
	public static function getFullURL(Request $request, $urlPath) {

		$fullURL = $urlPath;

		// Fix the url if necessary
		if (strpos($urlPath, '://') === false) {
			// For now we assume that if the scheme wasn't provided then
			// the URL is relative from the root of the domain.
			$fullURL = $request->getSchemeAndHttpHost() . $urlPath;
		}
		
		return $fullURL;
	}
}

