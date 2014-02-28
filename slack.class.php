<?php

abstract class slackbot{
	public $response = array();
	public $commands = array('bad_command' => '_bad_command', 'debug' => '_debug');

	function __construct(){
		$this->response['username'] = BOT_USERNAME;
		$this->register_callback(array('weather', 'wether'), '_post_weather');
	}

	// Public facing functions
	public function handle($command = ''){
		$command = strtolower($command);
		if(!isset($this->commands[$command])) $callback = $this->commands['bad_command'];
		else $callback = $this->commands[$command];
		$this->{$callback}($command);
	}
	public function respond($text = null){
		if(!is_null($text)) $this->response['text'] = $text;
		echo json_encode($this->response);
		exit;
	}
	public function register_callback($keys, $callback){
		if(!is_array($keys)) $keys = array($keys);
		foreach($keys as $key){
			$this->commands[$key] = $callback;
		}
	}

	// Default callbacks
	protected function _bad_command($command = ''){
		$this->respond('The command "' . $command. '" has no registered callback.');
	}
	protected function _debug(){
		$response = $this->response;
		unset($response['text']);
		$this->respond(json_encode($response));
	}

	// Custom callbacks
	protected function _post_weather(){
		$weather = $this->__get_weather();
		$response  = 'It is ' . strtolower($weather['currently']['summary']) . ".\n";
		$response .= 'The temperature is ' . round($weather['currently']['temperature']) . ' and it feels like ' . round($weather['currently']['apparentTemperature']) . '. ';
		$this->respond($response);
	}

	// Helper functions
	protected function __get_weather(){
		$weather = curl_init('https://api.forecast.io/forecast/'.FORECASTIO_API.'/'.FORECASTIO_LATLON.'?units='.FORECASTIO_UNITS);
		curl_setopt($weather, CURLOPT_RETURNTRANSFER, true);
		return json_decode(curl_exec($weather), true);
	}
	protected function __build_image($src, $text = null){
		return '<' . $src . '?' . time() . (is_string($text) ? '|' . $text : '') . '>';
	}
	public function __d($a){
		$o = print_r($a, true);
		if(!$o || !$a) $o = var_export($a, true);
		echo '<pre>',$o,'</pre>';
	}


}
