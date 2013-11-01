<?php

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class dsrotterdam_plugin extends research_base_plugin {
	static function getName() {
		return 'Digitale Stamboom Rotterdam';
	}

	/**
	* Based on a small part of function print_name_record() in /library/WT/Controller/Individual.php
	*/
	static function create_link(WT_Fact $event) {
		if (!$event->canShow()) {
			return false;
		}		
		return $link = 'http://rotterdam.digitalestamboom.nl/search.aspx?lang=nl';
	}
}