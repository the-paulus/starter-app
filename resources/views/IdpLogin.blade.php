<html>
    <head>
        <title>Emulated IdP Login</title>
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
                <h2 class="panel-title">Login to Continue</h2>
            </div>
            <form class="panel-body" action="" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
                <p>{{ $error or "Please login below." }}</p>
                <p>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                    </div>
                </p>
                <p>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                </p>
                <p>
                    <button type="submit" class="btn btn-default form-control">Login</button>
                </p>
            </form>
        </div>
        </div></div>
    </div>
</html>
