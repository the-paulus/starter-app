<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Auth;
use Validator;
use Tests\TestCase;

class ValidationTest extends TestCase
{

    const APPLICATION_ADMIN = 0;
    const ADMIN_USER = 1;
    const USER = 2;

    protected function setUp()
    {
        parent::setUp();

        $this->seed();
    }

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
     * Tests the required_password validation rule.
     *
     * - No password should fail.
     * - No password with auth_type of local should fail.
     * - No password with auth_type of ldap should pass.
     * - Password with auth_type of local should pass.
     *
     * @group password
     * @group validation
     */
    public function testRequiredPasswordValidation() {

        $validator = Validator::make(['password' => ''], ['password' => 'required_password']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['auth_type' => 'local', 'password' => ''], ['password' => 'required_password']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['auth_type' => 'shibboleth', 'password' => ''], ['password' => 'required_password']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['auth_type' => 'local', 'password' => 'new pass'], ['password' => 'required_password']);
        $this->assertTrue($validator->passes());

        $user = $this->getSeededUser(self::USER);
        $user_attributes = $user->getAttributes();
        $auth_type_id = $user_attributes['auth_type'];

        Auth::login($user);

        $user_attributes['auth_type'] = $auth_type_id;
        $user_attributes['password'] = 'password';
        // auth_type = 1 (local)
        // password is set
        // Should pass
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type set to local (' . $auth_type_id . ') with password should pass.');

        $user_attributes['auth_type'] = $auth_type_id;
        $user_attributes['password'] = '';
        // auth_type = 1 (local)
        // password = ''
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to local ('. $auth_type_id . ') with no password should fail.');

        $user_attributes['auth_type'] = $auth_type_id;
        unset($user_attributes['password']);
        // auth_type = 1 (local)
        // password is not set
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to local ('. $auth_type_id . ') with no password field should fail.');

        $user_attributes['auth_type'] = 'local';
        $user_attributes['password'] = 12348988;
        // auth_type = 1 (local)
        // password is set 12348988 (int)
        // should fail.
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type set to local (' . $auth_type_id . ') and password is an integer should fail.');

        /*
         * auth_type is a string
         */
        $user_attributes['auth_type'] = 'local';
        $user_attributes['password'] = 'password';
        // auth_type = 'local'
        // password is set
        // Should pass
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type set to local with password should pass.');

        $user_attributes['auth_type'] = 'local';
        $user_attributes['password'] = '';
        // auth_type = local
        // password = ''
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to local with no password should fail.');

        $user_attributes['auth_type'] = $auth_type_id;
        unset($user_attributes['password']);
        // auth_type = local
        // password is not set
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to local with no password field should fail.');

        $user_attributes['auth_type'] = 'local';
        $user_attributes['password'] = 12348988;
        // auth_type = local
        // password is set 12348988 (int)
        // should fail.
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type set to local and password is an integer should fail.');

        /*
         * auth_type is an invalid integer.
         */
        $user_attributes['auth_type'] = -2;
        $user_attributes['password'] = 'password';
        // auth_type = -2
        // password is set
        // Should pass
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to -2 with password should fail.');

        $user_attributes['auth_type'] = -2;
        $user_attributes['password'] = '';
        // auth_type = -2
        // password = ''
        // should pass
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to -2 with no password should fail.');

        $user_attributes['auth_type'] = -2;
        unset($user_attributes['password']);
        // auth_type = -2
        // password is not set
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to -2 with no password field should fail.');

        $user_attributes['auth_type'] = -2;
        $user_attributes['password'] = 12348988;
        // auth_type = -2
        // password is set 12348988 (int)
        // should fail.
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type set to -2 and password is an integer should fail.');

        /*
         * auth_type is an invalid string
         */
        $user_attributes['auth_type'] = 'trollolololol';
        $user_attributes['password'] = 'password';
        // auth_type = 'trollolololol'
        // password is set
        // Should pass
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to \'trollolololol\' with password should pass.');

        $user_attributes['auth_type'] = 'trollolololol';
        $user_attributes['password'] = '';
        // auth_type = 'trollolololol'
        // password = ''
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to \'trollolololol\' with no password should fail.');

        $user_attributes['auth_type'] = 'trollolololol';
        unset($user_attributes['password']);
        // auth_type = 'trollolololol'
        // password is not set
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type set to \'trollolololol\' with no password field should fail.');

        $user_attributes['auth_type'] = 'trollolololol';
        $user_attributes['password'] = 12348988;
        // auth_type = 'trollolololol'
        // password is set 12348988 (int)
        // should fail.
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type set to \'trollolololol\' and password is an integer should fail.');

        /*
         * auth_type is unset
         */
        unset($user_attributes['auth_type']);
        $user_attributes['password'] = 'password';
        // auth_type is unset
        // password is set
        // Should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type is not set with password should pass.');

        $user_attributes['password'] = '';
        // auth_type is unset
        // password = ''
        // should pass
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type is not set with no password should fail.');

        unset($user_attributes['password']);
        // auth_type is unset
        // password is not set
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type is not with no password field should fail.');

        $user_attributes['password'] = 12348988;
        // auth_type is unset
        // password is set 12348988 (int)
        // should fail.
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type is not set and password is an integer should fail.');

        /*
          * auth_type is ''
          */
        $user_attributes['auth_type'] = '';
        $user_attributes['password'] = 'password';
        // auth_type is empty
        // password is set
        // Should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->fails(), 'auth_type is empty with password should pass.');

        $user_attributes['password'] = '';
        // auth_type is empty
        // password = ''
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type is empty with no password should fail.');

        unset($user_attributes['password']);
        // auth_type is empty
        // password is not set
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type is empty with no password field should fail.');

        $user_attributes['password'] = 12348988;
        // auth_type is empty
        // password is set 12348988 (int)
        // should fail.
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type is empty and password is an integer should fail.');

        /*
         * Auth type set so something other than local
         */
        $user_attributes['auth_type'] = 'ldap';
        $user_attributes['password'] = 'password';
        // auth_type = ldap
        // password is set
        // Should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type set to ldap with a password should fail.');

        $user_attributes['auth_type'] = 'shibboleth';
        $user_attributes['password'] = '';
        // auth_type = shibboleth
        // password is set
        // Should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->passes(), 'auth_type set to ldap with a password should fail.');

        $user_attributes['auth_type'] = 'shibboleth';
        $user_attributes['password'] = 'password';
        // auth_type = shibboleth
        // password is set
        // Should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type set to ldap with a password should fail.');

        $user_attributes['password'] = '';
        // auth_type = shibboleth
        // password = ''
        // should pass
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type is set to ldap with no password should pass.');

        unset($user_attributes['password']);
        // auth_type = shibboleth
        // password is not set
        // should fail
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']);
        $this->assertTrue($validator->passes(), 'auth_type is set to ldap with no password field should pass.');

        $user_attributes['password'] = 12348988;
        // auth_type is empty
        // password is set 12348988 (int)
        // should fail.
        $validator = Validator::make(
            $user_attributes,
            ['password' => 'required_password']
        );
        $this->assertTrue($validator->fails(), 'auth_type is set to ldap and password is an integer should fail.');

    }

    /**
     * Tests exists_in validation rule.
     *
     * - auth_type local should pass.
     * - auth_type oauth should pass.
     * - auth_type ldap should fail.
     *
     * @group validation
     * @group user_validation
     */
    public function testExistsInValidation() {

        $validator = Validator::make(['auth_type' => 'local'], ['auth_type' => 'exists_in:auth_types,name']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['auth_type' => 'oauth'], ['auth_type' => 'exists_in:auth_types,name']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['auth_type' => 'ldap'], ['auth_type' => 'exists_in:auth_types,name']);
        $this->assertTrue($validator->fails());

    }

    /**
     * Tests unique_email validation rule.
     *
     * - User being validating with the same email should pass.
     * - Different User with an email already in the database should fail.
     *
     * @group validation
     * @group user_validation
     */
    public function testUniqueEmailValidation() {

        $user = $this->getSeededUser(self::USER);
        $user_attributes = $user->getAttributes();

        Auth::login($user);

        $validator = Validator::make($user_attributes, ['email' => 'required|unique_email']);
        $this->assertTrue($validator->passes(), $validator->errors());

        $user_attributes['email'] = $this->getSeededUser(self::APPLICATION_ADMIN)->email;

        $validator = Validator::make($user_attributes, ['email' => 'required|unique_email']);

        $this->assertTrue($validator->fails(), $validator->errors());

    }

    /**
     * Tests required_with_permission validation rule.
     *
     * - Field is required if user has a specified permission and is empty should fail.
     * - Field is required if a user has a specified permission and is not empty should pass.
     * - Field is not required if a user doesn't have the specified permission and is empty should pass
     * - Field is not required if a user doesn't have the specified permission and is not empty should pass.
     *
     * @group permissions
     * @group validation
     * @group user_validation
     */
    public function testRequiredWithPermissionValidation() {

        $user = $this->getSeededUser(self::APPLICATION_ADMIN);

        Auth::login($user);

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_with_permission:create users']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_with_permission:create users']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_with_permission:non-existent permission']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_with_permission:non-existent permission']);
        $this->assertTrue($validator->passes());

    }

    /**
     * Tests required_without_permission validation rule.
     *
     * - Field is required if user does not have the specified permission and is empty should fail.
     * - Field is required if a user does not have the specified permission and is not empty should pass.
     * - Field is not required if a user has the specified permission and is empty should pass
     * - Field is not required if a user has the specified permission and is not empty should pass.
     *
     * @group permissions
     * @group validation
     * @group user_validation
     */
    public function testRequiredWithoutPermissionValidation() {

        $user = $this->getSeededUser(self::APPLICATION_ADMIN);

        Auth::login($user);

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_without_permission:non-existent permission']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_without_permission:non-existent permission']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_without_permission:create users']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_with_permission:create users']);
        $this->assertTrue($validator->passes());

    }

    /**
     * Tests required_with_membership validation rule.
     *
     * - Field is required if user is in a specified group and is empty should fail.
     * - Field is required if a user is in a specified group and is not empty should pass.
     * - Field is not required if a user isn't in the specified group and is empty should pass
     * - Field is not required if a user isn't in the specified group and is not empty should pass.
     *
     * @group groups
     * @group validation
     * @group user_validation
     */
    public function testRequiredWithMembershipValidation() {

        $user = $this->getSeededUser(self::APPLICATION_ADMIN);

        Auth::login($user);

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_with_membership:Application Administrator']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_with_membership:Administrator']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_with_membership:user']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_with_permission:user']);
        $this->assertTrue($validator->passes());

    }

    /**
     * Tests required_without_membership validation rule.
     *
     * - Field is required if user is not in a specified group and is empty should fail.
     * - Field is required if a user is not in a specified group and is not empty should pass.
     * - Field is not required if a user is in the specified group and is empty should pass
     * - Field is not required if a user is in the specified group and is not empty should pass.
     *
     * @group groups
     * @group validation
     * @group user_validation
     */
    public function testRequiredWithoutMembershipValidation() {

        $user = $this->getSeededUser(self::APPLICATION_ADMIN);

        Auth::login($user);

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_without_membership:Application Administrator']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_without_membership:user']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => ''], ['test_field' => 'required_without_membership:administrator']);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['test_field' => 'not empty'], ['test_field' => 'required_without_permission:administrator']);
        $this->assertTrue($validator->passes());

    }

    public function testHasPermissionValidation() {

        $user = $this->getSeededUser(self::APPLICATION_ADMIN);

        Auth::login($user);

        $validator = Validator::make(['test_field' => 'test'], ['test_field' => 'has_permission:create users']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make(['test_field' => 'test'], ['test_field' => 'has_permission:no permission']);

        $this->assertTrue($validator->fails());

    }

}
