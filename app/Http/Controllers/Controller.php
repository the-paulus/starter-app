<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        'destroy'   => Response::HTTP_GONE,
    ];

    /**
     * Standard controller HTTP response codes that are used when associated operations have failed.
     */
    const METHOD_FAILURE_CODE = [
        'index'     => Response::HTTP_NO_CONTENT,
        'show'      => Response::HTTP_NOT_FOUND,
        'store'     => Response::HTTP_NOT_ACCEPTABLE,
        'update'    => Response::HTTP_NOT_MODIFIED,
        'destroy'   => Response::HTTP_NOT_FOUND,
    ];

    protected static $model;

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

    /**
     * @param array $errors
     * @return array
     */
    protected function flattenJSONValidationErrors(array $errors) {

        return Arr::flatten($errors);

    }

    /**
     * Callback function used in array_walk to call model mutators.
     *
     * @param $item     Array   Element being analyzed.
     * @param $key      Array   Element's key.
     * @param $model    Model   Model to perform mutations on.
     */
    protected function callMutator(&$item, $key, $model) {

        $mutations = $model->getMutatedAttributes();

        if( in_array($key, $mutations) ) {

            $func = 'set' . ucfirst(camel_case($key).'Attribute');

            if(method_exists($model, $func)) {

                $model->$func($item);

            }

        }

    }

    /**
     * Returns all models.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        $this->authorize('view', static::$model::all()->first());

        return response()->json(['data' => static::$model::all()], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

    /**
     * Returns a specific model.
     *
     * @param $id   int     ID of the model to return.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $model = static::$model::findOrFail($id);

        $this->authorize('view', $model);

        return response()->json(['data' => [$model]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

    /**
     * Creates a new model based on the submitted data stored within the Request object.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        $model_class = static::$model;

        $this->authorize('create', $model_class);

        try {

            $model = static::$model::validateAndCreate($request->all());
            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $model);

            return response()->json(['data' => [$model->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ValidationException $validationException) {

            return response()->json(['data' => [], 'errors' => $validationException->errors()], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

    /**
     * Updates the model with $id with the provided information stored within the Request object.
     *
     * @param Request   $request    User request containing information that the model should be updated with.
     * @param integer   $id         ID of the model to be updated.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {

        try {

            $model = static::$model::find($id);

            $this->authorize('update', $model);
            $model->validateAndUpdate($request->all());

            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $model);

            return response()->json(['data' => [$model->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ValidationException $validationException) {

            return response()->json(['data' => [], 'errors' => $validationException->errors()], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

    /**
     * Deletes a specific model.
     *
     * @param integer $id   ID of the model to delete from the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        try {

            $model = static::$model::findOrFail($id);

            $this->authorize('delete', $model);

            $model->delete();

            return response()->json(['data' => []], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch( ModelNotFoundException $modelNotFoundException ) {

            return response()->json(['data' => [], 'errors' => ['Model with ' . $id . ' was not found.']], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

}
