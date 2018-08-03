<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

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
     * Helper function that generates a paginated result.
     *
     * @param $operator string  Operator to use during comparison of the primary key.
     * @param $value    mixed   What the primary key should be compared to.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function paginateResponse($operator, $value) {

        $per_page = request('perPage', static::$default_per_page);
        $sort = request('sortBy', static::$default_sort);
        $order = request('orderBy', static::$default_order);

        return static::$model::where((new static::$model)->getKeyName(), $operator, $value)
            ->orderBy($sort, $order)
            ->paginate($per_page)
            ->appends([
                'orderBy' => $order,
                'perPage' => $per_page,
                'sortBy' => $sort
            ]);
    }

    /**
     * Returns all models.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {

        $this->authorize('view', static::$model::all()->first());

        return response()->json($this->paginateResponse(
            '>',
            1
        ), self::METHOD_SUCCESS_CODE[__FUNCTION__]);

    }

    /**
     * Returns a specific model.
     *
     * @param $id   int     ID of the model to return.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        $model_class = static::$model;

        $this->authorize('create', $model_class);
        $this->authorize('create', $model_class);

        try {

            $model = static::$model::validateAndCreate($request->all());
            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $model);

            return response()->json(['data' => [$model->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ValidationException $validationException) {

            return response()->json([
                'data' => [$model->freshRelationships()],
                'errors' => ['validation' => $validationException->errors()],
            ], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        } catch(AuthorizationException $authorizationException) {

            return response()->json([
                'data' => [],
                'errors' => ['Permission to create model was denied'],
            ], Response::HTTP_UNAUTHORIZED);

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

            return response()->json(['data' => [$model->freshRelationships()], 'errors' => ['validation' => $validationException->errors()]], self::METHOD_FAILURE_CODE[__FUNCTION__]);

        } catch(ModelNotFoundException $modelNotFoundException) {

            return response()->json(['data' => [], 'errors' => ['Content was not found']], Response::HTTP_NOT_FOUND);

        } catch(AuthorizationException $authorizationException) {

            return \response()->json(['data' => [], 'errors' => ['Permission to update this was denied']], Response::HTTP_UNAUTHORIZED);

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

        } catch (AuthorizationException $authorizationException) {

            return response()->json(['data' => [], 'errors' => ['Permission to delete this was denied']], Response::HTTP_UNAUTHORIZED);

        }

    }

}
