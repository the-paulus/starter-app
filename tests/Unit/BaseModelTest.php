<?php

namespace Tests\Unit;

use DB;
use App\Models\User;
use Tests\TestCase;

class BaseModelTest extends TestCase
{

    /**
     * @group model
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

    /**
     * @group model
     */
    public function testBaseModelValidateModel() {

        DB::table('auth_types')->insert([
            ['id' => 1, 'name' => 'local'],
            ['id' => 2, 'name' => 'oauth'],
        ]);

        $attributes = [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.com',
            'password' => 'adminpasssecret',
            'auth_type' => 1,
        ];

        User::validate($attributes);

        $user = User::validateAndCreate($attributes);

        $attributes['email'] = 'ponies@home.com';

        $user->validateAndUpdate($attributes);

    }

    /**
     * @group model
     * 
     * @expectedException Illuminate\Validation\ValidationException
     */
    public function testBaseModelValidateException() {

        $attributes = [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.com',
            'password' => 'adminpasssecret',
            'auth_type' => 1,
        ];

        User::validate($attributes);

    }
}
