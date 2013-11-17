<?php

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class voorouder_plugin extends research_base_plugin {
	static function getName() {
		return 'Voorouder.nl';
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
		
		$givn   = $primary_name['givn'];
		$surn   = $primary_name['surn'];
		if($surn != $primary_name['surname']) {
			$prefix = substr($primary_name['surname'], 0, strpos($primary_name['surname'], $surn) - 1);
		}
		else {
			$prefix = "";
		}
				
		return $link = 'http://www.voorouder.nl/genealogie/search.php?mybool=AND&amp;nr=50&amp;showdeath=yes&amp;mylastname='.$primary_name['surname'].'&amp;lnqualify=equals&amp;myfirstname='.rawurlencode($givn).'&amp;fnqualify=contains';
	}
	
	static function create_sublink(WT_Fact $event) {
		return false;
	}
}