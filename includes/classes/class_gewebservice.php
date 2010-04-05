<?php
/**
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
 * @subpackage Tools
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_GEWEBSERVICE_PHP', '');

require_once WT_ROOT.'includes/classes/class_grampsexport.php';

class GEWebService extends GrampsExport
{
	var $eRoot;
/**
 * Creates a GrampsXML for a person, if that person exists in the given GEDCOM record
 * @param $personRec - The GEDCOM record where the person can be found
 * @param $personID - The id of the person record
 * @return GrampsXML for the person
 */
function create_person($personRec = "", $personID = "") {
		global $eRoot;
		if (!isset($this->dom))
		{
	     $this->dom = new DomDocument("1.0", "UTF-8");
		 $this->dom->formatOutput = true;

		 $eRoot = $this->dom->createElementNS("http://gramps-project.org/xml/1.1.0/", "database");
		 $eRoot = $this->dom->appendChild($eRoot);
		 $this->eEvents = $this->dom->createElement("events");
		 $this->eEvents = $eRoot->appendChild($this->eEvents);
		 $this->ePlaces = $this->dom->createElement("places");
		 $this->ePlaces = $eRoot->appendChild($this->ePlaces);

		}
			$ePerson_new = $this->dom->createElement("person");

			$ePerson=$eRoot->appendChild($ePerson_new);

			$ePerson->setAttribute("id", $personID);
			$ePerson->setAttribute("handle",$personID);
			$ePerson->setAttribute("change", time());

			$eGender = $this->dom->createElement("gender");
			$eGender = $ePerson->appendChild($eGender);
			if (($gender = get_gedcom_value("SEX", 1, $personRec)) != null)
				$etGender = $this->dom->createTextNode($gender);
			else
				$etGender = $this->dom->createTextNode("U");

			$num = 1;
			$etGender = $eGender->appendChild($etGender);

if (($nameRec = get_sub_record(1, "1 NAME", $personRec)) != null) {
				//creates name
				$eName = $this->dom->createElement("name");
				$eName->setAttribute("type", "Birth Name");

				$givn = get_gedcom_value("GIVN", 2, $nameRec);
				$surn = get_gedcom_value("SURN", 2, $nameRec);
				//-- if no GIVN/SURN sub records then get the names from the 1 NAME line
				if (empty($surn) || empty($givn)) {
					$name = get_gedcom_value("NAME", 1, $nameRec);
					if (!empty($name)) {
						$nparts = explode('/', $name);
						$givn = trim($nparts[0]);
						if (count($nparts)>1) $surn = trim($nparts[1]);
						if (count($nparts)>2) $nnsfx = trim($nparts[2]);
					}
				}
				if (empty($surn))
					$surn = i18n::translate('unknown');
				if (empty($givn))
					$givn = i18n::translate('unknown');

				$eFirstName = $this->dom->createElement("first");
				$etFirstName = $this->dom->createTextNode($givn);
				$etFirstName = $eFirstName->appendChild($etFirstName);
				$eFirstName = $eName->appendChild($eFirstName);

				$eLastName = $this->dom->createElement("last");
				$etLastName = $this->dom->createTextNode($surn);
				$etLastName = $eLastName->appendChild($etLastName);
				$eLastName = $eName->appendChild($eLastName);
				$eName = $ePerson->appendChild($eName);

				if (!empty($nnsfx) || (($nsfx = get_gedcom_value("NSFX", 2, $nameRec)) != null)) {
					$eSuffix = $this->dom->createElement("suffix");
					if (empty($nsfx)) $nsfx = $nnsfx;
					$etSuffix = $this->dom->createTextNode($nsfx);
					$etSuffix = $eSuffix->appendChild($etSuffix);
					$eSuffix = $eName->appendChild($eSuffix);
				}

				//retrieves name prefix
				if (($npfx = get_gedcom_value("NPFX", 2, $nameRec)) != null) {
					$eTitle = $this->dom->createElement("title");
					$etTitle = $this->dom->createTextNode($npfx);
					$etTitle = $eTitle->appendChild($etTitle);
					$eTitle = $eName->appendChild($eTitle);
				}

				//retrieves the nickname
				if (($nick = get_gedcom_value("NICK", 2, $nameRec)) != null) {
					$eNick = $this->dom->createElement("nick");
					$etNick = $this->dom->createTextNode($nick);
					$etNick = $eNick->appendChild($etNick);
					$eNick = $ePerson->appendChild($eNick);
				}

				//creates note
				if (($nameNote = get_sub_record(2, "2 NOTE", $nameRec)) != null) {
					$this->create_note($eName, $nameNote, 2);
				}

				//creates SourceRef
				$num = 1;
				while (($nameSource = get_sub_record(2, "2 SOUR", $nameRec, $num)) != null) {
					$this->create_sourceref($eName, $nameSource, 2);
					$num++;
				}

			}

			foreach ($this->eventsArray as $event) {
				$this->create_event_ref($ePerson, $personRec, $event);
			}

			$this->create_lds_event($personRec, "baptism", "BAPL", $ePerson);
			$this->create_lds_event($personRec, "endowment", "ENDL", $ePerson);
			$this->create_lds_event($personRec, "sealed_to_parents", "SLGC", $ePerson);

			while (($nameSource = get_sub_record(1, "1 OBJE", $personRec, $num)) != null) {

				$this->create_mediaref($ePerson, $nameSource, 1,0);
				$num++;
			}

			/* This creates the family relation for a person, to link them to
			 * the family they are a child in and to link them to the family
			 * where they are a spouse. These relations will only be included
			 * if the family is also in the clippings cart. Otherwise, the relations
			 * are simply left out of the XML file.
			 *
			 *
			*create_fam_relation($ePerson,$personRec,"FAMC");
			*create_fam_relation($ePerson,$personRec,"FAMS");
			*/
			if (($note = get_sub_record(1, "1 NOTE", $personRec)) != null) {
				$this->create_note($ePerson, $note, 1);
			}
			$num = 1;
			while (($sourcerefRec = get_sub_record(1, "1 SOUR", $personRec, $num)) != null) {
				$this->create_sourceref($ePerson, $sourcerefRec, 1,0);
				$num++;
			}
			return $this->dom->saveXML();
		}

	/**
	* Creates the lds_ord element and appends the correct information depending
	* on the type of lds_ord (Endowment, Sealing, Baptism). If there is a sealing,
	* the function will search if the family is in the clippings cart and if the
	* family is created or not. If the family is not created yet, it will be created
	* and added to the DOMDocument
	*
	* @param $indirec - The full INDI GEDCOM record of the person the lds_ord is being created
	* @param $eventName - the name of the LDS event (Baptism, Sealing, Endowment, etc...)
	* @param $eventABV - the event abbreviation in the GEDCOM (ie. SLGC, BAPL, ENDL)
	* @param $eParent - The parent element the lds event is attached to
	*/
	function create_lds_event($indirec, $eventName, $eventABV, $eParent) {
		global $ePerson, $TEMPLE_CODES, $clipping, $eRoot;
		require_once WT_ROOT.'includes/classes/class_person.php';
		if (($hasldsevent = get_sub_record(1, "1 " . $eventABV, $indirec)) != null) {

			// Create <lds_ord> and attaches the type attribute
			$eLdsEvent = $this->dom->createElement("lds_ord");
			$eLdsEvent->setAttribute("type", $eventName);

			if (($dateRec = get_sub_record(1, "2 DATE", $hasldsevent)) != null)
				$this->create_date($eLdsEvent, $dateRec, 2);

			// Create <temple>, this element is common with all lds ords
			if (($temple = get_gedcom_value($eventABV . ":TEMP", 1, $indirec)) != null) {
				$eTemple = $this->dom->createElement("temple");
				$eTemple->setAttribute("val", $temple);
				$eTemple = $eLdsEvent->appendChild($eTemple);
			}

			if (($place = get_gedcom_value($eventABV . ":PLAC", 1, $indirec)) != null) {
				$hlink = $this->query_dom("./places/placeobj[@title=\"$place\"]/@handle");
				if ($hlink == null) {
					$hlink = $this->generateHandle();
					$this->create_placeobj($place, $hlink);
					$this->create_place($eLdsEvent, $hlink);
				} else {
					$this->create_place($eLdsEvent, $hlink);
				}
			}

			// Check to see if the STAT of the ordinance is set and add it to the
			// <lds_ord> element
			if (($stat = get_gedcom_value($eventABV . ":STAT", 1, $indirec)) != null) {
				$eStatus = $this->dom->createElement("status");
				$stat = get_gedcom_value($eventABV . ":STAT", 1, $indirec);
				$eStatus->setAttribute("val", isset ($stat));
				$eStatus = $eLdsEvent->appendChild($eStatus);
			}
			// If the event is a sealing
			if ($eventABV == "SLGC") {
				// Create an instance of person and look for their family record
				$person = Person :: getInstance($clipping["id"]);
				$famId = $person->getChildFamilyIds();
				$famrec = find_family_record($famId[0], WT_GED_ID);
				$fid = $famId[0];
				$handle = $this->query_dom("./families/family[@id=\"$fid\"]/@handle");
				if ($handle == null && id_in_cart($fid)) {
					/*
					 * If the family does not exist and their ID is in the clippings cart,
					 * you must create the family before you can query them in the dom to get
					 * their hlink. The hlink is generated when the person element is created.
					 * This causes overhead creating objects that are never added to the XML file
					 * perhaps there is some other way this can be done reducing the overhead?
					 *
					 */
					$this->create_family($famrec, $famId[0]);
					$handle = $this->query_dom("./families/family[@id=\"$fid\"]/@handle");
					$eFam = $this->dom->createElement("sealed_to");
					$eFam->setAttribute("hlink", $handle);
					$eFam = $eLdsEvent->appendChild($eFam);
					$person = null;
				} else
					if ($handle != null && id_in_cart($fid)) {
						$eFam = $this->dom->createElement("sealed_to");
						$eFam->setAttribute("hlink", $handle);
						$eFam = $eLdsEvent->appendChild($eFam);
						$person = null;
					}
			}

			if (($note = get_sub_record(1, "2 NOTE", $hasldsevent)) != null)
				$this->create_note($eLdsEvent, $note, 2);

			$num = 1;
			while (($sourcerefRec = get_sub_record(2, "2 SOUR", $hasldsevent, $num)) != null) {
				$this->create_sourceref($eLdsEvent, $sourcerefRec, 2,0);
				$num++;
			}
			$eLdsEvent = $eParent->appendChild($eLdsEvent);
		}
	}

/**
 * Creates a GrampsXML for a source, if that source exists in the given GEDCOM record
 * @param $sourceRec - The GEDCOM record where the source is found
 * @param $sourceID - The ID of the source
 * @param $level - the level the source is on, the default is one
 * @return GrampsXML for source
 */
function create_source($sourceRec, $sourceID, $level = 1,$done=1) {
	global $eRoot;
			if (!isset($this->dom))
		{
	     $this->dom = new DomDocument("1.0", "UTF-8");
		 $this->dom->formatOutput = true;

		 $eRoot = $this->dom->createElementNS("http://gramps-project.org/xml/1.1.0/", "database");
		 $eRoot = $this->dom->appendChild($eRoot);

//		 $this->ePlaces = $this->dom->createElement("places");
//		 $this->ePlaces = $eRoot->appendChild($this->ePlaces);
		}
		$eSource = $this->dom->createElement("source");
		$eSource = $eRoot->appendChild($eSource);
		$eSource->setAttribute("id", $sourceID);
		$eSource->setAttribute("handle", $this->generateHandle());
		$eSource->setAttribute("change", time());
		if (($title = get_gedcom_value("TITL", $level, $sourceRec)) != null) {
			$eSTitle = $this->dom->createElement("stitle");
			$etSTitle = $this->dom->createTextNode($title);
			$etSTitle = $eSTitle->appendChild($etSTitle);
			$eSTitle = $eSource->appendChild($eSTitle);
		}
		if (($author = get_gedcom_value("AUTH", $level, $sourceRec)) != null) {
			$eSAuthor = $this->dom->createElement("sauthor");
			$etSAuthor = $this->dom->createTextNode($author);
			$etSAuthor = $eSAuthor->appendChild($etSAuthor);
			$eSAuthor = $eSource->appendChild($eSAuthor);
		}
		if (($pubInfo = get_gedcom_value("PUBL", $level, $sourceRec)) != null) {
			$eSPubInfo = $this->dom->createElement("spubinfo");
			$etSPubInfo = $this->dom->createTextNode($pubInfo);
			$etSPubInfo = $eSPubInfo->appendChild($etSPubInfo);
			$eSPubInfo = $eSource->appendChild($eSPubInfo);
		}
		if (($abbrev = get_gedcom_value("ABBR", $level, $sourceRec)) != null) {
			$eSAbbrev = $this->dom->createElement("sabbrev");
			$etSAbbrev = $this->dom->createTextNode($abbrev);
			$etSAbbrev = $eSAbbrev->appendChild($etSAbbrev);
			$eSAbbrev = $eSource->appendChild($eSAbbrev);
		}
		if (($note = get_sub_record($level, $level . " NOTE", $sourceRec)) != null) {
			$this->create_note($eSource, $note, $level);
		}
		$num = 1;
		while (($nameSource = get_sub_record(1, "1 OBJE", $sourceRec, $num)) != null) {
			$this->create_mediaref($this->eSources, $nameSource, 1,0);
			$num++;
		}
		return $this->dom->saveXML();
	}


/**
 * Creates a GrampsXML for a family, if that family exists in the given GEDCOM record
 * @param $frec - The GEDCOM record where the family can be found
 * @param $fid - The id of the family record
 * @return GrampsXML for family
 */
function create_family($frec, $fid) {
	global $eRoot;
			if (!isset($this->dom))
		{
	     $this->dom = new DomDocument("1.0", "UTF-8");
		 $this->dom->formatOutput = true;

		 $eRoot = $this->dom->createElementNS("http://gramps-project.org/xml/1.1.0/", "database");
		 $eRoot = $this->dom->appendChild($eRoot);
		 $this->eEvents = $this->dom->createElement("events");
		 $this->eEvents = $eRoot->appendChild($this->eEvents);
		 $this->ePlaces = $this->dom->createElement("places");
		 $this->ePlaces = $eRoot->appendChild($this->ePlaces);
		}
			$famrec = $frec;
			$eFamily_new = $this->dom->createElement("family");
			$eFamily = $eRoot->appendChild($eFamily_new);
			$eFamily->setAttribute("id", $fid);
			$eFamily->setAttribute("handle", $fid);
			$eFamily->setAttribute("change", time());

			// Add the <father> element
			$id = get_gedcom_value("HUSB", 1, $famrec);
			if (isset($id))
			{
		    $eFather = $this->dom->createElement("father");
		    $eFather->setAttribute("hlink", $id);
			$eFather = $eFamily->appendChild($eFather);
			}

			// Add the <mother> element
			$id = get_gedcom_value("WIFE", 1, $famrec);
			if (isset($id))
			{
				$eMother = $this->dom->createElement("mother");
				$eMother->setAttribute("hlink", $id);
				$eMother = $eFamily->appendChild($eMother);
			}

			foreach ($this->familyevents as $event) {
				$this->create_event_ref($eFamily, $frec, $event);
			}

			// Add the <child> element
			$id = get_gedcom_value("CHIL", 1, $famrec);
			if (isset ($id)) {
				$eChild = $this->dom->createElement("child");
				$eChild->setAttribute("hlink", $id);
				$eChild = $eFamily->appendChild($eChild);
			}
			if (($note = get_sub_record(1, "1 NOTE", $frec)) != null) {
				$this->create_note($eFamily, $note, 1);
			}

			$num = 1;
			while (($sourcerefRec = get_sub_record(1, "1 SOUR", $frec, $num)) != null) {
				$this->create_sourceref($eFamily, $sourcerefRec, 1, 0);
				$num++;
			}
			$num = 1;
			while (($nameSource = get_sub_record(1, "1 OBJE", $frec, $num)) != null) {

				$this->create_mediaref($eFamily, $nameSource, 1, 0);
				$num++;
			}
		return $this->dom->saveXML();
	}

/**
 * Creates a GrampsXML for a record, if that record exists in the given GEDCOM record
 * @param $fid - The id of the record
 * @return GrampsXML for the recrord
 */
function create_record($fid)
{
	$gedrec = find_gedcom_record($fid, WT_GED_ID);
	//0 @I1@ INDI - person
	//0 @F1@ FAM - family
	//0 @S1@ SOUR - source
	//0 @O1@ OBJE - object
	//0 @R1@ REPO - reposotory
	//return $gedrec;
	$ct = preg_match("/0 @.*@ (.*)/", $gedrec, $match);
	if ($ct>0) {
		$type = trim($match[1]);
		// $type;
		if ($type == 'INDI') return $this->create_person($gedrec, $fid);
		if ($type == 'FAM')  return $this->create_family($gedrec, $fid);
		if ($type == 'SOUR') return $this->create_source($gedrec, $fid);
		if ($type == 'OBJE') return $this->create_media($gedrec, $fid);
	}
}

/**
 * Creates a GrampsXML for a media, if that media exists in the given GEDCOM record
 * @param $mediaRec - The GEDCOM record of the media
 * @param $mediaID - The id of the media
 * @param $level - The level on which the media can be found, the default is 1
 * @return GrampsXML for the recrord
 */
function create_media($mediaRec, $mediaID, $level = 1)
{
		global $file, $eRoot;
		if (!isset($this->dom))
		{
	     $this->dom = new DomDocument("1.0", "UTF-8");
		 $this->dom->formatOutput = true;

		 $eRoot = $this->dom->createElementNS("http://gramps-project.org/xml/1.1.0/", "database");
		 $eRoot = $this->dom->appendChild($eRoot);
		}

		$object = $this->dom->createElement("object");
		/*primary object elements and attributes*/
		$object->setAttribute("id", $mediaID);
		$object->setAttribute("handle", $mediaID);
		$object->setAttribute("change", time());
		/*elements and attributes of the object element*/
		/*File elements*/
		$file_ = get_gedcom_value("FILE", 1, $mediaRec);
		$fileNode = $this->dom->createElement("file");

		/*Source*/
		$src = $this->dom->createAttribute("src");
		$srcData = $this->dom->createTextNode($file_);
		$srcData = $src->appendChild($srcData);
		$src = $fileNode->appendChild($src);
		/*MIME*/
		$mime_ = get_gedcom_value("FORM", 1, $mediaRec);
		$mime = $this->dom->createAttribute("mime");
		if (empty ($mime_)) {
			$path = pathinfo($file_);
			if(!isset($path["extension"]))
				$mime_ = "unknown_file_extension";
			else
				$mime_ = $path["extension"];
		}
		$mimeData = $this->dom->createTextNode($mime_);
		$mimeData = $mime->appendChild($mimeData);
		$mime = $fileNode->appendChild($mime);
		/*DESCRIPTION*/
		$description_ = get_gedcom_value("TITL", 1, $mediaRec);
		$description = $this->dom->createAttribute("description");
		$descriptionData = $this->dom->createTextNode($description_);
		$descriptionData = $description->appendChild($descriptionData);
		$description = $fileNode->appendChild($description);
		/*fileNode elements*/
		$fileNode = $object->appendChild($fileNode);

		$fileNode = $this->dom->createElement("file");
		if (($note = get_sub_record(1, "1 NOTE", $mediaRec)) != null) {
			$this->create_note($object, $note, 1);
		}
		$num = 1;
		while (($nameSource = get_sub_record($level, $level . " SOUR", $mediaRec, $num)) != null) {
			$this->create_sourceref($object, $nameSource, 1);
			$num++;
		}
		$eRoot->appendChild($object);
		return $this->dom->saveXML();
}
	function create_mediaref($eParent, $sourcerefRec, $level,$done=1) {
		$mediaId = get_gedcom_value("OBJE", $level, $sourcerefRec);
		$eMediaRef = $this->dom->createElement("objref");
		$eMediaRef = $eParent->appendChild($eMediaRef);
		$eMediaRef->setAttribute("hlink", $mediaId);
		$eParent->appendChild($eMediaRef);
	}

function create_sourceref($eParent, $sourcerefRec, $level,$done=1)
	{
		if (($sourceID = get_gedcom_value("SOUR", $level, $sourcerefRec)) != null) {
				$eSourceRef = $this->dom->createElement("sourceref");
				$eSourceRef = $eParent->appendChild($eSourceRef);
				$eSourceRef->setAttribute("hlink", $sourceID);
		}
	}
}
?>
