<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    const PERMISSION_COUNT = 10;
    const PERMISSION_GROUP_COUNT = 3;

    public function setUp()
    {
        parent::setUp();

        factory(PermissionGroup::class)->times(PermissionTest::PERMISSION_GROUP_COUNT)->create();
        factory(Permission::class)->times(PermissionTest::PERMISSION_COUNT)->create();

    }

    public function testCreation() {

        $this->assertEquals(PermissionTest::PERMISSION_COUNT, count(Permission::all()));
        $this->assertEquals(PermissionTest::PERMISSION_GROUP_COUNT, count(PermissionGroup::all()));

    }

    public function testPermissionInGroup() {

        $permissions = Permission::all();

        foreach($permissions as $permission) {

            $random_group = PermissionGroup::all()->random(1)->first();

            $this->assertNotNull($random_group);
            $permission->groups()->save($random_group);
            $this->assertEquals(1, $permission->groups()->count());

        }
    }

    public function testAddPermssionToMultipleGroups() {

        $random_permission = null;
        $permission_groups = PermissionGroup::all();

        for($i = 0; $i < 2; $i++) {

            $random_permission = Permission::all()->get($i);

            for($j = 0; $j < 2; $j++) {

                $random_permission->groups()->save($permission_groups->get($j));

            }

            $this->assertEquals(2, $random_permission->groups()->count());

        }

        $this->assertEquals(2, PermissionGroup::all()->get(0)->permissions()->count());

    }
}