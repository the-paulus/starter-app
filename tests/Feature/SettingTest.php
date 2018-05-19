<?php

namespace Tests\Feature;

use DB;
use SettingsTableSeeder;
use App\Models\Setting;
use App\Models\SettingGroup;
use Tests\TestCase;

class SettingTest extends TestCase
{
    const SETTING_COUNT = 10;
    const SETTING_GROUP_COUNT = 3;

    public function setUp()
    {
        parent::setUp();

        foreach(SettingsTableSeeder::$setting_types as $id => $type) {

            DB::table('setting_types')->insert(['id' => ($id+1), 'name' => $type]);

        }

        factory(SettingGroup::class)->times(SettingTest::SETTING_GROUP_COUNT)->create();
        factory(Setting::class)->times(SettingTest::SETTING_COUNT)->create();

    }

    public function testSettingAndSettingGroupCreation() {

        $this->assertEquals(SettingTest::SETTING_COUNT, count(Setting::all()));
        $this->assertEquals(SettingTest::SETTING_GROUP_COUNT, count(SettingGroup::all()));

    }

    public function testSettingInGroup() {

        $settings = Setting::all();

        foreach($settings as $setting) {

            $random_group = SettingGroup::all()->random(1)->first();

            $this->assertNotNull($random_group);
            $setting->group()->associate($random_group);
            $this->assertEquals($random_group->id, $setting->group()->get(['id'])->first()->id);

        }
    }

    public function testSettingWeight() {

        $settings = Setting::all();
        $setting_group = SettingGroup::all()->first();

        foreach($settings as $setting) {

            $setting->group()->associate($setting_group)->save();

        }

        $this->assertEquals(SettingTest::SETTING_COUNT, $setting_group->settings()->get()->count());
    }

}
