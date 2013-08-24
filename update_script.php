<?php

	/**
	 * Attempts to update current file from URL.
	 * @param string $update_url Target URL to read updates from.
	 * @param array|object $options Array of options (see docs for more info).
	 */
	function update_script($update_url, $options){
		// initialize
		$options = array_merge(array(
			'current_version' => '0.0.0',				// Version of the current file/script.
			'version_regex' => '',						// Regular expression for finding version in target file.
			'try_run' => false,							// Try running downloaded file to ensure it works.
			'on_event' => create_function('', ''),		// Used by updater to notify callee on event changes.
		), (array)$options);
		$notify = $options['on_event'];
		// process
		$notify('start');
		$notify('stop');
	}
	
	
	
	
	
	/**
	 * The code below is a sample of how the function is to be used.
	 */
	
	define('VERSION', '0.1.12');
	
	// no web access pls!
	if(isset($_SERVER['SERVER_NAME']) || !isset($argv)){
		echo 'This is a shell script, not a web service.'.PHP_EOL;
		exit(1);
	}
	
	$switch = (isset($argv) && isset($argv[1])) ? $argv[1] : '';
	
	function write_ln($message){
		echo $message . PHP_EOL;
	}
	
	function event_handler($event, $args=array()){
		write_ln($event.': '.json_encode($args));
		// if an error happened, take note
		if($event=='error' && !defined('IS_ERROR'))define('IS_ERROR', true);
	}
	
	switch($switch){
		case '-v': case 'version':
			write_ln(basename(__FILE__, '.php').' '.VERSION);
			write_ln('Copyright (c) 2013-'.date('Y').' Christian Sciberras');
			exit(0);
		case '-u': case 'update':
			update_script(
				'https://raw....',
				array(
					'current_version' => VERSION,
					'version_regex' => '',
					'try_run' => true,
					'on_event' => 'event_handler',
				)
			);
			exit(defined('IS_ERROR') ? 1 : 0);
		default:
			write_ln('Usage: '.basename(__FILE__, '.php').' --update    Shows script version');
			write_ln('       '.basename(__FILE__, '.php').' --version   Updates script file');
			exit(0);
	}
	