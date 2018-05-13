<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\UserGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    protected static $model = User::class;

    public function index() {

        return response()->json(['data' => User::all()], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

    public function show($id) {

        $user = null;

        try {

            $user = User::findOrFail($id);

            return response()->json(['data' => [$user]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ModelNotFoundException $e) {

            return response()->json([], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

    public function store(Request $request) {

        $user = null;

        try {

            // TODO: Ensure that relationship rules are validated.
            $user = User::validateAndCreate($request->all());

            if( $request->has('user_group_ids') ) {

                $user->groups()->sync($request->get('user_group_ids'));

            }

            return response()->json($user->freshRelationships(), self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ValidationException $validationException) {

            return response()->json($validationException->errors(), self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

    public function update(Request $request, $id) {

        $user = null;

        try {

            $user = User::findOrFail($id)->validateAndUpdate($request->all());

            if ($request->has('user_group_ids')) {

                $user->groups()->sync($request->get('user_group_ids'));

            }

            return response()->json($user->freshRelationships(), self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ModelNotFoundException $modelNotFoundException) {

            return response()->json(User::baseModelClassName() . ' not found.', Response::HTTP_NOT_FOUND);

        } catch(ValidationException $validationException) {

            return response()->json($validationException->errors(), self::METHOD_FAILURE_CODE[__FUNCTION__]);

        }

    }

    public function destroy($id) {

        $user = null;

        try {

            User::findOrFail($id)->delete();

            return response()->json([], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch( ModelNotFoundException $modelNotFoundException) {

            return response()->json(User::baseModelClassName() . ' not found.', Response::HTTP_NOT_FOUND);

        }

    }
}
