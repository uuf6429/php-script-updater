PHP Script Updater
==================

This is a PHP function you can embed in your CLI scripts to allow your clients to update said scripts.

Intro and Reason
----------------

I ended up writing lots of small utility scripts in PHP which I end up publishing somewhere (ex; github).
It is very tedious to update these scripts manually and keep track of them. During one particular occasion, I had to update a particular script several times a day.
That's when I wrote this update functionality right into the script. Happens I ended up needing this functionality in another script...that's when I saw the opportunity to write the functionality as a separate entity.

Usage and Options
-----------------

Copy & paste the function into the script you want to make updatable (or as a separate file to include in your script) and call the function when you want to run the update!

The function takes two parameters: *target update url* and *an array of options*:

| Option Name      | Default Value                                                                   | Description                                                               |
|------------------|---------------------------------------------------------------------------------|---------------------------------------------------------------------------|
| current_version  | `'0.0.0'`                                                                       | Version of the current file/script.                                       |
| version_regex    | `/define\\(\\s*[\'"]version[\'"]\\s*,` `\\s*[\'"](.*?)[\'"]\\s*\\)/i`           | Regular expression for finding version in target file.                    |
| try_run          | `true`                                                                          | Try running downloaded file to ensure it works.                           |
| on_event         | empty function                                                                  | Used by updater to notify callee on event changes.                        |
| target_file      | `$_SERVER['SCRIPT_FILENAME']`                                                   | The file to be overwritten by the updater.                                |
| force_update     | `false`                                                                         | Force local file to be overwritten by remote file regardless of version.  |
| try_run_cmd      | `'php -f '` + `target_file`                                                     | Command to be called for verifying that the upgrade is fine.              |

Example of Use
--------------

The script itself contains an example of how the function is used [starting on line 75](https://github.com/uuf6429/php-script-updater/blob/master/update_script.php#L75).

For your convenience, here's another example (much simpler than the one in the script):

	<?php

		define('VERSION', '1.2.4');

		update_script(
			'http://mywebsite.com/thescript.php.txt?nc='.mt_rand(),
			array( 'current_version' => VERSION )
		);

	?>