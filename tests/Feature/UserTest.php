<?php

namespace Tests\Feature;

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

    public function setUp()
    {
        parent::setUp();

        factory(UserGroup::class)->times(UserTest::USER_GROUP_COUNT)->create();
        factory(User::class)->times(UserTest::USER_COUNT)->create();

    }

    public function testCreation() {

        $this->assertEquals(UserTest::USER_COUNT, count(User::all()));
        $this->assertEquals(UserTest::USER_GROUP_COUNT, count(UserGroup::all()));

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

    public function testAddUserToMultipleGroups() {

        $random_user = null;
        $user_groups = UserGroup::all();

        for($i = 0; $i < 2; $i++) {

            $random_user = User::all()->get($i);

            for($j = 0; $j < 2; $j++) {

                $random_user->groups()->save($user_groups->get($j));

            }

            $this->assertEquals(2, $random_user->groups()->count());

        }

        $this->assertEquals(2, UserGroup::all()->get(0)->users()->count());

    }
}
