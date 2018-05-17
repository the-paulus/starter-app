<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class UserGroupController
 *
 * @package App\Http\Controllers
 */
class UserGroupController extends Controller
{
    /**
     * @var string $model Model class that the controller is bound to.
     */
    protected static $model = UserGroup::class;

    /**
     * Callback function used in array_walk to call model mutators.
     *
     * @param $item     Array   Element being analyzed.
     * @param $key      Array   Element's key.
     * @param $model    Model   Model to perform mutations on.
     */
    private function callMutator(&$item, $key, $model) {

        $mutations = $model->getMutatedAttributes();

        if( in_array($key, $mutations) ) {

            $func = ucfirst('set'.camel_case($key).'Attribute');

            $model->$func($item);

        }
    }

    /**
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        return response()->json(['data' => self::$model::all()], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

    /**
     *
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $model = self::$model::findOrFail($id);

        return response()->json(['data' => [$model]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

    /**
     *
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        try {

            $model = self::$model::validateAndCreate($request->all());
            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $model);

            return response()->json(['data' => [$model->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ValidationException $validationException) {

            return response()->json(['data' => [], 'errors' => $validationException->errors()], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

    /**
     *
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {

        try {

            $model = self::$model::find($id)->validateAndUpdate($request->all());

            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $model);

            return response()->json(['data' => [$model->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ValidationException $validationException) {

            return response()->json(['data' => [], 'errors' => $validationException->errors()], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

    /**
     *
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        self::$model::findOrFail($id)->delete();

        return response()->json(['data' => []], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }
}
