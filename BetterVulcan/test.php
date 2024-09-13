<?php
$password = "Uczen1";  // Replace with the actual password
$hash = password_hash($password, PASSWORD_DEFAULT);

echo $hash;
?>