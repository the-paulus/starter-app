<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserGroup;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use DatabaseMigrations, RefreshDatabase;

    const USER_COUNT = 10;
    const USER_GROUP_COUNT = 3;
    const PERMISSION_COUNT = 10;

    public function setUp()
    {
        parent::setUp();

        factory(UserGroup::class)->times(UserTest::USER_GROUP_COUNT)->create();
        factory(Permission::class)->times(UserTest::PERMISSION_COUNT)->create();
        factory(User::class)->times(UserTest::USER_COUNT)->create();

    }

    public function testCreation() {

        $this->assertEquals(UserTest::USER_COUNT, count(User::all()));
        $this->assertEquals(UserTest::USER_GROUP_COUNT, count(UserGroup::all()));
        $this->assertEquals(UserTest::PERMISSION_COUNT, count(Permission::all()));

    }

    public function testUserInGroup() {

        $users = User::all();

        foreach($users as $user) {

            $random_group = UserGroup::all()->random(1)->first();

            $this->assertNotNull($random_group);
            $user->groups()->save($random_group);
            $this->assertEquals(1, $user->groups()->count());

        }
    }

    public function testPermissionInGroup() {

        $permissions = Permission::all();

        foreach($permissions as $permission) {

            $random_group = UserGroup::all()->random(1)->first();

            $this->assertNotNull($random_group);
            $permission->groups()->save($random_group);
            $this->assertEquals(1, $permission->groups()->count());

        }
    }

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

        $this->assertEquals(UserTest::USER_GROUP_COUNT, $user->groups()->count());

    }

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

}
