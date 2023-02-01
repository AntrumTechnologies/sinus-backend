<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Love waves</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    </head>
    <body>
        <div class="container-md">
            <div class="row justify-content-center">
                <h1>Love waves - Reset password</h1>
                <form method="POST" action="/api/reset-password">
                    <input type="hidden" text="{{ $token }}" />
                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right">Email address</label>
                        <div class="col-md-6">
                            <input type="email" id="email" value="{{ request()->get('email') }}" required />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                        <div class="col-md-6">
                            <input type="password" id="password" required />
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">Password confirmation</label>
                        <div class="col-md-6">
                            <input type="password_confirmation" id="password_confirmation" required />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" value="Reset password" class="btn btn-primary">Reset password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
