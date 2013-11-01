<?php

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class wiewaswie_plugin extends research_base_plugin {
	static function getName() {
		return 'WieWasWie';
	}

	/**
	* Based on a small part of function print_name_record() in /library/WT/Controller/Individual.php
	*/
	static function create_link(WT_Fact $event) {
		if (!$event->canShow()) {
			return false;
		}
		$factrec = $event->getGedcom();
		// Create a dummy record, so we can extract the formatted NAME value from the event.
		$dummy=new WT_Individual(
			'xref',
			"0 @xref@ INDI\n1 DEAT Y\n".$factrec,
			null,
			WT_GED_ID
		);
		$all_names=$dummy->getAllNames();
		$primary_name=$all_names[0];
		
		return $link = 'https://www.wiewaswie.nl/personen-zoeken/zoeken/q/'.str_replace(" ", "+", $primary_name['fullNN']).'/type/documenten';
	}
}