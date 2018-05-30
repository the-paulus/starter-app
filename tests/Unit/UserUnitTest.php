<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserGroup;
use Tests\TestCase;

/**
 * Class UserUnitTest contains tests targeting only the backend logic code. These tests do not take access controls
 * into account.
 *
 * @package Tests\Unit
 */
class UserUnitTest extends TestCase
{

    use RefreshDatabase;

    const USER_COUNT = 10;
    const USER_GROUP_COUNT = 3;
    const PERMISSION_COUNT = 10;

    public function setUp()
    {
        parent::setUp();

        DB::table('auth_types')->insert([
            [ 'name' => 'local' ],
            [ 'name' => 'ldap' ]
        ]);

        factory(UserGroup::class)->times(UserUnitTest::USER_GROUP_COUNT)->create();
        factory(Permission::class)->times(UserUnitTest::PERMISSION_COUNT)->create();
        factory(User::class)->times(UserUnitTest::USER_COUNT)->create();

    }

    /**
     * @group users
     * @group database
     * @group authentication
     */
    public function testAuthTypes() {

        $this->assertDatabaseHas('auth_types', ['name' => 'local']);
        $this->assertDatabaseHas('auth_types', ['name' => 'ldap']);

    }

    /**
     * @group users
     * @group usergroups
     * @group permissions
     */
    public function testUserUserGroupCreation() {

        $this->assertEquals(UserUnitTest::USER_COUNT, User::all()->count());
        $this->assertEquals(UserUnitTest::USER_GROUP_COUNT, UserGroup::all()->count());
        $this->assertEquals(UserUnitTest::PERMISSION_COUNT, Permission::all()->count());

    }

    /**
     * @group users
     * @group usergroups
     */
    public function testUserInGroup() {

        $users = User::all();

        foreach($users as $user) {

            $random_group = UserGroup::all()->random(1)->first();
            $expected = $user->groups()->count() + 1;
            $this->assertNotNull($random_group);
            $user->groups()->save($random_group);
            $this->assertEquals($expected, $user->groups()->count());

        }
    }

    /**
     * @group permissions
     * @group permissiongroups
     */
    public function testPermissionInGroup() {

        $permissions = Permission::all();

        foreach($permissions as $permission) {

            $random_group = UserGroup::all()->random(1)->first();
            $expected = $permission->groups()->count() + 1;
            $this->assertNotNull($random_group);
            $permission->groups()->save($random_group);
            $this->assertEquals($expected, $permission->groups()->count());

        }
    }

    /**
     * @group users
     * @group usergroups
     */
    public function testAddUserToMultipleGroups() {

        $user = User::all()->first();
        $user_groups = UserGroup::all()->whereNotIn('id', $user->groups()->get()->keyBy('id')->keys());

        foreach($user_groups as $user_group) {

            $user->groups()->save($user_group);

        }

        // Retrieve the 'random' user again to ensure that they were added to the group correctly.
        $user = User::all()->first();

        foreach($user_groups as $user_group) {

            $this->assertTrue($user->memberOf($user_group), 'User is not a member of the specified UserGroup object.');
            $this->assertTrue($user->memberOf($user_group->id), 'User is not a member of the specified UserGroup id.');
            $this->assertTrue($user->memberOf($user_group->name), 'User is not a member of the specified UserGroup name.');

            $this->assertTrue($user_group->hasMember($user), 'UserGroup does not have the specified User object.');
            $this->assertTrue($user_group->hasMember($user->id), 'UserGroup does not have the specified User ID.');

        }

        $this->assertEquals(UserUnitTest::USER_GROUP_COUNT, $user->groups()->count());

    }

    /**
     * @group users
     * @group usergroups
     * @group permissions
     */
    public function testUserPermissions() {

        $permissions = Permission::all()->take(3);
        $user_group = UserGroup::all()->first();
        $user = User::all()->first();

        foreach($permissions as $permission) {

            $user_group->permissions()->save($permission);

        }

        $user->groups()->save($user_group);

        // Retrieve the UserGroup again to ensure that the permissions were added correctly
        $user_group = UserGroup::all()->firstWhere('id', '=', $user_group->id);

        foreach($permissions as $permission) {

            $this->assertEquals($permission->id, $user_group->permissions()->get()->firstWhere('id', '=', $permission->id)->id, 'Permission not found in group.');

            $this->assertTrue($user->hasPermission($permission), 'User does not have the provided permission object associated with them.');
            $this->assertTrue($user->hasPermission($permission->id), 'User does not have the provided permission id associated with them.');
            $this->assertTrue($user->hasPermission($permission->name), 'User does not have the provided permission name associated with them.');

            $this->assertTrue($user->memberOf($user_group), 'User is not a member of specified group object.');
            $this->assertTrue($user->memberOf($user_group->id), 'User is not a member of specified group id.');
            $this->assertTrue($user->memberOf($user_group->name), 'User is not a member of specified group name.');

        }

    }

    /**
     * @group users
     * @group api
     * @group controller
     * @group authentication
     */
    public function testUserAuthenticatables() {

        $user = User::firstOrFail();

        $this->assertEquals('id', $user->getAuthIdentifierName());
        $this->assertEquals($user->password, $user->getAuthPassword());
        $this->assertEquals($user->remember_token, $user->getRememberToken());
        $this->assertEquals('remember_token', $user->getRememberTokenName());

        $user->setRememberToken('forgetme');

        $this->assertEquals('forgetme', $user->getRememberToken());

    }

}
