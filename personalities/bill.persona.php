<?php

class billpersona extends slackbot{

	function __construct(){
		$this->response['username'] = 'Bill Murray';
		$this->response['icon_emoji'] = ':classy-bill:';

		$this->register_callback(array('wiki', 'random wikipedia article', 'random wiki'), '_post_random_wiki_page');
		$this->register_callback(array('space', 'how many people are in space'), '_post_space');
		$this->register_callback(array('food', 'food truck', 'food trucks'), '_post_food');
		$this->register_callback(array('test'), '_post_test');
		$this->register_callback(array('waki', 'how'), '_post_waki_how');

		$this->help = array(
			'_post_random_wiki_page' => 'Pattern: wiki <hide>. Posts random wiki link. If <hide> is specified, the link is obscured.',
			'_post_waki_how' => 'Pattern: waki OR waki <How...> OR How <...>. Posts a helpful how-to guide on a random subject or one queried.'
		);
	}

	// Command callbacks
	protected function _bad_command($args = array()){
		$_POST['func_args'] = $args;
		$this->respond(json_encode($_POST));
	}
	protected function _post_random_wiki_page($args = array()){
		$c = curl_init('http://en.wikipedia.org/wiki/Special:Random');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($c);
		$info = curl_getinfo($c);

		if($args[0] == 'hide') $this->respond('<' . $this->__shorten_link($info['redirect_url'] . '|Click to see!>'));
		else $this->respond('<' . $info['redirect_url'] . '>');
	}
	protected function _post_space(){
		$json = json_decode(file_get_contents('http://howmanypeopleareinspacerightnow.com/space.json'), TRUE);
		$this->respond('There are currently '. $json['number'] .' people in space right now.');
	}
	protected function _post_test(){
		$this->respond("Line one\nLine two?");
	}
	protected function _post_food($args = array()){
		$city = (empty($args[0])) ? 'ottawa' : strtolower($args[0]);
		$c  = curl_init('data.streetfoodapp.com/1.1/schedule/' . $city . '/');
		curl_setopt($c,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($c, CURLOPT_USERAGENT, 'slackbot');
		$d = curl_exec($c);
		$data = json_decode($d, TRUE);
		$open = strtotime('today midnight');
		$close = strtotime('today midnight') + 86400;
		$response = "Trucks open today: \n";
		foreach ($data['vendors'] as $vendor) {
			foreach ($vendor['open'] as $day) {
				if($day['start'] >= $open && $day['start'] <= $close) {
					$response .= $vendor['name'] . ' at ' . $day['display'] . ' from ' . date('g:ia', $day['start']) . ' to ' . date('g:ia', $day['end']) . "\n";
				}
			}
		}

		$this->respond($response);
	}

	protected function _post_waki_how($topic = array()){
		require_once 'personalities/libraries/simple_html_dom.php';

		$response = array('text' => '', 'attachments' => array());

		$needs = array();
		$warnings = array();
		$steps = array();
		$images = array();

		if(empty($topic)){
			$page = $this->_get_page();

			$heading = $page->find('.firstHeading a', 0);
			$heading = $heading->text();
		}else if(property_exists($this, 'active_command')){

			if($this->active_command === 'how'){
				$heading = 'How ' . implode(' ', $topic);
			}else{
				$heading = ucfirst(implode(' ', $topic));
			}

			if($heading{strlen($heading)-1} === '?') $heading = substr($heading, 0, strlen($heading) -1);
		}

		$response['text'] = '*' . $heading . '*';
		$response['mrkdwn'] = true;

		sleep(1);

		for($i = 0; $i < 3; $i++){
			do{
				$page = $this->_get_page();
				$new_needs = $this->_get_list($page, 'thingsyoullneed', 2);
			}while(empty($new_needs));

			$needs = array_merge($needs, $new_needs);
		}

		sleep(1);

		do{
			$page = $this->_get_page();
			$new_warnings = $this->_get_list($page, 'warnings', 1);
			$warnings = array_merge($warnings, $new_warnings);
		}while(empty($new_warnings));

		for($i = 0; $i < 4; $i++){
			$page = $this->_get_page();
			do{
				$new_steps = $this->_get_steps($page);
				if(!empty($new_steps)){
					$selected_step = (empty($steps)) ? 0 : mt_rand(0, count($new_steps) - 1);
					if($i === 3) $selected_step = count($new_steps) - 1;
					$steps[] = $new_steps[$selected_step];
				}
			}while(empty($new_steps));
		}

		shuffle($needs);
		shuffle($warnings);

		/*
		$response['attachments'][] = array(
			'color' => '#BE8E13',
			'pretext' => 'You will need:',
			'text' => '• ' . implode("\n• ", $needs)
		);

		if(!empty($warnings)){
			$response['attachments'][] = array(
				'color' => '#c22',
				'pretext' => 'Warnings:',
				'text' => '• ' . implode("\n• ", $warnings)
			);
		}

		foreach($steps as $delta => $step){
			$response['attachments'][] = array(
				'color' => '#22CC6D',
				'text' => '*' . ($delta+1) . '. ' . $step . '*',
				'image_url' => $images[$delta],
				'mrkdwn_in' => array('text')
			);
		}
		*/


		// $response['text'] .= "\n\n*You will need:*\n";
		// foreach($needs as $need) $response['text'] .= '• ' . $need . "\n";

		$response['text'] .= "\n\n";
		foreach($steps as $delta => $step){
			$response['text'] .= 'Step ' . ($delta+1) . ': ' . $step . "\n";
		}

		if(!empty($warnings)){
			$response['text'] .= "\n*Warning!*\n";
			foreach($warnings as $warning) $response['text'] .= '• ' . $warning . "\n";
		}

		/*
		$c = curl_init('https://hooks.slack.com/services/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');
		curl_setopt_array($c, array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => 'payload=' . json_encode($response)
		));
		curl_exec($c);
		*/

		$response['text'] = str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $response['text']);

		$this->respond($response);
	}

	// Helper functions

	protected function __shorten_link($link = ''){
		$c = curl_init('http://is.gd/create.php?format=simple&url=' . $link);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		return curl_exec($c);
	}

	protected function _get_page(){
		$page = false;

		while($page === false){
			$page = file_get_html('http://www.wikihow.com/Special:Randomizer');
		}

		return $page;

	}
	protected function _get_list($page, $type, $max = 2){

		$needs = array();
		$needs_section = $page->getElementById($type);
		if(!is_null($needs_section)){

			$all_needs = $needs_section->find('ul li');
			$needs_count = count($all_needs);
			if($needs_count > $max) $needs_count = $max;

			for($i = 0; $i < $needs_count; $i++){
				$available_needs = count($all_needs);
				if(!$available_needs) break;
				$selection = mt_rand(0, $available_needs-1);
				$needs[] = $all_needs[$selection]->text();
				unset($all_needs[$selection]);
				$all_needs = array_values($all_needs);
			}

		}

		return $needs;

	}
	protected function _get_steps($page){

		$steps_list = array();
		$steps = $page->find('.step b');
		foreach($steps as $step) $steps_list[] = $step->text();
		return $steps_list;

	}
	protected function _get_images($page){

		$images_list = array();
		$images = $page->find('div.mwimg');
		foreach($images as $img){
			$images_list[] = $img->find('img', 0)->src;
		}

		return $images_list;

	}
}
