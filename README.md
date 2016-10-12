# Wurrd Client Interface extension for LiveHelperChat

This extension provides an interface for the Wurrd app to communicate with a LiveHelperChat server.


## Wurrd App

Wurrd is an app that provides operators the ability to chat with website visitors from their mobile devices. This extension exposes an API that the Wurrd app uses to communicate with a LiveHelperChat server. The app can be downloaded from [Google Play](https://play.google.com/store/apps/details?id=com.scalior.wurrd) and from the [App Store](https://itunes.apple.com/us/app/wurrd/id1017128684?mt=8). _(iOS not yet integrated with LHC)_


## Installation
Full install and update instructions with pictures can be found on the [Wurrd website](http://wurrdapp.com/how-to-install-the-livehelperchat-extension/)

1. Get the built archive for this extension from [here](http://wurrdapp.com/get-it-now).
1. Untar/unzip the extension's archive.
1. Copy the entire directory structure for the extension into the `<LHC root>/extension`  folder.
1. Activate the extension by adding wurrd to the extension array in "`<LHC Base URL>`/settings/settings.ini.php" (e.g. array (0 => 'wurrd')).
1. Navigate to "`<LHC Base URL>`/index.php/wurrd/install" and follow the steps to complete the installation.
1. Click 'basic test' at the end of the installation to confirm that the installation was successful. You should see the following text:

`
{"message":"Success","apiversion":"1000","chatplatform":"livehelperchat","usepost":false}
`


## Updating

Not yet implemented.


## Extension's configurations

A configuration tool is provided to make some changes to configuration after installation. 
Access the tool at `<LHC Base URL>`/extension/wurrd/configure.php

The following config properties are available:

### use_http_post (Force use of POST)

Type: `Boolean`

This is needed only if you attempt to login from your device and you receive error 501 or null. This is caused by this issue where some hosting providers block or redirect PUT and DELETE requests. Enable this checkbox if you experience an error logging in. 

### wurrd_ci_installation_id

Type: `String`

This is unique ID autogenerated upon installation and should not be changed.

### contact_email

Type: `String`

This is needed for reporting on the health of the app. 



## Change log
The change log can be found at https://github.com/alberto234/wurrd-lhc-client-interface-extension/blob/master/wurrd/changelog.txt




## Build from sources

There are several actions one should do before using the latest version of the extension from the repository:

1. Obtain a copy of the repository using `git clone`, download button, or another way.
2. Install [node.js](http://nodejs.org/) and [npm](https://www.npmjs.org/).
3. Install [Gulp](http://gulpjs.com/).
4. Install npm dependencies using `npm install`.
5. Run Gulp to build the sources using `gulp default`.

Finally `.tar.gz` and `.zip` archives of the ready-to-use extension will be available in `release` directory.


## License

[Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
