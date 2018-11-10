#!/usr/bin/env php
<?php
/**
 * IMAP folders maintenance - retention
 * (C) 2018 Sinisa Burina <sburina@gmail.com>
 *
 * Purge the old messages from one or more IMAP accounts based on the given criteria
 * (see imap_maint_config_example.php).
 *
 * NOTE: The script will not remove starred (flagged) messages.
 *
 * Run daily as a cron job on any server or workstation.
 */

/**
 * @var array $accounts
 *
 * If you prefer to keep everything in the same file for simplicity, you can move
 * the configration right here, defining all the options directly in the $accounts
 * array and commenting out the line below.
 */
$accounts = require_once __DIR__ . DIRECTORY_SEPARATOR . 'imap_maint_config.php';

/**
 * Nothing to edit below this comment
 */

set_time_limit(600);
ini_set('display_errors', 0);
error_reporting(NAN);

foreach ($accounts as $account => $data) {
	processAccount($account, $data);
}


/**
 * @param string $account // Account name
 * @param array  $data    // Account data
 */
function processAccount($account, $data)
{
	say("* Connecting to the IMAP account: " . $account);
	$imap_stream = imap_open($data['mailbox'], $data['username'],
		$data['password'], null, 1);
	if ($imap_stream) {
		say("--- Trimming the IMAP account: " . $account);
		foreach ($data['folders'] as $folder => $pinfo) {
			list($criteria, $val) = explode(':', $pinfo);
			switch ($criteria) {
				case 'days':
				default:
					purgeFolderByAge($imap_stream, $data['mailbox'],
						$data['separator'], $folder, $val);
					break;
				case 'last':
					purgeFolderByNumber($imap_stream, $data['mailbox'],
						$data['separator'], $folder, $val);
					break;
			}
		}
		imap_close($imap_stream);
	} else {
		say("!!! ERROR - Could not connect to the IMAP server. Check your credentials.");
	}
	say("");
}


/**
 * @param $imap_stream
 * @param $mailbox
 * @param $separator
 * @param $folder
 * @param $days
 */
function purgeFolderByAge($imap_stream, $mailbox, $separator, $folder, $days)
{
	$eFolder = imap_utf7_encode($folder);
	if (imap_reopen($imap_stream, $mailbox . $separator . $eFolder)) {
		$imap_date = date("j M Y", strtotime("-" . $days . " days"));
		say("--> Searching for messages older than " . $imap_date . " in folder " . $folder);
		$to_del = imap_search($imap_stream, 'UNFLAGGED BEFORE "' . $imap_date . '"', SE_UID);
		if ($to_del) {   // Array or null
			say("*** Found " . sizeof($to_del) . " messages to delete");
			$status = imap_status($imap_stream, $mailbox . $separator . $eFolder, SA_ALL);
			say("??? Number of messages before deletion: " . $status->messages);
			foreach ($to_del as $uid) {
				imap_delete($imap_stream, $uid, FT_UID);
			}
			imap_expunge($imap_stream);
			$status = imap_status($imap_stream, $mailbox . $separator . $eFolder, SA_ALL);
			say("??? Number of messages after deletion: " . $status->messages);
		} else {
			say("<-- No messages to delete in this IMAP folder.");
		}
	} else {
		say("!!! ERROR: Could not open IMAP folder " . $folder);
	}
}

/**
 * @param $imap_stream
 * @param $mailbox
 * @param $separator
 * @param $folder
 * @param $num
 */
function purgeFolderByNumber($imap_stream, $mailbox, $separator, $folder, $num)
{
	$eFolder = imap_utf7_encode($folder);
	if (imap_reopen($imap_stream, $mailbox . $separator . $eFolder)) {
		say("--> Looking for messages that exceed the maximum of " . $num . " in folder " . $folder);
		$status = imap_status($imap_stream, $mailbox . $separator . $eFolder, SA_ALL);
		if ($status->messages > $num) {
			say("*** Found " . ($status->messages - $num) . " messages to delete, keeping the last " . $num);
			say("??? Number of messages before deletion: " . $status->messages);
			for ($i = 1; $i <= ($status->messages - $num); $i ++) {
				$overview = imap_fetch_overview($imap_stream, (string)$i)[0];
				if ($overview->flagged == 0) {
					imap_delete($imap_stream, $i);
				} else {
					say("<-- The message $i is flagged, skipping...");
				}
			}
			imap_expunge($imap_stream);
			$status = imap_status($imap_stream, $mailbox . $separator . $eFolder, SA_ALL);
			say("??? Number of messages after deletion: " . $status->messages);
		} else {
			say("<-- No messages to delete in this IMAP folder.");
		}
	} else {
		say("!!! ERROR: Could not open IMAP folder " . $folder);
	}
}

function say($string) { echo $string . PHP_EOL; }
