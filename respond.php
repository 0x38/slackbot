<?php

require_once 'slack.class.php';

list($trigger, $command) = explode(':', strtolower($_REQUEST['text']));
$command = trim($command);

if(!file_exists('bots/' . $trigger . '.bot.php')) die('Nothing implements ' . $trigger);

require_once 'bots/' . $trigger . '.bot.php';
$bot = $trigger . 'bot';
$bot = new $bot();

$bot->handle($command);

?>