PHP Script Updater
==================

This is a PHP function you can embed in your CLI scripts to allow your clients to update said scripts.

The function takes two methods: *target update url* and *an array of options*:

Option Name      | Default Value                                                                   | Description                                                               |
-----------------|---------------------------------------------------------------------------------|---------------------------------------------------------------------------|
current_version  | `'0.0.0'`                                                                       | Version of the current file/script.                                       |
version_regex    | <code>'/define\\(\\s*[\'"]version[\'"]\\s*,\\s*[\'"](.*?)[\'"]\\s*\\)/i</code>  | Regular expression for finding version in target file.                    |
try_run          | `true`                                                                          | Try running downloaded file to ensure it works.                           |
on_event         | empty function                                                                  | Used by updater to notify callee on event changes.                        |
target_file      | `$_SERVER['SCRIPT_FILENAME']`                                                   | The file to be overwritten by the updater.                                |
force_update     | `false`                                                                         | Force local file to be overwritten by remote file regardless of version.  |
try_run_cmd      | `'php -f '` + `target_file`                                                     | Command to be called for verifying that the upgrade is fine.              |