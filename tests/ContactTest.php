<?php

namespace Alegra\Tests;

use Alegra\Contact;
use Alegra\Supplier;
use Alegra\Customer;
use Illuminate\Api\Resource\Collection;
use Illuminate\Support\Collection as BaseCollection;

class ContactTest extends TestCase
{
    public function testResolvePath()
    {
        $this->assertEquals(Contact::resolvePath(), 'contacts');
        $this->assertEquals(Customer::resolvePath(), 'contacts');
        $this->assertEquals(Supplier::resolvePath(), 'contacts');
    }

    /**
     * @itest
     *
     * @return [type]
     */
    public function destroyAll()
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
            'type'           => Contact::TYPE_CUSTOMER,
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
        $this->assertContains(Contact::TYPE_CUSTOMER, $contact->type);
        $this->assertCount(1, $contact->type);
        $this->assertInstanceOf(BaseCollection::class, $contact->type);

        $customer = Customer::create([
            'name' => $this->faker('name')
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertNotNull($customer->id);
        $this->assertContains(Contact::TYPE_CUSTOMER, $customer->type);
        $this->assertCount(1, $customer->type);
        $this->assertInstanceOf(BaseCollection::class, $customer->type);

        $supplier = Supplier::create([
            'name' => $this->faker('name')
        ]);

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertNotNull($supplier->id);
        $this->assertContains(Contact::TYPE_SUPPLIER, $supplier->type);
        $this->assertCount(1, $supplier->type);
        $this->assertInstanceOf(BaseCollection::class, $supplier->type);

        $contact = Contact::create([
            'name' => $this->faker('name')
        ]);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertNotNull($contact->id);
        $this->assertCount(0, $contact->type);
        $this->assertInstanceOf(BaseCollection::class, $contact->type);

        $contact->type->push(Contact::TYPE_CUSTOMER);
        $this->assertCount(1, $contact->type);
        $this->assertContains(Contact::TYPE_CUSTOMER, $contact->type);

        $contact->type->push(Contact::TYPE_SUPPLIER);
        $this->assertCount(2, $contact->type);
        $this->assertContains(Contact::TYPE_SUPPLIER, $contact->type);

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

        $customer = new Customer;
        $customer->name = $this->faker('name');
        $customer->save();
        $this->assertNotNull($customer->id);

        $supplier = new Supplier;
        $supplier->name = $this->faker('name');
        $supplier->save();
        $this->assertNotNull($supplier->id);

        $contact = new Contact;
        $contact->name = $this->faker->name;
        $contact->type[] = Contact::TYPE_CUSTOMER;
        $contact->type[] = Contact::TYPE_SUPPLIER;

        $this->assertCount(2, $contact->type);
        $this->assertInstanceOf(BaseCollection::class, $contact->type);
    }

    public function testGet()
    {
        $contact = new Contact([
            'name' => 'Prueba'
        ]);
        $contact->save();
        $createdContact = Contact::get($contact->id);
        $this->assertSame('Prueba', $createdContact->name);

        $contact = new Customer([
            'name' => 'Customer'
        ]);
        $contact->save();
        $createdContact = Customer::get($contact->id);
        $this->assertSame('Customer', $createdContact->name);

        $contact = new Supplier([
            'name' => 'Supplier'
        ]);
        $contact->save();
        $createdContact = Supplier::get($contact->id);
        $this->assertSame('Supplier', $createdContact->name);
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


        $contacts = Customer::all();
        $this->assertGreaterThanOrEqual(1, count($contacts));
        $contacts->each(function ($contact) {
            $this->assertInstanceOf(Customer::class, $contact);
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
