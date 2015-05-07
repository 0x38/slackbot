<?php

require_once 'config.php';
require_once 'slack.class.php';

$parts = explode(':', strtolower($_REQUEST['text']));
if(!isset($_GET['stream'])){
	$trigger = $parts[0];
	$command = $parts[1];
}else{
	$trigger = 'base';
	$command = $parts[0];
}

$command = trim($command);

if(file_exists('personalities/' . $trigger . '.persona.pvt')) $persona_file = 'personalities/' . $trigger . '.persona.pvt';
else if(file_exists('personalities/' . $trigger . '.persona.php')) $persona_file = 'personalities/' . $trigger . '.persona.php';
else die('Nothing implements ' . $trigger);

require_once $persona_file;

$bot = $trigger . 'persona';
$bot = new $bot();
$bot->handle($command, $_POST);

?>
