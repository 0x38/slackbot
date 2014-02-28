<?php

require_once 'config.php';
require_once 'slack.class.php';

list($trigger, $command) = explode(':', strtolower($_REQUEST['text']));
$command = trim($command);

if(!file_exists('personalities/' . $trigger . '.persona.php')) die('Nothing implements ' . $trigger);

require_once 'personalities/' . $trigger . '.persona.php';
$bot = $trigger . 'persona';
$bot = new $bot();

$bot->handle($command);

?>
