<?php

class Forrst_API {

	// Forrst API URL
	var $API_URL = 'http://api.forrst.com/api/v2/users/info';
	
	// Forrst username or user id
	var $user;
	
	public function __construct($user)
	{
		$this->user = $user;
	}
	
	public function get_user_info()
	{
		// Check to see if WP's native function exists, if not use PHP's native function.
		if ( function_exists('wp_remote_get') ) {
			$json = wp_remote_get($this->API_URL . '?username='. $this->user);
			$user_info = @json_decode($json['body'], true);
		} else {
			$json = @file_get_contents($this->API_URL . '?username='. $this->user);
			$user_info = @json_decode($json, true);
		}
		
		return $user_info['resp'];
	}
}