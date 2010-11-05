<?php

require '../Bitfield.php';
require '../BitfieldMapper.php';

class UserPrefs extends BitfieldMapper
{
	protected $is_admin;
	protected $is_banned;
	protected $agreed_to_terms;
	protected $gold_member;
	protected $pref_autologin;
	protected $pref_show_ads;
	protected $pref_newsletter;
	protected $pref_chat_enabled;
}

// Writes some random UserPref entries to disk.
function generate_data($entries=10000)
{
	$i = 0;
	$buffer = '';
	for($i=0; $i < $entries; $i++) {
		$userprefs = new UserPrefs;
		$userprefs->is_admin = rand(0,1);
		$userprefs->is_banned = rand(0,1);
		$userprefs->agreed_to_terms = rand(0,1);
		$userprefs->gold_member = rand(0,1);
		$userprefs->pref_autologin = rand(0,1);
		$userprefs->pref_show_ads = rand(0,1);
		$userprefs->pref_newsletter = rand(0,1);
		$userprefs->pref_chat_enabled = rand(0,1);
		$buffer .= $userprefs->toBinary();
	}
	
	if ($fp = fopen(__DIR__.'/example.data', 'wb+')) {
		fwrite($fp, $buffer);
		fclose($fp);
	}
	unset($buffer);
}

// Reads UserPref entries back into UserPref objects.
function read_data_entries()
{
	if ($fp = fopen(__DIR__.'/example.data', "rb")) {
		$i = 1;
		while(!feof($fp)) {
			
			// Unsigned long types are kind of huge. But I'd
			// like a reasonable default for now. if you need
			// less, hack the class.
			$read = fread($fp, 4);
			
			// Skip cruft at end of file.
			if (strlen($read) < 4) {
				continue;
			}
			
			$data = unpack('V', $read);
			$item = new UserPrefs($data[1]);
			
			printf(str_repeat("-", 50)."\n");
			printf("    Is Admin: %s\n", $item->is_admin?'Y':'N');
			printf("   Is Banned: %s\n", $item->is_banned?'Y':'N');
			printf("Agreed Terms: %s\n", $item->agreed_to_terms?'Y':'N');
			printf(" Gold Member: %s\n", $item->gold_member?'Y':'N');
			printf("   Autologin: %s\n", $item->pref_autologin?'Y':'N');
			printf("    Show Ads: %s\n", $item->pref_show_ads?'Y':'N');
			printf("  Newsletter: %s\n", 
				$item->pref_newsletter?'Y':'N');
			printf("Chat Enabled: %s\n",
				$item->pref_chat_enabled?'Y':'N');
			printf("\n");
		}
		fclose($fp);
		return;
	}
	throw new Exception("Failed to open example.data");
}

echo("Writing random data...\n");
generate_data(1000);
echo("Reading entries...\n");
read_data_entries();
echo("Done.\n");
exit;

// Example of how to use Bitfield directly:

$field = new Bitfield;
$field->addOption('USER_IS_ADMIN');
$field->addOption('USER_IS_BANNED');
$field->addOption('USER_IS_ADVERTISER');
$field->addOption('PREF_AUTOLOGIN');
$field->addOption('PREF_SHOW_ADS');

$field->setBitfield(USER_IS_ADMIN|PREF_AUTOLOGIN|PREF_SHOW_ADS);

if ($field->isEnabled(USER_IS_ADMIN)) {
	echo("Hello sir!\n");
	if ($field->isEnabled(PREF_AUTOLOGIN)) {
		echo("Autologin enabled.\n");
	}
	if ($field->isEnabled(USER_IS_ADVERTISER)) {
		echo("Gimme some offers.\n");
	}
} else {
	echo("Meh, what do you want?\n");
}
