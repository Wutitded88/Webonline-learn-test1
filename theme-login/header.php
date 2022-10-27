<?php 
require_once 'api/config.php';
$q_website_tb1 = dd_q('SELECT * FROM website_tb WHERE id = ?', [1]);
$row_website_tb1 = $q_website_tb1->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
   <!-- Font Awesome JS -->
   <link href="https://kit-pro.fontawesome.com/releases/v5.15.3/css/pro.min.css" rel="stylesheet">
   <!-- Our Custom CSS -->
   <link rel="stylesheet" href="theme-login/style.css?<?php echo time(); ?>">

</head>
<body class="d-flex flex-column h-100">
<style>
body::after {
    content: "";
    background: url(<?php echo $row_u['bg']; ?>);
    filter: blur(20px);
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    position: fixed;
    z-index: -5;
    overflow-x: hidden;
}
.containlogin {
 
	background: linear-gradient(140deg, rgb(24 28 41 / 41%) 0%, rgb(11 14 23 / 90%) 100%),url(<?php echo $row_u['bg']; ?>);
	box-shadow: 0 14px 28px rgb(0 0 0 / 25%), 0 10px 10px rgb(0 0 0 / 22%);
	background-size: cover;
	background-position: center;
	border-radius: 10px;
	border-top: 2px solid #a79c71;
	width: 100%;
	border-radius: 0px;
	padding-bottom: 100px;
}
</style>
