<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * Class UserController handles CRUD requests.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    /**
     * @var string $model Model class that the controller is bound to.
     */
    protected static $model = User::class;

    /**
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        return response()->json(['data' => User::all()], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

    /**
     *
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $user = User::findOrFail($id);

        return response()->json(['data' => [$user]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

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
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        try {

            // TODO: Ensure that relationship rules are validated.
            $user = User::validateAndCreate($request->all());
            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $user);

            return response()->json(['data' => [$user->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

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

            $user = User::find($id)->validateAndUpdate($request->all());
            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $user);

            return response()->json(['data' => [$user->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

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

        User::findOrFail($id)->delete();

        return response()->json(['data' => []], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }
}
