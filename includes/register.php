<form action="register.php" method="post">
    <input type="text" placeholder="username" name="registerName" class="form-control mb-2 mt-2 form-control-sm">
    <input type="password" placeholder="password" name="registerPass" class="form-control mb-2 form-control-sm">
    <input type="submit" name="register" class="btn btn-outline-dark btn-sm">
</form>

<?php
    require_once('database.php');

    if (isset($_POST['register'])) {
        if ($_POST['registerName'] != '' && $_POST['registerPass'] != '') {
            $username = $_POST['registerName'];
            $password = $_POST['registerPass'];
            
	        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if ($conn === false) {
		        die("ERROR: Could not connect. " . mysqli_connect_error());
            }

            $sql = "INSERT INTO users (id, username, password) VALUES (NULL, '$username', '$hashed_password')";
            mysqli_query($conn, $sql);
        }
    }
?>

