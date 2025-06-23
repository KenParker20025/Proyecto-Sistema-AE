<?php
session_start();
session_destroy();
header("Location: ../Logs/inicio.php");
exit();
