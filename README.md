# U2F-php-server
Server-side handling of U2F registration and authentication for PHP


## Installation

`composer require samyoul/u2f-php`


## Things You Need

A few **things you need** to know before working with this:

1. **_A Datastore._** You need some kind of datastore for all your U2F registered users (although if you have a system with user authentication I'm presuming you've got this one sorted).
2. **_Client-side Handling._** You need to be able to communicate with a some kind of device. I've got help for this here [in this readme](#client-side)
3. **_A HTTPS URL._** This is very important, without HTTPS Chrome will refuse to communicate with you.

### Client-side

My presumption is that if you are looking to add U2F authentication to a php system, then you'll probably are also looking for some client-side handling. You've got a U2F enabled USB device and you want to get the USB device speaking with the browser and then with your server running php.

1. Google already have this bit sorted : https://github.com/google/u2f-ref-code/blob/master/u2f-gae-demo/war/js/u2f-api.js
2. [Mastahyeti](https://github.com/mastahyeti) has created a repo dedicated to Google's JavaScript Client-side API : https://github.com/mastahyeti/u2f-api


## Example Code

```php
<?php
    // All the amazing code

```
