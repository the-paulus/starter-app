<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const METHOD_SUCCESS_CODE = [
        'index'     => Response::HTTP_OK,
        'show'      => Response::HTTP_OK,
        'store'     => Response::HTTP_CREATED,
        'update'    => Response::HTTP_SEE_OTHER,
        'destroy'   => Response::HTTP_GONE
    ];

    const METHOD_FAILURE_CODE = [
        'index'     => Response::HTTP_NO_CONTENT,
        'show'      => Response::HTTP_NOT_FOUND,
        'store'     => Response::HTTP_NOT_ACCEPTABLE,
        'update'    => Response::HTTP_NOT_MODIFIED,
        'destroy'   => Response::HTTP_I_AM_A_TEAPOT
    ];
}
