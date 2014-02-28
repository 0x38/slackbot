<?php

require_once 'config.php';
require_once 'slack.class.php';

list($trigger, $command) = explode(':', strtolower($_REQUEST['text']));
$command = trim($command);

if(file_exists('personalities/' . $trigger . '.persona.pvt')) $persona_file = 'personalities/' . $trigger . '.persona.pvt';
else if(file_exists('personalities/' . $trigger . '.persona.php')) $persona_file = 'personalities/' . $trigger . '.persona.php';
else die('Nothing implements ' . $trigger);

require_once $persona_file;

$bot = $trigger . 'persona';
$bot = new $bot();
$bot->handle($command);

?>
