<?php

class jeffpersona extends slackbot{
	
	function __construct(){
		$this->response['username'] = 'Jeff Goldblum';
		$this->response['icon_emoji'] = ':goldblum-whisper:';

		$this->register_callback(array('weather', 'wether'), '_post_weather');
		$this->register_callback(array('shirtless', 'take it off'), '_post_shirtless');
	}

	// Command callbacks
	protected function _bad_command($command = ''){
		$this->respond('I uh uh... don\'t know what "' . $command. '" is.');
	}
	protected function _post_weather($args = array()){
		$weather = $this->__get_weather($args[0]);
		$response  = 'It\'s uh... uh currently ' . strtolower($weather['currently']['summary']) . ($weather['region'] ? ' in ' . ucfirst($weather['region']) : '') . ".\n";
		$response .= 'The temperature is ' . round($weather['currently']['temperature']) . ' and... uh feels like ' . round($weather['currently']['apparentTemperature']) . '. ';
		
		$this->respond($response);
	}
	protected function _post_shirtless(){
		$this->respond($this->__build_image('http://i.imgur.com/LVSMqPY.gif', 'Shirtlessness uh uh finds a way.'));
	}
}
