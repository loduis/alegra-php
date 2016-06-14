# Alegra PHP bindings

You can sign up for a Alegra account at http://www.alegra.com/.

## Requirements

PHP 5.6.11 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require loduis/alegra-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/00-intro.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Dependencies

The bindings require the following extension in order to work properly:

- [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Getting Started

Simple usage looks like:

Your composer.json file
```json
{
    "minimum-stability": "dev",
    "prefer-stable": true,
    "require": {
        "loduis/alegra-php": "1.0.*"
    }
}
```

Your test.php script
```php
<?php

use Alegra\Api;
use Alegra\Contact;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

require './vendor/autoload.php';

Api::auth('Your user', 'Your api token');

try {
    // Save using create method

    $contact = Contact::create(['name' => 'Your contact name']); // Create the contact
    print_r($contact);

    // Save using constructor
    $contact = new Contact;
    $contact->name = 'My second contact';
    $contact->save(); // Update the contact
    print_r($contact);

    // Update an existing contact

    $contact = Contact::get(1); // where 1 is the id of resource.
    $contact->identification = '900.123.123-8';
    $contact->email = 'email@server.com';
    $contact->save();
    print_r($contact);

    // Get all contacts

    $contacts = Contact::all();
    $contacts->each(function ($contact) {
        print_r($contact);
    });

    // Get a delete

    Contact::get(1)->delete();

    // Delete without get

    (new Contact(1))->delete();

    // Delete using static interface

    Contact::delete(1);

} catch (ClientException $e) { // 4.x
    // code
} catch (ServerException $e) { // 5.x
    // code
} catch (ConnectException $e) {
    // code
} catch (RequestException $e) {
    // code
} catch (Exception $e) {
    // code
}


```

## Documentation

Please see http://developer.alegra.com/docs for up-to-date documentation.

