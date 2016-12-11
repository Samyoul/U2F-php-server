# U2F-php-server
Server-side handling of FIDO U2F registration and authentication for PHP.

Securing your online accounts and doing your bit to protect your data is extremely important and increasingly more so as hackers get more sophisticated.
FIDO's U2F enables you to add a simple unobtrusive method of 2nd factor authentication, allowing users of your service and/or application to link a hardware key to their account.

## Contents

1. [Installation](#installation)
2. [Requirements](#requirements)
    1. [OpenSSL](#openssl)
    1. [Clientside Magic](#client-side-the-magic-javascript-bit-of-talking-with-a-usb-device)
    1. [HTTPS and SSL](#https-and-ssl)
3. [Terminology](#terminology)
4. [Recommended Datastore Structure](#recommended-datastore-structure)
5. [Process Workflow](#process-workflow)
    1. [Registration Process Flow](#registration-process-flow)
    1. [Authentication Process Flow](#authentication-process-flow)
6. [Example Code](#example-code)
    1. [Compatibility Check](#compatibility-code)
    1. [Registration Code](#registration-code)
    1. [Authentication Code](#authentication-code)
7. [Frameworks](#frameworks)
    1. [Laravel](#laravel-framework)
    1. [Yii](#yii-framework)
    1. [CodeIgniter](#codeigniter-framework)
8. [Licence](#licence)
9. [Credits](#credits)

## Installation

`composer require samyoul/u2f-php-server`

## Requirements

A few **things you need** to know before working with this:

1. [**_OpenSSL_**](#openssl) You need at least OpenSSL 1.0.0 or higher.
2. [**_Client-side Handling_**](#client-side) You need to be able to communicate with a some kind of device.
3. [**_A HTTPS URL_**](#https-and-ssl) This is very important, without HTTPS U2F simply will not work.
4. [**_A Datastore_**](#recommended-datastore-structure) You need some kind of datastore for all your U2F registered users (although if you have a system with user authentication I'm presuming you've got this one sorted).

### OpenSSL

This repository requires OpenSSL 1.0.0 or higher. For further details on installing OpenSSL please refer to the php manual http://php.net/manual/en/openssl.installation.php .

Also see [Compatibility Code](#compatibility-code), to check if you have the correct version of OpenSSL installed, and are unsure how else to check.

### Client-side (The magic JavaScript Bit of talking with a USB device)

My presumption is that if you are looking to add U2F authentication to a php system, then you'll probably are also looking for some client-side handling. You've got a U2F enabled USB device and you want to get the USB device speaking with the browser and then with your server running php.

1. Google already have this bit sorted : https://github.com/google/u2f-ref-code/blob/master/u2f-gae-demo/war/js/u2f-api.js
2. [Mastahyeti](https://github.com/mastahyeti) has created a repo dedicated to Google's JavaScript Client-side API : https://github.com/mastahyeti/u2f-api

### HTTPS and SSL

For U2F to work your website/service must use a HTTPS URL. Without a HTTPS URL your code won't work, so get one for your localhost, get one for your production. https://letsencrypt.org/
Basically encrypt everything.


## Terminology

**_HID_** : _Human Interface Device_, like A USB Device [like these things](https://www.google.co.uk/search?q=fido+usb+key&safe=off&tbm=isch)

## Recommended Datastore Structure

You don't need to follow this structure exactly, but you will need to associate your Registration data with a user. You'll also need to store the key handle, public key and the certificate, counter isn't 100% essential but it makes your application more secure.


|Name|Type|Description|
|---|---|---|
|id|integer primary key||
|user_id|integer||
|key_handle|varchar(255)||
|public_key|varchar(255)||
|certificate|text||
|counter|integer||

TODO the descriptions

## Process Workflow

### Registration Process Flow

1. User navigates to a 2nd factor authentication page in your application.
... TODO add the rest of the registration process flow ...

### Authentication Process Flow

1. User navigates to their login page as they usually would, submits username and password.
2. Server received POST request authentication data, normal username + password validation occurs
3. On successful authentication, the application checks 2nd factor authentication is required. We're going to presume it is, otherwise the user would just be logged in at this stage.
4. Application gets the user's registered signatures from the application datastore: `$registrations`.
5. Application makes a `$U2F->makeAuthentication($registrations)` call, the method returns an array of `SignRequest` objects: `$signRequest`.
6. Application JSON encodes the array and passes the data to the view
7. When the browser loads the page the JavaScript fires the `u2f.sign(sign_requests, function(data){ // Callback logic })` function
8. The view will use JavaScript / Browser to poll the host machine's ports for a FIDO U2F device
9. Once the HID has been found the JavaScript / Browser will send the sign request with data.
10. The HID will prompt the user to authorise the sign request
11. On success the HID returns authentication data
12. The JavaScript receives the HID's returned data and passes it to the server
13. The application takes the returned data passes it to the `$U2F->authenticate($signRequest, $registrations, $incomingData)` method
14. If the method returns a registration and doesn't throw an Exception, authentication is complete.
15. Set the user's session, inform the user of the success, and redirect them.

## Example Code

For a full working example of this repository please see [the dedicated example repository](https://github.com/Samyoul/U2F-php-server-examples)

You can also install it with the following:

`composer require samyoul/u2f-php-server-examples`


1. [Compatibility Code](#compatibility-code)
2. [Registration Code](#registration-code)
    1. [Step 1: Starting](#registration-step-1)
    1. [Step 2: Talking to the HID](#registration-step-2)
    1. [Step 3: Validation & Storage](#registration-step-3)
3. [Authentication Code]()
    1. [Step 1: Starting]()
    1. [Step 2: Talking to the HID]()
    1. [Step 3: Validation]()

### Compatibility Code

You'll only ever need to use this method call once per installation and only in the context of debugging if the class is giving you unexpected errors. This method call will check your OpenSSL version and ensure it is at least 1.0.0 .

```php
<?php

require('vendor/autoload.php');
use Samyoul\U2F;

var_dump(U2F::checkOpenSSLVersion());
```

### Registration Code

#### Registration Step 1:
**Starting the registration process:**

We assume that user has successfully authenticated and wishes to register.

```php
<?php

require('vendor/autoload.php');
use Samyoul\U2F;

session_start();

// This can be anything, but usually easier if you choose your applications domain and top level domain.
$appId = "yourdomain.tld";

// Call the makeRegistration method, passing in the app ID
$registrationData = U2F::makeRegistration($appId);

// Store the request for later
$_SESSION['registrationRequest'] = $registrationData['request'];

// Extract the request and signatures, JSON encode them so we can give the data to our javaScript magic
$jsRequest = json_encode($registrationData['request']);
$jsSignatures = json_encode($registrationData['signatures']);

// now pass the data to a fictitious view.
echo View::make('template/location/u2f-registration.html', compact("jsRequest", "jsSignatures"));
```

#### Registration Step 2:
**Client-side, Talking To The USB**

Non-AJAX client-side registration of U2F key token. AJAX can of course be used in your application, but it is easier to demonstrate a linear process without AJAX and callbacks. 

```html
<html>
<head>
    <title>U2F Key Registration</title>
</head>
<body>
    <h1>U2F Registration</h1>
    <h2>Please enter your FIDO U2F device into your computer's USB port. Then confirm registration on the device.</h2>
    
    <div style="display:none;">
        <form id="u2f_submission" method="post" action="auth/u2f-registration/confirm">
            <input id="u2f_registration_response" name="registration_response" value="" />
        </form>
    </div>
    
<script type="javascript" src="https://raw.githubusercontent.com/google/u2f-ref-code/master/u2f-gae-demo/war/js/u2f-api.js"></script>
<script>
    setTimeout(function() {
    
        // A magic JS function that talks to the USB device. This function will keep polling for the USB device until it finds one.
        u2f.register([<?php echo $jsRequest ?>], <?php echo $jsSignatures ?>], function(data) {
           
            // Handle returning error data
            if(data.errorCode && errorCode != 0) {
                alert("registration failed with error: " + data.errorCode);
                // Or handle the error however you'd like. 
                
                return;
            }
    
            // On success process the data from USB device to send to the server
            var registration_response = JSON.stringify(data);
            
            // Get the form items so we can send data back to the server
            var form = document.getElementById('u2f_submission');
            var response = document.getElementById('u2f_registration_response');
            
            // Fill and submit form.
            response.value = JSON.stringify(registration_response);
            form.submit();
        });
    }, 1000);
</script>
</body>
</html>
```

#### Registration Step 3:
**Validation and Key Storage**

This is the last stage of registration. Validate the registration response data against the original request data.

```php
<?php

require('vendor/autoload.php');
use Samyoul\U2F;

session_start();

// Fictitious function representing getting the authenticated user object
$user = getAuthenticatedUser();

try {
    
    // Validate the registration response against the registration request.
    // The output are the credentials you need to store for U2F authentication.
    $validatedRegistration = U2F::register(
        $_SESSION['registrationRequest'],
        json_decode($_POST['u2f_registration_response'])
    );
    
    // Fictitious function representing the storing of the validated U2F registration data against the authenticated user. 
    $user->storeRegistration($validatedRegistration);
    
    // Then let your user know what happened
    $userMessage = "Success";
} catch( Exception $e ) {
    $userMessage = "We had an error: ". $e->getMessage();
}

//Fictitious view.
echo View::make('template/location/u2f-registration-result.html', ['userMessage' => $userMessage]);
```

### Authentication Code

**Starting the authentication process:**

We assume that user has successfully authenticated and has previously registered.

```php
<?php
    // All the amazing authentication code

```

## Frameworks

### Laravel Framework

See the dedicated repository : https://github.com/Samyoul/U2F-Laravel-server

Installation:

`composer require u2f-laravel-server`

### Yii Framework

See the dedicated repository : https://github.com/Samyoul/U2F-Yii-server

Installation:

`composer require u2f-yii-server`

### CodeIgniter Framework

See the dedicated repository : https://github.com/Samyoul/U2F-CodeIgniter-server

Installation:

`composer require u2f-codeigniter-server`

### Can't see yours?

**Your favourite php framework not in this list? Get coding and submit a pull request and get your framework extension included here.**

## Licence

The repository is licensed under a BSD license. [Read details here](https://github.com/Samyoul/U2F-php-server/blob/master/LICENCE.md)

## Credits

This repo was originally based on the Yubico php-u2flib-server https://github.com/Yubico/php-u2flib-server
