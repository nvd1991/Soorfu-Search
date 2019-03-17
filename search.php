<?php
//search.php

if(isset($_GET['term'])){
    $term = $_GET['term'];
} else {
    header('Location: index.php');
}

$type = isset($_GET['type']) ? $_GET['type'] : 'sites';

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>- Soorfu Search</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="headerContent">
                <div class="logoContainer">
                    <a href="index.php">
                        <img src="assets/images/logo/logo.png" alt="Logo">
                    </a>
                </div>
                <div class="searchContainer">
                    <form action="search.php" method="get">
                        <div class="searchBarContainer">
                            <input type="text" class="searchBox" name="term" value="<?php echo isset($term) ? $term : ''; ?>">
                            <button type="submit" class="searchButton">
                                <img src="assets/images/icons/search.png" alt="Search">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="tabsContainer">
                <ul class="tabList">
                    <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                        <a href="search.php?term=<?php echo $term; ?>&type=sites">Sites</a>
                    </li>
                    <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
                        <a href="search.php?term=<?php echo $term; ?>&type=images">Images</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
