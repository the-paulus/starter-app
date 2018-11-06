<?php
/**
 * Created by PhpStorm.
 * User: lyonp
 * Date: 10/2/18
 * Time: 12:09 PM
 */

namespace Tests;


use App\Http\Controllers\Controller;
use App\Models\BaseModel;

trait ControllerModelTesting
{
    protected $controller = Controller::class;
    protected $model = BaseModel::class;

    /**
     *
     * @return void
     */
    abstract function testCreateModel();

    /**
     * @return void
     */
    abstract function testDeleteModel();

    /**
     * @return void
     */
    abstract function testUpdateModel();

    /**
     * @return void
     */
    abstract function testViewModel();

    /**
     *
     */
    abstract function testControllerIndex();

    abstract function testControllerShow();

    abstract function testControllerStore();

    abstract function testControllerUpdate();

    abstract function testControllerDestroy();
}