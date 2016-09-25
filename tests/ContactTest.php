<?php

namespace Alegra\Tests;

use Alegra\Client;
use Alegra\Seller;
use Alegra\Contact;
use Alegra\Provider;
use Alegra\Support\Address;
use Alegra\Contact\Internal as InternalContact;
use Alegra\Application;
use Illuminate\Api\Resource\Collection;
use Illuminate\Support\Collection as BaseCollection;

class ContactTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals(Contact::resolvePath(), 'contacts');
        $this->assertEquals(Client::resolvePath(), 'contacts');
        $this->assertEquals(Provider::resolvePath(), 'contacts');
    }

    /**
     * Puege account on live run
     *
     * @afterClass
     */
    public static function destroyAll()
    {
        Contact::all()->except(1, 2, 3)->each(function ($contact) {
            $contact->delete();
        });
    }

    /**
     * @expectedException     \GuzzleHttp\Exception\ClientException
     * @expectedExceptionCode 400
     */
    public function testShouldFailWhenInvalidEmailIsSet()
    {
        $contact = new Contact;
        $contact->name = $this->faker->name;
        $contact->email = 'invalid';
        $contact->save();
    }

    public function testShouldSetTypeToNullWhenInvalidValueIfGiven()
    {
        $contact = new Contact;
        $contact->name = $this->faker->name;
        $contact->type = 'unknow';
        $contact->save();
        $this->assertCount(0, $contact->type);
    }

    public function testCreateUsingArray()
    {
        $contact = Contact::create([
            'name'           => $this->faker->name,
            'type'           => Contact::TYPE_CLIENT,
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
        $this->assertContains(Contact::TYPE_CLIENT, $contact->type);
        $this->assertCount(1, $contact->type);
        $this->assertInstanceOf(BaseCollection::class, $contact->type);

        $customer = Client::create([
            'name' => $this->faker('name')
        ]);

        $this->assertInstanceOf(Client::class, $customer);
        $this->assertNotNull($customer->id);
        $this->assertContains(Contact::TYPE_CLIENT, $customer->type);
        $this->assertCount(1, $customer->type);
        $this->assertInstanceOf(BaseCollection::class, $customer->type);

        $supplier = Provider::create([
            'name' => $this->faker('name')
        ]);

        $this->assertInstanceOf(Provider::class, $supplier);
        $this->assertNotNull($supplier->id);
        $this->assertContains(Contact::TYPE_PROVIDER, $supplier->type);
        $this->assertCount(1, $supplier->type);
        $this->assertInstanceOf(BaseCollection::class, $supplier->type);

        $contact = Contact::create([
            'name' => $this->faker('name')
        ]);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertNotNull($contact->id);
        $this->assertCount(0, $contact->type);
        $this->assertInstanceOf(BaseCollection::class, $contact->type);

        $contact->type->push(Contact::TYPE_CLIENT);
        $this->assertCount(1, $contact->type);
        $this->assertContains(Contact::TYPE_CLIENT, $contact->type);

        $contact->type->push(Contact::TYPE_PROVIDER);
        $this->assertCount(2, $contact->type);
        $this->assertContains(Contact::TYPE_PROVIDER, $contact->type);

        $contact->save();
        $this->assertCount(2, $contact->type);
        $this->assertInstanceOf(BaseCollection::class, $contact->type);
    }

    public function testCreateUsingConstructor()
    {
        $contact = new Contact;
        $contact->name = $this->faker->name;
        $contact->type = 'provider';
        $contact->save();
        $this->assertNotNull($contact->id);

        $customer = new Client;
        $customer->name = $this->faker('name');
        $customer->save();
        $this->assertNotNull($customer->id);

        $supplier = new Provider;
        $supplier->name = $this->faker('name');
        $supplier->save();
        $this->assertNotNull($supplier->id);

        $contact = new Contact;
        $contact->name = $this->faker->name;
        $contact->type[] = Contact::TYPE_CLIENT;
        $contact->type[] = Contact::TYPE_PROVIDER;

        $this->assertCount(2, $contact->type);
        $this->assertInstanceOf(BaseCollection::class, $contact->type);
    }

    public function testShouldAssignAndSellerToContact()
    {
        $contact = new Contact(['name' => 'test']);
        $contact->seller = Seller::first();
        $contact->save();
        $this->assertInstanceOf(Seller::class, $contact->seller);
        $this->assertInternalType('int', $contact->seller->id);
        $this->assertInternalType('string', $contact->seller->name);
        $this->assertInternalType('string', $contact->seller->identification);
        $this->assertInternalType('string', $contact->seller->observations);

        // $this->assertInternalType('string', $contact->seller->status);
    }

    public function testShouldAssignAndInternalContactsAttribute()
    {
        $contact = new Contact(['name' => 'test']);
        $contact->internalContacts->add([
            'name' => 'Internal contact'
        ]);
        $contact->save();

        $this->assertInstanceOf(Collection::class, $contact->internalContacts);
        $this->assertCount(1, $contact->internalContacts);
        $contact->internalContacts->each(function ($contact) {
            $this->assertInstanceOf(InternalContact::class, $contact);
            $this->assertInternalType('string', $contact->name);
        });

        $contact = Contact::create(['name' => 'other']);
        $contact->internalContacts[] = [
            'name' => 'Using array notation, first'
        ];
        $contact->internalContacts[] = [
            'name' => 'Using array notation, second'
        ];
        $contact->save();
        $this->assertCount(2, $contact->internalContacts);

        $contact = Contact::create(['name' => 'Other']);
        $contact->internalContacts = [
            'name' => 'Set as array'
        ];

        $this->assertInstanceOf(Collection::class, $contact->internalContacts);
        $this->assertCount(1, $contact->internalContacts);
        $contact->internalContacts->each(function ($contact) {
            $this->assertInstanceOf(InternalContact::class, $contact);
            $this->assertInternalType('string', $contact->name);
        });

        $contact = new Contact(['name' => 'Other']);
        $contact->internalContacts = 'Set name of the contact';

        $this->assertInstanceOf(Collection::class, $contact->internalContacts);
        $this->assertCount(1, $contact->internalContacts);
        $contact->internalContacts->each(function ($contact) {
            $this->assertInstanceOf(InternalContact::class, $contact);
            $this->assertInternalType('string', $contact->name);
        });
    }

    public function testShouldAssignAttributeAddress()
    {
        Application::version('mexico');
        $contact = new Contact(['name' => 'test']);

        $contact->address = 'Calle 10 # 55-31';
        $this->assertInstanceOf(Address::class, $contact->address);
        $this->assertEquals($contact->address->address, 'Calle 10 # 55-31');
        $contact->save();

        $contact->address = [
            'address' => 'Calle 10 # 55-31',
            'city' => 'Bogota'
        ];
        $contact->save();
        $this->assertEquals($contact->address->address, 'Calle 10 # 55-31');
        $this->assertEquals($contact->address->city, 'Bogota');

        $contact->address = new Address([
            'address' => 'Calle 10 # 55-31',
            'city' => 'Bogota'
        ]);
        $contact->save();

        $this->assertEquals($contact->address->address, 'Calle 10 # 55-31');
        $this->assertEquals($contact->address->city, 'Bogota');
    }

    public function testGet()
    {
        $contact = new Contact([
            'name' => 'Prueba'
        ]);
        $contact->save();
        $createdContact = Contact::get($contact->id);
        $this->assertSame('Prueba', $createdContact->name);

        $contact = new Client([
            'name' => 'Client'
        ]);
        $contact->save();
        $createdContact = Client::get($contact->id);
        $this->assertSame('Client', $createdContact->name);

        $contact = new Provider([
            'name' => 'Provider'
        ]);
        $contact->save();
        $createdContact = Provider::get($contact->id);
        $this->assertSame('Provider', $createdContact->name);
        $this->assertSame($contact->id, $createdContact->id);
    }

    public function testAll()
    {
        $contacts = Contact::all();
        $this->assertGreaterThanOrEqual(1, count($contacts));
        $contacts->each(function ($contact) {
            $this->assertInstanceOf(Contact::class, $contact);
            $this->assertNotNull($contact->id);
        });
        $this->assertInstanceOf(Collection::class, $contacts);


        $contacts = Client::all();
        $this->assertGreaterThanOrEqual(1, count($contacts));
        $contacts->each(function ($contact) {
            $this->assertInstanceOf(Client::class, $contact);
            $this->assertNotNull($contact->id);
        });
    }

    /**
     * @expectedException     \GuzzleHttp\Exception\ClientException
     * @expectedExceptionCode 404
     */
    public function testDelete()
    {
        $contact   = Contact::create(['name' => 'Prueba']);
        $contact->delete();
        $this->assertSame(null, $contact->id);
        $contact   = Contact::create(['name' => 'Prueba']);
        Contact::delete($contact->id);
        $contact = Contact::get($contact->id);
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
