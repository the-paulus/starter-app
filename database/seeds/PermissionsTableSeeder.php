<?php

use Illuminate\Database\Seeder;

use App\Models\UserGroup;
use App\Models\Permission;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class PermissionsTableSeeder extends DatabaseSeeder
{
    public static $permissions = [
        [
            'name' => 'create users',
            'description' => 'Create user accounts.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'update users',
            'description' => 'Update other user account information. Users will be able to update their own account information without having this permission.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'delete users',
            'description' => 'Delete user accounts.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'view users',
            'description' => 'View other user information. Users will still be able to view their own account information without having this permission.',
            'groups' => [
                'Application Administrator',
                'Administrator',
                'User'
            ],
        ],
        [
            'name' => 'create user groups',
            'description' => 'Create user groups.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'update user groups',
            'description' => 'Update user group details and group members.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'delete user groups',
            'description' => 'Delete user groups.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'view user groups',
            'description' => 'View user group details.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'create settings',
            'description' => 'Create application settings.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'update settings',
            'description' => 'Modify application settings.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'delete settings',
            'description' => 'Delete application settings.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'view settings',
            'description' => 'View application settings.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'create setting groups',
            'description' => 'Create application setting groups.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'update setting groups',
            'description' => 'Modify application setting groups.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'delete setting groups',
            'description' => 'Delete application setting groups.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'view setting groups',
            'description' => 'View application setting groups.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'create permissions',
            'description' => 'Create new permissions.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'update permissions',
            'description' => 'Update permission details.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'delete permissions',
            'description' => 'Delete permissions.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
        [
            'name' => 'view permissions',
            'description' => 'View permissions.',
            'groups' => [
                'Application Administrator',
                'Administrator',
            ],
        ],
    ];

    // TODO: Is this really needed?
    public static $specific_permissions = [
        [
            'name' => 'modify :usergroup user group',
            'description' => '',
            'groups' => [],
        ],
        [
            'name' => 'access :usergroup user group',
            'description' => '',
            'groups' => [],
        ],
        [
            'name' => 'modify :usergroup user group',
            'description' => '',
            'groups' => [],
        ],
    ];

    public function run()
    {

        Permission::unguard(true);

        foreach(self::$permissions as $permission) {


                try {

                    $groups = $permission['groups'];
                    unset($permission['groups']);

                    $permission = Permission::validateAndCreate($permission);

                    $this->command->info('Created ' . $permission['name']);

                    foreach($groups as $group) {

                        $permission->groups()->attach(UserGroup::where('name', '=', $group)->first());

                        $this->command->info('Added \'' . $permission['name'] . '\' permission to ' . $group);

                    }


                } catch (ValidationException $validationException) {

                    $this->command->error(print_r(implode("\n", Arr::flatten($validationException->errors())), TRUE));
                    $this->command->info('Data: ' . print_r($permission, TRUE));

                }

        }

        Permission::unguard(false);

    }
}
