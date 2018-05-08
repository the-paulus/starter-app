<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Route;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Standard controller HTTP response codes that are used when associated operations are successful.
     */
    const METHOD_SUCCESS_CODE = [
        'index'     => Response::HTTP_OK,
        'show'      => Response::HTTP_OK,
        'store'     => Response::HTTP_CREATED,
        'update'    => Response::HTTP_SEE_OTHER,
        'destroy'   => Response::HTTP_GONE
    ];

    /**
     * Standard controller HTTP response codes that are used when associated operations have failed.
     */
    const METHOD_FAILURE_CODE = [
        'index'     => Response::HTTP_NO_CONTENT,
        'show'      => Response::HTTP_NOT_FOUND,
        'store'     => Response::HTTP_NOT_ACCEPTABLE,
        'update'    => Response::HTTP_NOT_MODIFIED,
        'destroy'   => Response::HTTP_I_AM_A_TEAPOT
    ];

    /**
     * @var bool Indicates whether or not the controller should create the standard API routes.
     */
    protected static $createApiRoutes = TRUE;

    /**
     * @var bool Indicates whether or not the controller should create the standard web routes.
     */
    protected static $createWebRoutes = TRUE;

    /**
     * Calls Route::apiResource() to create the standard API endpoints for the controller.
     *
     * When creating the routes, the base class name of the model attached to the controller will be used.
     *
     * Example, If the controller has the $model set to User::class, and the Controller's class name is UserController,
     * then this function is identical to calling:
     *
     * Route::apiResource('user', 'User');
     */
    private function standardAPIRoutes() {

        Route::apiResource(strtolower(self::$model::baseModelClassName()), __CLASS__);

    }

    /**
     * This is intended to be overridden in the inherited class to allow the developer to add specialized routes.
     *
     * If the application requires any changes to the standard routes created with
     * Route::apiResource('path', 'Callback'), then the $createApiRoutes should be set to false and all routes manually
     * created in the overridden method.
     */
    public static function customAPIRoutes() {

        // Add code here.

    }

    /**
     * Calls Route::resource() to create the standard URLs for the controller.
     *
     * When creating the routes, the base class name of the model attached to the controller will be used.
     *
     * Example, If the controller has the $model set to User::class, and the Controller's class name is UserController,
     * then this function is identical to calling:
     *
     * Route::resource('user', 'User');
     */
    private function standardWebRoutes() {

        Route::resource(strtolower(self::$model::baseModelClassName()), __CLASS__);

    }

    /**
     * This is intended to be overridden in the inherited class to allow the developer to add specialized routes.
     *
     * If the application requires any changes to the standard routes created with
     * Route::resource('path', 'Callback'), then the $createWebRoutes should be set to false and all routes manually
     * created in the overridden method.
     */
    public static function customWebRoutes() {

        // Add code here.

    }

    /**
     * Calls any methods to create api routes for the controller.
     */
    public static function createApiRoutes() {

        if( self::$createApiRoutes ) {

            self::standardAPIRoutes();

        }

        static::customAPIRoutes();

    }

    /**
     * Calls any methods to create web routes for the controller.
     */
    public static function createWebRoutes() {

        if( self::$createWebRoutes ) {

            self::standardWebRoutes();

        }

        static::customWebRoutes();

    }
}
