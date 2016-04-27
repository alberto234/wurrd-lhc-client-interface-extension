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

namespace Wurrd\Http\Exception;

/**
 * Base HTTP exceptions.
 *
 */
class HttpException extends \RuntimeException
{
    private $statusCode;

    /**
     * Class contructor.
     *
     * @param int $status_code HTTP status code related with the exception.
     * @param string $message The Exception message to throw.
     * @param \Exception $previous The previous exception used for the exception
     * chaining.
     * @param int $code The Exception code.
     */
    public function __construct($status_code, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->statusCode = $status_code;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns HTTP status code related with the exception.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
