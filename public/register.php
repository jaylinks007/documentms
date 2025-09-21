<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Document Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Register</h1>
        <form action="../src/register_handler.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="organisation_name">Organisation Name (Optional)</label>
                <input type="text" name="organisation_name" id="organisation_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="department">Department (Optional)</label>
                <input type="text" name="department" id="department" class="form-control">
            </div>
            <div class="form-group">
                <label for="project_name">Project Name (Optional)</label>
                <input type="text" name="project_name" id="project_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p class="mt-3">
            Already have an account? <a href="login.php">Login here</a>.
        </p>
    </div>
</body>
</html>
