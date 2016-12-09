# U2F-php-server
Server-side handling of U2F registration and authentication for PHP


## Installation

`composer require samyoul/u2f-php-server`

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

## Things You Need

A few **things you need** to know before working with this:

1. **_A Datastore._** You need some kind of datastore for all your U2F registered users (although if you have a system with user authentication I'm presuming you've got this one sorted).
2. **_Client-side Handling._** You need to be able to communicate with a some kind of device. I've got help for this [here](#client-side)
3. **_A HTTPS URL._** This is very important, without HTTPS Chrome will refuse to communicate with you. [See here](#https-and-ssl)

### Client-side (The magic JavaScript Bit of talking with a USB device)

My presumption is that if you are looking to add U2F authentication to a php system, then you'll probably are also looking for some client-side handling. You've got a U2F enabled USB device and you want to get the USB device speaking with the browser and then with your server running php.

1. Google already have this bit sorted : https://github.com/google/u2f-ref-code/blob/master/u2f-gae-demo/war/js/u2f-api.js
2. [Mastahyeti](https://github.com/mastahyeti) has created a repo dedicated to Google's JavaScript Client-side API : https://github.com/mastahyeti/u2f-api

### HTTPS and SSL

Without a HTTPS URL your code won't work, so get one for your localhost, get one for your production. https://letsencrypt.org/

## Process Workflow

### Registration Process flow

1. User navigates to a 2nd factor authentication page in your application.

### Authentication Process flow

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

### Registration Code

```php
<?php
    // All the amazing registration code

```

### Authentication Code

```php
<?php
    // All the amazing authentication code

```

## Licence

The repository is licensed under a BSD license.

## Credits

This repo was originally based on the Yubico php-u2flib-server https://github.com/Yubico/php-u2flib-server
