<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use UsersTableSeeder;
use App\Models\UserGroup;
use UserGroupsTableSeeder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class UserGroupFeatureTest
 *
 * @package Tests\Feature
 */
class UserGroupFeatureTest extends TestCase
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
     * Try to create a user group as an unauthenticated user then create one as an application admin, admin, and normal user.
     *
     * @group application
     * @group usergroup
     * @group authentication
     * @group localauth
     */
  public function testCreateUserGroup() {

    // Try creating a user group without logging in.
    $this->call('post', 'api/usergroup', factory(UserGroup::class)->raw())->assertStatus(Response::HTTP_UNAUTHORIZED);

    // Create a user group with application admin and user admin accounts.
    for($i = 0; $i < 2; $i++) {

        $user = $this->getSeededUser($i);

        $new_group = factory(UserGroup::class)->raw();

        $this->performJWTActionAs($user, 'post', 'api/usergroup', $new_group, Controller::METHOD_SUCCESS_CODE['store']);

    }

    // Verify that a normal user should not be able to create a user group.
    $user = $this->getSeededUser(self::USER);
    $new_group = factory(UserGroup::class)->raw();

    $this->performJWTActionAs($user, 'post', 'api/usergroup', $new_group)
        ->assertStatus(Response::HTTP_FORBIDDEN);

  }

    /**
     * Test creation of user groups with users.
     *
     * @group application
     * @group usergroup
     * @group authentication
     * @group localauth
     */
  public function testCreateUserGroupWithUsers() {

    factory(User::class)->times(10)->create();
    factory(Permission::class)->times(5)->create();

    for($i = 0; $i < 2; $i++) {

        $user = $this->getSeededUser($i);
        $new_group = factory(UserGroup::class)->raw();
        $new_group['permission_ids'] = Permission::all()->where('id', '>', 10)->values('id');
        $new_group['user_ids'] = User::all()->where('id', '>', '3')->values('id');

        $group = $this->performJWTActionAs($user, 'post', 'api/usergroup', $new_group, Controller::METHOD_SUCCESS_CODE['store'])->json()['data'][0];

        foreach($group['user_ids'] as $user_id) {

            $this->assertTrue(User::find($user_id)->memberOf(UserGroup::find($group['id'])));

            foreach($new_group['permission_ids'] as $permission_id) {

                $this->assertTrue(User::find($user_id)->hasPermission($permission_id));

            }

        }

    }

  }

    /**
     * Tests updating of user groups.
     *
     * @group application
     * @group usergroup
     * @group authentication
     * @group localauth
     */
  public function testUpdateUserGroup() {

      $user = $this->getSeededUser(1);
      $group_id = UserGroup::all()->last()->id;
      $updated_group = factory(UserGroup::class)->raw();
      $group = $this->performJWTActionAs($user, 'put', 'api/usergroup/' . $group_id, $updated_group, Controller::METHOD_SUCCESS_CODE['update'])->json()['data'][0];

      foreach($updated_group as $key => $val) {

          $this->assertEquals($updated_group[$key], $group[$key]);

      }

      unset($updated_group['name']);

      $this->performJWTActionAs($user, 'put', 'api/usergroup/' . $group_id, $updated_group, Controller::METHOD_FAILURE_CODE['update']);

  }

    /**
     * Tests deletion of users.
     *
     * @group application
     * @group usergroup
     * @group authentication
     * @group localauth
     */
  public function testDeleteUserGroups() {

      $user = $this->getSeededUser(1);
      $group = $this->performJWTActionAs($user, 'post', 'api/usergroup', factory(UserGroup::class)->raw(), Controller::METHOD_SUCCESS_CODE['store'])->json()['data'][0];

      $this->performJWTActionAs($user, 'delete', 'api/usergroup/' . $group['id'], array(), Controller::METHOD_SUCCESS_CODE['destroy']);

      $this->performJWTActionAs($user, 'delete', 'api/usergroup/' . UserGroup::all()->first()->id, array(), Response::HTTP_BAD_REQUEST);

  }

}
