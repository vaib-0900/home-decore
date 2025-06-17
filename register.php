<?php
include "header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
  
    
</head>
<body>
<div class="container mt-5 bg-info mt-4">
    <h2 class="text-center pt-4">Register</h2>
    <form action="register_out.php" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Name</label>
            <input type="text" class="form-control"name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control"  name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control"  name="password" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" name="phone" pattern="[0-9]{10}" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" name="address" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="landmark" class="form-label">Landmark</label>
            <input type="text" class="form-control"  name="landmark">
        </div>
        <button type="submit" class="btn btn-primary w-100 my-4">Register</button>
    </form>
</div>
    
</body>
</html>
<?php
include "footer.php";
?>