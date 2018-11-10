<?php
/**
 * Configuration array.
 *
 * mailbox: @see https://php.net/manual/en/function.imap-open.php
 */

return [
	// IMAP Account 1
	'ACCOUNT_NAME_1' => [
		'mailbox'   => '{mail.example.com:993/imap/ssl/novalidate-cert}INBOX',
		'separator' => '.',
		'username'  => 'IMAP_USERNAME',
		'password'  => 'IMAP_PASSWORD',
		'folders'   => [
			'Spam'               => 'days:5', // Folder name => days to keep
			'Trash'              => 'days:15',
			'Notifications.Home' => 'days:10',
			'Notifications.Work' => 'days:15',
			'Lists.mylist1'      => 'last:100', // Folder name => messages to keep
			'Lists.mylist2'      => 'last:50',
		],
	],

	// IMAP Account 2
	'ACCOUNT_NAME_2' => [
		'mailbox'   => '{mail.example.com:993/imap/ssl/novalidate-cert}INBOX',
		'separator' => '/',
		'username'  => 'IMAP_USERNAME',
		'password'  => 'IMAP_PASSWORD',
		'folders'   => [
			'Spam'               => 'days:15', // Folder name => days to keep
			'Trash'              => 'days:20',
			'Notifications/Home' => 'days:10',
			'Notifications/Work' => 'days:15',
			'Lists/mylist1'      => 'last:100', // Folder name => messages to keep
			'Lists/mylist2'      => 'last:50',
		],
	],

	// IMAP Account 3 ...
];
