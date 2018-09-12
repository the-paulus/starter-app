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
     * Tests the 'required_or_empty_array validation rule.
     *
     * - Empty array should pass.
     * - Field not present should fail.
     * - Field present but is not an empty array should fail.
     * - Field present with value and not an empty array should pass.
     * - Field present as an array with a single element that is an empty string should pass.
     * - Field present as an array with a single string element should pass.
     * - Field present as an array of files should pass.
     * - Field present as a file should fail.
     *
     * @group file
     * @group validation
     */
    public function testRequiredOrEmptyArrayValidation() {

        $validator = Validator::make(['test_field' => []], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['name_field' => []], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => 'test'], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => ['']], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => ['test']], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->passes());

        $fake_file = File::create('fakefile.txt', 1);

        $validator = Validator::make(['test_field' => [$fake_file]], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => $fake_file], ['test_field' => 'required_or_empty_array']);
        $this->assertTrue($validator->fails());

    }

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

    public function testUniqueValidation() {

        \DB::table('auth_types')->insert(['id' => 1, 'name' => 'local']);
        factory(User::class)->create();

        $user = User::firstOrFail();

        Auth::login($user);

        $validator = Validator::make($user->getAttributes(), ['email' => 'required|dynamic_unique:users,email,{id}']);
        $this->assertTrue($validator->passes(), $validator->errors());


    }

}
