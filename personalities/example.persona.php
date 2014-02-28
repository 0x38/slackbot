<?php

class examplepersona extends slackbot{
	
	function __construct(){

		// This set up is straight forward. Username is the name under which your bot will post
		// and icon_emoji is the custom/stock emoji you'd like to use as its avatar
		// icon_emoji can be ommitted and a default will be used instead
		$this->response['username'] = 'Jeff Goldblum';
		$this->response['icon_emoji'] = ':goldblum-whisper:';

		// register_callback takes an array of commands and then a string 
		// referencing a protected function here. And they *have* to be 
		// protected! Not private or public.
		$this->register_callback(array('hello'), '_post_hello_world');
		$this->register_callback(array('weather', 'wether'), '_post_weather');
		$this->register_callback(array('shirtless', 'take it off'), '_post_shirtless');
	}

	// _bad_command has a default implementation so this can be ommitted
	// if you're happy with that. Otherwise it can be re-implemented to give
	// your bot personality.
	protected function _bad_command($command = ''){
		$this->respond('I uh uh... don\'t know what "' . $command. '" is.');
	}

	protected _post_hello_world(){
		// respond takes an optional string and outputs the expected json to the browser
		// that slack will pick up on and submit to the channel the triggering string was
		// posted in. respond also terminates the script so nothing else can be called afterwards
		$this->respond('Hello, world!');
	}

	// This callback references a helper function called __get_weather in
	// the slackerbot abstract class. All of your shared helper functions
	// should be put in that abstract.
	protected function _post_weather(){
		$weather = $this->__get_weather();
		$response  = 'It\'s uh... uh currently ' . strtolower($weather['currently']['summary']) . ".\n";
		$response .= 'The temperature is ' . round($weather['currently']['temperature']) . ' and... uh feels like ' . round($weather['currently']['apparentTemperature']) . '. ';
		$this->respond($response);
	}

	// __build_image takes a url (mandatory) and link text (optional)
	// and builds out the text for posting an image for you. Slack only
	// displays an image inline once so __build_image takes care of that
	protected function _post_shirtless(){
		$this->respond($this->__build_image('http://i.imgur.com/8av3ydt.jpg', 'Better?'));
	}
}
