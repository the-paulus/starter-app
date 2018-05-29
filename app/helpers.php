<?php

if( !function_exists('dv') ) {

    function dv($var) {

        print_r(var_dump($var));

    }

}

if( !function_exists('dpj') ) {

    function dpj($json) {

        dv(json_decode($json, TRUE));

    }

}

if( !function_exists('filter_exception_message') ) {

    function filter_exception_message(\Exception $exception) {

        $message = '';

        foreach($exception->getTrace() as $step) {

            if(str_contains($step, basename(__DIR__ . '..'))) {

                $message .= $step;
            }

        }

    }
}

if( !function_exists('camel_case_conversion') ) {

    function camel_case_conversion(string $classname) {

       return str_plural(strtolower(implode(' ', preg_split('/([A-Z][^A-Z]*)/', $classname, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY))));

    }

}