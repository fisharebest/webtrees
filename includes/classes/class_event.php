<?php
/**
 * Class that defines an event details object
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2008  PGV Development Team.  All rights reserved.
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
 * @author Joel A. Bruce
 * @version $Id$
 *
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_EVENT_PHP', '');

require_once WT_ROOT.'includes/classes/class_date.php';

/**
 * Event
 *
 */
class Event {
//	These objects need further refinement in their implementations and parsing
//	var $address = null;
//	var $notes = array(); //[0..*]: string
//	var $sourceCitations = array(); //[0..*]: SourceCitation
//	var $multimediaLinks = array(); //[0..*]: MultimediaLink

	var $lineNumber = null;
	var $canShow = null;
	var $canShowDetails = null;
	var $canEdit = null;
	var $state = "";
	var $type = NULL;
	var $tag = NULL;
	var $date = NULL;
	var $place = null;
	var $gedcomRecord = null;
	var $resn = null;
	var $dest = false;
	var $label = null;
	var $parentObject = null;
	var $detail = NULL;
	var $values = NULL;
	var $sortOrder = 0;
	var $sortDate = NULL;
	//-- temporary state variable that can be used by other scripts
	var $temp = NULL;

	/**
	 * Get the value for the first given GEDCOM tag
	 *
	 * @param string $code
	 * @return string
	 */
	function getValue($code) {
		if (is_null($this->values)) {
			$this->values=array();
			preg_match_all('/\n2 ('.WT_REGEX_TAG.') (.+)/', $this->gedcomRecord, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				// If this is a link, remove the "@"
				if (preg_match('/^@'.WT_REGEX_XREF.'@$/', $match[2])) {
					$this->values[$match[1]]=trim($match[2], "@");
				} else {
					$this->values[$match[1]]=$match[2];
				}
			}
		}
		if (array_key_exists($code, $this->values)) {
			return $this->values[$code];
		}
		return null;
	}

	/**
	 * Parses supplied subrecord to fill in the properties of the class.
	 * Assumes the level of the subrecord is 1, and that all its associated sub records are provided.
	 *
	 * @param string $subrecord
	 * @param int $lineNumber
	 * @return Event
	 */
	function __construct($subrecord, $lineNumber=-1) {
		if (preg_match('/^1 ('.WT_REGEX_TAG.') *(.*)/', $subrecord, $match)) {
			$this->tag=$match[1];
			$this->detail=$match[2];
			$this->lineNumber=$lineNumber;
			$this->gedcomRecord=$subrecord;
		}
	}

	function setState($s) {
		$this->state = $s;
	}

	function getState() {
		return $this->state;
	}

	/**
	 * Check whether or not this event can be shown
	 *
	 * @return boolean
	 */
	function canShow() {
		if (is_null($this->canShow)) {
			if (empty($this->gedcomRecord)) $this->canShow = false;
			else if (!is_null($this->parentObject)) {
				$this->canShow = showFact($this->tag, $this->parentObject->getXref()) && !FactViewRestricted($this->parentObject->getXref(), $this->gedcomRecord);
			}
			else $this->canShow = true;
		}
		return $this->canShow;
	}

	/**
	 * Check whether or not the details of this event can be shown
	 *
	 * @return boolean
	 */
	function canShowDetails() {
		if (!$this->canShow()) return false;
		if (is_null($this->canShowDetails)) {
			if (!is_null($this->parentObject)) {
				$this->canShowDetails = showFactDetails($this->tag, $this->parentObject->getXref());
			}
			else $this->canShowDetails = true;
		}
		return $this->canShowDetails;
	}

	/**
	 * check whether or not this fact can be edited
	 *
	 * @return boolean
	 */
	function canEdit() {
		if (!$this->canShowDetails()) return false;
		if (is_null($this->canEdit)) {
			if (!is_null($this->parentObject)) {
				$this->canEdit = !FactEditRestricted($this->parentObject->getXref(), $this->gedcomRecord);
			}
			else $this->canEdit = true;
		}
		return $this->canEdit;
	}

	/**
	 * The 4 character event type specified by GEDCom.
	 *
	 * @return string
	 */
	function getType() {
		if (is_null($this->type))
			$this->type=$this->getValue('TYPE');
		return $this->type;
	}

	/**
	 * The place where the event occured.
	 *
	 * @return string
	 */
	function getPlace() {
		if (is_null($this->place)) {
			$this->place=$this->getValue('PLAC');
		}
		return $this->place;
	}

	function getFamilyId() {
		return $this->getValue("_PGVFS");
	}

	function getSpouseId() {
		return $this->getValue("_PGVS");
	}

	/**
	 * Get the date object for this event
	 *
	 * @return GedcomDate
	 */
	function getDate($estimate = true) {
		if (is_null($this->date))
			$this->date=new GedcomDate($this->getValue('DATE'));

		if (!$estimate && $this->dest) return null;
		return $this->date;
	}

	/**
	 * Set the date of this event.  This method should only be used to force a date.
	 *
	 * @param GedcomDate $date
	 */
	function setDate(&$date) {
		$this->date = $date;
		$this->dest = true;
	}

	/**
	 * The remaining unparsed GEDCom record
	 *
	 * @return string
	 */
	function getGedcomRecord() {
		return $this->gedcomRecord;
	}

	/**
	 * The line number, or line of occurrence in the GEDCom record.
	 *
	 * @return unknown
	 */
	function getLineNumber() {
		return $this->lineNumber;
	}

	/**
	 *
	 */
	function getTag() {
		return $this->tag;
	}

	/**
	 * The Person/Family record where this Event came from
	 *
	 * @return GedcomRecord
	 */
	function getParentObject() {
		return $this->parentObject;
	}

	/**
	 *
	 */
	function setParentObject(&$parent) {
		$this->parentObject = $parent;
	}

	/**
	 *
	 */
	function getDetail() {
		return $this->detail;
	}

	/**
	 * Check whether this fact has information to display
	 * Checks for a date or a place
	 *
	 * @return boolean
	 */
	function hasDatePlace() {
		return ($this->getDate() || $this->getPlace());
	}

	function getLabel($abbreviate=false) {
		if ($abbreviate) {
			return i18n::fact_abbreviation($this->tag);
		} else {
			return i18n::translate($this->tag);
		}
	}

	/**
	 * Print a simple fact version of this event
	 *
	 * @param boolean $return	whether to print or return
	 * @param boolean $anchor	whether to add anchor to date and place
	 */
	function print_simple_fact($return=false, $anchor=false) {
		global $SHOW_PEDIGREE_PLACES, $ABBREVIATE_CHART_LABELS;

		if (!$this->canShow()) return "";
		$data = "";
		if ($this->gedcomRecord != "1 DEAT"){
		   $data .= "<span class=\"details_label\">".$this->getLabel($ABBREVIATE_CHART_LABELS)."</span> ";
		}
		if ($this->canShowDetails()) {
			$emptyfacts = array("BIRT","CHR","DEAT","BURI","CREM","ADOP","BAPM","BARM","BASM","BLES","CHRA","CONF","FCOM","ORDN","NATU","EMIG","IMMI","CENS","PROB","WILL","GRAD","RETI","BAPL","CONL","ENDL","SLGC","EVEN","MARR","SLGS","MARL","ANUL","CENS","DIV","DIVF","ENGA","MARB","MARC","MARS","OBJE","CHAN","_SEPR","RESI", "DATA", "MAP");
			if (!in_array($this->tag, $emptyfacts))
				$data .= PrintReady($this->detail);
			if (!$this->dest)
				$data .= format_fact_date($this, $anchor, false, true);
			$data .= format_fact_place($this, $anchor, false, false);
		}
		$data .= "<br />\n";
		if (!$return) print $data;
		else return $data;
	}

/* Print a fact icon that varies by the decade, century, and subtype
 *
 * Many facts change over time.  Military uniforms, marriage dress, census forms.
 * This is a cutesy way to show the changes over time.  More icons need to be added
 * to the themes/?????/images/facts/ directory with a form of nn00_TYPE.gif or nnn0_TYPE.gif.
 * A special case of nn00_OCCU_FARM.gif has been added to celebrate farmers and farm hands.
 * A special case of nn00_OCCU_HOUS.gif has been added for KEEPing HOUSe or HOUSe KEEPers.
 * Generic subtyping is done by storing the first four characters of the value of the
 * record in a filename.  "1 RELI Methodist" is RELI_METH.gif or 1900_RELI_METH.gif.
 * 1960__MILI_CONF.gif would be Confederate soldier, and 1860__MILI_UNIO.gif would be
 * the counterpart Union soldier.  The most specific match wins.
 * Examples: 1900_CENS.gif 1910_CENS.gif 1900_OCCU_FARM.gif 1800_OCCU_FARM.gif
 */
 function Icon() {
		global $WT_IMAGE_DIR;

		// Need the gregorian century/decade
		$date=$this->getDate();

		// If no year, use birth date as fallback
		if ($date->date1->y==0 && is_object($this->parentObject) && $this->parentObject->getType()=='INDI')
			$date=$this->parentObject->getEstimatedBirthDate();

		$gdate=new GregorianDate($date->MinDate());
		$century=floor($gdate->y/100).'00';
		$decade=floor($gdate->y/10).'0';

		$tag=$this->getTag();
		$dir="{$WT_IMAGE_DIR}/facts";

		// Which era (century/decade)
		$eras=array("{$decade}_", "{$century}_", '');

		// Extra details, such as 1 OCCU Shoemaker or 1 RELI Catholic
		$detail=trim(strtoupper(substr($this->getDetail(),0,4)));
		if ($detail=='KEEP') // Keeping House => House Keeper
			$detail='HOUS';
		$details=array('_'.$detail, '');

		// Variations for different sexes
		if (is_object($this->parentObject) && $this->parentObject->getType()=='INDI')
			$sexes=array('_'.$this->parentObject->getSex(), '');
		else
			$sexes=array('');

		// Image naming structure is [era]tag[detail][sex].gif
		// e.g. 1860_OCCU_FARM_M.gif for a male farm-worker in the 1860s
		$image='';
		foreach ($eras as $era)
			foreach ($details as $detail)
				foreach ($sexes as $sex)
					if (file_exists("{$dir}/{$era}{$tag}{$detail}{$sex}.gif")) {
						$label=$this->getLabel();
						return "<img src=\"{$dir}/{$era}{$tag}{$detail}{$sex}.gif\" alt=\"{$label}\" title=\"{$label}\" align=\"middle\" />";
					}

		return '';
	}

	/**
	 * Static Helper functions to sort events
	 *
	 * @param Event $a
	 * @param Event $b
	 * @return int
	 */
	static function CompareDate(&$a, &$b) {
		$adate = $a->getDate();
		$bdate = $b->getDate();
		//-- non-dated events should sort according to the preferred sort order
		if (is_null($adate) && !is_null($a->sortDate)) {
			$ret = $a->sortOrder - $b->sortOrder;
		} elseif (is_null($bdate) && !is_null($b->sortDate)) {
			$ret = $a->sortOrder - $b->sortOrder;
		} else {
			$ret = GedcomDate::Compare($adate, $bdate);
		}
		if ($ret==0) {
			$ret = $a->sortOrder - $b->sortOrder;
			//-- if dates are the same they should be ordered by their fact type
			if ($ret==0) {
				$ret = Event::CompareType($a, $b);
			}
		}
//		print "[".$a->getTag().":".$adate->isOK().":".$adate->MinJD()."-".$adate->MaxJD()." ".$b->getTag().":".$bdate->isOK().":".$bdate->MinJD()."-".$bdate->MaxJD()." ".$ret."] ";
		return $ret;
	}

	/**
	 * Static method to Compare two events by their type
	 *
	 * @param Event $a
	 * @param Event $b
	 * @return int
	 */
	static function CompareType(&$a, &$b) {
		global $factsort;

		if (empty($factsort))
			$factsort=array_flip(array(
				"BIRT",
				"_HNM",
				"ALIA", "_AKA", "_AKAN",
				"ADOP", "_ADPF", "_ADPF",
				"_BRTM",
				"CHR", "BAPM",
				"FCOM",
				"CONF",
				"BARM", "BASM",
				"EDUC",
				"GRAD",
				"_DEG",
				"EMIG", "IMMI",
				"NATU",
				"_MILI", "_MILT",
				"ENGA",
				"MARB", "MARC", "MARL", "_MARI", "_MBON",
				"MARR", "MARR_CIVIL", "MARR_RELIGIOUS", "MARR_PARTNERS", "MARR_UNKNOWN", "_COML",
				"_STAT",
				"_SEPR",
				"DIVF",
				"MARS",
				"_BIRT_CHIL",
				"DIV", "ANUL",
				"_BIRT_", "_MARR_", "_DEAT_","_BURI_", // other events of close relatives
				"CENS",
				"OCCU",
				"RESI",
				"PROP",
				"CHRA",
				"RETI",
				"FACT", "EVEN",
				"_NMR", "_NMAR", "NMR",
				"NCHI",
				"WILL",
				"_HOL",
				"_????_",
				"DEAT", "CAUS",
				"_FNRL", "BURI", "CREM", "_INTE", "CEME",
				"_YART",
				"_NLIV",
				"PROB",
				"TITL",
				"COMM",
				"NATI",
				"CITN",
				"CAST",
				"RELI",
				"SSN", "IDNO",
				"TEMP",
				"SLGC", "BAPL", "CONL", "ENDL", "SLGS",
				"ADDR", "PHON", "EMAIL", "_EMAIL", "EMAL", "FAX", "WWW", "URL", "_URL",
				"AFN", "REFN", "_PRMN", "REF", "RIN", "_UID",
				"CHAN", "_TODO"
			));

		// Facts from same families stay grouped together
		// Keep MARR and DIV from the same families from mixing with events from other FAMs
		// Use the original order in which the facts were added
		if ($a->getFamilyId() && $b->getFamilyId() && $a->getFamilyId()!=$b->getFamilyId()) {
			return $a->sortOrder - $b->sortOrder;
		}

		$atag = $a->getTag();
		$btag = $b->getTag();

		// Events not in the above list get mapped onto one that is.
		if (!array_key_exists($atag, $factsort)) {
			if (preg_match('/^(_(BIRT|MARR|DEAT|BURI)_)/', $atag, $match)) {
				$atag=$match[1];
			} else {
				$atag="_????_";
			}
		}
		if (!array_key_exists($btag, $factsort)) {
			if (preg_match('/^(_(BIRT|MARR|DEAT|BURI)_)/', $btag, $match)) {
				$btag=$match[1];
			} else {
				$btag="_????_";
			}
		}

		//-- don't let dated after DEAT/BURI facts sort non-dated facts before DEAT/BURI
		//-- treat dated after BURI facts as BURI instead
		if ($a->getValue('DATE')!=NULL && $factsort[$atag]>$factsort['BURI'] && $factsort[$atag]<$factsort['CHAN']) $atag='BURI';
		if ($b->getValue('DATE')!=NULL && $factsort[$btag]>$factsort['BURI'] && $factsort[$btag]<$factsort['CHAN']) $btag='BURI';
		$ret = $factsort[$atag]-$factsort[$btag];
		//-- if facts are the same then put dated facts before non-dated facts
		if ($ret==0) {
			if ($a->getValue('DATE')!=NULL && $b->getValue('DATE')==NULL) return -1;
			if ($b->getValue('DATE')!=NULL && $a->getValue('DATE')==NULL) return 1;
			//-- if no sorting preference, then keep original ordering
			$ret = $a->sortOrder - $b->sortOrder;
		}
		return $ret;
	}
}
?>
