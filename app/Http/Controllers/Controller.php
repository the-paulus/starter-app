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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * BaseController is the class that all other controller inherit from. This class provides the basics for the index,
 * show, store, update, and delete methods. Depending on which method is called and whether it failed or not, a
 * different HTTP status code is returned. See the METHOD_SCCESS_CODE and METHOD_FAILURE constants for which method
 * returns which success code.
 *
 * @package App\Http\Controllers
 */
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
     * Standard Controller HTTP response codes that are returned when an operation failed.
     */
    const METHOD_FAILURE_CODE = [
        'AuthorizationException'    => Response::HTTP_UNAUTHORIZED,
        'ModelNotFoundException'    => Response::HTTP_NOT_FOUND,
        'ValidationException'       => Response::HTTP_NOT_ACCEPTABLE,
    ];

    /**
     * @var string $model   Class name of the model the control is handling.
     */
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

    private function paginateSearchResults($search_results) {

        $per_page = request('perPage', static::$default_per_page);
        $sort = request('sortBy', static::$default_sort);
        $order = request('orderBy', static::$default_order);
        $query = \request('q');

        return static::$model::whereIn((new static::$model)->getKeyName(), $search_results)->orderBy($sort, $order)->paginate($per_page)->appends([
            'orderBy' => $order,
            'perPage' => $per_page,
            'q' => $query,
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
            0
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
        $model = (new $model_class);

        try {
            $this->authorize('create', $model_class);
            $model = static::$model::validateAndCreate($request->all());
            $data = $request->all();

            array_walk($data, array(self::class, 'callMutator'), $model);

            return response()->json(['data' => [$model->freshRelationships()]], self::METHOD_SUCCESS_CODE[__FUNCTION__]);

        } catch(ValidationException $validationException) {

            return response()->json([
                'data' => [$model->freshRelationships()],
                'errors' => ['validation' => $validationException->errors()],
            ], Response::HTTP_NOT_ACCEPTABLE);

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

            return response()->json(['data' => [$model->freshRelationships()], 'errors' => ['validation' => $validationException->errors()]], Response::HTTP_NOT_ACCEPTABLE);

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

            return response()->json(['data' => [], 'errors' => ['Model with ' . $id . ' was not found.']], Response::HTTP_NOT_FOUND);

        } catch (AuthorizationException $authorizationException) {

            return response()->json(['data' => [], 'errors' => ['Permission to delete this was denied']], Response::HTTP_UNAUTHORIZED);

        }

    }

    /**
     * Performs search and returns results.
     *
     * @param Request $request  Request containing the search query.
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {

        $model = new static::$model;

        if( method_exists($model, 'shouldBeSearchable') && $model->shouldBeSearchable() ) {
            $query = $request->get('q');
            $per_page = request('perPage', static::$default_per_page);
            $sort = request('sortBy', static::$default_sort);
            $order = request('orderBy', static::$default_order);

            /**
             * The return statement should actually be:
             * return response()->json([
             *  static::$model::search($query)
             *      ->orderBy($order, $sort)
             *      ->paginate($per_page)
             *      ->appends([
             *          'orderBy' => $order,
             *          'perPage' => $per_page,
             *          'sortBy' => $sort
             *      ]);
             * But there is a bug in laravel-scout-tntsearch-driver: https://github.com/teamtnt/laravel-scout-tntsearch-driver/issues/171
             */
            return response()->json($this->paginateSearchResults(static::$model::search($query)->keys()), Response::HTTP_OK);
            /*return static::$model::search($query)->orderBy($order, $sort)->paginate($per_page)->appends([

            ]);*/
        }
    }

}
