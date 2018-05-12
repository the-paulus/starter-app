<?php

namespace Tests\Unit;

use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseModelTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBaseModel()
    {
        $model_permissions = [
            'manage',
            'create',
            'modify',
            'delete',
            'access'
        ];

        $this->assertEquals('User', User::baseModelClassName());

        foreach ($model_permissions as $permission) {

            $this->assertTrue(in_array($permission . ' users', User::getModelPermissions()));

        }
    }

    public function testBaseModelValidateModel() {

        $attributes = [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.com',
            'password' => 'adminpasssecret',
            'auth_type' => 'local',
        ];

        User::validate($attributes);

        $user = User::validateAndCreate($attributes);

        $attributes['email'] = 'ponies@home.com';

        try {
            $user->validateAndUpdate($attributes);
        } catch(ValidationException $validationException) {

            print_r(implode("\n", Arr::flatten($validationException->errors())));

        }
        //$user->validateAndUpdate(['first_name' => 'test']);

    }
}
