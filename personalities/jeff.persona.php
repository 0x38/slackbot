<?php

class jeffpersona extends slackbot{

	function __construct(){
		$this->response['username'] = 'Jeff Goldblum';
		$this->response['icon_emoji'] = ':goldblum-whisper:';

		$this->register_callback(array('weather', 'wether'), '_post_weather');
		$this->register_callback(array('shirtless', 'take it off'), '_post_shirtless');
		$this->register_callback(array('is there room for one more?', 'can I come in?'), '_post_oh_yeah_roomy');

		$this->help = array(
			'_post_weather' => 'Pattern: weather <address>. Posts weather from Ottawa or from <address>.',
			'_post_shirtless' => 'Just try it.'
		);
	}

	// Command callbacks
	protected function _bad_command($args = array()){
		$this->respond('I uh uh... don\'t know what "' . $args[0] . '" is.');
	}
	protected function _post_weather($args = array()){
		$weather = $this->__get_weather(implode(' ', $args));
		$response  = 'It\'s uh... uh currently ' . strtolower($weather['currently']['summary']) . ($weather['region'] ? ' in ' . ucfirst($weather['region']) : '') . ".\n";
		$response .= 'The temperature is ' . round($weather['currently']['temperature']) . ' and... uh feels like ' . round($weather['currently']['apparentTemperature']) . '. ';

		$this->respond($response);
	}
	protected function _post_shirtless(){
		$this->respond($this->__build_image('http://i.imgur.com/RW4dl4i.gif', 'My shirtlessness comes with a hot tub.'));
	}
	protected function _post_oh_yeah_roomy(){
		$this->respond($this->__build_image('http://i.imgur.com/oq74Yw9.gif', 'Oh but of course, mon chou.'));
	}
}
