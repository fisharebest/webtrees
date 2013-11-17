<?php

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class familysearch_plugin extends research_base_plugin {
	static function getName() {
		return 'Family Search';
	}
	
	/**
	* Based on function print_name_record() in /library/WT/Controller/Individual.php
	*/
	static function create_link(WT_Fact $event) {
		if (!$event->canShow()) {
			return false;
		}
		$factrec = $event->getGedCom();
		// Create a dummy record, so we can extract the formatted NAME value from the event.
		$dummy=new WT_Individual(
			'xref',
			"0 @xref@ INDI\n1 DEAT Y\n".$factrec,
			null,
			WT_GED_ID
		);
		$all_names=$dummy->getAllNames();
		$primary_name=$all_names[0];
		
		return $link = 'https://familysearch.org/search/record/results#count=20&query=%2Bgivenname%3A%22'
						.rawurlencode($primary_name['givn'])
						.'%22~%20%2Bsurname%3A%22'
						.rawurlencode($primary_name['surname'])
						.'%22~';
	}
	
	static function create_sublink(WT_Fact $event) {
		return false;
	}
}
