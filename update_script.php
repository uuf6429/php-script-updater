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
			'try_run' => true,							// Try running downloaded file to ensure it works.
			'on_event' => create_function('', ''),		// Used by updater to notify callee on event changes.
			'target_file' => __FILE__,					// The file to be overwritten by the updater.
			'force_update' => false,					// Force local file to be overwritten by remote file regardless of version.
		), (array)$options);
		$notify = $options['on_event'];
		$next_version = null;
		static $intentions = array(-1=>'fail',0=>'ignore',1=>'update');
		// process
		$notify('start');
		if(!rename($options['target_file'], $options['target_file'].'.bak'))
			$notify('warn', array('reason'=>'Backup operation failed', 'target'=>$options['target_file']));
		if(!($data = file_get_contents($update_url)))
			return $notify('error', array('reason'=>'File download failed', 'target'=>$update_url)) && false;
		if(!preg_match($options['version_regex'], $data, $next_version))
			return $notify('error', array('reason'=>'Could not determine version of target file', 'target'=>$data, 'result'=>$next_version)) && false;
		if(!($next_version = array_pop($next_version)))
			return $notify('error', array('reason'=>'Version of target file is empty', 'target'=>$data, 'result'=>$next_version)) && false;
		$v_diff = version_compare($next_version, $options['current_version'], $next_version);
		$should_fail = $notify('version_check', array('intention'=>$intentions[$v_diff], 'curr_version'=>$options['current_version'], 'next_version'=>$next_version));
		if($should_fail === false)
			return $notify('error', array('reason'=>'Update cancelled by user code')) && false;
		if($v_diff === 0 && !$options['force_update'])
			return $notify('warn', array('reason'=>'Update not required')) && false;
		if($v_diff === -1 && !$options['force_update'])
			return $notify('warn', array('reason'=>'Local file is newer than remote one', 'curr_version'=>$options['current_version'], 'next_version'=>$next_version)) && false;
		if(!file_put_contents($options['target_file'], $data))
			return $notify('error', array('reason'=>'Failed writing to file', 'target'=>$options['target_file'])) && false;
		if($options['try_run']){
			
		}
		if(!unlink($options['target_file'].'.bak'))
			$notify('warn', array('reason'=>'Cleanup operation failed', 'target'=>$options['target_file'].'.bak'));
		$notify('stop');
	}
	
	
	
	
	
	/**
	 * The code below is a sample of how the function is to be used.
	 */
	
	define('VERSION', '0.0.1');
	
	// no web access pls!
	if(isset($_SERVER['SERVER_NAME']) || !isset($argv)){
		echo 'This is a shell script, not a web service.'.PHP_EOL;
		exit(1);
	}
	
	// HANDY FUNCTIONS
	
	function write_ln($message){
		fwrite(STDOUT, $message . PHP_EOL);
	}
	
	function read_ln(){
		$result = '';
		while(($char = fread(STDIN, 1)) != chr(10))$result .= $char;
		return $result;
	}
	
	function error_ln($message){
		fwrite(STDOUT, $message . PHP_EOL);
	}
	
	function event_handler($event, $args=array()){
		if($event=='error'){
			error_ln($event.': '.json_encode($args));
			if(!defined('IS_ERROR'))define('IS_ERROR', true);
		}else{
			write_ln($event.': '.json_encode($args));
		}
	}
	
	// MAIN CLI CODE
	
	switch((isset($argv) && isset($argv[1])) ? $argv[1] : ''){
		case 'version':
			date_default_timezone_set('Europe/Malta');
			write_ln(basename(__FILE__, '.php').' '.VERSION);
			write_ln('Copyright (c) 2013-'.date('Y').' Christian Sciberras');
			exit(0);
		case 'update':
			update_script(
				'https://raw.github.com/uuf6429/php-script-updater/master/update_script.php',
				array(
					'current_version' => VERSION,
					'version_regex' => '',
					'try_run' => true,
					'on_event' => 'event_handler',
				)
			);
			exit(defined('IS_ERROR') ? 1 : 0);
		case '': case 'help':
			write_ln('Usage: '.basename(__FILE__, '.php').' help      This help screen');
			write_ln('       '.basename(__FILE__, '.php').' update    Show script version');
			write_ln('       '.basename(__FILE__, '.php').' version   Updates script file');
			exit(0);
		default:
			error_ln('Could not understand option "'.$argv[1].'", see "'.basename(__FILE__, '.php').' help" for usage details.');
			exit(1);
	}
	