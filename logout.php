<?php
session_start();
$_SESSION = [];       // kosongkan isi session
session_destroy();    // hancurkan session
header("Location: login.php");
exit;