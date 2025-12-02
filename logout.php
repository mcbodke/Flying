<?php
require 'inc/config.php';
session_unset();
session_destroy();
header('Location: /flying/index.php');
exit;
