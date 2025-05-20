<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Payment Failed</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Oops!</h4>
                            <p>{{ $message }}</p>
                            <hr>
                            <p class="mb-0">Transaction ID: {{ $transId }}</p>
                        </div>
                        <a href="{{ $url }}" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 