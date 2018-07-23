<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * @var string $model Model class that the controller is bound to.
     */
    protected static $model = Permission::class;

}
