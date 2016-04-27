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

/**
 * @file Constants used by the Wurrd:ClientInterface extension.
 */

namespace Wurrd\ClientInterface;

/**
 * Constants
 */
class Constants
{
	// Version and installation informatiom    
    const WCI_VERSION 				= '1.0.0';
	const WCI_API_VERSION			= '1000';
	const WCI_CHAT_PLATFORM			= 'livehelperchat';
	const WCI_CONFIG_PREFIX			= 'wurrd_ci_';
	const WCI_VERSION_KEY			= 'wurrd_ci_version';
	const WCI_INSTALLATION_ID_KEY	= 'wurrd_ci_installation_id';
	const WCI_API_VERSION_KEY		= 'wurrd_ci_api_version';
	const SUPPORTED_LHC_PROTOCOL	= '1.0';
    const TOKEN_VERSION 			= '1';
		
	// API response messages
	const MSG_SUCCESS			 		= 'Success';
	const MSG_UNKNOWN_ERROR				= 'UnknownError';
	const MSG_BAD_USERNAME_PASSWORD 	= 'BadUsernameOrPassword';
	const MSG_INVALID_ACCESS_TOKEN 		= 'InvalidAccessToken';
	const MSG_EXPIRED_ACCESS_TOKEN 		= 'ExpiredAccessToken';
	const MSG_INVALID_REFRESH_TOKEN 	= 'InvalidRefreshToken';
	const MSG_EXPIRED_REFRESH_TOKEN 	= 'ExpiredRefreshToken';
	const MSG_NEW_TOKEN_GENERATED 		= 'NewTokenGenerated';
	const MSG_INVALID_OPERATOR			= 'InvalidOperator';
	const MSG_INVALID_DEVICE			= 'InvalidDevice';
	const MSG_INVALID_JSON				= 'InvalidJSON';
	const MSG_INVALID_CLIENTID			= 'InvalidClientID';
	const MSG_WRONG_TRHEAD				= 'WrongThread';
	const MSG_CANNOT_TAKEOVER			= 'CannotTakeover';
	const MSG_CANNOT_TAKE_THREAD		= 'CannotTakeThread';
	const MSG_CANNOT_VIEW_THREADS		= 'CannotViewThreads';
	const MSG_CONFIRM_TAKEOVER			= 'ConfirmTakeover';
	
	// Constants for keys used to request access
	const CLIENTID_KEY 			= 'clientid';
	const USERNAME_KEY 			= 'username';
	const PASSWORD_KEY 			= 'password';
	const DEVICEUUID_KEY 		= 'deviceuuid';
	const PLATFORM_KEY 			= 'platform';
	const TYPE_KEY 				= 'type';
	const DEVICENAME_KEY 		= 'devicename';
	const DEVICEOS_KEY 			= 'os';
	const DEVICEOSVERSION_KEY 	= 'osversion';
	const ACCESSTOKEN_KEY		= 'accesstoken';
	const CLIENTAPIVER_KEY		= 'clientapiver';
	const CLIENTREVISION_KEY	= 'clientrevision';
		
	// Defaults for authorization.
	// These could be provided as plugin configurations
	const ACCESS_DURATION	 	= 3600;			// One hour
	const REFRESH_DURATION 		= 2592000;		// 30 days

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
 