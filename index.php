<?php

require_once('includes/database.php');
require_once('includes/header.php');
require_once('includes/login.php');
require_once('helpers/funcs.php');

?>

<?php if (isset($_COOKIE['loggedIn']) && compare_session_id($_COOKIE['loggedInUsername'], $_COOKIE['sessionId']) === true){ ?>
    <div class="card" style="width: 15rem;left:1em;top:1em;position:fixed;">
        <div class="card-text text-center p-3">
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="uploadFile" class="form-control form-control-sm"/><br/>
                <input type="text" name="tags" placeholder="Add tags separated by spaces." class="form-control form-control-sm"/><br/>
                <input type="submit" name="upload" value="Upload" class="btn btn-outline-dark btn-sm"/>
            </form>
        </div>
    </div>
<?php } ?>

<div style="margin: auto;width: 50%;z-index:10">
    <h1><a href="<?php echo $site_url ?>" style="color:black;text-decoration: none;">shobi.</a></h1>
    <form method="get">
        <input type="text" style="width:30%" name="tag" placeholder="Input a tag" class="form-control form-control-sm mb-3"/>
        <button type="submit" class="btn btn-outline-dark btn-sm">search for tag</button>
    </form>
</div>
<div class="card text-center p-3 mb-3" style="margin: auto;width: 50%;z-index:1">
    <div class="card-text">
        
<?php 

// Check database
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// User attempted login
if (isset($_POST['login'])) {
    $loginName = $_POST['loginName'];
    $loginPass = $_POST['loginPass'];

    // Strip the username
    $loginName = preg_replace("/[^a-zA-Z0-9]+/", "", $loginName);

    $sql = "SELECT * FROM users WHERE username = '$loginName'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $hash = $row['password'];

    // Check the password with the hashed value
    if (password_verify($loginPass, $hash)) {
        $cookieExp = time() + 86400 * 30; // 1 month
        $id = $row['id'];
        setcookie("loggedIn", 1, $cookieExp, $site_path);
        setcookie("loggedInId", $id, $cookieExp, $site_path);
        setcookie("loggedInUsername", $row['username'], $cookieExp, $site_path);
        $session_id = hash('sha256', $row['username'] . get_user_ip() . rand());
        setcookie("sessionId", $session_id, $cookieExp, $site_path);
        
        // Update the session.
        $sql = "UPDATE users SET session_id = '$session_id' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            header("Refresh:0; url=$site_url");
        } else {
            echo "<br>session failed. XD";
        }
    }
    else {
        echo "wrong password........";
    }
} // User attempted logout
elseif (isset($_POST['logout'])) {
    setcookie("loggedIn", NULL, time() - 3600, $site_path);
    setcookie("loggedInId", NULL, time() - 3600, $site_path);
    setcookie("loggedInUsername", NULL, time() - 3600, $site_path);
    setcookie("sessionId", NULL, time() - 3600, $site_path);
    header("Refresh:0; url=$site_url");
}

if (isset($_GET['delete'])){
    $id = $_GET['delete'];
    $sql = "DELETE FROM posts WHERE id = '$id'";
    mysqli_query($conn, $sql);
    header("Refresh:0;url=$site_url");
}


// User attempted file upload
if (isset($_POST['upload']) && isset($_COOKIE['loggedIn']) && compare_session_id($_COOKIE['loggedInUsername'], $_COOKIE['sessionId']) === true) {
    if ($_FILES['uploadFile']['error'] === 1) {
        die("An error has occured.");
    }
    if ($_FILES['uploadFile']['size'] > 10000000) {
        die("Filesize must be lower than 10MB.");
    }

    $filetype = explode('/',mime_content_type($_FILES['uploadFile']['tmp_name']));
    if ($filetype[0] !== 'image') {
        die('Invalid type.');
    }

    $tags = NULL;
    if (isset($_POST['tags'])) {
        $tags = explode(' ', strtolower($_POST['tags']));
        $cleanTags = array();
        foreach ($tags as $tag) {
            $tag = preg_replace("/[^a-z0-9]+/", "", $tag);
            if ($tag != NULL) {
                array_push($cleanTags, $tag);
            }
        }
        $tags = implode(',', $cleanTags) . ',';
    }

    $image = file_get_contents($_FILES['uploadFile']['tmp_name']);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . $clientId ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, array( 'image' => base64_encode($image) ));

    $reply = curl_exec($ch);

    curl_close($ch);

    $reply = json_decode($reply);

    $imageLink = $reply->data->link;
    
    $sql = "INSERT INTO `posts`(`author`, `content`, `tags`) VALUES ('{$_COOKIE['loggedInUsername']}', '$imageLink', '$tags')";
    mysqli_query($conn, $sql);
    header("Refresh:0");
}

// Start displaying the posts
if (isset($_GET['tag'])) {
    $tag = $_GET['tag'] . ",";
    
    $sql = "SELECT * FROM posts WHERE 1 ORDER BY id DESC";

    $result = mysqli_query($conn, $sql);
    if ($result !== false) {
        if ($result->num_rows > 0) {
            // go in each row
            while($row = $result->fetch_assoc()) {
                $imgLink = $row['content'];
                $author = $row['author'];
                $createdAt = $row['createdAt'];
                $tags = $row['tags'];
                $id = $row['id'];
                if (str_contains($tags, $tag)) {
                    create_post($imgLink, $author, $createdAt, $tags, $id);
                }
            }
        }
    }
    else {
        echo "<b>No posts found.</b>";
    }
}
// Display all posts
else {
    $sql = "SELECT * FROM posts WHERE 1 ORDER BY id DESC LIMIT 10";

    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
        // go in each row
        while($row = $result->fetch_assoc()) {
            $imgLink = $row['content'];
            $author = $row['author'];
            $createdAt = $row['createdAt'];
            $tags = $row['tags'];
            $id = $row['id'];
            create_post($imgLink, $author, $createdAt, $tags, $id);
        }
    }
}


?>
<?php

require_once('includes/footer.php');

?>
    </div>
</div>