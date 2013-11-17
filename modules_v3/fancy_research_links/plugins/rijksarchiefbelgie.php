<?php

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class rijksarchiefbelgie_plugin extends research_base_plugin {
	static function getName() {
		return 'Rijksarchief Belgi&euml;';
	}
	
	/**
	* Based on function print_name_record() in /library/WT/Controller/Individual.php
	*/
	static function create_link(WT_Fact $event) {					
		return $link = '#';
	}
	
	static function create_sublink(WT_Fact $event) {
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
				
		return $link = array(
			array(
				'title' => 'geboorte',
				'link' => 'http://search.arch.be/nl/zoeken-naar-personen/zoekresultaat/q/persoon_achternaam_t_0/'.$primary_name['surname'].'/q/persoon_voornaam_t_0/'.$givn.'/q/zoekwijze/s?M=0&amp;V=0&amp;O=1&amp;persoon_0_periode_soort=geboorte&persoon_0_periode_geen=0'
			),
			array(
				'title' => 'overlijden',
				'link' => 'http://search.arch.be/nl/zoeken-naar-personen/zoekresultaat/q/persoon_achternaam_t_0/'.$primary_name['surname'].'/q/persoon_voornaam_t_0/'.$givn.'/q/zoekwijze/s?M=0&amp;V=0&amp;O=1&amp;persoon_0_periode_soort=overlijden&persoon_0_periode_geen=0'
			)			
		);	
	}
}