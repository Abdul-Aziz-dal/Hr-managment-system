<?php 
session_start();
if(!$_SESSION["user_id"]) header("location:./login.php");

require_once('./googleApi.config/config.php');
require_once("./googleApi.config/GoogleDriveUploadAPI.php");
$gdriveAPI = new GoogleDriveUploadAPI();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - Upload File in Gdrive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="assets/css/styles.css"> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/all.min.js" integrity="sha512-naukR7I+Nk6gp7p5TMA4ycgfxaZBJ7MO5iC3Fp6ySQyKFHOGfpkSZkYVWV5R7u7cfAicxanwYQ5D1e17EfJcMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <!-- <script src="assets/js/script.js"></script> -->
</head>
<body>
    <main>
        <nav class="navbar navbar-expand-lg navbar-dark bg-gradient">
            <div class="container">
                <a class="navbar-brand" href="./">Upload File in Gdrive - PHP</a>
                
                <div>
                    <a href="#" class="text-light fw-bolder h6 text-decoration-none">Abdul Aziz Dal</a>
                </div>
            </div>
        </nav>
        <div id="main-wrapper">
            <div class="container px-5 my-3" >
              
                <div class="mx-auto col-lg-10 col-md-12 col-sm-12 col-xs-12">
                    <?php if(isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])): ?>
                      <?php header("location:./userProfile/") ?>
                    <?php else: ?>
                        <div class="col-lg-3 col-md-5 col-sm-10 col-xs-12 mx-auto">
                            <a class="btn btn-primary rounded-pill w-100" href="<?= $gOauthURL ?>">Sign with Google</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <footer class="bg-gradient bg-light shadow-top py-4 col-auto">
            <div class="">
                <div class="text-center">
                    All Rights Reserved &copy; <span id="dt-year"></span> | <span class="text-muted">Upload File in Gdrivep - PHP</span>
                </div>
                
            </div>
        </footer>
    </main>
</body>
</html>