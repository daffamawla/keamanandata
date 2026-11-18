<?php
require 'lib.php';
$_SESSION = []; session_destroy();
header('Location: index.php');
