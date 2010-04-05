<?php
/**
 * Outputs calendar events in the iCalendar (RFC 2445 http://www.ietf.org/rfc/rfc2445.txt) format.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Export
 * @version $Id$
 * @TODO This currently generates the file with only descendants of  the root person. We have to retrieve ancestors descendants, aunts, uncles and cousins, nieces nephews and great-aunts/uncles etc.
 * @TODO Add a menu item in the "Other" menu in the individual page to "Export Birthday and Anniversary Events". This should bring the user to a page with options to select events of what relationship to user should be included. Options should be ancestors, descendants, siblings, aunts & uncles, in-laws, cousins etc
 * @TODO Add support to export all events in a calendar (day month or year) in the iCal format. Option to be recurring or one time should be available.
 * @TODO investigate just using the Bennu http://bennu.sourceforge.net/ package, though since we do not need to parse iCalendar files, this will probably not be needed
 * @TODO Format tested in Outlook 2003. Should be validated by testing other iCalendar apps such as iCal on the Mac
 * @TODO use language files
 */

define('WT_SCRIPT_NAME', 'ical.php');
require './includes/session.php';
require_once WT_ROOT.'includes/classes/class_person.php';
require_once WT_ROOT.'includes/classes/class_family.php';

//Basic http auth needed for non browser authentication. If the user is not logged in and fails basic auth, nothing will be returned
basicHTTPAuthenticateUser();

$icalEvents = array(); //array of all ical events

$iCalCalendarType = "lr"; // default to living relatives

if (!empty($_REQUEST["ict"])) $iCalCalendarType = $_REQUEST["ict"]; //iCalCalendarType

if($iCalCalendarType == "lr") { //living relatives
	$pid = strtoupper($_REQUEST["pid"]);
	$indi = Person::getInstance($pid); //get indi
	generateLivingRelativesIcal($indi);
}

//output the iCal
outputIcal();

/**
 * Generate iCal events for all living relatives of an individual.
 * This should include the root's siblings, aunts, uncles, cousins in-laws etc.
 *
 * @param string $person the root person
 * @TODO currently only generates descendants
 */
function generateLivingRelativesIcal(&$person) {
	//move up from indi to grandparents
	//and generate events for 2 level (all root's siblings, aunts, uncles and cousins in that branch
	generateChildDescendancyIcal($person, 2);
	//get root's father and
	//get all descendants (nieces nephews and great-aunts/uncles etc
	//generateChildDescendancyIcal($person, 4);
}

/**
 * Generate iCal events for a child descendancy
 *
 * @param string $pid individual Gedcom Id
 * @param int $depth the descendancy depth to show
 */
function generateChildDescendancyIcal(&$person, $depth) {
	global $personcount;
	global $icalEvents;

	$icalEvents[$person->getXref()] = getIndiBDIcalEvent($person);

	// loop for each spouse
	$sfam = $person->getSpouseFamilies();
	foreach ($sfam as $famid => $family) {
		generateFamilyDescendancyIcal($person, $family, $depth);
	}
}

/**
 * Generate iCalendar events for all living family descendants
 *
 * @param string $pid individual Gedcom Id
 * @param Family $famid family record
 * @param int $depth the descendancy depth to show
 */
function generateFamilyDescendancyIcal(&$person, &$family, $depth) {
	global $GEDCOM, $WT_IMAGE_DIR, $WT_IMAGES, $Dindent, $personcount;
	global $icalEvents;
	if (is_null($family)) return;

	$famrec = $family->getGedcomRecord();
	$famid = $family->getXref();
	$parents = find_parents($famid);
	if ($parents) {
		// spouse id
		$id = $parents["WIFE"];
		if ($id==$person->getXref()) $id = $parents["HUSB"];

		// get marriage info
		$icalEvents[$family->getXref()] = getFamilyAnniversaryIcalEvent($family);

		// get spouse
		$person = Person::getInstance($id);
		$icalEvents[$person->getXref()] = getIndiBDIcalEvent($person);
		// children
		$children = $family->getChildren();
		if ($depth>0) foreach ($children as $childid => $child) {
			generateChildDescendancyIcal($child, $depth-1);
		}
	}
}

/**
 * Outputs the iCalendar
 */
function outputIcal(){
	global $icalEvents;

	echo getIcalHeader();
	$eventList = "";
	foreach ($icalEvents  as $event) {
		$eventList .=  $event;
	}
	echo $eventList;
	echo getIcalFooter();
}

/**
 * Returns a birthday iCalendar event.
 * If there is no date for the event, or the person is not alive, no iCalendar event will be returned
 * @param Person $indi The Person Object
 * @return the birthday iCalendar event.
 */
function getIndiBDIcalEvent($indi){
	if($indi->isDead()){
		return;
	}
	$birthDate = $indi->getBirthDate();
	if (!$birthDate->isOK()){
		return;
	}
	$summary = $indi->getFullName() ."'s Birthday";
	$place = $indi->getBirthPlace();
	$description = "Born on " . $birthDate->Display(false) . ($place==""?"" : "in " .$place) . "\n" . encode_url($indi->getAbsoluteLinkUrl());
 	$iCalRecord = getIcalRecord($birthDate, $summary, $description, encode_url($indi->getAbsoluteLinkUrl()));


 	return $iCalRecord;
}

/**
 * returns a marriage anniversary iCalendar event for a family marriage.
 * If there is no date for the event, or either of the spouses is not alive,
 * no iCalendar event will be returned
 * @param Family $family the Family Object used as the source of the marriage anniversary info
 * @return the marriage anniversary iCalendar event.
 */
function getFamilyAnniversaryIcalEvent($family){
	$anniversaryDate = $family->getMarriageDate();
	if($anniversaryDate ==""){
			return;
	}

	if($family->isDivorced()){
		return;
	}

	$wife = $family->getWife();
	$husband = $family->getHusband();
	if($wife->isDead() || $husband->isDead() ){
		return;
	}
	$anniversaryDate=new GedcomDate($anniversaryDate);

	$summary = "Anniversary of " . $husband->getFullName() . " and " . $wife->getFullName();
	$place = $family->getMarriagePlace() ;
	$description = "Married on " . $anniversaryDate->Display(false) . ($place==""?"" : "in " .$place) . "\n" . encode_url($family->getAbsoluteLinkUrl());
	$iCalRecord = getIcalRecord($anniversaryDate, $summary, $description, encode_url($family->getAbsoluteLinkUrl()));

 	return $iCalRecord;
}

/**
 * creates a single iCalendar formatted event.
 * The events are full day anually recurring events.
 * The events are set to CLASS:CONFIDENTIAL, and should therefore be transparent
 * to other users sharing your calendar (for example the time will show you as
 * available to other Outlook users)
 * @param string $data the data to be formatted
 * @return	the formatted string
 * @TODO Fix &lrm; tags by using proper unicode 200E code. Since the iCalendar does not user HTML, the &lrm; tag should not be used. This should only be an issue when Hebrew dates are used
 */
function getIcalRecord($date, $summary, $description, $URL=""){
	$dtstamp = getIcalTS(); //current TS
	$startDate = getIcalDate($date);
	$endDate = getIcalDate($date, true); //not needed as per RFC2445 spec
	$iCalString = "\r\nBEGIN:VEVENT"
						. "\r\nDTSTAMP:$dtstamp"
						. "\r\nDTSTART;VALUE=DATE:$startDate"
						. "\r\nDTEND;VALUE=DATE:$endDate" //not needed as per RFC2445 spec
						. "\r\n" . formatIcalData("SUMMARY:$summary")
						. "\r\n" . formatIcalData("DESCRIPTION:$description")
						. "\r\nRRULE:FREQ=YEARLY"
						. "\r\nCLASS:CONFIDENTIAL" //CLASS:PRIVATE together with TRANSP:TRANSPARENT can be used as well
						. "\r\nTRANSP:TRANSPARENT" //Not needed if CLASS:CONFIDENTIAL is used, but will not hurt
						. "\r\nUID:".WT_WEBTREES.'-'.generate_guid() //unique ID
						. "\r\nCATEGORIES:".WT_WEBTREES." Events"
					. "\r\n" . formatIcalData("URL:$URL")
					. "\r\nEND:VEVENT";
	return $iCalString;
}

/**
 * Converts a GEDCOM date to an iCalendar format date.
 *
 * @param $gedDate the GEDCOM date
 * @REMOVEDparam $endDate an optional boolean flag indicating that is the end date for the event, and will return the following date
 * @return the iCalendar date in the format 18991230
 * @TODO by the spec, an end date is not needed, but since most applications generate and end date, we should test iCal before the commented out DTEND support is removed is removed
 */
function getIcalDate($gedDate, $endDate=false){
	$date = new GedcomDate($gedDate);
	$date = $date->MinDate();
	$date = $date->convert_to_cal('gregorian');

	$tmp = $date->Format('%Y');
	$tmp.=($date->m) ? $date->Format('%m') : '00';
	$tmp.=($date->d) ? $date->Format('%d') : '00';

	return $tmp;
}

/**
 * returns a timestamp in an iCalendar format
 * @param $time. The optional timestamp parameter. The default parameter is the current timestamp
 */
function getIcalTS($time=""){
	if(empty($time)){
		$time= time();
	}
	return date('Ymd', $time) . 'T' . date('His', $time) . 'Z'; // The 'Z' indicates we're using UTC time
}

/**
 * Sets the http headers to 'Content-type: text/calendar; method=PUBLISH' and returns the iCalendar headers.
 * @return the iCalendar headers.
 */
function getIcalHeader(){
	//header('Content-type: text/plain');
	header('Content-type: text/calendar; method=PUBLISH');
 	header('Content-Disposition: attachment; filename="'.WT_WEBTREES.'.ics"');
	return "BEGIN:VCALENDAR"
			."\r\nVERSION:2.0"
			."\r\nCALSCALE:GREGORIAN"
			."\r\nPRODID:-//".WT_WEBTREES."//".WT_WEBTREES."//EN"
			."\r\nX-WR-CALNAME:".WT_WEBTREES
			."\r\nMETHOD:PUBLISH";
}

/**
 * @return the iCalendar footer
 */
function getIcalFooter(){
	return "\r\nEND:VCALENDAR\r\n";
}

/**
 * format summary and description text properly.
 * Lines must wrap at 75 chars, use \r\n as delimiter, and have a space at the beginning of extra lines
 * @param string $data the data to be formatted
 * @return	the formatted string
 * @TODO Tested with Outlook 2003. Needs testing with other iCalendar apps such as iCal
 */
function formatIcalData($data){
	$data = strip_tags($data);
	$data = unhtmlentities($data); //convert html entities to chars (&quot; &lrm; etc)
	$data = strtr($data, array("\n" => '\\n', '\\' => '\\\\', ',' => '\\,', ';' => '\\;')); //escape special chars as per RFC 2445 spec
	return rfc2445Fold($data);
}

/**
 * Function to fold (wrap) lines at the RFC2445 specified line length of 75.
 * (c) 2005-2006 Ioannis Papaioannou (pj@moodle.org)
 * Released under the LGPL.
 * See http://bennu.sourceforge.net/ for more information and downloads
 *
 * @author Ioannis Papaioannou
 * @return	string the properly folded value
 */
function rfc2445Fold($string) {
 if(strlen($string) <= 75) {
		return $string;
	}
	$retval = '';
	while(strlen($string) > 75) {
		$retval .= substr($string, 0, 75 - 1) . "\r\n" . ' ';
		$string  = substr($string, 75 - 1);
	}
	$retval .= $string;
	return $retval;
}

/**
 * Function to generate proper GUID, Implemented as per the Network Working Group draft on UUIDs and GUIDs
 * (c) 2005-2006 Ioannis Papaioannou (pj@moodle.org)
 * Released under the LGPL.
 * See http://bennu.sourceforge.net/ for more information and downloads
 *
 * @author Ioannis Papaioannou
 * @return	string the generated UID
 * @TODO This function should be used to generate unique IDs for the mail functionality as well, and should probably be moved there
 */
function generate_guid() {
	// These two octets get special treatment
	$time_hi_and_version       = sprintf('%02x', (1 << 6) + mt_rand(0, 15)); // 0100 plus 4 random bits
	$clock_seq_hi_and_reserved = sprintf('%02x', (1 << 7) + mt_rand(0, 63)); // 10 plus 6 random bits

	// Need another 14 random octects
	$pool = '';
	for($i = 0; $i < 7; ++$i) {
		$pool .= sprintf('%04x', mt_rand(0, 65535));
	}

	// time_low = 4 octets
	$random  = substr($pool, 0, 8).'-';

	// time_mid = 2 octets
	$random .= substr($pool, 8, 4).'-';

	// time_high_and_version = 2 octets
	$random .= $time_hi_and_version.substr($pool, 12, 2).'-';

	// clock_seq_high_and_reserved = 1 octet
	$random .= $clock_seq_hi_and_reserved;

	// clock_seq_low = 1 octet
	$random .= substr($pool, 13, 2).'-';

	// node = 6 octets
	$random .= substr($pool, 14, 12);

	return $random;
}

?>
