<?php

abstract class slackbot{
	public $response = array();
	public $commands = array('bad_command' => '_bad_command', 'debug' => '_debug', 'help' => '_help', '?' => '_help');
	public $help = array();

	function __construct(){
		$this->response['username'] = BOT_USERNAME;
		$this->register_callback(array('weather', 'wether'), '_post_weather');
	}

	// Public facing functions
	public function handle($input = ''){
		$callback = $this->commands['bad_command'];
		$args = array($input);

		foreach($this->commands as $cmd_key => $cmd){
			if(strpos($input, $cmd_key) === 0){
				$this->active_command = $cmd_key;
				$args = explode(' ', trim(str_replace($cmd_key, '', $input)));
				$callback = $cmd;
				break;
			}
		}

		$this->{$callback}($args);
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
	protected function _bad_command($command = array()){
		$this->respond('The command "' . implode(' ', $command) . '" has no registered callback.');
	}
	protected function _debug(){
		$response = $this->response;
		unset($response['text']);
		$packet = json_encode($_POST);

		$out = $packet . "\n" . json_encode($response);
		$this->respond($out);
	}
	protected function _help(){
		$lines = array();
		foreach($this->help as $callback => $help_text){
			$commands = array_keys($this->commands, $callback);
			$lines[] = implode('; ', $commands) . ":\n\t" . $help_text;
		}
		$this->respond(implode("\n", $lines));
	}

	// Custom callbacks
	protected function _post_weather(){
		$weather = $this->__get_weather();
		$response  = 'It is ' . strtolower($weather['currently']['summary']) . ".\n";
		$response .= 'The temperature is ' . round($weather['currently']['temperature']) . ' and it feels like ' . round($weather['currently']['apparentTemperature']) . '. ';
		$this->respond($response);
	}

	// Helper functions
	protected function __latlng($location){
		if(empty($location) || GEOCODER_API === '') return array('region' => '', 'latlng' => FORECASTIO_LATLON);

		$query = array(
			'address' => $location,
			'key' => GEOCODER_API,
			'sensor' => 'false'
		);
		$address = curl_init('https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($query));
		curl_setopt($address, CURLOPT_RETURNTRANSFER, true);
		$address = json_decode(curl_exec($address), true);

		if(empty($address['results'])){
			return array('region' => '', 'latlng' => FORECASTIO_LATLON);
		}else{
			return array(
				'region' => $address['results'][0]['address_components'][0]['long_name'] . ', ' . $address['results'][0]['address_components'][2]['short_name'],
				'latlng' => $address['results'][0]['geometry']['location']['lat'] . ',' . $address['results'][0]['geometry']['location']['lng']
			);
		}
	}
	protected function __get_weather($location = ''){
		$location = $this->__latlng($location);
		$weather = curl_init('https://api.forecast.io/forecast/'.FORECASTIO_API.'/'.$location['latlng'].'?units='.FORECASTIO_UNITS);
		curl_setopt($weather, CURLOPT_RETURNTRANSFER, true);
		$weather = json_decode(curl_exec($weather), true);
		$weather['region'] = $location['region'];
		return $weather;
	}
	protected function __build_image($src, $text = null){
		return '<' . $src . '?' . time() . (is_string($text) ? '|' . $text : '') . '>';
	}
	public function __d($a){
		$o = print_r($a, true);
		if(!$o || !$a) $o = var_export($a, true);
		echo '<pre>',htmlentities($o),'</pre>';
	}


}
