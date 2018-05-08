<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;

class BaseAuthenticatable extends BaseModel
{
    use Authenticatable;
}