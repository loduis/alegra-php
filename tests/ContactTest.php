<?php

namespace Alegra\Tests;

use Alegra\Contact;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

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

    public function testCreateUsingFluent()
    {
        // With fluent instance
        $contact = Contact::create(new Fluent([
            'name'           => $this->faker->name,
            'identification' => $this->faker->dni,
            'email'          => $this->faker->email,
            'phonePrimary'   => $this->faker->phoneNumber,
            'phoneSecondary' => $this->faker->phoneNumber,
            'fax'            => $this->faker->e164PhoneNumber,
            'mobile'         => $this->faker->e164PhoneNumber,
            'observations'   => $this->faker->text
        ]));
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

    public function testFetch()
    {
        $contact = new Contact([
            'name' => 'Prueba'
        ]);
        $contact->save();
        $createdContact = Contact::fetch($contact->id);
        $this->assertSame('Prueba', $createdContact->name);
    }


    public function testAll()
    {
        $this->registerMockClient();
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
        $id        = $contact->id;
        $contact->delete();
        $this->assertSame(null, $contact->id);
    }
}
