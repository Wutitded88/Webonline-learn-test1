<?php
require_once 'config.php';
unset($_SESSION['u_user']);
session_destroy();

$link = base_url();
echo "<SCRIPT LANGUAGE='JavaScript'>
        window.location.href = '{$link}/login';
      </SCRIPT>";
exit();
 ?>
