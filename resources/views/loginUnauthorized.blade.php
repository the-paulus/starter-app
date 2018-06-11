<html>
    <head>
        <title>Login Error</title>
        <link rel="stylesheet" href="{{url('/components/bootstrap/dist/css/bootstrap.css')}}" />
        <style type="text/css">
            body {
                padding-top: 80px;
            }
        </style>
    </head>
    <body>
        <div class="container"><div class="col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title">Permission Denied!</h2>
            </div>
                <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
                <p>The identity service recognized your login, but this application could not find a record of your username. It's possible you do not have permission, or your user account has yet to be created.</p>
                <p>If you believe this is an error, contact the application administrator.</p>
                <p>
                    <a class="btn btn-default form-control" href="{{url('login')}}">Try to login again</a>
                </p>
                <p>
                    <a class="btn btn-default form-control" href="{{url('')}}">Go Back</a>
                </p>
            </form>
        </div>
        </div></div>
    </div>
</html>
