<?php

namespace Alegra\Tests;

use Alegra\Contact;
use Illuminate\Api\Resource\Collection;

class ContactTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals(Contact::resolvePath(), 'contacts');
    }

    public function testCreateUsingArray()
    {
        $contact = Contact::create([
            'name'           => $this->faker->name,
            'type'           => 'client',
            'identification' => $this->faker->dni,
            'email'          => $this->faker->email,
            'phonePrimary'   => $this->faker->phoneNumber,
            'phoneSecondary' => $this->faker->phoneNumber,
            'fax'            => $this->faker->e164PhoneNumber,
            'mobile'         => $this->faker->e164PhoneNumber,
            'observations'   => $this->faker->text
        ]);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertNotNull($contact->id);
    }

    public function testCreateUsingContactConstructor()
    {
        $contact = new Contact;
        $contact->name = $this->faker->name;
        $contact->type = 'provider';
        $contact->save();
        $this->assertNotNull($contact->id);
    }

    public function testGet()
    {
        $contact = new Contact([
            'name' => 'Prueba'
        ]);
        $contact->save();
        $createdContact = Contact::get($contact->id);
        $this->assertSame('Prueba', $createdContact->name);
    }


    public function testAll()
    {
        $contacts = Contact::all();
        $contacts->each(function ($contact) {
            $this->assertInstanceOf(Contact::class, $contact);
            $this->assertNotNull($contact->id);
        });
        $this->assertInstanceOf(Collection::class, $contacts);
    }

    public function testDelete()
    {
        $contact   = Contact::create(['name' => 'Prueba']);
        $contact->delete();
        $this->assertSame(null, $contact->id);
    }

    public function testSave()
    {
        $contact = Contact::create([
            'name' => 'test'
        ]);

        $name = $contact->name;

        $this->assertNotNull($name);

        $contact->name = 'Other test';

        $contact->save();

        $this->assertNotNull($contact->name);

        $this->assertNotEquals($contact->name, $name);
    }
}
