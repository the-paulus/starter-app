<?php

namespace Tests\Unit;

use DB;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BaseModelTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        DB::table('auth_types')->insert([
            [ 'name' => 'local' ],
            [ 'name' => 'ldap' ]
        ]);
    }

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
            'password' => '',
            'auth_type' => 'local',
        ];

        User::validate($attributes);

    }
}
