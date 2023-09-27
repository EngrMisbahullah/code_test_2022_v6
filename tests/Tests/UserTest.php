<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;
use App\Helpers\TeHelper;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateOrUpdateCustomerWithPaidConsumerType()
    {
        // Create a mock user
        $user = factory(User::class)->create();

        $requestData = [
            'role' => 'customer',
            'name' => 'John Doe',
            'company_id' => '',
            'department_id' => '', 
            'email' => 'john@example.com',
            'dob_or_orgid' => '06/01/1998',
            'phone' => '923314657858',
            'mobile' => '9876543210',
            'password' => 'secret', 
            'consumer_type' => 'paid',
        ];

        $response = $this->put("/url/{$user->id}", $requestData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
            'company_id' => $user->company_id,
            'department_id' => $user->department_id,
            'email' => 'john@example.com',
            'dob_or_orgid' => 'DOB',
            'phone' => '1234567890',
            'mobile' => '9876543210',
            'password' => bcrypt('secret'), 
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'John Doe',
            'type_id' => $type->id,
        ]);

        $this->assertDatabaseHas('departments', [
            'name' => 'John Doe',
            'company_id' => $company->id,
        ]);
        $this->assertDatabaseHas('user_meta', [
            'user_id' => $user->id,
            'consumer_type' => 'paid',
            'username' => 'Testing',
            'post_code'=> 27200;
            'address' => 'Address for Testing';
            'city' => 'City';
            'town' => 'towun';
            'country' => 'Country';
            'reference' => '1';
            'additional_info' => 'Information Additional';
        ]);

    }
    
}


