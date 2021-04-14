<?php
require_once('./helpers/funcs.php');
?>

<div class="card" style="width: 15rem;right:1em;top:1em;position: fixed;">
    <div class="card-body">
    <!-- We check if a user is already connected, otherwise serve them the login form -->
    <?php if (isset($_COOKIE['loggedIn']) && compare_session_id($_COOKIE['loggedInUsername'], $_COOKIE['sessionId']) === true) {?>
        <div class="card-text text-center mb-3">You are logged in as <b><?php echo $_COOKIE['loggedInUsername'] ?></b></div>
        <form action="index.php" method="post" class="text-center">
            <input type="submit" value="Log out" name="logout" class="btn btn-outline-dark btn-sm">
        </form>
    <?php } else { ?>
        <form action="index.php" method="post" class="text-center">
            <input type="text" placeholder="username" name="loginName" class="form-control mb-2 mt-2 form-control-sm">
            <input type="password" placeholder="password" name="loginPass" class="form-control mb-2 form-control-sm">
            <input type="submit" value="Log in" name="login" class="btn btn-outline-dark btn-sm">
        </form>
    <?php } ?>
    </div>
</div>
