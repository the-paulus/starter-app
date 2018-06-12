<!doctype html>
<html>
<body>
    <div>From: <a href="mailto:{{{$email}}}">{{{ $first_name }}} {{{ $last_name }}} <{{{$email}}}></a></div>
    <div>Category: {{{ $category }}}</div>
    <div>Details: </div>
    <p>{{{ $details or '(NONE)'}}}</p>
</body>
</html>
