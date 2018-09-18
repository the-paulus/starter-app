<?php
namespace App\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use OwenIt\Auditing\Contracts\UrlResolver as AuditingUrlResolver;

class UrlResolver implements AuditingUrlResolver {

    /**
     * {@inheritdoc}
     */
    public static function resolve(): string {

        if(App::runningInConsole()) {
            return 'console';
        }

        return Request::fullUrl();

    }
}