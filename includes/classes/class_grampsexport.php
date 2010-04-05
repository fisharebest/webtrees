<?php
/**
 * Gramps Export
 * An abstract class that has the basic methods for exporting GRAMPS XML implemented.
 * The class is not tied to any particlular web page(or GUI) and needs to be inherited for proper use
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
 * This program is distributed in the hope that it will be useful,b-
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
 *
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_GRAMPSEXPORT_PHP', '');

 /*
  * This is an abstract class and should only be used through its subclasses, all
  * of which are prefixed by GE.
  */

class GrampsExport {

	var $mediaFiles = array(),$i;
	var $dom, $ePeople, $eFams, $eSources, $ePlaces, $eObject, $eEvents;
	var $familyevents = array (
	"ANUL",
	"CENS",
	"DIV",
	"DIVF",
	"ENGA",
	"MARR",
	"MARB",
	"MARC",
	"MARL",
	"MARS"
);
	var $eventsArray = array (
	"ADOP",
	"BIRT",
	"BAPM",
	"BARM",
	"BASM",
	"BLES",
	"BURI",
	"CENS",
	"CHR",
	"CHRA",
	"CONF",
	"CREM",
	"DEAT",
	"EMIG",
	"FCOM",
	"GRAD",
	"IMMI",
	"NATU",
	"ORDN",
	"RETI",
	"PROB",
	"WILL",
	"EVEN"
);
/**
 * Creates the root elements for the GRAMPS XML file.
 *
 * The methods adds all the root elements and appends them to a DOMDocument.
 */
	function begin_xml() {
		$user = WT_USER_NAME;

		$this->dom = new DomDocument("1.0", "UTF-8");
		$this->dom->formatOutput = true;

		$eRoot = $this->dom->createElementNS("http://gramps-project.org/xml/1.1.0/", "database");
		$eRoot = $this->dom->appendChild($eRoot);

		$eHeader = $this->dom->createElement("header");
		$eHeader = $eRoot->appendChild($eHeader);

		$eCreated = $this->dom->createElement("created");
		$eCreated = $eHeader->appendChild($eCreated);
		$eCreated->setAttribute("date", date("Y-m-d"));
		$eCreated->setAttribute("version", "1.1.2.6");

		$eResearcher = $this->dom->createElement("researcher");
		$eResname = $this->dom->createElement("resname");
		$etResname = $this->dom->createTextNode(getUserFullName($user));
		$etResname = $eResname->appendChild($etResname);
		$eResname = $eResearcher->appendChild($eResname);
		$eResemail = $this->dom->createElement("resemail");
		$etResemail = $this->dom->createTextNode(getUserEmail($user));
		$etResemail = $eResemail->appendChild($etResemail);
		$eResemail = $eResearcher->appendChild($eResemail);
		$eResearcher = $eHeader->appendChild($eResearcher);

		$this->eEvents = $this->dom->createElement("events");
		$this->eEvents = $eRoot->appendChild($this->eEvents);

		$this->ePeople = $this->dom->createElement("people");
		$this->ePeople = $eRoot->appendChild($this->ePeople);

		$this->eFams = $this->dom->createElement("families");
		$this->eFams = $eRoot->appendChild($this->eFams);

		$this->eSources = $this->dom->createElement("sources");
		$this->eSources = $eRoot->appendChild($this->eSources);

		$this->ePlaces = $this->dom->createElement("places");
		$this->ePlaces = $eRoot->appendChild($this->ePlaces);

		$this->eObject = $this->dom->createElement("objects");
		$this->eObject = $eRoot->appendChild($this->eObject);
	}

	/**
	 * Creates the date elements used throughout the GRAMPS XML file.
	 * The function will parse the date record and determine the type of date
	 * (regular, range).
	 *
	 * @param DOMObject $eParent - The parent element the date should be attached to
	 * @param string $dateRec - the entire GEDCOM date record to be parsed
	 * @param int $level - the level the date record was found on in the GEDCOM
	 * @param int $done - whether the method is called from the GrampsExport($done=1) or a sub-class
	 */
	function create_date($eParent, $dateRec, $level) {
		if (empty($dateRec)) return;
		$date = new GedcomDate(get_gedcom_value("DATE", $level, $dateRec, '', false));

		//checks to see if there's is a 2nd date value and creates the daterange element
		if (!empty($date->qual2)) {
			$eDateRange = $this->dom->createElement("daterange");
			$eDateRange = $eParent->appendChild($eDateRange);

			//sets the start date
			$eDateRange->setAttribute("start", $this->create_date_value($date->MinDate()));

			//sets the stop date
			$eDateRange->setAttribute("stop", $this->create_date_value($date->MaxDate()));
		} else {
			//if there's no dateRange, this creates the normal dateval Element
			$eDateVal = $this->dom->createElement("dateval");
			$eDateVal = $eParent->appendChild($eDateVal);

			//checks for the Type attribute values
			switch ($date->qual1) {
			case 'bef':
				$eDateVal->setAttribute("type", "before");
				break;
			case 'aft':
				$eDateVal->setAttribute("type", "after");
				break;
			case 'abt':
				$eDateVal->setAttribute("type", "about");
				break;
			}

			//sets the date value
			$eDateVal->setAttribute("val", $this->create_date_value($date->MinDate()));
		}
	}

	/**
	 * Returns all the path to all media files that were used in the family, people, source in the created GRAMPS XML
	 */
	function get_all_media() {
		return $this->mediaFiles;
	}

	/**
	 * Convert a date to the GRAMPS date format
	 * @param CalendarDate $date - a date to convert
	 */
	function create_date_value($date) {
		// Since no calendar is specified, should we always convert to gregorian?
		$date=$date->convert_to_cal('gregorian');
		return
			($date->y==0 ? '????' : $date->Format('%Y')) .'-'.
			($date->m==0 ? '??'   : $date->Format('%m')) .'-'.
			($date->d==0 ? '??'   : $date->Format('%d'));
	}

	/**
	 * Creates a reference(handle) for xml element to element in the events element of the root element
	 * If such an element does not exists it is created using create_event method.
	 * @param DOMElement $eParent - the parent you want to apend the elemnt to
	 * @param GEDCOM record $personrec - the record use to get more information about the event(used when the event is being created)
	 * @param int $event - the $event record
	 */
	function create_event_ref($eParent, $personrec, $event)
	{
		if (($eventRec = get_sub_record(1, "1 " . $event, $personrec)) != null) {
		$eEventRef = $this->dom->createElement("eventref");
		$eEventRef = $eParent->appendChild($eEventRef);
		$eventID = $this->create_event($personrec,$event,$eventRec);
		$eEventRef->setAttribute("hlink", $this->query_dom("./events/event[@id = \"$eventID\"]/@handle"));
		$eParent->appendChild($eEventRef);
		}
	}

	/**
	  * Creates the Event Element given the record passed and the event abbreviation which is then
	  * 	appended to the parent element passed into the method. When the Event element is created
	  * 	all it's child elements are created and appended to it accordingly.  If the Place for the
	  * 	given event has not been created, this method calls create_placeobj thus creating the
	  * 	place and then links them together accordingly, otherwise the method just searches for
	  * 	the place and creates the link.
	  *
	  * @param DOMElement $personrec - the person(or top element) that contains the event
	  * @param string $event - the abbreviation of the event to be created; BIRT, DEAT, ADOP.....
	  * @param DOMElement $eventRec - the parent DOMElement to which the created Event Element is appended
	  * @param int $done - whether the method is called from the GrampsExport($done=1) or a sub-class
	  */
		function create_event($personrec, $event, $eventRec,$done=1) {
			$eventID = $this->generateHandle();
			$eventHandle = $eventID;
			$eEvent = $this->dom->createElement("event");
			$eType = $this->dom->createElement("type");
			$eTypeText = $this->dom->createTextNode($event);
			$eTypeText = $eType->appendChild($eTypeText);
			$eEvent->appendChild($eType);
		$eEvent->setAttribute("id", $eventID);
		$eEvent->setAttribute("handle", $eventHandle);
		$eEvent->setAttribute("change", time());

			if (($dateRec = get_sub_record(1, "2 DATE", $eventRec)) != null) {
				$this->create_date($eEvent, $dateRec, 2);
			}

			if (($place = get_gedcom_value($event . ":PLAC", 1, $personrec)) != null) {
				$hlink = $this->query_dom("./places/placeobj[@title=\"".preg_replace("~\"~", '&quot;', $place)."\"]/@handle");
				if ($hlink == null) {
					$hlink = $this->generateHandle();
					$this->create_placeobj($place, $hlink);
					$this->create_place($eEvent, $hlink);
				} else {
					$this->create_place($eEvent, $hlink);
				}
			}

			if (($cause = get_gedcom_value($event . ":CAUS", 1, $personrec)) != null) {
				$eCause = $this->dom->createElement("cause");
				$etCause = $this->dom->createTextNode($cause);
				$etCause = $eCause->appendChild($etCause);
				$eCause = $eEvent->appendChild($eCause);
			}

			if (($description = get_gedcom_value($event . ":TYPE", 1, $personrec)) != null) {
				$eDescription = $this->dom->createElement("description");
				$etDescription = $this->dom->createTextNode($description);
				$etDescription = $eDescription->appendChild($etDescription);
				$eDescription = $eEvent->appendChild($eDescription);
			}

			if (($note = get_sub_record(1, "2 NOTE", $eventRec)) != null) {
				$this->create_note($eEvent, $note, 2);
			}

			$num = 1;
			while (($sourcerefRec = get_sub_record(2, "2 SOUR", $personrec, $num)) != null) {
				$this->create_sourceref($eEvent, $sourcerefRec, 2);
				$num++;
			}
			$num = 1;
			while (($nameSource = get_sub_record(1, "1 OBJE", $personrec, $num)) != null) {

				$this->create_mediaref($eEvent, $nameSource, 1,$done);

				$num++;
			}
			$eEvent = $this->eEvents->appendChild($eEvent);
			return $eventID;

	}


	/**
	 * This function creates a family relation for a person and appends the relation
	 * to the person element.
	 *
	 * It searches through the DOMDocument first to see if the person is created,
	 * if they are not, the person is created and then the DOMDocument is queried
	 * and the persons HLINK is retrieved.
	 *
	 * @param DOMElement $eParent - the parent XML element the date element should be appended to
	 * @param GEDCOM record $personRec - the full INDI GEDCOM record of the person that the relation is being created
	 * @param int $tag -  the name of the GEDCOM tag (FAMC, FAMS). This is used to allow the same function to work with childin and parent_in_family relations
	 */
	function create_fam_relation($eParent, $personRec, $tag)
	{
		//throw new exception("create fam rel - this function is not implemented");
	}

	/**
	 * Creates the Family element and all of it's child elements, and appends it to the
	 * Families element.  This function will search through the DOMDocument looking
	 * for people in the family. If they are not created yet and they are in the clippings
	 * cart, they will be created and ther hlink added to the family element.
	 *
	 * @param string $frec - the full FAM GEDCOM record of the family to be created
	 * @param string $fid = the ID (F1, F2, F3) of the family that is being created
	 */
	function create_family($frec, $fid)
	{
		//throw new exception("create_family - this function is not implemented");
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
	function create_lds_event($indirec, $eventName, $eventABV, $eParent)
	{
		//throw new exception("create_lds_event - this function is not implemented");
	}

	/**
	  * Creates the Note element and appends it to the parent element
	  *
	  * @param DOMElement $eParent - the parent DOMElement to which the created Note Element is appended
	  * @param string $noteRec - the entire Family, Individual, etc. record in which the event may be found
	  * @param int $level - The GEDCOM line level where the NOTE tag may be found
	  */
	function create_note($eParent, $noteRec, $level) {
		$note = get_gedcom_value("NOTE", $level, $noteRec);
		$note .= get_cont($level+1, $noteRec, false);
//		$num = 1;
//		while (($cont = get_gedcom_value("NOTE:CONT", $level, $noteRec, $num)) != null) {
//			$note .= $cont;
//			$num++;
//		}
		$eNote = $this->dom->createElement("note");
		$etNote = $this->dom->createTextNode(htmlentities($note,ENT_COMPAT,'UTF-8'));
		$etNote = $eNote->appendChild($etNote);
		$eNote = $eParent->appendChild($eNote);
	}

	/**
	  * Creates the Person element and all of it's child elements, and appends it to the
	  * 	People element.  Given the link for certain LDS events to a family, if the Family
	  * 	has not been previously created, create_family is called to create the family.
	  * 	The family relations in the LDS events and in the person element are only created
	  * 	if the family they have a relation with are also included in the clippings cart
	  *
	  * @param string $personRec - the full INDI GEDCOM record of the person to be created
	  * @param string $personID - the ID (I1, I2, I3) of the person the is being created
	  */
	function create_person($personRec = "", $personID = "")
	{
		//throw new exception("create_person - this function is not implemented");

	}
	/**
	  * Creates the Place Element and appends it to the Parent element given
	  *
	  * @param DOMElement $eParent - the parent DOMElement to which the created Place Element is appended
	  * @param string $hlink - the value to which the 'hlink' attribute is set
	  */
	function create_place($eParent, $hlink) {
		$ePlace = $this->dom->createElement("place");
		$ePlace->setAttribute("hlink", $hlink);
		$ePlace = $eParent->appendChild($ePlace);
	}

	/**
	  * Creates the PlaceObj element and appends it to the Places element
	  *
	  * @param string $place - the string containing the value for the placeobj to be created
	  * @param string $hlink - the value to which the 'hlink' attribute is set \
	  * @param int $done - whether the method is called from the GrampsExport($done=1) or a sub-class
	  */
	function create_placeobj($place, $hlink,$done=1) {
		$ePlaceObj = $this->dom->createElement("placeobj");
		$ePlaceObj->setAttribute("handle", $hlink);
		$ePlaceObj->setAttribute("id", $hlink);
		$ePlaceObj->setAttribute("change", time());

		$ePTitle = $this->dom->createElement("ptitle");
		$ePTitle = $ePlaceObj->appendChild($ePTitle);
		$ePlaceObj = $this->ePlaces->appendChild($ePlaceObj);
		$ePlaceText = $this->dom->createTextNode($place);
		$ePTitle->appendChild($ePlaceText);
		$num = 1;
		while (($nameSource = get_sub_record(1, "1 OBJE", $place, $num)) != null) {
			$this->create_mediaref($this->ePlaces, $nameSource, 1,$done);
			$num++;
		}
	}
 /**
  * Creates a reference(handle) for xml element to element in the objects element of the root element
  * If such an element does not exists it is created using create_media method.
  * @param DOMElement $eParent - the parent you want to apend the elemnt to
  * @param GEDCOM record $sourcerefRec - the record use to get more information about the event(used when the event is being created)
  * @param int $level - the level the media could be found
  */
	function create_mediaref($eParent, $sourcerefRec, $level,$done=1) {
		$mediaId = get_gedcom_value("OBJE", $level, $sourcerefRec);
		$eMediaRef = $this->dom->createElement("objref");
		$eMediaRef = $eParent->appendChild($eMediaRef);

		if (($sourceHlink = $this->query_dom("./objects/object[@id = \"$mediaId\"]/@handle")) == null)
			$this->create_media($mediaId, find_media_record($mediaId), WT_GED_ID);
		$eMediaRef->setAttribute("hlink", $this->query_dom("./objects/object[@id = \"$mediaId\"]/@handle"));

		$eParent->appendChild($eMediaRef);
	}
	/**
	 * Creates an object element(for the media) and puts it unde the objects element of the root
	 * @param string $mediaID - the id of the media you want to create
	 * @param string $mediaRec - the GEDCOM record that contains the media
	 * @param int $level - the level the media is in the GEDCOM method
	 */
	function create_media($mediaID, $mediaRec, $level = 1) {
		global $file;
		$object = $this->dom->createElement("object");
		/*primary object elements and attributes*/
		$object->setAttribute("id", $mediaID);
		$object->setAttribute("handle", $this->generateHandle());
		$object->setAttribute("change", time());
		/*elements and attributes of the object element*/
		/*File elements*/
		$file_ = get_gedcom_value("FILE", 1, $mediaRec);
		$this->mediaFiles[] = $file_;
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
		$object = $this->eObject->appendChild($object);
		//find_highlighted_media();//find the primary picture for the gedcom record

	}
	/**
	  * Creates the SourceRef element and appends it to the Parent Element.  If the actual Source has not
	  * 	been previously created, this will retrieve the record for that, and create that also.
	  *
	  * @param DOMElement $eParent - the parent DOMElement to which the created Note Element is appended
	  * @param string $sourcerefRec - the record containing the reference to a Source
	  * @param int $level - The GEDCOM line level where the SOUR tag may be found
	  */
	function create_sourceref($eParent, $sourcerefRec, $level)
	{
		// throw new exception("create_sourceref - this function is not implemented");
	}

	/**
	  * Creates the Source and appends it to the Sources Element
	  *
	  * @param string $sourceID - the ID of the source to be created
	  * @param string $sourceRec - the entire GEDCOM record containing the Source
	  * @param int $level - the level the source is on in the GEDCOM record
	  * @param int $done - whether the method is called from the GrampsExport($done=1) or a sub-class
	  */
function create_source($sourceID, $sourceRec, $level = 1, $done=1) {
		$eSource = $this->dom->createElement("source");
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
			$this->create_mediaref($this->eSources, $nameSource, 1,$done);
			$num++;
		}
		$eSource = $this->eSources->appendChild($eSource);
	}

	/**
	* Generates a unique identifier for linking elements in the DOMDocument
	* This function was created to conform to how GRAMPS formats their XML
	* documents. The handle only has to be unique among the file, but this
	* allows for a creation of a huge XML file with little chance of overlap
	*/
	function generateHandle() {
		return strtoupper("_" . dechex(rand() * (time() * 877)) . dechex(time() * rand(1, 4)));
	}


	/**
	* Reads in an xpath expression and returns the value searched for by the expression
	*
	* @param string $query - XPath expression to be executed on the DOMDocument
	*
	* @return string - The result of the XPath expression (null if no record is found)
	*/
	function query_dom($query) {
		$xpath = new DOMXpath($this->dom);
		$id = $xpath->query($query);
		if ($id->length == 0)
			$id = null;
		if (isset ($id)) {
			foreach ($id as $handle) {
				$id = $handle->nodeValue;
			}
		}
		return $id;
	}

	/**
	* This function takes the dom document and validates it against the
	* GRAMPS RNG schema. It then prints out the results of the validation
	* to the screen.
	*
	* Validating against the DTD could be easily added
	* if that is deemed useful.
	*
	* @param DOMDocument $domObj - this is the dom document to be validated
	* @param boolean $printXML - set to true, this parameter will make validate print out the DOMDocuments XML to the screen
	*
	*/
	function validate($domObj, $printXML = true) {
		if ($printXML) {
			print "<br /><br /><br />" . nl2br(htmlentities($domObj->saveXML(),ENT_COMPAT,'UTF-8'));
		}
		print "Loading GRAMPS file... <br />";
		$domObj->loadXML($domObj->saveXML());
		$res = $domObj->relaxNGValidate('includes/grampsxml.rng');
		if ($res)
			print "Validation: <em style=\"color:green;\"><h1> PASSED against the .RNG</h1></em>";
		else
			print "Validation: <em style=\"color:red;\"><h1> FAILED against the .RNG</h1></em>";
	}
}
?>
