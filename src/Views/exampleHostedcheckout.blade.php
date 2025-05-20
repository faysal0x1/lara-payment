<!DOCTYPE html>
<html>
<head>
    <title>SSL Commerz Payment Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">SSL Commerz Payment Gateway</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('/pay') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Pay Now</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 