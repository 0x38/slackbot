<?php

class redditpersona extends slackbot{
	
	function __construct(){
		$this->response['username'] = 'Reddit';
		$this->response['icon_emoji'] = ':/r/:';

		$this->register_callback(array('random'), '_post_random_picture');
	}

	// Command callbacks
	protected function _post_random_picture($subreddit = array()){
		$subreddit = array_pop($subreddit);
		if(strpos($subreddit, '/r/') !== false) $subreddit = substr($subreddit, 3);

		if(isset($GLOBALS['reddit_blacklist']) && in_array($subreddit, $GLOBALS['reddit_blacklist'])) $this->respond('Aw hell naw.');

		$subreddit = strtolower($subreddit);

		$c  = curl_init('http://www.reddit.com/r/' . $subreddit .'/.json');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$posts = json_decode(curl_exec($c), true);
		$posts = $posts['data']['children'];
		
		do{
			$post_delta = mt_rand(0, count($posts) - 1);
			$post = $posts[$post_delta];
			unset($posts[$post_delta]);
		}while($post['kind'] !== 't3' || $post['data']['domain'] !== 'i.imgur.com' && !empty($post['data']['selftext']) && $post['data']['over_18'] && !empty($posts));
		
		// Try to get direct link for imgur
		if($post['data']['domain'] === 'imgur.com'){
			preg_match('#/[a-zA-Z0-9]+$#', $post['data']['url'], $code);
			if(!empty($code)) $post['data']['url'] = 'http://i.imgur.com/' . $code[0] . '.jpg';
		}

		$this->respond($this->__build_image($post['data']['url'], $post['data']['title']));
	}

}
