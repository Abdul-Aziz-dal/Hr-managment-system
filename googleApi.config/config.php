<?php
// Define Google Client Credentials, Scopes, and URIs
define('GCLIENT_ID', "158640470724-g7c2tda757lq3fgeemapv9ulhov5grc5.apps.googleusercontent.com");
define('GCLIENT_SECRET', "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");
define('GCLIENT_SCOPE', "https://www.googleapis.com/auth/drive");

define('GCLIENT_REDIRECT', "http://localhost/php-gdrive-upload/userProfile/index.php");

define('OAUTH2_TOKEN_URI',"https://oauth2.googleapis.com/token");
define('DRIVE_FILE_UPLOAD_URI',"https://www.googleapis.com/upload/drive/v3/files");
define('DRIVE_FILE_META_URI',"https://www.googleapis.com/drive/v3/files/");

if(!session_id()) session_start();

$gOauthURL = "https://accounts.google.com/o/oauth2/auth?scope=" . urlencode(GCLIENT_SCOPE) . 
             "&redirect_uri=" . urlencode(GCLIENT_REDIRECT) . 
             "&client_id=" . urlencode(GCLIENT_ID) . 
             "&access_type=offline" . 
             "&response_type=code";


if(isset($_GET['code'])){
    $_SESSION['code'] = $_GET['code'];
    require_once("GoogleDriveUploadAPI.php");
    $gdriveAPI = new GoogleDriveUploadAPI();

    $_SESSION['access_token'] = $gdriveAPI->GetValidAccessToken();
    header('location:../userProfile/index.php');
    // exit;
}
// if(!isset($_SESSION['code']))
// header("location:{$gOauthURL}");