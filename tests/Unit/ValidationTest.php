<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Auth;
use Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExistsInValidation() {

        \DB::table('auth_types')->insert([
            ['name'=>'local'],
            ['name'=>'oauth']
        ]);

        $validator = Validator::make(['auth_type' => 'local'], ['auth_type' => 'exists_in:auth_types,name']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['auth_type' => 'oauth'], ['auth_type' => 'exists_in:auth_types,name']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['auth_type' => 'ldap'], ['auth_type' => 'exists_in:auth_types,name']);
        $this->assertTrue($validator->fails());

    }

    public function testRequiredPasswordValidation() {

        $validator = Validator::make(['auth_type' => 'local', 'password' => ''], ['password' => 'required_password:auth_type,local']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['auth_type' => 'local', 'password' => 'new pass'], ['password' => 'required_password:auth_type,local']);
        $this->assertTrue($validator->passes());
    }

    public function testRequiredOrEmptyArrayValidation() {

        $validator = Validator::make(['test_field' => []], ['test_field' => 'required_or_empty_array']);
        $this->assertFalse($validator->fails());

        $validator = Validator::make(['name_field' => []], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => 'test'], ['test_field' => 'required_or_empty_array']);
        $this->assertFalse($validator->fails());

        $validator = Validator::make(['test_field' => ['']], ['test_field' => 'required_or_empty_array']);
        $this->assertFalse($validator->fails());

        $validator = Validator::make(['test_field' => ['test']], ['test_field' => 'required_or_empty_array']);
        $this->assertFalse($validator->fails());

        $fake_file = File::create('fakefile.txt', 1);

        $validator = Validator::make(['test_field' => [$fake_file]], ['test_field' => 'required_or_empty_array']);
        $this->assertFalse($validator->fails());

        $validator = Validator::make(['test_field' => $fake_file], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->fails());

    }

    public function testUniqueValidation() {

        \DB::table('auth_types')->insert(['id' => 1, 'name' => 'local']);
        factory(User::class)->create();

        $user = User::firstOrFail();

        Auth::login($user);

        $validator = Validator::make($user->getAttributes(), ['email' => 'required|dynamic_unique:users,email,{id}']);
        $this->assertTrue($validator->passes(), $validator->errors());


    }

}
