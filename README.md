Remote IMAP Account Folders Maintenance - Retention
=====

Delete the old messages from one or more IMAP accounts based on the given criteria:
- older than a number of days
- maximum number of messages to keep

## Requirements
- PHP >= 5.6
- PHP imap extension

## Installation

- Copy the included imap_maint_config_example.php to imap_maint_config.php and add your IMAP account details there. You can add as many IMAP accounts and folders as you like.

- Keep the sensitive data secure! chmod 0600 the file that contains your IMAP credentials.

- Optionally, if you prefer to keep everything in the same file for simplicity, you can move the configration right into the main script, defining all the options directly in the $accounts array and commenting out the line:

```php
$accounts = require_once __DIR__ . DIRECTORY_SEPARATOR . 'imap_maint_config.php';
```

- For details on the possible ways to configure the "mailbox" parameter, please see [PHP imap_open page](https://php.net/manual/en/function.imap-open.php).

## Running the script
Run daily as a cron job. This script connects to IMAP accounts remotely, so any server you have a shell/crontab access to will do.

## License
This package is released under the MIT License. See the bundled
[LICENSE](https://github.com/sburina/imap-retention/blob/master/LICENSE) file for details.
