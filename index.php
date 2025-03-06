<?php
require_once "Session.php";
require_once "Command.php";

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