<?php
/**
* GEDCOM Statistics Class
*
* This class provides a quick & easy method for accessing statistics
* about the GEDCOM.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
*
* Modifications Copyright (c) 2010 Greg Roach
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
* @version $Id$
* @author Patrick Kellum
* @package webtrees
* @subpackage Lists
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_STATS_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_lists.php';

// Methods not allowed to be used in a statistic
define('STATS_NOT_ALLOWED', 'stats,getAllTags,getTags');

class stats {
	var $_gedcom;
	var $_gedcom_url;
	var $_ged_id;
	var $_server_url; // Absolute URL for generating external links.  e.g. in RSS feeds
	static $_not_allowed = false;
	static $_media_types = array('audio', 'book', 'card', 'certificate', 'coat', 'document', 'electronic', 'magazine', 'manuscript', 'map', 'fiche', 'film', 'newspaper', 'painting', 'photo', 'tombstone', 'video', 'other');

	static $_xencoding = WT_GOOGLE_CHART_ENCODING;

	function __construct($gedcom, $server_url='') {
		self::$_not_allowed = explode(',', STATS_NOT_ALLOWED);
		$this->_setGedcom($gedcom);
		$this->_server_url = $server_url;
	}

	function _setGedcom($gedcom) {
		$this->_gedcom = $gedcom;
		$this->_ged_id = PrintReady(get_id_from_gedcom($gedcom));
		$this->_gedcom_url = encode_url($gedcom);
	}

	/**
	* Return an array of all supported tags and an example of its output.
	*/
	function getAllTags() {
		$examples = array();
		$methods = get_class_methods('stats');
		$c = count($methods);
		for ($i=0; $i < $c; $i++) {
			if ($methods[$i][0] == '_' || in_array($methods[$i], self::$_not_allowed)) {
				continue;
			}
			$examples[$methods[$i]] = $this->$methods[$i]();
			if (stristr($methods[$i], 'percentage')) {
				$examples[$methods[$i]] .='%';
			}
			if (stristr($methods[$i], 'highlight')) {
				$examples[$methods[$i]]=str_replace(array(' align="left"', ' align="right"'), '', $examples[$methods[$i]]);
			}
		}
		ksort($examples);
		return $examples;
	}

	/**
	* Return a string of all supported tags and an example of its output in table row form.
	*/
	function getAllTagsTable() {
		global $TEXT_DIRECTION;
		$examples = array();
		$methods = get_class_methods($this);
		$c = count($methods);
		for ($i=0; $i < $c; $i++) {
			if (in_array($methods[$i], self::$_not_allowed) || $methods[$i][0] == '_' || $methods[$i] == 'getAllTagsTable' || $methods[$i] == 'getAllTagsText') {
				continue;
			} // Include this method name to prevent bad stuff happening
			$examples[$methods[$i]] = $this->$methods[$i]();
			if (stristr($methods[$i], 'percentage')) {
				$examples[$methods[$i]] .='%';
			}
			if (stristr($methods[$i], 'highlight')) {
				$examples[$methods[$i]]=str_replace(array(' align="left"', ' align="right"'), '', $examples[$methods[$i]]);
			}
		}
		$out = '';
		if ($TEXT_DIRECTION=='ltr') {
			$alignVar = 'right';
			$alignRes = 'left';
		} else {
			$alignVar = 'left';
			$alignRes = 'right';
		}
		foreach ($examples as $tag=>$v) {
			$out .= "\t<tr class=\"vevent\">"
				."<td class=\"list_value_wrap\" align=\"{$alignVar}\" valign=\"top\" style=\"padding:3px\">{$tag}</td>"
				."<td class=\"list_value_wrap\" align=\"{$alignRes}\" valign=\"top\">{$v}</td>"
				."</tr>\n"
			;
		}
		return $out;
	}

	/**
	* Return a string of all supported tags in plain text.
	*/
	function getAllTagsText() {
		$examples=array();
		$methods=get_class_methods($this);
		$c=count($methods);
		for ($i=0; $i < $c; $i++) {
			if (in_array($methods[$i], self::$_not_allowed) || $methods[$i][0] == '_' || $methods[$i] == 'getAllTagsTable' || $methods[$i] == 'getAllTagsText') {continue;} // Include this method name to prevent bad stuff happining
			$examples[$methods[$i]] = $methods[$i];
		}
		$out = '';
		foreach ($examples as $tag=>$v) {
			$out .= "{$tag}<br />\n";
		}
		return $out;
	}

	/*
	* Get tags and their parsed results.
	*/
	function getTags($text) {
		static $funcs;

		// Retrive all class methods
		isset($funcs) or $funcs = get_class_methods($this);

		// Extract all tags from the provided text
		$ct = preg_match_all("/#(.+)#/U", (string)$text, $match);
		$tags = $match[1];
		$c = count($tags);
		$new_tags = array(); // tag to replace
		$new_values = array(); // value to replace it with

		/*
		* Parse block tags.
		*/
		for($i=0; $i < $c; $i++)
		{
			$full_tag = $tags[$i];
			// Added for new parameter support
			$params = explode(':', $tags[$i]);
			if (count($params) > 1) {
				$tags[$i] = array_shift($params);
			} else {
				$params = null;
			}

			// Skip non-tags and non-allowed tags
			if ($tags[$i][0] == '_' || in_array($tags[$i], self::$_not_allowed)) {continue;}

			// Generate the replacement value for the tag
			if (method_exists($this, $tags[$i]))
			{
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = $this->$tags[$i]($params);
			}
			elseif ($tags[$i] == 'help')
			{
				// re-merge, just in case
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = help_link(join(':', $params));
			}
		}
		return array($new_tags, $new_values);
	}

///////////////////////////////////////////////////////////////////////////////
// GEDCOM                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function gedcomFilename() {return get_gedcom_from_id($this->_ged_id);}

	function gedcomID() {return $this->_ged_id;}

	function gedcomTitle() {return PrintReady(get_gedcom_setting($this->_ged_id, 'title'));}

	function _gedcomHead() {
		$title = "";
		$version = '';
		$source = '';
		static $cache=null;
		if (is_array($cache)) {
			return $cache;
		}
		$head=find_other_record('HEAD', $this->_ged_id);
		$ct=preg_match("/1 SOUR (.*)/", $head, $match);
		if ($ct > 0) {
			$softrec=get_sub_record(1, '1 SOUR', $head);
			$tt=preg_match("/2 NAME (.*)/", $softrec, $tmatch);
			if ($tt > 0) {
				$title=trim($tmatch[1]);
			} else {
				$title=trim($match[1]);
			}
			if (!empty($title)) {
				$tt=preg_match("/2 VERS (.*)/", $softrec, $tmatch);
				if ($tt > 0) {
					$version=trim($tmatch[1]);
				} else {
					$version='';
				}
			} else {
				$version='';
			}
			$tt=preg_match("/1 SOUR (.*)/", $softrec, $tmatch);
			if ($tt > 0) {
				$source=trim($tmatch[1]);
			} else {
				$source=trim($match[1]);
			}
		}
		$cache=array($title, $version, $source);
		return $cache;
	}

	function gedcomCreatedSoftware() {
		$head=self::_gedcomHead();
		return $head[0];
	}

	function gedcomCreatedVersion() {
		$head=self::_gedcomHead();
		// fix broken version string in Family Tree Maker
		if (strstr($head[1], 'Family Tree Maker ')) {
			$p=strpos($head[1], '(') + 1;
			$p2=strpos($head[1], ')');
			$head[1]=substr($head[1], $p, ($p2 - $p));
		}
		// Fix EasyTree version
		if ($head[2]=='EasyTree') {
			$head[1]=substr($head[1], 1);
		}
		return $head[1];
	}

	function gedcomDate() {
		global $DATE_FORMAT;

		$head=find_other_record('HEAD', $this->_ged_id);
		if (preg_match("/1 DATE (.+)/", $head, $match)) {
			$date=new GedcomDate($match[1]);
			return $date->Display(false, $DATE_FORMAT); // Override $PUBLIC_DATE_FORMAT
		}
		return '';
	}

	function gedcomUpdated() {
		global $TBLPREFIX;

		$row=
			WT_DB::prepareLimit("SELECT d_year, d_month, d_day FROM {$TBLPREFIX}dates WHERE d_file=? AND d_fact=? ORDER BY d_julianday1 DESC, d_type", 1)
			->execute(array($this->_ged_id, 'CHAN'))
			->fetchOneRow();
		if ($row) {
			$date=new GedcomDate("{$row->d_day} {$row->d_month} {$row->d_year}");
			return $date->Display(false);
		} else {
			return self::gedcomDate();
		}
	}

	function gedcomHighlight() {
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom}.jpg";
		}
		elseif (file_exists("images/gedcoms/{$this->_gedcom}.png")) {
			$highlight="images/gedcoms/{$this->_gedcom}.png";
		}
		if (!$highlight) {return '';}
		$imgsize=findImageSize($highlight);
		return "<a href=\"".encode_url("{$this->_server_url}index.php?ctype=gedcom&ged={$this->_gedcom_url}")."\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" class=\"gedcom_highlight\" alt=\"\" /></a>";
	}

	function gedcomHighlightLeft() {
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom}.jpg";
		} else {
			if (file_exists("images/gedcoms/{$this->_gedcom}.png")) {
				$highlight="images/gedcoms/{$this->_gedcom}.png";
			}
		}
		if (!$highlight) {
			return '';
		}
		$imgsize=findImageSize($highlight);
		return "<a href=\"".encode_url("{$this->_server_url}index.php?ctype=gedcom&ged={$this->_gedcom_url}")."\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"left\" class=\"gedcom_highlight\" alt=\"\" /></a>";
	}

	function gedcomHighlightRight() {
		$highlight=false;
		if (file_exists("images/gedcoms/{$this->_gedcom}.jpg")) {
			$highlight="images/gedcoms/{$this->_gedcom}.jpg";
		} else {
			if (file_exists("images/gedcoms/{$this->_gedcom}.png")) {
				$highlight="images/gedcoms/{$this->_gedcom}.png";
			}
		}
		if (!$highlight) {
			return '';
		}
		$imgsize=findImageSize($highlight);
		return "<a href=\"".encode_url("{$this->_server_url}index.php?ctype=gedcom&ged={$this->_gedcom_url}")."\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"right\" class=\"gedcom_highlight\" alt=\"\" /></a>";
	}

///////////////////////////////////////////////////////////////////////////////
// Totals                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _getPercentage($total, $type) {
		$per=null;
		switch($type) {
			default:
			case 'all':
				$type = $this->totalIndividuals() + $this->totalFamilies() + $this->totalSources() + $this->totalOtherRecords();
				break;
			case 'individual':
				$type = $this->totalIndividuals();
				break;
			case 'family':
				$type = $this->totalFamilies();
				break;
			case 'source':
				$type = $this->totalSources();
				break;
			case 'note':
				$type = $this->totalNotes();
				break;
			case 'other':
				$type = $this->totalOtherRecords();
				break;
		}
		if ($type>0) {
			$per = round(100 * $total / $type, 2);
		} else {
			$per = 0;
		}
		return $per;
	}

	function totalRecords() {
		return ($this->totalIndividuals() + $this->totalFamilies() + $this->totalSources() + $this->totalOtherRecords());
	}

	function totalIndividuals() {
		global $TBLPREFIX;

		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalIndisWithSources() {
		global $TBLPREFIX, $DBTYPE;
		$rows=self::_runSQL("SELECT COUNT(DISTINCT i_id) AS tot FROM {$TBLPREFIX}link, {$TBLPREFIX}individuals WHERE i_id=l_from AND i_file=l_file AND l_file=".$this->_ged_id." AND l_type='SOUR'");
		return $rows[0]['tot'];
	}

	function chartIndisWithSources($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividuals();
		if ($tot_indi==0) {
			return '';
		} else {
			$tot_sindi = $this->totalIndisWithSources();
			$tot_indi_per = round(100 *  ($tot_indi-$tot_sindi) / $tot_indi, 2);
			$tot_sindi_per = round(100 * $tot_sindi / $tot_indi, 2);
		}
		$chd = self::_array_to_extended_encoding(array($tot_sindi_per, 100-$tot_sindi_per));
		$chl =  i18n::translate('With sources').' - '.round($tot_sindi_per,1).'%|'.
				i18n::translate('Without sources').' - '.round($tot_indi_per,1).'%';
		$chart_title =  i18n::translate('With sources').' ['.round($tot_sindi_per,1).'%], '.
						i18n::translate('Without sources').' ['.round($tot_indi_per,1).'%]';
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

	function totalIndividualsPercentage() {
		return $this->_getPercentage($this->totalIndividuals(), 'all', 2);
	}

	function totalFamilies() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}families WHERE f_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalFamsWithSources() {
		global $TBLPREFIX, $DBTYPE;
		$rows=self::_runSQL("SELECT COUNT(DISTINCT f_id) AS tot FROM {$TBLPREFIX}link, {$TBLPREFIX}families WHERE f_id=l_from AND f_file=l_file AND l_file=".$this->_ged_id." AND l_type='SOUR'");
		return $rows[0]['tot'];
	}

	function chartFamsWithSources($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot_fam = $this->totalFamilies();
		$tot_sfam = $this->totalFamsWithSources();
		if ($tot_fam==0) {
			return '';
		} else {
			$tot_fam_per = round(100 *  ($tot_fam-$tot_sfam) / $tot_fam, 2);
			$tot_sfam_per = round(100 * $tot_sfam / $tot_fam, 2);
		}
		$chd = self::_array_to_extended_encoding(array($tot_sfam_per, 100-$tot_sfam_per));
		$chl =  i18n::translate('With sources').' - '.round($tot_sfam_per,1).'%|'.
				i18n::translate('Without sources').' - '.round($tot_fam_per,1).'%';
		$chart_title =  i18n::translate('With sources').' ['.round($tot_sfam_per,1).'%], '.
						i18n::translate('Without sources').' ['.round($tot_fam_per,1).'%]';
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

	function totalFamiliesPercentage() {
		return $this->_getPercentage($this->totalFamilies(), 'all', 2);
	}

	function totalSources() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}sources WHERE s_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalSourcesPercentage() {
		return $this->_getPercentage($this->totalSources(), 'all', 2);
	}

	function totalNotes() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}other WHERE o_type=? AND o_file=?")
			->execute(array('NOTE', $this->_ged_id))
			->fetchOne();
	}

	function totalNotesPercentage() {
		return $this->_getPercentage($this->totalNotes(), 'all', 2);
	}

	function totalOtherRecords() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}other WHERE o_type<>? AND o_file=?")
			->execute(array('NOTE', $this->_ged_id))
			->fetchOne();
	}

	function totalOtherPercentage() {
		return $this->_getPercentage($this->totalOtherRecords(), 'all', 2);
	}

	function totalSurnames($params = null) {
		global $DBTYPE, $TBLPREFIX;
		if ($params) {
			$qs=implode(',', array_fill(0, count($params), '?'));
			$opt="IN ({$qs})";
			$vars=$params;
			$distinct='';
		} else {
			$opt ="IS NOT NULL";
			$vars='';
			$distinct='DISTINCT';
		}
		$vars[]=$this->_ged_id;
		return (int)
			WT_DB::prepare("SELECT COUNT({$distinct} n_surn) FROM {$TBLPREFIX}name WHERE n_surn {$opt} AND n_file=?")
			->execute($vars)
			->fetchOne();
	}

	function totalGivennames($params = null) {
		global $DBTYPE, $TBLPREFIX;
		if ($params) {
			$qs=implode(',', array_fill(0, count($params), '?'));
			$opt="IN ({$qs})";
			$vars=$params;
			$distinct='';
		} else {
			$opt ="IS NOT NULL";
			$vars='';
			$distinct='DISTINCT';
		}
		$vars[]=$this->_ged_id;
		return (int)
			WT_DB::prepare("SELECT COUNT({$distinct} n_givn) FROM {$TBLPREFIX}name WHERE n_givn {$opt} AND n_file=?")
			->execute($vars)
			->fetchOne();
	}

	function totalEvents($params = null) {
		global $TBLPREFIX;

		$sql="SELECT COUNT(*) AS tot FROM {$TBLPREFIX}dates WHERE d_file=?";
		$vars=array($this->_ged_id);

		$no_types=array('HEAD', 'CHAN');
		if ($params) {
			$types=array();
			foreach ($params as $type) {
				if (substr($type, 0, 1)=='!') {
					$no_types[]=substr($type, 1);
				} else {
					$types[]=$type;
				}
			}
			if ($types) {
				$sql.=' AND d_fact IN ('.implode(', ', array_fill(0, count($types), '?')).')';
				$vars=array_merge($vars, $types);
			}
		}
		$sql.=' AND d_fact NOT IN ('.implode(', ', array_fill(0, count($no_types), '?')).')';
		$vars=array_merge($vars, $no_types);
		return WT_DB::prepare($sql)->execute($vars)->fetchOne();
	}

	function totalEventsBirth() {
		return $this->totalEvents(explode('|',WT_EVENTS_BIRT));
	}

	function totalBirths() {
		return $this->totalEvents(array('BIRT'));
	}

	function totalEventsDeath() {
		return $this->totalEvents(explode('|',WT_EVENTS_DEAT));
	}

	function totalDeaths() {
		return $this->totalEvents(array('DEAT'));
	}

	function totalEventsMarriage() {
		return $this->totalEvents(explode('|',WT_EVENTS_MARR));
	}

	function totalMarriages() {
		return $this->totalEvents(array('MARR'));
	}

	function totalEventsDivorce() {
		return $this->totalEvents(explode('|',WT_EVENTS_DIV));
	}

	function totalDivorces() {
		return $this->totalEvents(array('DIV'));
	}

	function totalEventsOther() {
		$facts = array_merge(explode('|', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT));
		$no_facts = array();
		foreach ($facts as $fact) {
			$fact = '!'.str_replace('\'', '', $fact);
			$no_facts[] = $fact;
		}
		return $this->totalEvents($no_facts);
	}

	function totalSexMales() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_sex=?")
			->execute(array($this->_ged_id, 'M'))
			->fetchOne();
	}

	function totalSexMalesPercentage() {
		return $this->_getPercentage($this->totalSexMales(), 'individual');
	}

	function totalSexFemales() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_sex=?")
			->execute(array($this->_ged_id, 'F'))
			->fetchOne();
	}

	function totalSexFemalesPercentage() {
		return $this->_getPercentage($this->totalSexFemales(), 'individual');
	}

	function totalSexUnknown() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_sex=?")
			->execute(array($this->_ged_id, 'U'))
			->fetchOne();
	}

	function totalSexUnknownPercentage() {
		return $this->_getPercentage($this->totalSexUnknown(), 'individual');
	}

	function chartSex($params=null) {
		global $TEXT_DIRECTION, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_female = strtolower($params[1]);}else{$color_female = 'ffd1dc';}
		if (isset($params[2]) && $params[2] != '') {$color_male = strtolower($params[2]);}else{$color_male = '84beff';}
		if (isset($params[3]) && $params[3] != '') {$color_unknown = strtolower($params[3]);}else{$color_unknown = '777777';}
		$sizes = explode('x', $size);
		$tot_f = $this->totalSexFemalesPercentage();
		$tot_m = $this->totalSexMalesPercentage();
		$tot_u = $this->totalSexUnknownPercentage();
		if ($tot_f == 0 && $tot_m == 0 && $tot_u == 0) {
			return '';
		} else if ($tot_u > 0) {
			$chd = self::_array_to_extended_encoding(array($tot_u, $tot_f, $tot_m));
			$chl =
				i18n::translate('Unknown').' - '.round($tot_u,1).'%|'.
				i18n::translate('Females').' - '.round($tot_f,1).'%|'.
				i18n::translate('Males').' - '.round($tot_m,1).'%';
			$chart_title =
				i18n::translate('Males').' ['.round($tot_m,1).'%], '.
				i18n::translate('Females').' ['.round($tot_f,1).'%], '.
				i18n::translate('Unknown').' ['.round($tot_u,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_unknown},{$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		} else {
			$chd = self::_array_to_extended_encoding(array($tot_f, $tot_m));
			$chl =
				i18n::translate('Females').' - '.round($tot_f,1).'%|'.
				i18n::translate('Males').' - '.round($tot_m,1).'%';
			$chart_title =  i18n::translate('Males').' ['.round($tot_m,1).'%], '.
							i18n::translate('Females').' ['.round($tot_f,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	function totalLiving() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_isdead=?")
			->execute(array($this->_ged_id, 0))
			->fetchOne();
	}

	function totalLivingPercentage() {
		return $this->_getPercentage($this->totalLiving(), 'individual');
	}

	function totalDeceased() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_isdead=?")
			->execute(array($this->_ged_id, 1))
			->fetchOne();
	}

	function totalDeceasedPercentage() {
		return $this->_getPercentage($this->totalDeceased(), 'individual');
	}

	function totalMortalityUnknown() {
		global $TBLPREFIX;
		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_isdead=?")
			->execute(array($this->_ged_id, -1))
			->fetchOne();
	}

	function totalMortalityUnknownPercentage() {
		return $this->_getPercentage($this->totalMortalityUnknown(), 'individual');
	}

	function mortalityUnknown() {
		global $TBLPREFIX;
		$rows=self::_runSQL("SELECT i_id AS id FROM {$TBLPREFIX}individuals WHERE i_file={$this->_ged_id} AND i_isdead=-1");
		if (!isset($rows[0])) {return '';}
		return $rows;
	}

	function chartMortality($params=null) {
		global $TEXT_DIRECTION, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_living = strtolower($params[1]);}else{$color_living = 'ffffff';}
		if (isset($params[2]) && $params[2] != '') {$color_dead = strtolower($params[2]);}else{$color_dead = 'cccccc';}
		if (isset($params[3]) && $params[3] != '') {$color_unknown = strtolower($params[3]);}else{$color_unknown = '777777';}
		$sizes = explode('x', $size);
		$tot_l = $this->totalLivingPercentage();
		$tot_d = $this->totalDeceasedPercentage();
		$tot_u = $this->totalMortalityUnknownPercentage();
		if ($tot_l == 0 && $tot_d == 0 && $tot_u == 0) {
			return '';
		} else if ($tot_u > 0) {
			$chd = self::_array_to_extended_encoding(array($tot_u, $tot_l, $tot_d));
			$chl =
				i18n::translate('Unknown').' - '.round($tot_u,1).'%|'.
				i18n::translate('Living').' - '.round($tot_l,1).'%|'.
				i18n::translate('Dead').' - '.round($tot_d,1).'%';
			$chart_title =
				i18n::translate('Living').' ['.round($tot_l,1).'%], '.
				i18n::translate('Dead').' ['.round($tot_d,1).'%], '.
				i18n::translate('Unknown').' ['.round($tot_u,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_unknown},{$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		} else {
			$chd = self::_array_to_extended_encoding(array($tot_l, $tot_d));
			$chl =
				i18n::translate('Living').' - '.round($tot_l,1).'%|'.
				i18n::translate('Dead').' - '.round($tot_d,1).'%|';
			$chart_title =  i18n::translate('Living').' ['.round($tot_l,1).'%], '.
							i18n::translate('Dead').' ['.round($tot_d,1).'%]';
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	static function totalUsers($params=null) {
		if (!empty($params[0])) {
			return get_user_count() + (int)$params[0];
		} else {
			return get_user_count();
		}
	}

	static function totalAdmins() {
		return get_admin_user_count();
	}

	static function totalNonAdmins() {
		return get_non_admin_user_count();
	}

	function _totalMediaType($type='all') {
		global $TBLPREFIX, $MULTI_MEDIA;

		if (!$MULTI_MEDIA || !in_array($type, self::$_media_types) && $type != 'all' && $type != 'unknown') {
			return 0;
		}
		$sql="SELECT COUNT(*) AS tot FROM {$TBLPREFIX}media WHERE m_gedfile=?";
		$vars=array($this->_ged_id);

		if ($type != 'all') {
			if ($type=='unknown') {
				// There has to be a better way then this :(
				foreach (self::$_media_types as $t) {
					$sql.=" AND (m_gedrec NOT LIKE ? AND m_gedrec NOT LIKE ?)";
					$vars[]="%3 TYPE {$t}%";
					$vars[]="%1 _TYPE {$t}%";
				}
			} else {
				$sql.=" AND (m_gedrec LIKE ? OR m_gedrec LIKE ?)";
				$vars[]="%3 TYPE {$type}%";
				$vars[]="%1 _TYPE {$type}%";
			}
		}
		return WT_DB::prepare($sql)->execute($vars)->fetchOne();
	}

	function totalMedia() {return $this->_totalMediaType('all');}
	function totalMediaAudio() {return $this->_totalMediaType('audio');}
	function totalMediaBook() {return $this->_totalMediaType('book');}
	function totalMediaCard() {return $this->_totalMediaType('card');}
	function totalMediaCertificate() {return $this->_totalMediaType('certificate');}
	function totalMediaCoatOfArms() {return $this->_totalMediaType('coat');}
	function totalMediaDocument() {return $this->_totalMediaType('document');}
	function totalMediaElectronic() {return $this->_totalMediaType('electronic');}
	function totalMediaMagazine() {return $this->_totalMediaType('magazine');}
	function totalMediaManuscript() {return $this->_totalMediaType('manuscript');}
	function totalMediaMap() {return $this->_totalMediaType('map');}
	function totalMediaFiche() {return $this->_totalMediaType('fiche');}
	function totalMediaFilm() {return $this->_totalMediaType('film');}
	function totalMediaNewspaper() {return $this->_totalMediaType('newspaper');}
	function totalMediaPainting() {return $this->_totalMediaType('painting');}
	function totalMediaPhoto() {return $this->_totalMediaType('photo');}
	function totalMediaTombstone() {return $this->_totalMediaType('tombstone');}
	function totalMediaVideo() {return $this->_totalMediaType('video');}
	function totalMediaOther() {return $this->_totalMediaType('other');}
	function totalMediaUnknown() {return $this->_totalMediaType('unknown');}

	function chartMedia($params=null) {
		global $TEXT_DIRECTION, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y, $MEDIA_TYPES;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot = $this->_totalMediaType('all');
		// Beware divide by zero
		if ($tot==0) return i18n::translate('None');
		// Build a table listing only the media types actually present in the GEDCOM
		$mediaCounts = array();
		$mediaTypes = "";
		$chart_title = "";
		$c = 0;
		$max = 0;
		foreach (self::$_media_types as $type) {
			$count = $this->_totalMediaType($type);
			if ($count>0) {
				$media[$type] = $count;
				if ($count > $max) {
					$max = $count;
				}
				$c += $count;
			}
		}
		$count = $this->totalMediaUnknown();
		if ($count>0) {
			$media['unknown'] = $tot-$c;
			if ($tot-$c > $max) {
				$max = $count;
			}
		}
		if (($max/$tot)>0.6 && count($media)>10) {
			arsort($media);
			$media = array_slice($media, 0, 10);
			$c = $tot;
			foreach ($media as $cm) {
				$c -= $cm;
			}
			if (isset($media['other'])) {
				$media['other'] += $c;
			} else {
				$media['other'] = $c;
			}
		}
		asort($media);
		foreach ($media as $type=>$count) {
			$mediaCounts[] = round(100 * $count / $tot, 0);
			if (array_key_exists($type, $MEDIA_TYPES)) {
				$mediaTypes .= $MEDIA_TYPES[$type].' - '.$count.'|';
				$chart_title .= $MEDIA_TYPES[$type].' ['.$count.'], ';
			} else {
				$mediaTypes .= i18n::translate('unknown').' - '.$count.'|';
				$chart_title .= i18n::translate('unknown').' ['.$count.'], ';
			}
		}
		$chart_title = substr($chart_title,0,-2);
		$chd = self::_array_to_extended_encoding($mediaCounts);
		$chl = substr($mediaTypes,0,-1);
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

///////////////////////////////////////////////////////////////////////////////
// Birth & Death                                                             //
///////////////////////////////////////////////////////////////////////////////

	function _mortalityQuery($type='full', $life_dir='ASC', $birth_death='BIRT') {
		global $TBLPREFIX, $SHOW_ID_NUMBERS, $listDir, $DBTYPE, $TEXT_DIRECTION;
		if ($birth_death == 'MARR') {
			$query_field = "'".str_replace('|', "','", WT_EVENTS_MARR)."'";
		} else if ($birth_death == 'DIV') {
			$query_field = "'".str_replace('|', "','", WT_EVENTS_DIV)."'";
		} else if ($birth_death == 'BIRT') {
			$query_field = "'".str_replace('|', "','", WT_EVENTS_BIRT)."'";
		} else {
			$birth_death = 'DEAT';
			$query_field = "'".str_replace('|', "','", WT_EVENTS_DEAT)."'";
		}
		if ($life_dir == 'ASC') {
			$dmod = 'MIN';
		} else {
			$dmod = 'MAX';
			$life_dir = 'DESC';
		}
		$rows=self::_runSQL(''
			."SELECT d_year, d_type, d_fact, d_gid"
			." FROM {$TBLPREFIX}dates"
			." WHERE d_file={$this->_ged_id} AND d_fact IN ({$query_field}) AND d_julianday1<>0"
			." ORDER BY d_julianday1 {$life_dir}, d_type",
			1
		);
		//testing
		/*
		$rows=self::_runSQL(''
			.' SELECT'
				.' d2.d_year,'
				.' d2.d_type,'
				.' d2.d_fact,'
				.' d2.d_gid'
			.' FROM'
				." {$TBLPREFIX}dates AS d2"
			.' WHERE'
				." d2.d_file={$this->_ged_id} AND"
				." d2.d_fact IN ({$query_field}) AND"
				.' d2.d_julianday1=('
					.' SELECT'
						." {$dmod}(d_julianday1)"
					.' FROM'
						." {$TBLPREFIX}dates"
					.' JOIN ('
						.' SELECT'
							.' d1.d_gid, MIN(d1.d_julianday1) as date'
						.' FROM'
							."  {$TBLPREFIX}dates AS d1"
						.' WHERE'
							." d1.d_fact IN ({$query_field}) AND"
							." d1.d_file={$this->_ged_id} AND"
							.' d1.d_julianday1!=0'
						.' GROUP BY'
							.' d1.d_gid'
					.') AS d3'
					.' WHERE'
						." d_file={$this->_ged_id} AND"
						." d_fact IN ({$query_field}) AND"
						.' d_julianday1=date'
				.' )'
			.' ORDER BY'
				." d_julianday1 {$life_dir}, d_type"
		);
		*/
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		$record=GedcomRecord::getInstance($row['d_gid']);
		switch($type) {
			default:
			case 'full':
				if ($record->canDisplayDetails()) {
					$result=$record->format_list('span', false, $record->getFullName());
				} else {
					$result=i18n::translate('This information is private and cannot be shown.');
				}
				break;
			case 'year':
				$date=new GedcomDate($row['d_type'].' '.$row['d_year']);
				$result=$date->Display(true);
				break;
			case 'name':
				$id='';
				if ($SHOW_ID_NUMBERS) {
					if ($listDir=='rtl' || $TEXT_DIRECTION=='rtl') { //do we need $listDir here?
						$id="&nbsp;&nbsp;" . getRLM() . "({$row['d_gid']})" . getRLM();
					} else {
						$id="&nbsp;&nbsp;({$row['d_gid']})";
					}
				}
				$result="<a href=\"".$record->getLinkUrl()."\">".$record->getFullName()."{$id}</a>";
				break;
			case 'place':
				$result=format_fact_place(GedcomRecord::getInstance($row['d_gid'])->getFactByType($row['d_fact']), true, true, true);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _statsPlaces($what='ALL', $fact=false, $parent=0, $country=false) {
		global $TBLPREFIX;
		if ($fact) {
			if ($what=='INDI') {
				$rows=
					WT_DB::prepare("SELECT i_gedcom AS ged FROM {$TBLPREFIX}individuals WHERE i_file=?")
					->execute(array($this->_ged_id))
					->fetchAll();
			}
			else if ($what=='FAM') {
				$rows=
					WT_DB::prepare("SELECT f_gedcom AS ged FROM {$TBLPREFIX}families WHERE f_file=?")
					->execute(array($this->_ged_id))
					->fetchAll();
			}
			$placelist = array();
			foreach ($rows as $row) {
				$factrec = trim(get_sub_record(1, "1 {$fact}", $row->ged, 1));
				if (!empty($factrec) && preg_match("/2 PLAC (.+)/", $factrec, $match)) {
					if ($country) {
						$place = getPlaceCountry(trim($match[1]));
					}
					else {
						$place = trim($match[1]);
					}
					if (!isset($placelist[$place])) {
						$placelist[$place] = 1;
					}
					else {
						$placelist[$place] ++;
					}
				}
			}
			return $placelist;
		}
		// used by placehierarchy googlemap module
		else if ($parent>0) {
			if ($what=='INDI') {
				$join = " JOIN {$TBLPREFIX}individuals ON pl_file = i_file AND pl_gid = i_id";
			}
			else if ($what=='FAM') {
				$join = " JOIN {$TBLPREFIX}families ON pl_file = f_file AND pl_gid = f_id";
			}
			else {
				$join = "";
			}
			$rows=self::_runSQL(''
				.' SELECT'
				.' p_place AS place,'
				.' COUNT(*) AS tot'
				.' FROM'
					." {$TBLPREFIX}places"
				." JOIN {$TBLPREFIX}placelinks ON pl_file=p_file AND p_id=pl_p_id"
				.$join
				.' WHERE'
					." p_id={$parent} AND"
					." p_file={$this->_ged_id}"
				.' GROUP BY place'
			);
			if (!isset($rows[0])) {return '';}
			return $rows;
		}
		else {
			if ($what=='INDI') {
				$join = " JOIN {$TBLPREFIX}individuals ON pl_file = i_file AND pl_gid = i_id";
			}
			else if ($what=='FAM') {
				$join = " JOIN {$TBLPREFIX}families ON pl_file = f_file AND pl_gid = f_id";
			}
			else {
				$join = "";
			}
			$rows=self::_runSQL(''
					.' SELECT'
						.' p_place AS country,'
						.' COUNT(*) AS tot'
					.' FROM'
						." {$TBLPREFIX}places"
					." JOIN {$TBLPREFIX}placelinks ON pl_file=p_file AND p_id=pl_p_id"
					.$join
					.' WHERE'
						." p_file={$this->_ged_id}"
						." AND p_parent_id='0'"
					.' GROUP BY country ORDER BY tot DESC, country ASC'
					);
			if (!isset($rows[0])) {return '';}
			return $rows;
		}
	}

	function totalPlaces() {
		global $TBLPREFIX;

		return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}places WHERE p_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function chartDistribution($chart_shows='world', $chart_type='', $surname='') {
		global $iso3166, $countries;
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_CHART_COLOR3, $WT_STATS_MAP_X, $WT_STATS_MAP_Y;

		if ($this->totalPlaces()==0) return '';

		// TODO: add translations from *ALL* languages, not just the current one.
		// TODO (longer term): use a proper geographic database!
		$country_to_iso3166=array();
		foreach ($iso3166 as $three=>$two) {
			$country_to_iso3166[$three]=$two;
			$country_to_iso3166[$countries[$three]]=$two;
		}
		switch ($chart_type) {
		case 'surname_distribution_chart':
			if ($surname=="") $surname = $this->getCommonSurname();
			$chart_title=i18n::translate('Surname distribution chart').': '.$surname;
			// Count how many people are events in each country
			$surn_countries=array();
			$indis = get_indilist_indis(utf8_strtoupper($surname), '', '', false, false, WT_GED_ID);
			foreach ($indis as $person) {
				if (preg_match_all('/^2 PLAC (?:.*, *)*(.*)/m', $person->gedrec, $matches)) {
					// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
					foreach ($matches[1] as $country) {
						$country=trim($country);
						if (array_key_exists($country, $country_to_iso3166)) {
							if (array_key_exists($country_to_iso3166[$country], $surn_countries)) {
								$surn_countries[$country_to_iso3166[$country]]++;
							} else {
								$surn_countries[$country_to_iso3166[$country]]=1;
							}
						}
					}
				}
			};
			break;
		case 'birth_distribution_chart':
			$chart_title=i18n::translate('Birth by country');
			// Count how many people were born in each country
			$surn_countries=array();
			$b_countries=$this->_statsPlaces('INDI', 'BIRT', 0, true);
			foreach ($b_countries as $place=>$count) {
				$country=$place;
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]]=$count;
					}
					else {
						$surn_countries[$country_to_iso3166[$country]]+=$count;
					}
				}
			}
			break;
		case 'death_distribution_chart':
			$chart_title=i18n::translate('Death by country');
			// Count how many people were death in each country
			$surn_countries=array();
			$d_countries=$this->_statsPlaces('INDI', 'DEAT', 0, true);
			foreach ($d_countries as $place=>$count) {
				$country=$place;
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]]=$count;
					}
					else {
						$surn_countries[$country_to_iso3166[$country]]+=$count;
					}
				}
			}
			break;
		case 'marriage_distribution_chart':
			$chart_title=i18n::translate('Marriage by country');
			// Count how many families got marriage in each country
			$surn_countries=array();
			$m_countries=$this->_statsPlaces('FAM');
			// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			foreach ($m_countries as $place) {
				$country=trim($place['country']);
				if (array_key_exists($country, $country_to_iso3166)) {
					$surn_countries[$country_to_iso3166[$country]]=$place['tot'];
				}
			}
			break;
		case 'indi_distribution_chart':
		default:
			$chart_title=i18n::translate('Individual distribution chart');
			// Count how many people are events in each country
			$surn_countries=array();
			$a_countries=$this->_statsPlaces('INDI');
			// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			foreach ($a_countries as $place) {
				$country=trim($place['country']);
				if (array_key_exists($country, $country_to_iso3166)) {
					$surn_countries[$country_to_iso3166[$country]]=$place['tot'];
				}
			}
			break;
		}
		$chart_url ="http://chart.apis.google.com/chart?cht=t&amp;chtm=".$chart_shows;
		$chart_url.="&amp;chco=".$WT_STATS_CHART_COLOR1.",".$WT_STATS_CHART_COLOR3.",".$WT_STATS_CHART_COLOR2; // country colours
		$chart_url.="&amp;chf=bg,s,ECF5FF"; // sea colour
		$chart_url.="&amp;chs=".$WT_STATS_MAP_X."x".$WT_STATS_MAP_Y;
		$chart_url.="&amp;chld=".implode('', array_keys($surn_countries))."&amp;chd=s:";
		foreach ($surn_countries as $count) {
			$chart_url.=substr(WT_GOOGLE_CHART_ENCODING, floor($count/max($surn_countries)*61), 1);
		}
		$chart = '<div id="google_charts" class="center">';
		$chart .= '<b>'.$chart_title.'</b><br /><br />';
		$chart .= '<div align="center"><img src="'.$chart_url.'" alt="'.$chart_title.'" title="'.$chart_title.'" class="gchart" /><br />';
		$chart .= '<table align="center" border="0" cellpadding="1" cellspacing="1"><tr>';
		$chart .= '<td bgcolor="#'.$WT_STATS_CHART_COLOR2.'" width="12"></td><td>'.i18n::translate('Highest population').'&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#'.$WT_STATS_CHART_COLOR3.'" width="12"></td><td>'.i18n::translate('Lowest population').'&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#'.$WT_STATS_CHART_COLOR1.'" width="12"></td><td>'.i18n::translate('Nobody at all').'&nbsp;&nbsp;</td>';
		$chart .= '</tr></table></div></div>';
		return $chart;
	}

	function commonCountriesList() {
		global $TEXT_DIRECTION;
		$countries = $this->_statsPlaces();
		if (!is_array($countries)) return '';
		$top10 = array();
		$i = 1;
		foreach ($countries as $country) {
			$place = '<a href="'.encode_url(get_place_url($country['country'])).'" class="list_item">'.PrintReady($country['country']).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$country['tot']."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonBirthPlacesList() {
		global $TEXT_DIRECTION;
		$places = $this->_statsPlaces('INDI', 'BIRT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.encode_url(get_place_url($place)).'" class="list_item">'.PrintReady($place).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$count."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonDeathPlacesList() {
		global $TEXT_DIRECTION;
		$places = $this->_statsPlaces('INDI', 'DEAT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.encode_url(get_place_url($place)).'" class="list_item">'.PrintReady($place).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$count."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonMarriagePlacesList() {
		global $TEXT_DIRECTION;
		$places = $this->_statsPlaces('FAM', 'MARR');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.encode_url(get_place_url($place)).'" class="list_item">'.PrintReady($place).'</a>';
			$top10[]="\t<li>".$place." ".PrintReady("[".$count."]")."</li>\n";
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function statsBirth($simple=true, $sex=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						.'d_year<>0 AND '
						."d_fact='BIRT' AND "
						."d_type='@#DGREGORIAN@'";
		} else if ($sex) {
			$sql = "SELECT d_month, i_sex, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."JOIN {$TBLPREFIX}individuals ON d_file = i_file AND d_gid = i_id "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='BIRT' AND "
						."d_type='@#DGREGORIAN@'";
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='BIRT' AND "
						."d_type='@#DGREGORIAN@'";
		}
		if ($year1>=0 && $year2>=0) {
			$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
		}
		if ($simple) {
			$sql .= " GROUP BY century ORDER BY century";
		} else {
			$sql .= " GROUP BY d_month";
			if ($sex) $sql .= ", i_sex";
		}
		$rows=self::_runSQL($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			$func="century_localisation_".WT_LOCALE;
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $century.' - '.$values['total'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Births by century')."\" title=\"".i18n::translate('Births by century')."\" />";
		}
		if (!isset($rows)) return 0;
		return $rows;
	}

	function statsDeath($simple=true, $sex=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						.'d_year<>0 AND '
						."d_fact='DEAT' AND "
						."d_type='@#DGREGORIAN@'";
		} else if ($sex) {
			$sql = "SELECT d_month, i_sex, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."JOIN {$TBLPREFIX}individuals ON d_file = i_file AND d_gid = i_id "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='DEAT' AND "
						."d_type='@#DGREGORIAN@'";
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='DEAT' AND "
						."d_type='@#DGREGORIAN@'";
		}
		if ($year1>=0 && $year2>=0) {
			$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
		}
		if ($simple) {
			$sql .= " GROUP BY century ORDER BY century";
		} else {
			$sql .= " GROUP BY d_month";
			if ($sex) $sql .= ", i_sex";
		}
		$rows=self::_runSQL($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			$func="century_localisation_".WT_LOCALE;
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $century.' - '.$values['total'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Deaths by century')."\" title=\"".i18n::translate('Deaths by century')."\" />";
		}
		if (!isset($rows)) {return 0;}
		return $rows;
	}

	//
	// Birth
	//

	function firstBirth() {return $this->_mortalityQuery('full', 'ASC', 'BIRT');}
	function firstBirthYear() {return $this->_mortalityQuery('year', 'ASC', 'BIRT');}
	function firstBirthName() {return $this->_mortalityQuery('name', 'ASC', 'BIRT');}
	function firstBirthPlace() {return $this->_mortalityQuery('place', 'ASC', 'BIRT');}

	function lastBirth() {return $this->_mortalityQuery('full', 'DESC', 'BIRT');}
	function lastBirthYear() {return $this->_mortalityQuery('year', 'DESC', 'BIRT');}
	function lastBirthName() {return $this->_mortalityQuery('name', 'DESC', 'BIRT');}
	function lastBirthPlace() {return $this->_mortalityQuery('place', 'DESC', 'BIRT');}

	//
	// Death
	//

	function firstDeath() {return $this->_mortalityQuery('full', 'ASC', 'DEAT');}
	function firstDeathYear() {return $this->_mortalityQuery('year', 'ASC', 'DEAT');}
	function firstDeathName() {return $this->_mortalityQuery('name', 'ASC', 'DEAT');}
	function firstDeathPlace() {return $this->_mortalityQuery('place', 'ASC', 'DEAT');}

	function lastDeath() {return $this->_mortalityQuery('full', 'DESC', 'DEAT');}
	function lastDeathYear() {return $this->_mortalityQuery('year', 'DESC', 'DEAT');}
	function lastDeathName() {return $this->_mortalityQuery('name', 'DESC', 'DEAT');}
	function lastDeathPlace() {return $this->_mortalityQuery('place', 'DESC', 'DEAT');}

///////////////////////////////////////////////////////////////////////////////
// Lifespan                                                                  //
///////////////////////////////////////////////////////////////////////////////

	function _longlifeQuery($type='full', $sex='F') {
		global $TBLPREFIX, $SHOW_ID_NUMBERS, $listDir;

		$sex_search = ' 1=1';
		if ($sex == 'F') {
			$sex_search = " i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " i_sex='M'";
		}

		$rows=self::_runSQL(''
			.' SELECT'
				.' death.d_gid AS id,'
				.' death.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_ged_id} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1<>0 AND'
				.' death.d_julianday1>birth.d_julianday2 AND'
				.$sex_search
			.' ORDER BY'
				.' age DESC'
		, 1);
		//testing
		/*
		$rows=self::_runSQL(''
			.' SELECT'
				.' i_id AS id,'
				.' death.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				.' (SELECT d_gid, d_file, MIN(d_julianday1) AS birth_jd'
					.' FROM {$TBLPREFIX}date'
					." WHERE d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND d_julianday1>0"
					.' GROUP BY d_gid, d_file'
				.' ) AS birth'
			.' JOIN ('
				.' SELECT d_gid, d_file, MIN(d_julianday1) AS death_jd'
					.' FROM {$TBLPREFIX}date'
					." WHERE d_fact IN ('DEAT', 'BURI', 'CREM') AND d_julianday1>0"
					.' GROUP BY d_gid, d_file'
				.' ) AS death USING (d_gid, d_file)'
			.' JOIN {$TBLPREFIX}individuals ON (d_gid=i_id AND d_file=i_file)'
			.' WHERE'
				." i_file={$this->_ged_id} AND"
				.$sex_search
			.' ORDER BY'
				.' age DESC'
		, 1);
		*/
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$person=Person::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if (displayDetailsById($row['id'])) {
					$result=$person->format_list('span', false, $person->getFullName());
				} else {
					$result= i18n::translate('This information is private and cannot be shown.');
				}
				break;
			case 'age':
				$result=floor($row['age']/365.25);
				break;
			case 'name':
				$id = '';
				if ($SHOW_ID_NUMBERS) {
					if ($listDir == 'rtl') {
						$id = "&nbsp;&nbsp;".getRLM()."({$row['id']})".getRLM();
					} else {
						$id = "&nbsp;&nbsp;({$row['id']})";
					}
				}
				$result="<a href=\"".encode_url($person->getLinkUrl())."\">".$person->getFullName()."{$id}</a>";
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenOldest($type='list', $sex='BOTH', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION;

		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT '
				.' MAX(death.d_julianday2-birth.d_julianday1) AS age,'
				.' death.d_gid AS deathdate'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_ged_id} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1<>0 AND'
				.' death.d_julianday1>birth.d_julianday2'
				.$sex_search
			.' GROUP BY'
				.' deathdate'
			.' ORDER BY'
				.' age DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		$func = "age_localisation_".WT_LOCALE;
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $row) {
			$person = Person::getInstance($row['deathdate']);
			$age = $row['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($person->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[]="\t<li><a href=\"".encode_url($person->getLinkUrl())."\">".PrintReady($person->getFullName()."</a> [".$age."]")."</li>\n";
				} else {
					$top10[]="<a href=\"".encode_url($person->getLinkUrl())."\">".PrintReady($person->getFullName()."</a> [".$age."]");
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10=join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		// Statstics are used by RSS feeds, etc., so need absolute URLs.
		return $top10;
	}

	function _topTenOldestAlive($type='list', $sex='BOTH', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION;

		if (!WT_USER_CAN_ACCESS) return i18n::translate('This information is private and cannot be shown.');
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT'
				.' birth.d_gid AS id,'
				.' MIN(birth.d_julianday1) AS age'
			.' FROM'
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' indi.i_isdead=0 AND'
				." birth.d_file={$this->_ged_id} AND"
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				.' birth.d_julianday1<>0'
				.$sex_search
			.' GROUP BY'
				.' id'
			.' ORDER BY'
				.' age ASC'
		, $total);
		if (!isset($rows)) {return 0;}
		$top10 = array();
		$func = "age_localisation_".WT_LOCALE;
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $row) {
			$person=Person::getInstance($row['id']);
			$age = (client_jd()-$row['age']);
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($type == 'list') {
				$top10[]="\t<li><a href=\"".encode_url($person->getLinkUrl())."\">".PrintReady($person->getFullName()."</a> [".$age."]")."</li>\n";
			} else {
				$top10[]="<a href=\"".encode_url($person->getLinkUrl())."\">".PrintReady($person->getFullName()."</a> [".$age."]");
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10=join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array("[", "]", "(", ")", "+"), array("&rlm;[", "&rlm;]", "&rlm;(", "&rlm;)", "&rlm;+"), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _averageLifespanQuery($sex='BOTH', $show_years=false) {
		global $TBLPREFIX;
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		$rows=self::_runSQL(''
			.' SELECT'
				.' AVG(death.d_julianday2-birth.d_julianday1) AS age'
			.' FROM'
				." {$TBLPREFIX}dates AS death,"
				." {$TBLPREFIX}dates AS birth,"
				." {$TBLPREFIX}individuals AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_ged_id} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." death.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' birth.d_julianday1<>0 AND'
				.' death.d_julianday1>birth.d_julianday2'
				.$sex_search
		, 1);
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$age = $row['age'];
		if ($show_years) {
			$func = "age_localisation_".WT_LOCALE;
			if (!function_exists($func)) {
				$func="DefaultAgeLocalisation";
			}
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else if (!empty($age)) {
				$age = $age.'d';
			}
			$func($age, $show_years);
			return $age;
		} else {
			return floor($age/365.25);
		}
	}

	function statsAge($simple=true, $related='BIRT', $sex='BOTH', $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX;

		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '230x250';}
			$sizes = explode('x', $size);
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(death.d_julianday2-birth.d_julianday1)/365.25,1) AS age,'
					.' ROUND((death.d_year+49.1)/100) AS century,'
					.' i_sex AS sex'
				.' FROM'
					." {$TBLPREFIX}dates AS death,"
					." {$TBLPREFIX}dates AS birth,"
					." {$TBLPREFIX}individuals AS indi"
				.' WHERE'
					.' indi.i_id=birth.d_gid AND'
					.' birth.d_gid=death.d_gid AND'
					." death.d_file={$this->_ged_id} AND"
					.' birth.d_file=death.d_file AND'
					.' birth.d_file=indi.i_file AND'
					." birth.d_fact='BIRT' AND"
					." death.d_fact='DEAT' AND"
					.' birth.d_julianday1<>0 AND'
					." birth.d_type='@#DGREGORIAN@' AND"
					." death.d_type='@#DGREGORIAN@' AND"
					.' death.d_julianday1>birth.d_julianday2'
				.' GROUP BY century, sex ORDER BY century, sex');
			if (empty($rows)) return '';
			$func="century_localisation_".WT_LOCALE;
			$chxl = "0:|";
			$male = true;
			$temp = "";
			$countsm = "";
			$countsf = "";
			$countsa = "";
			foreach ($rows as $values) {
				if ($temp!=$values['century']) {
					$temp = $values['century'];
					if ($sizes[0]<980) $sizes[0] += 50;
					if (function_exists($func)) {
						$century = $func($values['century'], false);
					} else {
						$century = $values['century'];
					}
					$chxl .= $century."|";
					if ($values['sex'] == "F") {
						if (!$male) {
							$countsm .= "0,";
							$countsa .= $fage.",";
						}
						$countsf .= $values['age'].",";
						$fage = $values['age'];
						$male = false;
					} else if ($values['sex'] == "M") {
						$countsf .= "0,";
						$countsm .= $values['age'].",";
						$countsa .= $values['age'].",";
					} else if ($values['sex'] == "U") {
						$countsf .= "0,";
						$countsm .= "0,";
						$countsa .= "0,";
					}
				}
				else if ($values['sex'] == "M") {
					$countsm .= $values['age'].",";
					$countsa .= round(($fage+$values['age'])/2,1).",";
					$male = true;
				}
			}
			if (!$male) {
				$countsa .= $fage.",";
			}
			$countsm = substr($countsm,0,-1);
			$countsf = substr($countsf,0,-1);
			$countsa = substr($countsa,0,-1);
			$chd = "t2:{$countsm}|{$countsf}|{$countsa}";
			$chxl .= "1:||".i18n::translate('century')."|2:|0|10|20|30|40|50|60|70|80|90|100|3:||".i18n::translate('Age')."|";
			if (count($rows)>4 || utf8_strlen(i18n::translate('Average age related to death century'))<30) {
				$chtt = i18n::translate('Average age related to death century');
			} else {
				$offset = 0;
				$counter = array();
				while($offset = strpos(i18n::translate('Average age related to death century'), " ", $offset + 1)){
					$counter[] = $offset;
				}
				$half = floor(count($counter)/2);
				$chtt = substr_replace(i18n::translate('Average age related to death century'), '|', $counter[$half], 1);
			}
			return '<img src="'.encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|N*f1*,000000,0,-1,11,1|N*f1*,000000,1,-1,11,1&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt={$chtt}&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}&amp;chdl=".i18n::translate('Males').'|'.i18n::translate('Females').'|'.i18n::translate('Average age at death'))."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Average age related to death century')."\" title=\"".i18n::translate('Average age related to death century')."\" />";
		} else {
			$sex_search = '';
			$years = '';
			if ($sex == 'F') {
				$sex_search = " AND i_sex='F'";
			} elseif ($sex == 'M') {
				$sex_search = " AND i_sex='M'";
			}
			if ($year1>=0 && $year2>=0) {
				if ($related=='BIRT') {
					$years = " AND birth.d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
				else if ($related=='DEAT') {
					$years = " AND death.d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			}
			$rows=self::_runSQL(''
				.' SELECT'
					.' death.d_julianday2-birth.d_julianday1 AS age'
				.' FROM'
					." {$TBLPREFIX}dates AS death,"
					." {$TBLPREFIX}dates AS birth,"
					." {$TBLPREFIX}individuals AS indi"
				.' WHERE'
					.' indi.i_id=birth.d_gid AND'
					.' birth.d_gid=death.d_gid AND'
					." death.d_file={$this->_ged_id} AND"
					.' birth.d_file=death.d_file AND'
					.' birth.d_file=indi.i_file AND'
					." birth.d_fact='BIRT' AND"
					." death.d_fact='DEAT' AND"
					.' birth.d_julianday1<>0 AND'
					.' death.d_julianday1>birth.d_julianday2'
					.$years
					.$sex_search
				.' ORDER BY age DESC');
			if (!isset($rows)) {return 0;}
			return $rows;
		}
	}

	// Both Sexes

	function longestLife() {return $this->_longlifeQuery('full', 'BOTH');}
	function longestLifeAge() {return $this->_longlifeQuery('age', 'BOTH');}
	function longestLifeName() {return $this->_longlifeQuery('name', 'BOTH');}

	function topTenOldest($params=null) {return $this->_topTenOldest('nolist', 'BOTH', $params);}
	function topTenOldestList($params=null) {return $this->_topTenOldest('list', 'BOTH', $params);}
	function topTenOldestAlive($params=null) {return $this->_topTenOldestAlive('nolist', 'BOTH', $params);}
	function topTenOldestListAlive($params=null) {return $this->_topTenOldestAlive('list', 'BOTH', $params);}

	function averageLifespan($show_years=false) {return $this->_averageLifespanQuery('BOTH', $show_years);}

	// Female Only

	function longestLifeFemale() {return $this->_longlifeQuery('full', 'F');}
	function longestLifeFemaleAge() {return $this->_longlifeQuery('age', 'F');}
	function longestLifeFemaleName() {return $this->_longlifeQuery('name', 'F');}

	function topTenOldestFemale($params=null) {return $this->_topTenOldest('nolist', 'F', $params);}
	function topTenOldestFemaleList($params=null) {return $this->_topTenOldest('list', 'F', $params);}
	function topTenOldestFemaleAlive($params=null) {return $this->_topTenOldestAlive('nolist', 'F', $params);}
	function topTenOldestFemaleListAlive($params=null) {return $this->_topTenOldestAlive('list', 'F', $params);}

	function averageLifespanFemale($show_years=false) {return $this->_averageLifespanQuery('F', $show_years);}

	// Male Only

	function longestLifeMale() {return $this->_longlifeQuery('full', 'M');}
	function longestLifeMaleAge() {return $this->_longlifeQuery('age', 'M');}
	function longestLifeMaleName() {return $this->_longlifeQuery('name', 'M');}

	function topTenOldestMale($params=null) {return $this->_topTenOldest('nolist', 'M', $params);}
	function topTenOldestMaleList($params=null) {return $this->_topTenOldest('list', 'M', $params);}
	function topTenOldestMaleAlive($params=null) {return $this->_topTenOldestAlive('nolist', 'M', $params);}
	function topTenOldestMaleListAlive($params=null) {return $this->_topTenOldestAlive('list', 'M', $params);}

	function averageLifespanMale($show_years=false) {return $this->_averageLifespanQuery('M', $show_years);}

///////////////////////////////////////////////////////////////////////////////
// Events                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _eventQuery($type, $direction, $facts) {
		global $TBLPREFIX, $SHOW_ID_NUMBERS, $listDir;
		$eventTypes = array(
			'BIRT'=>i18n::translate('birth'),
			'DEAT'=>i18n::translate('death'),
			'MARR'=>i18n::translate('marriage'),
			'ADOP'=>i18n::translate('adoption'),
			'BURI'=>i18n::translate('burial'),
			'CENS'=>i18n::translate('census added')
		);

		$fact_query = "IN ('".str_replace('|', "','", $facts)."')";

		if ($direction != 'ASC') {$direction = 'DESC';}
		$rows=self::_runSQL(''
			.' SELECT'
				.' d_gid AS id,'
				.' d_year AS year,'
				.' d_fact AS fact,'
				.' d_type AS type'
			.' FROM'
				." {$TBLPREFIX}dates"
			.' WHERE'
				." d_file={$this->_ged_id} AND"
				." d_gid<>'HEAD' AND"
				." d_fact {$fact_query} AND"
				.' d_julianday1<>0'
			.' ORDER BY'
				." d_julianday1 {$direction}, d_type"
		, 1);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		$record=GedcomRecord::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($record->canDisplayDetails()) {
					$result=$record->format_list('span', false, $record->getFullName());
				} else {
					$result=i18n::translate('This information is private and cannot be shown.');
				}
				break;
			case 'year':
				$date=new GedcomDate($row['type'].' '.$row['year']);
				$result=$date->Display(true);
				break;
			case 'type':
				if (isset($eventTypes[$row['fact']])) {
					$result=$eventTypes[$row['fact']];
				} else {
					$result=i18n::translate($row['fact']);
				}
				break;
			case 'name':
				$id = '';
				if ($SHOW_ID_NUMBERS) {
					if ($listDir == 'rtl') {
						$id="&nbsp;&nbsp;" . getRLM() . "({$row['id']})" . getRLM();
					} else {
						$id="&nbsp;&nbsp;({$row['id']})";
					}
				}
				$result="<a href=\"".encode_url($record->getLinkUrl())."\">".PrintReady($record->getFullName())."{$id}</a>";
				break;
			case 'place':
				$result=format_fact_place($record->getFactByType($row['fact']), true, true, true);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function firstEvent() {
		return $this->_eventQuery('full', 'ASC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function firstEventYear() {
		return $this->_eventQuery('year', 'ASC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function firstEventType() {
		return $this->_eventQuery('type', 'ASC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function firstEventName() {
		return $this->_eventQuery('name', 'ASC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function firstEventPlace() {
		return $this->_eventQuery('place', 'ASC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function lastEvent() {
		return $this->_eventQuery('full', 'DESC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function lastEventYear() {
		return $this->_eventQuery('year', 'DESC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function lastEventType() {
		return $this->_eventQuery('type', 'DESC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function lastEventName() {
		return $this->_eventQuery('name', 'DESC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}
	function lastEventPlace() {
		return $this->_eventQuery('place', 'DESC', WT_EVENTS_BIRT.'|'.WT_EVENTS_MARR.'|'.WT_EVENTS_DIV.'|'.WT_EVENTS_DEAT);
	}

///////////////////////////////////////////////////////////////////////////////
// Marriage                                                                  //
///////////////////////////////////////////////////////////////////////////////

	/*
	* Query the database for marriage tags.
	*/
	function _marriageQuery($type='full', $age_dir='ASC', $sex='F', $show_years=false) {
		global $TBLPREFIX;
		if ($sex == 'F') {$sex_field = 'f_wife';}else{$sex_field = 'f_husb';}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$rows=self::_runSQL(''
			.' SELECT'
				.' fam.f_id AS famid,'
				." fam.{$sex_field},"
				.' married.d_julianday2-birth.d_julianday1 AS age,'
				.' indi.i_id AS i_id'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' birth.d_gid = indi.i_id AND'
				.' married.d_gid = fam.f_id AND'
				." indi.i_id = fam.{$sex_field} AND"
				." fam.f_file = {$this->_ged_id} AND"
				." birth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." married.d_fact = 'MARR' AND"
				.' birth.d_julianday1 <> 0 AND'
				.' married.d_julianday2 > birth.d_julianday1 AND'
				." i_sex='{$sex}'"
			.' ORDER BY'
				." married.d_julianday2-birth.d_julianday1 {$age_dir}"
			//testing
			/*
			. 'SELECT'
				.' fam.f_id AS famid,'
				." fam.{$sex_field},"
				.' married.d_julianday2-birth.d_julianday1 AS age,'
				.' indi.i_id AS i_id'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id} AND birth.d_fact = 'BIRT'"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS birth_act ON birth_act.d_file = {$this->_ged_id} AND birth_act.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM')"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id} AND married.d_fact = 'MARR'"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' birth.d_gid = indi.i_id AND'
				.' birth_act.d_gid = indi.i_id AND'
				.' married.d_gid = fam.f_id AND'
				." indi.i_id = fam.{$sex_field} AND"
				." fam.f_file = {$this->_ged_id} AND"
				.' ((birth.d_julianday1 <> 0) OR (birth_act.d_julianday1 <> 0)) AND'
				.' ((married.d_julianday2 > birth.d_julianday1) OR (married.d_julianday2 > birth_act.d_julianday1)) AND'
				.' birth.d_julianday1 <= birth_act.d_julianday1 AND'
				." i_sex='{$sex}'"
			.' ORDER BY'
				." married.d_julianday2-birth.d_julianday1 {$age_dir}"
			*/
		, 1);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		if (isset($row['famid'])) $family=Family::getInstance($row['famid']);
		if (isset($row['i_id'])) $person=Person::getInstance($row['i_id']);
		switch($type) {
			default:
			case 'full':
				if ($family->canDisplayDetails()) {
					$result=$family->format_list('span', false, $person->getFullName());
				} else {
					$result=i18n::translate('This information is private and cannot be shown.');
				}
				break;
			case 'name':
				$result="<a href=\"".encode_url($family->getLinkUrl())."\">".$person->getFullName().'</a>';
				break;
			case 'age':
				$age = $row['age'];
				if ($show_years) {
					$func = "age_localisation_".WT_LOCALE;
					if (!function_exists($func)) {
						$func="DefaultAgeLocalisation";
					}
					if (floor($age/365.25)>0) {
						$age = floor($age/365.25).'y';
					} else if (floor($age/12)>0) {
						$age = floor($age/12).'m';
					} else {
						$age = $age.'d';
					}
					$func($age, $show_years);
					$result = $age;
				} else {
					$result = floor($age/365.25);
				}
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _ageOfMarriageQuery($type='list', $age_dir='ASC', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$hrows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.' MIN(husbdeath.d_julianday2-married.d_julianday1) AS age'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS husbdeath ON husbdeath.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' husbdeath.d_gid = fam.f_husb AND'
				." husbdeath.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' married.d_gid = fam.f_id AND'
				." married.d_fact = 'MARR' AND"
				.' married.d_julianday1 < husbdeath.d_julianday2 AND'
				.' married.d_julianday1 <> 0'
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age {$age_dir}");
		$wrows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.' MIN(wifedeath.d_julianday2-married.d_julianday1) AS age'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS wifedeath ON wifedeath.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' wifedeath.d_gid = fam.f_wife AND'
				." wifedeath.d_fact IN ('DEAT', 'BURI', 'CREM') AND"
				.' married.d_gid = fam.f_id AND'
				." married.d_fact = 'MARR' AND"
				.' married.d_julianday1 < wifedeath.d_julianday2 AND'
				.' married.d_julianday1 <> 0'
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age {$age_dir}");
		$drows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.' MIN(divorced.d_julianday2-married.d_julianday1) AS age'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS divorced ON divorced.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' married.d_gid = fam.f_id AND'
				." married.d_fact = 'MARR' AND"
				.' divorced.d_gid = fam.f_id AND'
				." divorced.d_fact IN ('DIV', 'ANUL', '_SEPR', '_DETS') AND"
				.' married.d_julianday1 < divorced.d_julianday2 AND'
				.' married.d_julianday1 <> 0'
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age {$age_dir}");
		if (!isset($hrows) && !isset($wrows) && !isset($drows)) {return 0;}
		$rows = array();
		foreach ($drows as $family) {
			$rows[$family['family']] = $family['age'];
		}
		foreach ($hrows as $family) {
			if (!isset($rows[$family['family']])) $rows[$family['family']] = $family['age'];
		}
		foreach ($wrows as $family) {
			if (!isset($rows[$family['family']])) {
				$rows[$family['family']] = $family['age'];
			} elseif ($rows[$family['family']] > $family['age']) {
				$rows[$family['family']] = $family['age'];
			}
		}
		if ($age_dir == 'DESC') {arsort($rows);}
		else {asort($rows);}
		$top10 = array();
		$i = 0;
		$func = "age_localisation_".WT_LOCALE;
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $fam=>$age) {
			$family = Family::getInstance($fam);
			if ($type == 'name') {
				return $family->format_list('span', false, $family->getFullName());
			}
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($type == 'age') {
				return $age;
			}
			$husb = $family->getHusband();
			$wife = $family->getWife();
			if (($husb->getAllDeathDates() && $wife->getAllDeathDates()) || !$husb->isDead() || !$wife->isDead()) {
				if ($family->canDisplayDetails()) {
					if ($type == 'list') {
						$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [".$age."]")."</li>\n";
					} else {
						$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [".$age."]");
					}
				}
				if (++$i==$total) break;
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _ageBetweenSpousesQuery($type='list', $age_dir='DESC', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		if ($age_dir=='DESC') {
			$query1 = ' MIN(wifebirth.d_julianday2-husbbirth.d_julianday1) AS age';
			$query2 = ' wifebirth.d_julianday2 >= husbbirth.d_julianday1 AND'
					 .' husbbirth.d_julianday1 <> 0';
		} else {
			$query1 = ' MIN(husbbirth.d_julianday2-wifebirth.d_julianday1) AS age';
			$query2 = ' wifebirth.d_julianday1 < husbbirth.d_julianday2 AND'
					 .' wifebirth.d_julianday1 <> 0';
		}
		$rows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.$query1
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS wifebirth ON wifebirth.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS husbbirth ON husbbirth.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' husbbirth.d_gid = fam.f_husb AND'
				." husbbirth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				.' wifebirth.d_gid = fam.f_wife AND'
				." wifebirth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				.$query2
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age DESC"
		,$total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		$func = "age_localisation_".WT_LOCALE;
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		foreach ($rows as $fam) {
			$family=Family::getInstance($fam['family']);
			if ($fam['age']<0) break;
			$age = $fam['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [".$age."]")."</li>\n";
				} else {
					$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [".$age."]");
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _parentsQuery($type='full', $age_dir='ASC', $sex='F', $show_years=false) {
		global $TBLPREFIX;
		if ($sex == 'F') {$sex_field = 'WIFE';}else{$sex_field = 'HUSB';}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$rows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' parentfamily.l_to AS id,'
				.' childbirth.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." {$TBLPREFIX}link AS parentfamily"
			.' JOIN'
				." {$TBLPREFIX}link AS childfamily ON childfamily.l_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}dates AS childbirth ON childbirth.d_file = {$this->_ged_id}"
			.' WHERE'
				.' birth.d_gid = parentfamily.l_to AND'
				.' childfamily.l_to = childbirth.d_gid AND'
				." childfamily.l_type = 'CHIL' AND"
				." parentfamily.l_type = '{$sex_field}' AND"
				.' childfamily.l_from = parentfamily.l_from AND'
				." parentfamily.l_file = {$this->_ged_id} AND"
				." birth.d_fact = 'BIRT' AND"
				." childbirth.d_fact = 'BIRT' AND"
				.' birth.d_julianday1 <> 0 AND'
				.' childbirth.d_julianday2 > birth.d_julianday1'
			.' ORDER BY'
				." age {$age_dir}"
		, 1);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		if (isset($row['id'])) $person=Person::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($person->canDisplayDetails()) {
					$result=$person->format_list('span', false, $person->getFullName());
				} else {
					$result=i18n::translate('This information is private and cannot be shown.');
				}
				break;
			case 'name':
				$result="<a href=\"".encode_url($person->getLinkUrl())."\">".$person->getFullName().'</a>';
				break;
			case 'age':
				$age = $row['age'];
				if ($show_years) {
					$func = "age_localisation_".WT_LOCALE;
					if (!function_exists($func)) {
						$func="DefaultAgeLocalisation";
					}
					if (floor($age/365.25)>0) {
						$age = floor($age/365.25).'y';
					} else if (floor($age/12)>0) {
						$age = floor($age/12).'m';
					} else {
						$age = $age.'d';
					}
					$func($age, $show_years);
					$result = $age;
				} else {
					$result = floor($age/365.25);
				}
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function statsMarr($simple=true, $first=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						.'d_year<>0 AND '
						."d_fact='MARR' AND "
						."d_type='@#DGREGORIAN@'";
						if ($year1>=0 && $year2>=0) {
							$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
						}
					$sql .= " GROUP BY century ORDER BY century";
		} else if ($first) {
			$years = '';
			if ($year1>=0 && $year2>=0) {
				$years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
			}
			$sql=''
			.' SELECT'
				.' fam.f_id AS fams,'
				.' fam.f_husb, fam.f_wife,'
				.' married.d_julianday2 AS age,'
				.' married.d_month AS month,'
				.' indi.i_id AS indi'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' married.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				." married.d_fact = 'MARR' AND"
				.' married.d_julianday2 <> 0 AND'
				.$years
				.' (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)'
			.' ORDER BY fams, indi, age ASC';
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM {$TBLPREFIX}dates "
				."WHERE "
				."d_file={$this->_ged_id} AND "
				."d_fact='MARR'";
				if ($year1>=0 && $year2>=0) {
					$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			$sql .= " GROUP BY d_month";
		}
		$rows=self::_runSQL($sql);
		if (!isset($rows)) {return 0;}
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			$func="century_localisation_".WT_LOCALE;
			$counts=array();
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $century.' - '.$values['total'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Marriages by century')."\" title=\"".i18n::translate('Marriages by century')."\" />";
		}
		return $rows;
	}

	function statsDiv($simple=true, $first=false, $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT ROUND((d_year+49.1)/100) AS century, COUNT(*) AS total FROM {$TBLPREFIX}dates "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						.'d_year<>0 AND '
						."d_fact IN ('DIV', 'ANUL', '_SEPR') AND "
						."d_type='@#DGREGORIAN@'";
						if ($year1>=0 && $year2>=0) {
							$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
						}
					$sql .= " GROUP BY century ORDER BY century";
		} else if ($first) {
			$years = '';
			if ($year1>=0 && $year2>=0) {
				$years = " divorced.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
			}
			$sql=''
			.' SELECT'
				.' fam.f_id AS fams,'
				.' fam.f_husb, fam.f_wife,'
				.' divorced.d_julianday2 AS age,'
				.' divorced.d_month AS month,'
				.' indi.i_id AS indi'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS divorced ON divorced.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' divorced.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				." divorced.d_fact IN ('DIV', 'ANUL', '_SEPR') AND"
				.' divorced.d_julianday2 <> 0 AND'
				.$years
				.' (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)'
			.' ORDER BY fams, indi, age ASC';
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM {$TBLPREFIX}dates "
				."WHERE "
				."d_file={$this->_ged_id} AND "
				."d_fact IN ('DIV', 'ANUL', '_SEPR')";
				if ($year1>=0 && $year2>=0) {
					$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			$sql .= " GROUP BY d_month";
		}
		$rows=self::_runSQL($sql);
		if (!isset($rows)) {return 0;}
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			$func="century_localisation_".WT_LOCALE;
			$counts=array();
			foreach ($rows as $values) {
				if (function_exists($func)) {
					$century = $func($values['century']);
				}
				else {
					$century = $values['century'];
				}
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $century.' - '.$values['total'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Divorces by century')."\" title=\"".i18n::translate('Divorces by century')."\" />";
		}
		return $rows;
	}

	//
	// Marriage
	//
	function firstMarriage() {return $this->_mortalityQuery('full', 'ASC', 'MARR');}
	function firstMarriageYear() {return $this->_mortalityQuery('year', 'ASC', 'MARR');}
	function firstMarriageName() {return $this->_mortalityQuery('name', 'ASC', 'MARR');}
	function firstMarriagePlace() {return $this->_mortalityQuery('place', 'ASC', 'MARR');}

	function lastMarriage() {return $this->_mortalityQuery('full', 'DESC', 'MARR');}
	function lastMarriageYear() {return $this->_mortalityQuery('year', 'DESC', 'MARR');}
	function lastMarriageName() {return $this->_mortalityQuery('name', 'DESC', 'MARR');}
	function lastMarriagePlace() {return $this->_mortalityQuery('place', 'DESC', 'MARR');}

	//
	// Divorce
	//
	function firstDivorce() {return $this->_mortalityQuery('full', 'ASC', 'DIV');}
	function firstDivorceYear() {return $this->_mortalityQuery('year', 'ASC', 'DIV');}
	function firstDivorceName() {return $this->_mortalityQuery('name', 'ASC', 'DIV');}
	function firstDivorcePlace() {return $this->_mortalityQuery('place', 'ASC', 'DIV');}

	function lastDivorce() {return $this->_mortalityQuery('full', 'DESC', 'DIV');}
	function lastDivorceYear() {return $this->_mortalityQuery('year', 'DESC', 'DIV');}
	function lastDivorceName() {return $this->_mortalityQuery('name', 'DESC', 'DIV');}
	function lastDivorcePlace() {return $this->_mortalityQuery('place', 'DESC', 'DIV');}

	function statsMarrAge($simple=true, $sex='M', $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX;

		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '200x250';}
			$sizes = explode('x', $size);
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age,'
					.' ROUND((married.d_year+49.1)/100) AS century,'
					.' indi.i_sex AS sex'
				.' FROM'
					." {$TBLPREFIX}families AS fam"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
				.' WHERE'
					.' birth.d_gid = indi.i_id AND'
					.' married.d_gid = fam.f_id AND'
					." (indi.i_id = fam.f_wife OR"
					." indi.i_id = fam.f_husb) AND"
					." fam.f_file = {$this->_ged_id} AND"
					." birth.d_fact = 'BIRT' AND"
					." married.d_fact = 'MARR' AND"
					.' birth.d_julianday1 <> 0 AND'
					." birth.d_type='@#DGREGORIAN@' AND"
					." married.d_type='@#DGREGORIAN@' AND"
					.' married.d_julianday2 > birth.d_julianday1'
				.' GROUP BY century, sex ORDER BY century, sex');
			if (empty($rows)) return'';
			$max = 0;
			foreach ($rows as $values) {
				if ($max<$values['age']) $max = $values['age'];
			}
			$func="century_localisation_".WT_LOCALE;
			$chxl = "0:|";
			$chmm = "";
			$chmf = "";
			$i = 0;
			$male = true;
			$temp = "";
			$countsm = "";
			$countsf = "";
			$countsa = "";
			foreach ($rows as $values) {
				if ($max<=50) $chage = $values['age']*2;
				else $chage = $values['age'];
				if ($temp!=$values['century']) {
					$temp = $values['century'];
					if ($sizes[0]<1000) $sizes[0] += 50;
					if (function_exists($func)) {
						$century = $func($values['century'], false);
					} else {
						$century = $values['century'];
					}
					$chxl .= $century."|";
					if ($values['sex'] == "F") {
						if (!$male) {
							$countsm .= "0,";
							$chmm .= 't0,000000,0,'.($i-1).',11,1|';
							$countsa .= $fage.",";
						}
						$countsf .= $chage.",";
						$chmf .= 't'.$values['age'].',000000,1,'.$i.',11,1|';
						$fage = $chage;
						$male = false;
					} else if ($values['sex'] == "M") {
						$countsf .= "0,";
						$chmf .= 't0,000000,1,'.$i.',11,1|';
						$countsm .= $chage.",";
						$chmm .= 't'.$values['age'].',000000,0,'.$i.',11,1|';
						$countsa .= $chage.",";
					} else if ($values['sex'] == "U") {
						$countsf .= "0,";
						$chmf .= 't0,000000,1,'.$i.',11,1|';
						$countsm .= "0,";
						$chmm .= 't0,000000,0,'.$i.',11,1|';
						$countsa .= "0,";
					}
					$i++;
				}
				else if ($values['sex'] == "M") {
					$countsm .= $chage.",";
					$chmm .= 't'.$values['age'].',000000,0,'.($i-1).',11,1|';
					$countsa .= round(($fage+$chage)/2,1).",";
					$male = true;
				}
			}
			if (!$male) {
				$countsa .= $fage.",";
			}
			$countsm = substr($countsm,0,-1);
			$countsf = substr($countsf,0,-1);
			$countsa = substr($countsa,0,-1);
			$chmf = substr($chmf,0,-1);
			$chd = "t2:{$countsm}|{$countsf}|{$countsa}";
			if ($max<=50) $chxl .= "1:||".i18n::translate('century')."|2:|0|10|20|30|40|50|3:||".i18n::translate('Age')."|";
			else 	$chxl .= "1:||".i18n::translate('century')."|2:|0|10|20|30|40|50|60|70|80|90|100|3:||".i18n::translate('Age')."|";
			if (count($rows)>4 || utf8_strlen(i18n::translate('Average age in century of marriage'))<30) {
				$chtt = i18n::translate('Average age in century of marriage');
			} else {
				$offset = 0;
				$counter = array();
				while($offset = strpos(i18n::translate('Average age in century of marriage'), " ", $offset + 1)){
					$counter[] = $offset;
				}
				$half = floor(count($counter)/2);
				$chtt = substr_replace(i18n::translate('Average age in century of marriage'), '|', $counter[$half], 1);
			}
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|{$chmm}{$chmf}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt={$chtt}&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}&amp;chdl=".i18n::translate('Males')."|".i18n::translate('Females')."|".i18n::translate('Average age'))."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Average age in century of marriage')."\" title=\"".i18n::translate('Average age in century of marriage')."\" />";
		} else {
			$years = '';
			if ($year1>=0 && $year2>=0) {
				$years = " AND married.d_year BETWEEN '{$year1}' AND '{$year2}'";
			}
			if ($sex == 'F') {
				$sex_field = 'fam.f_wife,';
				$sex_field2 = " indi.i_id = fam.f_wife AND";
				$sex_search = " AND i_sex='F'";
			}
			else if ($sex == 'M') {
				$sex_field = 'fam.f_husb,';
				$sex_field2 = " indi.i_id = fam.f_husb AND";
				$sex_search = " AND i_sex='M'";
			}
			$rows=self::_runSQL(''
				.' SELECT'
					.' fam.f_id,'
					.$sex_field
					.' married.d_julianday2-birth.d_julianday1 AS age,'
					.' indi.i_id AS indi'
				.' FROM'
					." {$TBLPREFIX}families AS fam"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS birth ON birth.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
				.' LEFT JOIN'
					." {$TBLPREFIX}individuals AS indi ON indi.i_file = {$this->_ged_id}"
				.' WHERE'
					.' birth.d_gid = indi.i_id AND'
					.' married.d_gid = fam.f_id AND'
					.$sex_field2
					." fam.f_file = {$this->_ged_id} AND"
					." birth.d_fact = 'BIRT' AND"
					." married.d_fact = 'MARR' AND"
					.' birth.d_julianday1 <> 0 AND'
					.' married.d_julianday2 > birth.d_julianday1'
					.$sex_search
					.$years
				.' ORDER BY indi, age ASC');
			if (!isset($rows)) {return 0;}
			return $rows;
		}
	}

	//
	// Female only
	//
	function youngestMarriageFemale() {return $this->_marriageQuery('full', 'ASC', 'F');}
	function youngestMarriageFemaleName() {return $this->_marriageQuery('name', 'ASC', 'F');}
	function youngestMarriageFemaleAge($show_years=false) {return $this->_marriageQuery('age', 'ASC', 'F', $show_years);}

	function oldestMarriageFemale() {return $this->_marriageQuery('full', 'DESC', 'F');}
	function oldestMarriageFemaleName() {return $this->_marriageQuery('name', 'DESC', 'F');}
	function oldestMarriageFemaleAge($show_years=false) {return $this->_marriageQuery('age', 'DESC', 'F', $show_years);}

	//
	// Male only
	//
	function youngestMarriageMale() {return $this->_marriageQuery('full', 'ASC', 'M');}
	function youngestMarriageMaleName() {return $this->_marriageQuery('name', 'ASC', 'M');}
	function youngestMarriageMaleAge($show_years=false) {return $this->_marriageQuery('age', 'ASC', 'M', $show_years);}

	function oldestMarriageMale() {return $this->_marriageQuery('full', 'DESC', 'M');}
	function oldestMarriageMaleName() {return $this->_marriageQuery('name', 'DESC', 'M');}
	function oldestMarriageMaleAge($show_years=false) {return $this->_marriageQuery('age', 'DESC', 'M', $show_years);}

	function ageBetweenSpousesMF($params=null) {return $this->_ageBetweenSpousesQuery($type='nolist', $age_dir='DESC', $params=null);}
	function ageBetweenSpousesMFList($params=null) {return $this->_ageBetweenSpousesQuery($type='list', $age_dir='DESC', $params=null);}

	function ageBetweenSpousesFM($params=null) {return $this->_ageBetweenSpousesQuery($type='nolist', $age_dir='ASC', $params=null);}
	function ageBetweenSpousesFMList($params=null) {return $this->_ageBetweenSpousesQuery($type='list', $age_dir='ASC', $params=null);}

	function topAgeOfMarriageFamily() {return $this->_ageOfMarriageQuery('name', 'DESC', array('1'));}
	function topAgeOfMarriage() {return $this->_ageOfMarriageQuery('age', 'DESC', array('1'));}
	function topAgeOfMarriageFamilies($params=null) {return $this->_ageOfMarriageQuery('nolist', 'DESC', $params);}
	function topAgeOfMarriageFamiliesList($params=null) {return $this->_ageOfMarriageQuery('list', 'DESC', $params);}

	function minAgeOfMarriageFamily() {return $this->_ageOfMarriageQuery('name', 'ASC', array('1'));}
	function minAgeOfMarriage() {return $this->_ageOfMarriageQuery('age', 'ASC', array('1'));}
	function minAgeOfMarriageFamilies($params=null) {return $this->_ageOfMarriageQuery('nolist', 'ASC', $params);}
	function minAgeOfMarriageFamiliesList($params=null) {return $this->_ageOfMarriageQuery('list', 'ASC', $params);}

	//
	// Mother only
	//
	function youngestMother() {return $this->_parentsQuery('full', 'ASC', 'F');}
	function youngestMotherName() {return $this->_parentsQuery('name', 'ASC', 'F');}
	function youngestMotherAge($show_years=false) {return $this->_parentsQuery('age', 'ASC', 'F', $show_years);}

	function oldestMother() {return $this->_parentsQuery('full', 'DESC', 'F');}
	function oldestMotherName() {return $this->_parentsQuery('name', 'DESC', 'F');}
	function oldestMotherAge($show_years=false) {return $this->_parentsQuery('age', 'DESC', 'F', $show_years);}

	//
	// Father only
	//
	function youngestFather() {return $this->_parentsQuery('full', 'ASC', 'M');}
	function youngestFatherName() {return $this->_parentsQuery('name', 'ASC', 'M');}
	function youngestFatherAge($show_years=false) {return $this->_parentsQuery('age', 'ASC', 'M', $show_years);}

	function oldestFather() {return $this->_parentsQuery('full', 'DESC', 'M');}
	function oldestFatherName() {return $this->_parentsQuery('name', 'DESC', 'M');}
	function oldestFatherAge($show_years=false) {return $this->_parentsQuery('age', 'DESC', 'M', $show_years);}

	function totalMarriedMales() {
		global $TBLPREFIX;

		$rows = WT_DB::prepare("SELECT f_gedcom AS ged, f_husb AS husb FROM {$TBLPREFIX}families WHERE f_file=?")
				->execute(array($this->_ged_id))
				->fetchAll();
		$husb = array();
		foreach ($rows as $row) {
			$factrec = trim(get_sub_record(1, "1 MARR", $row->ged, 1));
			if (!empty($factrec)) {
				$husb[] = $row->husb."<br />";
			}
		}
		return count(array_unique($husb));
	}

	function totalMarriedFemales() {
		global $TBLPREFIX;

		$rows = WT_DB::prepare("SELECT f_gedcom AS ged, f_wife AS wife FROM {$TBLPREFIX}families WHERE f_file=?")
				->execute(array($this->_ged_id))
				->fetchAll();
		$wife = array();
		foreach ($rows as $row) {
			$factrec = trim(get_sub_record(1, "1 MARR", $row->ged, 1));
			if (!empty($factrec)) {
				$wife[] = $row->wife."<br />";
			}
		}
		return count(array_unique($wife));
	}

///////////////////////////////////////////////////////////////////////////////
// Family Size                                                               //
///////////////////////////////////////////////////////////////////////////////

	function _familyQuery($type='full') {
		global $TBLPREFIX;
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC'
		, 1);
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$family=Family::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($family->canDisplayDetails()) {
					$result=$family->format_list('span', false, $family->getFullName());
				} else {
					$result = i18n::translate('This information is private and cannot be shown.');
				}
				break;
			case 'size':
				$result=$row['tot'];
				break;
			case 'name':
				$result="<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()).'</a>';
				break;
		}
		// Statistics are used by RSS feeds, etc., so need absolute URLs.
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenFamilyQuery($type='list', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		if(count($rows) < $total){$total = count($rows);}
		$top10 = array();
		for($c = 0; $c < $total; $c++) {
			$family=Family::getInstance($rows[$c]['id']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [{$rows[$c]['tot']} ".i18n::translate('children')."]")."</li>\n";
				} else {
					$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [{$rows[$c]['tot']} ".i18n::translate('children')."]");
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _ageBetweenSiblingsQuery($type='list', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION;
		if ($params === null) {$params = array();}
		if (isset($params[0])) {$total = $params[0];}else{$total = 10;}
		if (isset($params[1])) {$one = $params[1];}else{$one = false;} // each family only once if true
		$rows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' link1.l_from AS family,'
				.' link1.l_to AS ch1,'
				.' link2.l_to AS ch2,'
				.' child1.d_julianday2-child2.d_julianday2 AS age'
			.' FROM'
				." {$TBLPREFIX}link AS link1"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS child1 ON child1.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS child2 ON child2.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." {$TBLPREFIX}link AS link2 ON link2.l_file = {$this->_ged_id}"
			.' WHERE'
				." link1.l_file = {$this->_ged_id} AND"
				.' link1.l_from = link2.l_from AND'
				." link1.l_type = 'CHIL' AND"
				.' child1.d_gid = link1.l_to AND'
				." child1.d_fact = 'BIRT' AND"
				." link2.l_type = 'CHIL' AND"
				.' child2.d_gid = link2.l_to AND'
				." child2.d_fact = 'BIRT' AND"
				.' child1.d_julianday2 > child2.d_julianday2 AND'
				.' child2.d_julianday2 <> 0 AND'
				.' child1.d_gid <> child2.d_gid'
			.' ORDER BY'
				." age DESC"
		,$total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		$func = "age_localisation_".WT_LOCALE;
		if (!function_exists($func)) {
			$func="DefaultAgeLocalisation";
		}
		$show_years = true;
		if ($one) $dist = array();
		foreach ($rows as $fam) {
			$family = Family::getInstance($fam['family']);
			$child1 = Person::getInstance($fam['ch1']);
			$child2 = Person::getInstance($fam['ch2']);
			if ($type == 'name') {
				if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = "<a href=\"".encode_url($child2->getLinkUrl())."\">".PrintReady($child2->getFullName())."</a> ";
					$return .= i18n::translate('and')." ";
					$return .= "<a href=\"".encode_url($child1->getLinkUrl())."\">".PrintReady($child1->getFullName())."</a>";
					$return .= " <a href=\"family.php?famid=".$fam['family']."\">[".i18n::translate('View Family')."]</a>\n";
				} else {
					$return = i18n::translate('This information is private and cannot be shown.');
				}
				return $return;
			}
			$age = $fam['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/12)>0) {
				$age = floor($age/12).'m';
			} else {
				$age = $age.'d';
			}
			$func($age, $show_years);
			if ($type == 'age') {
				return $age;
			}
			if ($type == 'list') {
				if ($one && !in_array($fam['family'], $dist)) {
					if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
						$return = "\t<li>";
						$return .= "<a href=\"".encode_url($child2->getLinkUrl())."\">".PrintReady($child2->getFullName())."</a> ";
						$return .= i18n::translate('and')." ";
						$return .= "<a href=\"".encode_url($child1->getLinkUrl())."\">".PrintReady($child1->getFullName())."</a>";
						$return .= " [".$age."]";
						$return .= " <a href=\"family.php?famid=".$fam['family']."\">[".i18n::translate('View Family')."]</a>";
						$return .= "\t</li>\n";
						$top10[] = $return;
						$dist[] = $fam['family'];
					}
				} else if (!$one && $child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = "\t<li>";
					$return .= "<a href=\"".encode_url($child2->getLinkUrl())."\">".PrintReady($child2->getFullName())."</a> ";
					$return .= i18n::translate('and')." ";
					$return .= "<a href=\"".encode_url($child1->getLinkUrl())."\">".PrintReady($child1->getFullName())."</a>";
					$return .= " [".$age."]";
					$return .= " <a href=\"family.php?famid=".$fam['family']."\">[".i18n::translate('View Family')."]</a>";
					$return .= "\t</li>\n";
					$top10[] = $return;
				}
			} else {
				if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = $child2->format_list('span', false, $child2->getFullName());
					$return .= "<br />".i18n::translate('and')."<br />";
					$return .= $child1->format_list('span', false, $child1->getFullName());
					//$return .= "<br />[".$age."]";
					$return .= "<br /><a href=\"family.php?famid=".$fam['family']."\">[".i18n::translate('View Family')."]</a>\n";
					return $return;
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function largestFamily() {return $this->_familyQuery('full');}
	function largestFamilySize() {return $this->_familyQuery('size');}
	function largestFamilyName() {return $this->_familyQuery('name');}

	function topTenLargestFamily($params=null) {return $this->_topTenFamilyQuery('nolist', $params);}
	function topTenLargestFamilyList($params=null) {return $this->_topTenFamilyQuery('list', $params);}

	function chartLargestFamilies($params=null) {
		global $TBLPREFIX, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_L_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_L_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$total = strtolower($params[3]);}else{$total = 10;}
		$sizes = explode('x', $size);
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		$tot = 0;
		foreach ($rows as $row) {$tot += $row['tot'];}
		$chd = '';
		$chl = array();
		foreach ($rows as $row){
			$family=Family::getInstance($row['id']);
			if ($family->canDisplayDetails()) {
				if ($tot==0) {
					$per = 0;
				} else {
					$per = round(100 * $row['tot'] / $tot, 0);
				}
				$chd .= self::_array_to_extended_encoding(array($per));
				$chl[] = strip_tags(unhtmlentities($family->getFullName())).' - '.$row['tot'];
			}
		}
		$chl = join('|', $chl);

		// the following does not print Arabic letters in names - encode_url shows still the letters
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Largest families')."\" title=\"".i18n::translate('Largest families')."\" />";
	}

	function totalChildren() {
		global $TBLPREFIX;
		$rows=self::_runSQL("SELECT SUM(f_numchil) AS tot FROM {$TBLPREFIX}families WHERE f_file={$this->_ged_id}");
		$row=$rows[0];
		return $row['tot'];
	}


	function averageChildren() {
		global $TBLPREFIX;
		$rows=self::_runSQL("SELECT AVG(f_numchil) AS tot FROM {$TBLPREFIX}families WHERE f_file={$this->_ged_id}");
		$row=$rows[0];
		return sprintf('%.2f', $row['tot']);
	}

	function statsChildren($simple=true, $sex='BOTH', $year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX;

		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '220x200';}
			$sizes = explode('x', $size);
			$max = 0;
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(f_numchil),2) AS num,'
					.' ROUND((married.d_year+49.1)/100) AS century'
				.' FROM'
					." {$TBLPREFIX}families AS fam"
				.' LEFT JOIN'
					." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
				.' WHERE'
					.' married.d_gid = fam.f_id AND'
					." fam.f_file = {$this->_ged_id} AND"
					." married.d_fact = 'MARR' AND"
					." married.d_type='@#DGREGORIAN@'"
				.' GROUP BY century ORDER BY century');
			if (empty($rows)) return '';
			foreach ($rows as $values) {
				if ($max<$values['num']) $max = $values['num'];
			}
			$chm = "";
			$chxl = "0:|";
			$i = 0;
			$func="century_localisation_".WT_LOCALE;
			$counts=array();
			foreach ($rows as $values) {
				if ($sizes[0]<980) $sizes[0] += 38;
				if (function_exists($func)) {
					$chxl .= $func($values['century'], false)."|";
				}
				else {
					$chxl .= $values['century']."|";
				}
				if ($max<=5) $counts[] = round($values['num']*819.2-1, 1);
				else $counts[] = round($values['num']*409.6, 1);
				$chm .= 't'.$values['num'].',000000,0,'.$i.',11,1|';
				$i++;
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chm = substr($chm,0,-1);
			if ($max<=5) $chxl .= "1:||".i18n::translate('century')."|2:|0|1|2|3|4|5|3:||".i18n::translate('Number of children')."|";
			else $chxl .= "1:||".i18n::translate('century')."|2:|0|1|2|3|4|5|6|7|8|9|10|3:||".i18n::translate('Number of children')."|";
			return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0,3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Average number of children per family')."\" title=\"".i18n::translate('Average number of children per family')."\" />";
		} else {
			if ($sex=='M') {
				$sql = "SELECT num, COUNT(*) AS total FROM "
						."(SELECT count(i_sex) AS num FROM {$TBLPREFIX}link "
							."LEFT OUTER JOIN {$TBLPREFIX}individuals "
							."ON l_from=i_id AND l_file=i_file AND i_sex='M' AND l_type='FAMC' "
							."JOIN {$TBLPREFIX}families ON f_file=l_file AND f_id=l_to WHERE f_file={$this->_ged_id} GROUP BY l_to"
						.") boys"
						." GROUP BY num ORDER BY num ASC";
			}
			else if ($sex=='F') {
				$sql = "SELECT num, COUNT(*) AS total FROM "
						."(SELECT count(i_sex) AS num FROM {$TBLPREFIX}link "
							."LEFT OUTER JOIN {$TBLPREFIX}individuals "
							."ON l_from=i_id AND l_file=i_file AND i_sex='F' AND l_type='FAMC' "
							."JOIN {$TBLPREFIX}families ON f_file=l_file AND f_id=l_to WHERE f_file={$this->_ged_id} GROUP BY l_to"
						.") girls"
						." GROUP BY num ORDER BY num ASC";
			}
			else {
				$sql = "SELECT f_numchil, COUNT(*) AS total FROM {$TBLPREFIX}families ";
				if ($year1>=0 && $year2>=0) {
					$sql .= "AS fam LEFT JOIN {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
						.' WHERE'
						.' married.d_gid = fam.f_id AND'
						." fam.f_file = {$this->_ged_id} AND"
						." married.d_fact = 'MARR' AND"
						." married.d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
				else {
					$sql .='WHERE '
						."f_file={$this->_ged_id}";
				}
				$sql .= ' GROUP BY f_numchil';
			}
			$rows=self::_runSQL($sql);
			if (!isset($rows)) {return 0;}
			return $rows;
		}
	}

	function topAgeBetweenSiblingsName($params=null) {return $this->_ageBetweenSiblingsQuery($type='name', $params=null);}
	function topAgeBetweenSiblings($params=null) {return $this->_ageBetweenSiblingsQuery($type='age', $params=null);}
	function topAgeBetweenSiblingsFullName($params=null) {return $this->_ageBetweenSiblingsQuery($type='nolist', $params=null);}
	function topAgeBetweenSiblingsList($params=null) {return $this->_ageBetweenSiblingsQuery($type='list', $params=null);}

	function noChildrenFamilies() {
		global $TBLPREFIX;
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*) AS tot'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' WHERE'
				.' f_numchil = 0 AND'
				." fam.f_file = {$this->_ged_id}");
		$row=$rows[0];
		return $row['tot'];
	}


	function noChildrenFamiliesList($type='list') {
		global $TBLPREFIX, $TEXT_DIRECTION;
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_id AS family'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' WHERE'
				.' f_numchil = 0 AND'
				." fam.f_file = {$this->_ged_id}");
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		foreach ($rows as $row) {
			$family=Family::getInstance($row['family']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a></li>\n";
				} else {
					$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName())."</a>";
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function chartNoChildrenFamilies($year1=-1, $year2=-1, $params=null) {
		global $TBLPREFIX;

		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = '220x200';}
		$sizes = explode('x', $size);
		if ($year1>=0 && $year2>=0) {
			$years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
		} else {
			$years = "";
		}
		$max = 0;
		$tot = 0;
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*) AS count,'
				.' ROUND((married.d_year+49.1)/100) AS century'
			.' FROM'
				." {$TBLPREFIX}families AS fam"
			.' LEFT JOIN'
				." {$TBLPREFIX}dates AS married ON married.d_file = {$this->_ged_id}"
			.' WHERE'
				.' f_numchil = 0 AND'
				.' married.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				.$years
				." married.d_fact = 'MARR' AND"
				." married.d_type='@#DGREGORIAN@'"
			.' GROUP BY century ORDER BY century');
		if (empty($rows)) return '';
		foreach ($rows as $values) {
			if ($max<$values['count']) $max = $values['count'];
			$tot += $values['count'];
		}
		$unknown = $this->noChildrenFamilies()-$tot;
		if ($unknown>$max) $max=$unknown;
		$chm = "";
		$chxl = "0:|";
		$i = 0;
		$func="century_localisation_".WT_LOCALE;
		foreach ($rows as $values) {
			if ($sizes[0]<980) $sizes[0] += 38;
			if (function_exists($func)) {
				$chxl .= $func($values['century'], false)."|";
			}
			else {
				$chxl .= $values['century']."|";
			}
			$counts[] = round(4095*$values['count']/($max+1));
			$chm .= 't'.$values['count'].',000000,0,'.$i.',11,1|';
			$i++;
		}
		$counts[] = round(4095*$unknown/($max+1));
		$chd = self::_array_to_extended_encoding($counts);
		$chm .= 't'.$unknown.',000000,0,'.$i.',11,1';
		$chxl .= i18n::translate('unknown')."|1:||".i18n::translate('century')."|2:|0|";
		$step = $max+1;
		for ($d=floor($max+1); $d>0; $d--) {
			if (($max+1)<($d*10+1) && fmod(($max+1),$d)==0) {
				$step = $d;
			}
		}
		if ($step==floor($max+1)) {
			for ($d=floor($max); $d>0; $d--) {
				if ($max<($d*10+1) && fmod($max,$d)==0) {
					$step = $d;
				}
			}
		}
		for ($n=$step; $n<=($max+1); $n+=$step) {
			$chxl .= $n."|";
		}
		$chxl .= "3:||".i18n::translate('Total families')."|";
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0:".($i-1).",3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF,ffffff00&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl={$chxl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".i18n::translate('Number of families without children')."\" title=\"".i18n::translate('Number of families without children')."\" />";
	}

	function _topTenGrandFamilyQuery($type='list', $params=null) {
		global $TBLPREFIX, $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];}else{$total = 10;}
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*) AS tot,'
				.' f_id AS id'
			.' FROM'
				." {$TBLPREFIX}families"
			.' JOIN'
				." {$TBLPREFIX}link AS children ON children.l_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}link AS mchildren ON mchildren.l_file = {$this->_ged_id}"
			.' JOIN'
				." {$TBLPREFIX}link AS gchildren ON gchildren.l_file = {$this->_ged_id}"
			.' WHERE'
				." f_file={$this->_ged_id} AND"
				." children.l_from=f_id AND"
				." children.l_type='CHIL' AND"
				." children.l_to=mchildren.l_from AND"
				." mchildren.l_type='FAMS' AND"
				." mchildren.l_to=gchildren.l_from AND"
				." gchildren.l_type='CHIL'"
			.' GROUP BY'
				.' id'
			.' ORDER BY'
				.' tot DESC'
		, $total);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		foreach ($rows as $row) {
			$family=Family::getInstance($row['id']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [{$row['tot']} ".i18n::translate('grandchildren')."]")."</li>\n";
				} else {
					$top10[] = "<a href=\"".encode_url($family->getLinkUrl())."\">".PrintReady($family->getFullName()."</a> [{$row['tot']} ".i18n::translate('grandchildren')."]");
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10 = join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function topTenLargestGrandFamily($params=null) {return $this->_topTenGrandFamilyQuery('nolist', $params);}
	function topTenLargestGrandFamilyList($params=null) {return $this->_topTenGrandFamilyQuery('list', $params);}

///////////////////////////////////////////////////////////////////////////////
// Surnames                                                                  //
///////////////////////////////////////////////////////////////////////////////

	static function _commonSurnamesQuery($type='list', $show_tot=false, $params=null) {
		global $TEXT_DIRECTION, $COMMON_NAMES_THRESHOLD, $SURNAME_LIST_STYLE;

		if (is_array($params) && isset($params[0]) && $params[0] != '') {$threshold = strtolower($params[0]);}else{$threshold = $COMMON_NAMES_THRESHOLD;}
		if(is_array($params) && isset($params[1]) && $params[1] != '' && $params[1] >= 0){$maxtoshow = strtolower($params[1]);}else{$maxtoshow = false;}
		if(is_array($params) && isset($params[2]) && $params[2] != ''){$sorting = strtolower($params[2]);}else{$sorting = 'alpha';}
		$surname_list = get_common_surnames($threshold);
		if (count($surname_list) == 0) return '';
		uasort($surname_list, array('stats', '_name_total_rsort'));
		if ($maxtoshow>0) $surname_list = array_slice($surname_list, 0, $maxtoshow);

		switch($sorting) {
			default:
			case 'alpha':
				uasort($surname_list, array('stats', '_name_name_sort'));
				break;
			case 'ralpha':
				uasort($surname_list, array('stats', '_name_name_rsort'));
				break;
			case 'count':
				uasort($surname_list, array('stats', '_name_total_sort'));
				break;
			case 'rcount':
				uasort($surname_list, array('stats', '_name_total_rsort'));
				break;
		}

		// Note that we count/display SPFX SURN, but sort/group under just SURN
		$surnames=array();
		foreach (array_keys($surname_list) as $surname) {
			$surnames=array_merge($surnames, get_indilist_surns($surname, '', false, false, WT_GED_ID));
		}

		return format_surname_list($surnames, ($type=='list' ? 1 : 2), $show_tot);
	}

	function getCommonSurname() {
		$surnames=array_keys(get_top_surnames($this->_ged_id, 1, 1));
		return array_shift($surnames);
	}

	static function commonSurnames($params=array('','','alpha')) {return self::_commonSurnamesQuery('nolist', false, $params);}
	static function commonSurnamesTotals($params=array('','','rcount')) {return self::_commonSurnamesQuery('nolist', true, $params);}
	static function commonSurnamesList($params=array('','','alpha')) {return self::_commonSurnamesQuery('list', false, $params);}
	static function commonSurnamesListTotals($params=array('','','rcount')) {return self::_commonSurnamesQuery('list', true, $params);}

	function chartCommonSurnames($params=null) {
		global $COMMON_NAMES_THRESHOLD, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$threshold = strtolower($params[3]);}else{$threshold = $COMMON_NAMES_THRESHOLD;}
		if (isset($params[4]) && $params[4] != '') {$maxtoshow = strtolower($params[4]);}else{$maxtoshow = 7;}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividuals();
		$surnames = get_common_surnames($threshold);
		if (count($surnames) <= 0) {return '';}
		uasort($surnames, array('stats', '_name_total_rsort'));
		$surnames = array_slice($surnames, 0, $maxtoshow);
		$all_surnames = array();
		foreach (array_keys($surnames) as $n=>$surname) {
			if ($n>=$maxtoshow) {
				break;
			}
			$all_surnames = array_merge($all_surnames, get_indilist_surns(utf8_strtoupper($surname), '', false, false, WT_GED_ID));
		}
		$tot = 0;
		$per = 0;
		foreach ($surnames as $indexval=>$surname) {$tot += $surname['match'];}
		$chart_title = "";
		$chd = '';
		$chl = array();
		foreach ($all_surnames as $surn=>$surns) {
			$count_per = 0;
			$max_name = 0;
			foreach ($surns as $spfxsurn=>$indis) {
				$per = count($indis);
				$count_per += $per;
				// select most common surname from all variants
				if ($per>$max_name) {
					$max_name = $per;
					$top_name = $spfxsurn;
				}
			}
			$per = round(100 * $count_per / $tot_indi, 0);
			$chd .= self::_array_to_extended_encoding($per);
			//ToDo: RTL names are often printed LTR when also LTR names are present
			$chl[] = $top_name.' - '.$count_per;
			$chart_title .= $top_name.' - '.$count_per.', ';

		}
		$per = round(100 * ($tot_indi-$tot) / $tot_indi, 0);
		$chd .= self::_array_to_extended_encoding($per);
		$chl[] = i18n::translate('Other').' - '.($tot_indi-$tot);
		$chart_title .= i18n::translate('Other').' - '.($tot_indi-$tot);

		$chl = join('|', $chl);
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}


///////////////////////////////////////////////////////////////////////////////
// Given Names                                                               //
///////////////////////////////////////////////////////////////////////////////

	/*
	* [ 1977282 ] Most Common Given Names Block
	* Original block created by kiwi_pgv
	*/
	static function _commonGivenQuery($sex='B', $type='list', $show_tot=false, $params=null) {
		global $TEXT_DIRECTION, $GEDCOM, $TBLPREFIX;
		static $sort_types = array('count'=>'asort', 'rcount'=>'arsort', 'alpha'=>'ksort', 'ralpha'=>'krsort');
		static $sort_flags = array('count'=>SORT_NUMERIC, 'rcount'=>SORT_NUMERIC, 'alpha'=>SORT_STRING, 'ralpha'=>SORT_STRING);

		if(is_array($params) && isset($params[0]) && $params[0] != '' && $params[0] >= 0){$threshold = strtolower($params[0]);}else{$threshold = 1;}
		if(is_array($params) && isset($params[1]) && $params[1] != '' && $params[1] >= 0){$maxtoshow = strtolower($params[1]);}else{$maxtoshow = 10;}
		if(is_array($params) && isset($params[2]) && $params[2] != '' && isset($sort_types[strtolower($params[2])])){$sorting = strtolower($params[2]);}else{$sorting = 'rcount';}

		switch ($sex) {
		case 'M':
			$sex_sql="i_sex='M'";
			break;
		case 'F':
			$sex_sql="i_sex='F'";
			break;
		case 'U':
			$sex_sql="i_sex='U'";
			break;
		case 'B':
			$sex_sql="i_sex<>'U'";
			break;
		}
		$ged_id=get_id_from_gedcom($GEDCOM);

		$rows=WT_DB::prepare("SELECT n_givn, COUNT(*) AS num FROM {$TBLPREFIX}name JOIN {$TBLPREFIX}individuals ON (n_id=i_id AND n_file=i_file) WHERE n_file={$ged_id} AND n_type<>'_MARNM' AND n_givn NOT IN ('@P.N.', '') AND LENGTH(n_givn)>1 AND {$sex_sql} GROUP BY n_id, n_givn")
			->fetchAll();
		$nameList=array();
		foreach ($rows as $row) {
			// Split "John Thomas" into "John" and "Thomas" and count against both totals
			foreach (explode(' ', $row->n_givn) as $given) {
				$given=str_replace(array('*', '"'), '', $given);
				if (strlen($given)>1) {
					if (array_key_exists($given, $nameList)) {
						$nameList[$given]+=$row->num;
					} else {
						$nameList[$given]=$row->num;
					}
				}
			}
		}
		arsort($nameList, SORT_NUMERIC);
		$nameList=array_slice($nameList, 0, $maxtoshow);

		if (count($nameList)==0) return '';
		if ($type=='chart') return $nameList;
		$common = array();
		foreach ($nameList as $given=>$total) {
			if ($maxtoshow !== -1) {if($maxtoshow-- <= 0){break;}}
			if ($total < $threshold) {break;}
			if ($show_tot) {
				$tot = PrintReady("[{$total}]");
				if ($TEXT_DIRECTION=='ltr') {
					$totL = '';
					$totR = '&nbsp;'.$tot;
				} else {
					$totL = $tot.'&nbsp;';
					$totR = '';
				}
			} else {
				$totL = '';
				$totR = '';
			}
			switch ($type) {
			case 'table':
				$common[] = '<tr><td class="optionbox">'.PrintReady(utf8_substr($given,0,1).utf8_strtolower(utf8_substr($given,1))).'</td><td class="optionbox">'.$total.'</td></tr>';
				break;
			case 'list':
				$common[] = "\t<li>{$totL}".PrintReady(utf8_substr($given,0,1).utf8_strtolower(utf8_substr($given,1)))."{$totR}</li>\n";
				break;
			case 'nolist':
				$common[] = $totL.PrintReady(utf8_substr($given,0,1).utf8_strtolower(utf8_substr($given,1))).$totR;
				break;
			}
		}
		if ($common) {
			switch ($type) {
			case 'table':
				$lookup=array('M'=>i18n::translate('Male'), 'F'=>i18n::translate('Female'), 'U'=>i18n::translate('unknown'), 'B'=>i18n::translate('ALL'));
				return '<table><tr><td colspan="2" class="descriptionbox center">'.$lookup[$sex].'</td></tr><tr><td class="descriptionbox center">'.i18n::translate('Names').'</td><td class="descriptionbox center">'.i18n::translate('Count').'</td></tr>'.join('', $common).'</table>';
			case 'list':
				return "<ul>\n".join("\n", $common)."</ul>\n";
			case 'nolist':
				return join(';&nbsp; ', $common);
			}
		} else {
			return '';
		}
	}

	static function commonGiven($params=array(1,10,'alpha')){return self::_commonGivenQuery('B', 'nolist', false, $params);}
	static function commonGivenTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('B', 'nolist', true, $params);}
	static function commonGivenList($params=array(1,10,'alpha')){return self::_commonGivenQuery('B', 'list', false, $params);}
	static function commonGivenListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('B', 'list', true, $params);}
	static function commonGivenTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('B', 'table', false, $params);}

	static function commonGivenFemale($params=array(1,10,'alpha')){return self::_commonGivenQuery('F', 'nolist', false, $params);}
	static function commonGivenFemaleTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('F', 'nolist', true, $params);}
	static function commonGivenFemaleList($params=array(1,10,'alpha')){return self::_commonGivenQuery('F', 'list', false, $params);}
	static function commonGivenFemaleListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('F', 'list', true, $params);}
	static function commonGivenFemaleTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('F', 'table', false, $params);}

	static function commonGivenMale($params=array(1,10,'alpha')){return self::_commonGivenQuery('M', 'nolist', false, $params);}
	static function commonGivenMaleTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('M', 'nolist', true, $params);}
	static function commonGivenMaleList($params=array(1,10,'alpha')){return self::_commonGivenQuery('M', 'list', false, $params);}
	static function commonGivenMaleListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('M', 'list', true, $params);}
	static function commonGivenMaleTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('M', 'table', false, $params);}

	static function commonGivenUnknown($params=array(1,10,'alpha')){return self::_commonGivenQuery('U', 'nolist', false, $params);}
	static function commonGivenUnknownTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('U', 'nolist', true, $params);}
	static function commonGivenUnknownList($params=array(1,10,'alpha')){return self::_commonGivenQuery('U', 'list', false, $params);}
	static function commonGivenUnknownListTotals($params=array(1,10,'rcount')){return self::_commonGivenQuery('U', 'list', true, $params);}
	static function commonGivenUnknownTable($params=array(1,10,'rcount')){return self::_commonGivenQuery('U', 'table', false, $params);}

	function chartCommonGiven($params=null) {
		global $COMMON_NAMES_THRESHOLD, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);}else{$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);}else{$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);}else{$color_to = $WT_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$threshold = strtolower($params[3]);}else{$threshold = $COMMON_NAMES_THRESHOLD;}
		if (isset($params[4]) && $params[4] != '') {$maxtoshow = strtolower($params[4]);}else{$maxtoshow = 7;}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividuals();
		$given = self::_commonGivenQuery('B', 'chart');
		if (!is_array($given)) return '';
		$given = array_slice($given, 0, $maxtoshow);
		if (count($given) <= 0) {return '';}
		$tot = 0;
		foreach ($given as $givn=>$count) {$tot += $count;}
		$chart_title = "";
		$chd = '';
		$chl = array();
		foreach ($given as $givn=>$count) {
			if ($tot==0) {
				$per = 0;
			} else {
				$per = round(100 * $count / $tot_indi, 0);
			}
			$chd .= self::_array_to_extended_encoding($per);
			//ToDo: RTL names are often printed LTR when also LTR names are present
			$chl[] = $givn.' - '.$count;
			$chart_title .= $givn.' - '.$count.', ';

		}
		$per = round(100 * ($tot_indi-$tot) / $tot_indi, 0);
		$chd .= self::_array_to_extended_encoding($per);
		$chl[] = i18n::translate('Other').' - '.($tot_indi-$tot);
		$chart_title .= i18n::translate('Other').' - '.($tot_indi-$tot);

		$chl = join('|', $chl);
		return "<img src=\"".encode_url("http://chart.apis.google.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}")."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

///////////////////////////////////////////////////////////////////////////////
// Users                                                                     //
///////////////////////////////////////////////////////////////////////////////

	static function _usersLoggedIn($type='nolist') {
		global $WT_SESSION_TIME;
		// Log out inactive users
		foreach (get_idle_users(time() - $WT_SESSION_TIME) as $user_id=>$user_name) {
			if ($user_id != WT_USER_ID) {
				userLogout($user_id);
			}
		}

		$content = '';
		// List active users
		$NumAnonymous = 0;
		$loggedusers = array ();
		$x = get_logged_in_users();
		foreach ($x as $user_id=>$user_name) {
			if (WT_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline') == 'Y') {
				$loggedusers[$user_id] = $user_name;
			} else {
				$NumAnonymous++;
			}
		}
		$LoginUsers = count($loggedusers);
		if (($LoginUsers == 0) and ($NumAnonymous == 0)) {
			return i18n::translate('No logged-in and no anonymous users');
		}
		if ($NumAnonymous > 0) {
			$content.='<b>'.i18n::plural('%d anonymous logged-in user', '%d anonymous logged-in users', $NumAnonymous, $NumAnonymous).'</b>';
		}
		if ($LoginUsers > 0) {
			if ($NumAnonymous) {
				if ($type == 'list') {
					$content .= "<br /><br />\n";
				} else {
					$content .= " ".i18n::translate('and')." ";
				}
			}
			$content.='<b>'.i18n::plural('%d logged-in user', '%d logged-in users', $LoginUsers, $LoginUsers).'</b>';
			if ($type == 'list') {
				$content .= '<ul>';
			} else {
				$content .= ': ';
			}
		}
		if (WT_USER_ID) {
			foreach ($loggedusers as $user_id=>$user_name) {
				if ($type == 'list') {
					$content .= "\t<li>".PrintReady(getUserFullName($user_id))." - {$user_name}";
				} else {
					$content .= PrintReady(getUserFullName($user_id))." - {$user_name}";
				}
				if (WT_USER_ID != $user_id && get_user_setting($user_id, 'contactmethod') != 'none') {
					if ($type == 'list') {
						$content .= "<br /><a href=\"javascript:;\" onclick=\"return message('{$user_id}');\">".i18n::translate('Send Message')."</a>";
					} else {
						$content .= " <a href=\"javascript:;\" onclick=\"return message('{$user_id}');\">".i18n::translate('Send Message')."</a>";
					}
				}
				if ($type == 'list') {
					$content .= "</li>\n";
				}
			}
		}
		if ($type == 'list') {
			$content .= '</ul>';
		}
		return $content;
	}

	static function _usersLoggedInTotal($type='all') {
		global $WT_SESSION_TIME;

		foreach (get_idle_users(time() - $WT_SESSION_TIME) as $user_id=>$user_name) {
			if ($user_id != WT_USER_ID) {
				userLogout($user_id);
			}
		}
		$anon = 0;
		$visible = 0;
		$x = get_logged_in_users();
		foreach ($x as $user_id=>$user_name) {
			if (WT_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline') == 'Y') {$visible++;}else{$anon++;}
		}
		if ($type == 'anon') {return $anon;}
		elseif ($type == 'visible') {return $visible;}
		else{return $visible + $anon;}
	}

	static function usersLoggedIn() {return self::_usersLoggedIn('nolist');}
	static function usersLoggedInList() {return self::_usersLoggedIn('list');}

	static function usersLoggedInTotal() {return self::_usersLoggedInTotal('all');}
	static function usersLoggedInTotalAnon() {return self::_usersLoggedInTotal('anon');}
	static function usersLoggedInTotalVisible() {return self::_usersLoggedInTotal('visible');}

	static function userID() {return getUserId();}
	static function userName() {return getUserName();}
	static function userFullName() {return getUserFullName(getUserId());}

	static function _getLatestUserData($type='userid', $params=null) {
		global $DATE_FORMAT, $TIME_FORMAT;
		static $user_id = null;

		if ($user_id === null) {
			$user_id=get_newest_registered_user();
		}

		switch($type) {
			default:
			case 'userid':
				return $user_id;
			case 'username':
				return get_user_name($user_id);
			case 'fullname':
				return getUserFullName($user_id);
			case 'regdate':
				if(is_array($params) && isset($params[0]) && $params[0] != ''){$datestamp = $params[0];}else{$datestamp = $DATE_FORMAT;}
				return date($datestamp, get_user_setting($user_id, 'reg_timestamp'));
			case 'regtime':
				if(is_array($params) && isset($params[0]) && $params[0] != ''){$datestamp = $params[0];}else{$datestamp = str_replace('%', '', $TIME_FORMAT);}
				return date($datestamp, get_user_setting($user_id, 'reg_timestamp'));
			case 'loggedin':
				if(is_array($params) && isset($params[0]) && $params[0] != ''){$yes = $params[0];}else{$yes = i18n::translate('Yes');}
				if(is_array($params) && isset($params[1]) && $params[1] != ''){$no = $params[1];}else{$no = i18n::translate('No');}
				return (get_user_setting($user_id, 'loggedin') == 'Y')?$yes:$no;
		}
	}

	static function latestUserId(){return self::_getLatestUserData('userid');}
	static function latestUserName(){return self::_getLatestUserData('username');}
	static function latestUserFullName(){return self::_getLatestUserData('fullname');}
	static function latestUserRegDate($params=null){return self::_getLatestUserData('regdate', $params);}
	static function latestUserRegTime($params=null){return self::_getLatestUserData('regtime', $params);}
	static function latestUserLoggedin($params=null){return self::_getLatestUserData('loggedin', $params);}

///////////////////////////////////////////////////////////////////////////////
// Contact                                                                   //
///////////////////////////////////////////////////////////////////////////////

	static function contactWebmaster() {return user_contact_link(get_user_id($GLOBALS['WEBMASTER_EMAIL']), $GLOBALS['SUPPORT_METHOD']);}
	static function contactGedcom() {return user_contact_link(get_user_id($GLOBALS['CONTACT_EMAIL']), $GLOBALS['CONTACT_METHOD']);}

///////////////////////////////////////////////////////////////////////////////
// Date & Time                                                               //
///////////////////////////////////////////////////////////////////////////////

	static function serverDate() {return timestamp_to_gedcom_date(time())->Display(false);}

	static function serverTime() {return date('g:i a');}

	static function serverTime24() {return date('G:i');}

	static function serverTimezone() {return date('T');}

	static function browserDate() {return timestamp_to_gedcom_date(client_time())->Display(false);}

	static function browserTime() {return date('g:i a', client_time());}

	static function browserTime24() {return date('G:i', client_time());}

	static function browserTimezone() {return date('T', client_time());}

///////////////////////////////////////////////////////////////////////////////
// Tools                                                                     //
///////////////////////////////////////////////////////////////////////////////

	/*
	* Leave for backwards compatability? Anybody using this?
	*/
	static function _getEventType($type) {
		$eventTypes=array(
			'BIRT'=>i18n::translate('birth'),
			'DEAT'=>i18n::translate('death'),
			'MARR'=>i18n::translate('marriage'),
			'ADOP'=>i18n::translate('adoption'),
			'BURI'=>i18n::translate('burial'),
			'CENS'=>i18n::translate('census added')
		);
		if (isset($eventTypes[$type])) {
			return $eventTypes[$type];
		}
		return false;
	}

	// http://bendodson.com/news/google-extended-encoding-made-easy/
	static function _array_to_extended_encoding($a) {
		if (!is_array($a)) {$a = array($a);}
		$encoding = '';
		foreach ($a as $value) {
			if ($value<0) $value = 0;
			$first = floor($value / 64);
			$second = $value % 64;
			$encoding .= self::$_xencoding[$first].self::$_xencoding[$second];
		}
		return $encoding;
	}

	static function _name_name_sort($a, $b) {
		return utf8_strcasecmp(strip_prefix($a['name']), strip_prefix($b['name']), true);  // Case-insensitive compare
	}

	static function _name_name_rsort($a, $b) {
		return utf8_strcasecmp(strip_prefix($b['name']), strip_prefix($a['name']), true);  // Case-insensitive compare
	}

	static function _name_total_sort($a, $b) {
		return $a['match']-$b['match'];
	}

	static function _name_total_rsort($a, $b) {
		return $b['match']-$a['match'];
	}

	static function _runSQL($sql, $count=0) {
		static $cache = array();
		$id = md5($sql)."_{$count}";
		if (isset($cache[$id])) {
			return $cache[$id];
		}
		$rows=WT_DB::prepareLimit($sql, $count)->fetchAll(PDO::FETCH_ASSOC);
		$cache[$id]=$rows;
		return $rows;
	}
}

?>
