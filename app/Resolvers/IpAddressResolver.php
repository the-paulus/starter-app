<?php
namespace App\Resolvers;


use Illuminate\Support\Facades\Request;
use OwenIt\Auditing\Contracts\IpAddressResolver as AuditingIpAddressResolver;

class IpAddressResolver implements AuditingIpAddressResolver
{

    /**
     * {@inheritdoc}
     */
    public static function resolve(): string {

        return Request::header('HTTP_X_FORWARDED_FOR', '0.0.0.0');

    }
}