<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use UsersTableSeeder;
use UserGroupsTableSeeder;
use Tests\TestCase;

/**
 * Class UserFeatureTest
 *
 * @package Tests\Feature
 */
class UserFeatureTest extends TestCase
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
     * Test to ensure that valid users can log into the system using credentials managed by the application.
     *
     * @group application
     * @group user
     * @group authentication
     * @group localauth
     */
    public function testUserLogin() {

        $this->call('post', 'api/login', UsersTableSeeder::$users[0])->assertStatus(Response::HTTP_OK);

        foreach(UsersTableSeeder::$users as $user) {

            $this->call('post', 'api/login', $user)->assertStatus(Response::HTTP_OK)->json();

        }

        $invalidUser = UsersTableSeeder::$users[2];
        $invalidUser['password'] = 'ponies';

        $this->call('post', 'api/login', $invalidUser)->assertStatus(Response::HTTP_UNAUTHORIZED);

    }

    /**
     * Tests to ensure valid users are able to get a token.
     *
     * @group application
     * @group user
     * @group authentication
     * @group altauth
     */
    public function testUserLoginToken() {

        foreach(UsersTableSeeder::$users as $user) {

            $this->call('post', 'api/login', $user)->assertStatus(Response::HTTP_OK)->assertJsonCount(1);

        }

    }


    /**
     * Try to create a user as an unauthenticated user then create one as an application admin, admin, and normal user.
     *
     * @group application
     * @group user
     * @group authentication
     * @group localauth
     */
    public function testCreateUser() {

        $this->call('post', 'api/user', factory(User::class)->raw())->assertStatus(Response::HTTP_UNAUTHORIZED);

        for($i = 0; $i < 2; $i++) {

            $user = $this->getSeededUser($i);
            $new_user = factory(User::class)->raw();
            $new_user['user_group_ids'][] = UserGroup::all()->firstWhere('name', '=', 'User')->id;
            $data = $this->performJWTActionAs($user, 'post', 'api/user', $new_user)
                ->assertStatus(Controller::METHOD_SUCCESS_CODE['store'])
                ->json()['data'];

            foreach($new_user as $key => $value) {

                if( !in_array($key, $user->getHidden()) ) {

                    if($key == 'user_group_ids') {

                        $this->assertEquals($value[0], $data[0][$key][0]);

                    } else {

                        $this->assertArrayHasKey($key, $data[0]);
                        $this->assertEquals($value, $data[0][$key]);

                    }

                }

            }

        }

        // Test attempts made by unprivileged user to create a user.
        $user = $this->getSeededUser(self::USER);
        $new_user = factory(User::class)->raw();
        $new_user['user_group_ids'][] = UserGroup::all()->firstWhere('name', '=', 'User')->id;

        $this->performJWTActionAs($user, 'post', 'api/user', $new_user)->assertStatus(Response::HTTP_FORBIDDEN);


        // Test data violations
        $user = $this->getSeededUser(self::ADMIN_USER);

        unset($new_user['password']);
        unset($new_user['email']);

        $this->performJWTActionAs($user, 'post', 'api/user', $new_user)
            ->assertStatus(Controller::METHOD_FAILURE_CODE['store']);

    }

    /**
     * Tests to ensure that users can access information about other users.
     *
     * @group application
     * @group user
     * @group authentication
     * @group localauth
     */
    public function testShowUser() {

        $this->call('get','api/user/1')->assertStatus(Response::HTTP_UNAUTHORIZED);

        $user = $this->getSeededUser(self::ADMIN_USER);

        $this->performJWTActionAs($user, 'get', 'api/user/1')
            ->assertStatus(Controller::METHOD_SUCCESS_CODE['show'])
            ->assertJsonCount(1, 'data');

    }

    /**
     * Tests to ensure that users can access user listing.
     *
     * @group application
     * @group user
     * @group authentication
     * @group localauth
     */
    public function testGetAll() {

        $this->call('GET', 'api/user')->assertStatus(Response::HTTP_UNAUTHORIZED);

        for($i = 0; $i < 2; $i++) {

            $user = $this->getSeededUser($i);
            $data = $this->performJWTActionAs($user, 'get', 'api/user')
                ->assertStatus(Controller::METHOD_SUCCESS_CODE['index'])
                ->json()['data'];

            $this->assertCount(count(UsersTableSeeder::$users), $data);
        }

    }

    /**
     * Tests to ensure only users with the 'delete user' permission can delete users.
     *
     * @group application
     * @group user
     * @group authentication
     * @group localauth
     */
    public function testDestroyUser() {

        $this->call('DELETE', 'api/user/2')->assertStatus(Response::HTTP_UNAUTHORIZED);

        // Tests deleting a user.
        $user = $this->getSeededUser(self::ADMIN_USER);
        $del_user_id = $this->getSeededUser(self::USER)->id;

        $this->performJWTActionAs($user, 'delete', 'api/user/' . $del_user_id)
            ->assertStatus(Controller::METHOD_SUCCESS_CODE['destroy']);

        $this->performJWTActionAs($user, 'get', 'api/user/' . $del_user_id, array(), Controller::METHOD_FAILURE_CODE['show']);

        $id = User::all()->last()->id;
        $id++;

        $this->performJWTActionAs($user, 'delete', 'api/user/' . $id, array(), Controller::METHOD_FAILURE_CODE['destroy']);
    }

    /**
     * Tests to ensure that users cannot delete other users.
     *
     * @group application
     * @group user
     * @group authentication
     * @group localauth
     */
    public function testUserDestroyUser() {

        $user = $this->getSeededUser(self::USER);
        $del_user = $this->getSeededUser(self::ADMIN_USER);

        $this->performJWTActionAs($user, 'delete', 'api/user/' . $del_user->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Tests to ensure that the correct users can delete users.
     *
     * @group application
     * @group user
     * @group authentication
     * @group localauth
     */
    public function testCannotDestroySelf() {

        // Tests a user is unable to delete them self.
        $user = $this->getSeededUser(self::ADMIN_USER);
        $del_user = $this->getSeededUser(self::ADMIN_USER);

        $this->performJWTActionAs($user, 'delete', 'api/user/' . $del_user->id)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->performJWTActionAs($user, 'get', 'api/user/' . $del_user->id)
            ->assertStatus(Controller::METHOD_SUCCESS_CODE['show'])
            ->assertJsonCount(1, 'data');

    }
}
