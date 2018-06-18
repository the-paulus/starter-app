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

if( !function_exists('print_c') ) {
    function print_c(Closure $closure) {
        $fncText = 'function (';
        $rf = new ReflectionFunction($closure);
        $fncParams = $rf->getParameters();

        foreach($fncParams as $param) {

            if($param->isArray()) {
                $fncText .= 'array ';
            } else if($param->isCallable()) {
                $fncText .= 'Callable ';
            } else if($param->isPassedByReference()) {
                $fncText .= '&';
            } else if($param->getClass()) {
                $fncText .= $param->getClass();
            }

            $fncText .= $param->getName();

            if($param->isOptional()) {
                $fncText .= ' = ' . var_export($param->getDefaultValue(), TRUE);
            }

            $fncText .= ', ';
        }

        $fncText = str_replace_last(',', ')' . PHP_EOL, $fncText);

        $fileCode = file($rf->getFileName());

        for($ln = $rf->getStartLine(); $ln < $rf->getEndLine(); $ln++) {
            $fncText .= $fileCode[$ln];
        }

        return $rf->getFileName() . PHP_EOL . $fncText;

    }
}