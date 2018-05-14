<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use DB;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserTest extends TestCase
{

    const USER_COUNT = 10;
    const USER_GROUP_COUNT = 3;
    const PERMISSION_COUNT = 10;

    public function setUp()
    {
        parent::setUp();

        DB::table('auth_types')->insert([
            ['id' => 1, 'name' => 'local'],
            ['id' => 2, 'name' => 'oauth'],
        ]);

        factory(UserGroup::class)->times(UserTest::USER_GROUP_COUNT)->create();
        factory(Permission::class)->times(UserTest::PERMISSION_COUNT)->create();
        factory(User::class)->times(UserTest::USER_COUNT)->create();

    }

    /**
     * @group users
     * @group database
     * @group authentication
     */
    public function testAuthTypes() {

        $this->assertDatabaseHas('auth_types', ['name' => ['local', 'oauth']]);

    }

    /**
     * @group users
     * @group usergroups
     * @group permissions
     */
    public function testUserUserGroupCreation() {

        $this->assertEquals(UserTest::USER_COUNT, count(User::all()));
        $this->assertEquals(UserTest::USER_GROUP_COUNT, count(UserGroup::all()));
        $this->assertEquals(UserTest::PERMISSION_COUNT, count(Permission::all()));

    }

    /**
     * @group users
     * @group usergroups
     */
    public function testUserInGroup() {

        $users = User::all();

        foreach($users as $user) {

            $random_group = UserGroup::all()->random(1)->first();

            $this->assertNotNull($random_group);
            $user->groups()->save($random_group);
            $this->assertEquals(1, $user->groups()->count());

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

            $this->assertNotNull($random_group);
            $permission->groups()->save($random_group);
            $this->assertEquals(1, $permission->groups()->count());

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

        $this->assertEquals(UserTest::USER_GROUP_COUNT, $user->groups()->count());

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

    /**
     * @group users
     * @group api
     * @group controller
     * @group authentication
     */
    public function testUserControllerShowAllAPI() {

        $this->json('GET','api/user')->assertStatus(Response::HTTP_UNAUTHORIZED);

        $user = User::firstOrFail();

        $response = $this->actingAs($user, 'api')->json('GET', 'api/user');

        $response->assertStatus(UserController::METHOD_SUCCESS_CODE['index']);
        $response->assertJsonCount(UserTest::USER_COUNT, 'data');

        // TODO: Test 'access users' permission.

    }

    /**
     * @group users
     * @group api
     * @group controller
     * @group authentication
     */
    public function testUserControllerShowUserAPI() {

        $user = User::firstOrFail();

        $response = $this->actingAs($user, 'api')->json('GET', 'api/user/' . User::all()->random()->id);

        $response->assertStatus(UserController::METHOD_SUCCESS_CODE['show']);
        $response->assertJsonCount(1);

        // Test for requesting a non-existent user.
        $response = $this->actingAs($user, 'api')->json('GET', 'api/user/1000');

        $response->assertStatus(UserController::METHOD_FAILURE_CODE['show']);
        $response->assertJsonCount(0);

        // TODO: Test 'access users' permission.
    }

    /**
     * @group users
     * @group api
     * @group controller
     * @group authentication
     */
    public function testUserControllerCreateUserAPI() {

        $user = User::firstOrFail();

        $last_id = User::all()->last()->id+1;
        $response = $this->actingAs($user, 'api')->post('api/user', factory(User::class)->raw());
        $new_user_id = $response->json()['data'][0]['id'];

        $response->assertStatus(UserController::METHOD_SUCCESS_CODE['store']);
        $response->assertJsonCount(1,'data');
        $this->assertEquals($last_id, $new_user_id);

        $new_user_arr = factory(User::class)->raw();
        $new_user_arr['user_group_ids'][] = UserGroup::all()->random()->id;
        $new_user_arr['user_group_ids'][] = UserGroup::all()->random()->id;
        $response = $this->actingAs($user, 'api')->post('api/user', $new_user_arr);
        $new_user_id = $response->json()['data'][0]['id'];

        $response->assertStatus(UserController::METHOD_SUCCESS_CODE['store']);

        $new_user = User::findOrFail($new_user_id);

        foreach($new_user_arr['user_group_ids'] as $gid) {

            $this->assertTrue($new_user->memberOf(UserGroup::findOrFail($gid)));

        }

        $response = $this->actingAs($user, 'api')->post('api/user', []);

        $response->assertStatus(UserController::METHOD_FAILURE_CODE['store']);

    }

    /**
     * @group users
     * @group api
     * @group controller
     * @group authentication
     */
    public function testUserControllerUpdateUserAPI() {

        $user = User::firstOrFail();

        $user->first_name = 'test';
        $user->last_name = 'test';

        $response = $this->actingAs($user, 'api')->put('api/user/' . $user->id, ['first_name' => 'test', 'last_name' => 'test']);
        $response->assertStatus(UserController::METHOD_SUCCESS_CODE['update']);

        $updated_user = User::findOrFail($user->id)->first()->getFillable();

        foreach($updated_user as $key => $val) {

            $this->assertEquals($user->getAttribute($key), $val, $key . ' attribute was not updated.');

        }

        // Try updating a non-existent user.
        $response = $this->actingAs($user, 'api')->put('api/user/' . User::all()->last()->id+1, $user->getAttributes());
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        // Update user with invalid attribute values
        $user->email = 'me';
        $response = $this->actingAs($user, 'api')->put('api/user/' . $user->id, $user->getAttributes());
        $response->assertStatus(UserController::METHOD_FAILURE_CODE['update']);

    }
    

}
