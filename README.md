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

```php
\Alegra\Api::auth('Your user', 'Your api token');
$myContact = ['name' => 'Your contact name'];
$contact = \Alegra\Contact::create($myContact); // Create the contact
$contact->identification = '900.123.123-8';
$contact->save(); // Update the contact
$contact->delete(); // Delete the contact

// Fetch an existing contact
$contact = Contact::fetch(1);
$contact->email = 'email@server.com';
```

## Documentation

Please see http://developer.alegra.com/docs for up-to-date documentation.

