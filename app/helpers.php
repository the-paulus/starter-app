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