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

    // Save using instance
    $contact = new Contact;
    $contact->name = 'My second contact';
    $contact->save(); // Update the contact

    // Update an existing contact

    $contact = Contact::get(1); // where 1 is the id of resource.
    $contact->identification = '900.123.123-8';
    $contact->email = 'email@server.com';
    $contact->save();

    // Get all contacts

    $contacts = Contact::all();
    $contacts->each(function ($contact) {
        print_r($contact);
    });

    // Get a delete

    $contact = Contact::get(1);
    $contact->delete();

    // Delete without get

    $contact = new Contact(1);
    $contact->delete();

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
