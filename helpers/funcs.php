<?php

require_once('./includes/database.php');

function get_user_ip(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		//ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		//ip pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function compare_session_id($funcName, $funcId) {
    global $conn;
	$sql = "SELECT * FROM users WHERE username = '$funcName'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);

    if($conn === false) {
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	if ($funcId == $row['session_id']) {
		return true;
	} else {
		return false;
	}
}

function create_post($link, $author, $date, $tags, $id) {
    global $site_url;
	$tagText = "<br>";
    $tags = explode(',', $tags);
    foreach ($tags as $tag) {
        if ($tag != null) {
            $tagText .= "<a href=". $site_url . '/index.php?tag=' . $tag ." style='color:black;text-decoration: none;'><button class='btn btn-outline-dark btn-sm'>$tag</button></a> ";
        }
    }
	if (isset($_COOKIE['loggedInUsername']) && $_COOKIE['loggedInUsername'] == $author) {
		echo "<div class='card mb-3'>" .
        "<img src='$link' class='card-img-top'>" .
        "<div class='card-body'>" .
        "<p class='card-text'> uploaded by <b>$author</b> on <span class='text-muted'>$date</span>.$tagText".
		"<br><a style='color:red' href='".append_get('delete', $id)."'>delete post</a>.".
        "</div></div>";
	}
    else echo "<div class='card mb-3'>" .
        "<img src='$link' class='card-img-top'>" .
        "<div class='card-body'>" .
        "<p class='card-text'> uploaded by <b>$author</b> on <span class='text-muted'>$date</span>.$tagText".
        "</div></div>";
}

function append_get($param, $val) {
	global $site_url;
	$new_url = $site_url . '/index.php?';
	$duplicate = false;
	foreach ($_GET as $key => $value) {
		if ($key == $param) {
			$value = $val;
			$duplicate = true;
		}
		$new_url .= $key . '=' . $value . '&';
	}
	if (!$duplicate)
		$new_url .= $param . '=' . $val . '&';
	$new_url = substr($new_url, 0, -1);
	return $new_url;
}

?>
