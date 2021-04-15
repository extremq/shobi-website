<footer>
    <?php 
        if (isset($_GET['tag'])){
            $sql = "SELECT COUNT(*) FROM posts WHERE LOCATE('{$_GET['tag']}', posts.tags)";
        }
        else
            $sql = "SELECT COUNT(*) FROM posts";
        $pageCount = ceil((int) mysqli_fetch_assoc(mysqli_query($conn, $sql))['COUNT(*)'] / $pagination);
        
        $page = 1; 
        if (isset($_GET['page'])) 
            $page = (int) $_GET['page'];
        if ($page < 1) $page = 1;

        for($i = -1; $i <= 1; $i++) {
            $curr = $page + $i;
            $btn = "<a href='" . append_get("page", $curr) . "' style='color:black;text-decoration: none;'><button class='btn btn-outline-dark btn-sm'>$curr</button></a> ";
            if ($curr <= 0 || $curr > $pageCount) $btn = "<button class='btn btn-outline-dark btn-sm' disabled>$curr</button> ";;
            echo $btn;
        }
    ?>
    <br>made by extremq with love
</footer>
</html>