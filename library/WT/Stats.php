<?php
// Family tree Statistics Class
//
// This class provides a quick & easy method for accessing statistics
// about the family tree.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_print_lists.php';

// Methods not allowed to be used in a statistic
define('STATS_NOT_ALLOWED', 'stats,getAllTags,getTags,embedTags');

class WT_Stats {
	var $_gedcom;
	var $_gedcom_url;
	var $_ged_id;
	var $_server_url; // Absolute URL for generating external links. (TODO: is this really needed?)
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
		$this->_ged_id = get_id_from_gedcom($gedcom);
		$this->_gedcom_url = rawurlencode($gedcom);
	}

	/**
	* Return an array of all supported tags and an example of its output.
	*/
	function getAllTags() {
		$examples = array();
		$methods = get_class_methods('WT_Stats');
		$c = count($methods);
		for ($i=0; $i < $c; $i++) {
			if ($methods[$i][0] == '_' || in_array($methods[$i], self::$_not_allowed)) {
				continue;
			}
			$examples[$methods[$i]] = $this->$methods[$i]();
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
			if (stristr($methods[$i], 'highlight')) {
				$examples[$methods[$i]]=str_replace(array(' align="left"', ' align="right"'), '', $examples[$methods[$i]]);
			}
		}
		if ($TEXT_DIRECTION=='rtl') {
			$alignVar = 'right';
			$alignRes = 'right';
		} else {
			$alignVar = 'left';
			$alignRes = 'left';
		}
		$out = "<table id=\"keywords\">
					<tr>
						<th align=\"{$alignVar}\" class=\"list_label_wrap\">".WT_I18N::translate('Embedded variable')."</th>
						<th style=\"text-align:{$alignVar};\" class=\"list_label_wrap \">".WT_I18N::translate('Resulting value')."</th>
					</tr>";
					foreach ($examples as $tag=>$v) {
						$out .= "\t<tr>";
						$out .= "<td class=\"list_value_wrap\" align=\"{$alignVar}\" valign=\"top\">{$tag}</td>";
						$out .= "<td class=\"list_value_wrap\" align=\"{$alignRes}\" valign=\"top\">{$v}</td>";
						$out .= "</tr>\n";
					}
		$out .= '</table>';
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
		for ($i=0; $i < $c; $i++) {
			$full_tag = $tags[$i];
			// Added for new parameter support
			$params = explode(':', $tags[$i]);
			if (count($params) > 1) {
				$tags[$i] = array_shift($params);
			} else {
				$params = array();
			}

			// Skip non-tags and non-allowed tags
			if ($tags[$i][0] == '_' || in_array($tags[$i], self::$_not_allowed)) {
				continue;
			}

			// Generate the replacement value for the tag
			if (method_exists($this, $tags[$i])) {
				$new_tags[] = "#{$full_tag}#";
				$new_values[]=call_user_func_array(array($this, $tags[$i]), array($params));
			} elseif ($tags[$i] == 'help') {
				// re-merge, just in case
				$new_tags[] = "#{$full_tag}#";
				$new_values[] = help_link(join(':', $params));
			}
		}
		return array($new_tags, $new_values);
	}

	/*
	* Embed tags in text
	*/
	function embedTags($text) {
		if (strpos($text, '#')!==false) {
			list($new_tags, $new_values) = $this->getTags($text);
			$text = str_replace($new_tags, $new_values, $text);
		}
		return $text;
	}

///////////////////////////////////////////////////////////////////////////////
// GEDCOM                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function gedcomFilename() {return get_gedcom_from_id($this->_ged_id);}

	function gedcomID() {return $this->_ged_id;}

	function gedcomTitle() {return htmlspecialchars(get_gedcom_setting($this->_ged_id, 'title'));}

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
			$date=new WT_Date($match[1]);
			return $date->Display(false, $DATE_FORMAT); // Override $PUBLIC_DATE_FORMAT
		}
		return '';
	}

	function gedcomUpdated() {
		$row=
			WT_DB::prepare("SELECT d_year, d_month, d_day FROM `##dates` WHERE d_julianday1 = ( SELECT max( d_julianday1 ) FROM `##dates` WHERE d_file =? AND d_fact=? ) LIMIT 1")
			->execute(array($this->_ged_id, 'CHAN'))
			->fetchOneRow();
		if ($row) {
			$date=new WT_Date("{$row->d_day} {$row->d_month} {$row->d_year}");
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
		return "<a href=\"{$this->_server_url}index.php?ctype=gedcom&amp;ged={$this->_gedcom_url}\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" class=\"gedcom_highlight\" alt=\"\" /></a>";
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
		return "<a href=\"{$this->_server_url}index.php?ctype=gedcom&amp;ged={$this->_gedcom_url}\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"left\" class=\"gedcom_highlight\" alt=\"\" /></a>";
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
		return "<a href=\"{$this->_server_url}index.php?ctype=gedcom&amp;ged={$this->_gedcom_url}\" style=\"border-style:none;\"><img src=\"{$highlight}\" {$imgsize[3]} style=\"border:none; padding:2px 6px 2px 2px;\" align=\"right\" class=\"gedcom_highlight\" alt=\"\" /></a>";
	}

///////////////////////////////////////////////////////////////////////////////
// Totals                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _getPercentage($total, $type) {
		switch($type) {
			default:
			case 'all':
				$type = $this->_totalIndividuals() + $this->_totalFamilies() + $this->_totalSources();
				break;
			case 'individual':
				$type = $this->_totalIndividuals();
				break;
			case 'family':
				$type = $this->_totalFamilies();
				break;
			case 'source':
				$type = $this->_totalSources();
				break;
			case 'note':
				$type = $this->_totalNotes();
				break;
			default:
				return WT_I18N::percentage(0, 1);
		}
		if ($type==0) {
			return WT_I18N::percentage(0, 1);
		} else {
			return WT_I18N::percentage($total / $type, 1);
		}
	}

	function totalRecords() {
		return WT_I18N::number($this->_totalIndividuals() + $this->_totalFamilies() + $this->_totalSources());
	}

	function _totalIndividuals() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##individuals` WHERE i_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalIndividuals() {
		return WT_I18N::number($this->_totalIndividuals());
	}

	function _totalIndisWithSources() {
		$rows=self::_runSQL("SELECT COUNT(DISTINCT i_id) AS tot FROM `##link`, `##individuals` WHERE i_id=l_from AND i_file=l_file AND l_file=".$this->_ged_id." AND l_type='SOUR'");
		return $rows[0]['tot'];
	}

	function totalIndisWithSources() {
		return WT_I18N::number(self::_totalIndisWithSources());
	}

	function chartIndisWithSources($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot_indi = $this->_totalIndividuals();
		if ($tot_indi==0) {
			return '';
		} else {
			$tot_sindi_per = round($this->_totalIndisWithSources()/$tot_indi, 3);
			$chd = self::_array_to_extended_encoding(array(100-100*$tot_sindi_per, 100*$tot_sindi_per));
			$chl =  WT_I18N::translate('Without sources').' - '.WT_I18N::percentage(1-$tot_sindi_per,1).'|'.
					WT_I18N::translate('With sources').' - '.WT_I18N::percentage($tot_sindi_per,1);
			$chart_title = WT_I18N::translate('Individuals with sources');
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	function totalIndividualsPercentage() {
		return $this->_getPercentage($this->_totalIndividuals(), 'all');
	}

	function _totalFamilies() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##families` WHERE f_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalFamilies() {
		return WT_I18N::number($this->_totalFamilies());
	}

	function _totalFamsWithSources() {
		$rows=self::_runSQL("SELECT COUNT(DISTINCT f_id) AS tot FROM `##link`, `##families` WHERE f_id=l_from AND f_file=l_file AND l_file=".$this->_ged_id." AND l_type='SOUR'");
		return $rows[0]['tot'];
	}

	function totalFamsWithSources() {
		return WT_I18N::number(self::_totalFamsWithSources());
	}

	function chartFamsWithSources($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot_fam = $this->_totalFamilies();
		if ($tot_fam==0) {
			return '';
		} else {
			$tot_sfam_per = round($this->_totalFamsWithSources()/$tot_fam, 3);
			$chd = self::_array_to_extended_encoding(array(100-100*$tot_sfam_per, 100*$tot_sfam_per));
			$chl =  WT_I18N::translate('Without sources').' - '.WT_I18N::percentage(1-$tot_sfam_per,1).'|'.
					WT_I18N::translate('With sources').' - '.WT_I18N::percentage($tot_sfam_per,1);
			$chart_title = WT_I18N::translate('Families with sources');
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	function totalFamiliesPercentage() {
		return $this->_getPercentage($this->_totalFamilies(), 'all');
	}

	function _totalSources() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##sources` WHERE s_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalSources() {
		return WT_I18N::number($this->_totalSources());
	}

	function totalSourcesPercentage() {
		return $this->_getPercentage($this->_totalSources(), 'all');
	}

	function _totalNotes() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##other` WHERE o_type='NOTE' AND o_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalNotes() {
		return WT_I18N::number($this->_totalNotes());
	}

	function totalNotesPercentage() {
		return $this->_getPercentage($this->_totalNotes(), 'all');
	}

	function _totalRepositories() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##other` WHERE o_type='REPO' AND o_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalRepositories() {
		return WT_I18N::number($this->_totalRepositories());
	}

	function totalRepositoriesPercentage() {
		return $this->_getPercentage($this->_totalRepositories(), 'all');
	}

	function totalSurnames($params = null) {
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
		$total=
			WT_DB::prepare(
				"SELECT COUNT({$distinct} n_surn COLLATE '".WT_I18N::$collation."')".
				" FROM `##name`".
				" WHERE n_surn COLLATE '".WT_I18N::$collation."' {$opt} AND n_file=?")
			->execute($vars)
			->fetchOne();
		return WT_I18N::number($total);
	}

	function totalGivennames($params = null) {
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
		$total=
			WT_DB::prepare("SELECT COUNT({$distinct} n_givn) FROM `##name` WHERE n_givn {$opt} AND n_file=?")
			->execute($vars)
			->fetchOne();
		return WT_I18N::number($total);
	}

	function totalEvents($params = null) {
		$sql="SELECT COUNT(*) AS tot FROM `##dates` WHERE d_file=?";
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
		return WT_I18N::number(WT_DB::prepare($sql)->execute($vars)->fetchOne());
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

	function _totalSexMales() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##individuals` WHERE i_file=? AND i_sex=?")
			->execute(array($this->_ged_id, 'M'))
			->fetchOne();
	}

	function totalSexMales() {
		return WT_I18N::number($this->_totalSexMales());
	}

	function totalSexMalesPercentage() {
		return $this->_getPercentage($this->_totalSexMales(), 'individual');
	}

	function _totalSexFemales() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##individuals` WHERE i_file=? AND i_sex=?")
			->execute(array($this->_ged_id, 'F'))
			->fetchOne();
	}

	function totalSexFemales() {
		return WT_I18N::number($this->_totalSexFemales());
	}

	function totalSexFemalesPercentage() {
		return $this->_getPercentage($this->_totalSexFemales(), 'individual');
	}

	function _totalSexUnknown() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##individuals` WHERE i_file=? AND i_sex=?")
			->execute(array($this->_ged_id, 'U'))
			->fetchOne();
	}

	function totalSexUnknown() {
		return WT_I18N::number($this->_totalSexUnknown());
	}

	function totalSexUnknownPercentage() {
		return $this->_getPercentage($this->_totalSexUnknown(), 'individual');
	}

	function chartSex($params=null) {
		global $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_female = strtolower($params[1]);} else {$color_female = 'ffd1dc';}
		if (isset($params[2]) && $params[2] != '') {$color_male = strtolower($params[2]);} else {$color_male = '84beff';}
		if (isset($params[3]) && $params[3] != '') {$color_unknown = strtolower($params[3]);} else {$color_unknown = '777777';}
		$sizes = explode('x', $size);
		// Raw data - for calculation
		$tot_f = $this->_totalSexFemales();
		$tot_m = $this->_totalSexMales();
		$tot_u = $this->_totalSexUnknown();
		$tot=$tot_f+$tot_m+$tot_u;
		// I18N data - for display
		$per_f = $this->totalSexFemalesPercentage();
		$per_m = $this->totalSexMalesPercentage();
		$per_u = $this->totalSexUnknownPercentage();
		if ($tot==0) {
			return '';
		} else if ($tot_u > 0) {
			$chd = self::_array_to_extended_encoding(array(4095*$tot_u/$tot, 4095*$tot_f/$tot, 4095*$tot_m/$tot));
			$chl =
				WT_I18N::translate_c('unknown people', 'Unknown').' - '.$per_u.'|'.
				WT_I18N::translate('Females').' - '.$per_f.'|'.
				WT_I18N::translate('Males').' - '.$per_m;
			$chart_title =
				WT_I18N::translate('Males').' - '.$per_m.WT_I18N::$list_separator.
				WT_I18N::translate('Females').' - '.$per_f.WT_I18N::$list_separator.
				WT_I18N::translate_c('unknown people', 'Unknown').' - '.$per_u;
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_unknown},{$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		} else {
			$chd = self::_array_to_extended_encoding(array($tot_f, $tot_m));
			$chl =
				WT_I18N::translate('Females').' - '.$per_f.'|'.
				WT_I18N::translate('Males').' - '.$per_m;
			$chart_title =  WT_I18N::translate('Males').' - '.$per_m.WT_I18N::$list_separator.
							WT_I18N::translate('Females').' - '.$per_f;
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	// The totalLiving/totalDeceased queries assume that every dead person will
	// have a DEAT record.  It will not include individuals who were born more
	// than MAX_ALIVE_AGE years ago, and who have no DEAT record.
	// A good reason to run the "Add missing DEAT records" batch-update!
	// However, SQL cannot provide the same logic used by Person::isDead().
	function _totalLiving() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##individuals` WHERE i_file=? AND i_gedcom NOT REGEXP '\\n1 (".WT_EVENTS_DEAT.")'")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalLiving() {
		return WT_I18N::number($this->_totalLiving());
	}

	function totalLivingPercentage() {
		return $this->_getPercentage($this->_totalLiving(), 'individual');
	}

	function _totalDeceased() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##individuals` WHERE i_file=? AND i_gedcom REGEXP '\\n1 (".WT_EVENTS_DEAT.")'")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalDeceased() {
		return WT_I18N::number($this->_totalDeceased());
	}

	function totalDeceasedPercentage() {
		return $this->_getPercentage($this->_totalDeceased(), 'individual');
	}

	function chartMortality($params=null) {
		global $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_living = strtolower($params[1]);} else {$color_living = 'ffffff';}
		if (isset($params[2]) && $params[2] != '') {$color_dead = strtolower($params[2]);} else {$color_dead = 'cccccc';}
		$sizes = explode('x', $size);
		// Raw data - for calculation
		$tot_l = $this->_totalLiving();
		$tot_d = $this->_totalDeceased();
		$tot=$tot_l+$tot_d;
		// I18N data - for display
		$per_l = $this->totalLivingPercentage();
		$per_d = $this->totalDeceasedPercentage();
		if ($tot==0) {
			return '';
		} else {
			$chd = self::_array_to_extended_encoding(array(4095*$tot_l/$tot, 4095*$tot_d/$tot));
			$chl =
				WT_I18N::translate('Living').' - '.$per_l.'|'.
				WT_I18N::translate('Dead').' - '.$per_d.'|';
			$chart_title =  WT_I18N::translate('Living').' - '.$per_l.WT_I18N::$list_separator.
							WT_I18N::translate('Dead').' - '.$per_d;
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
		}
	}

	static function totalUsers($params=null) {
		if (!empty($params[0])) {
			$total=get_user_count() + (int)$params[0];
		} else {
			$total=get_user_count();
		}
		return WT_I18N::number($total);
	}

	static function totalAdmins() {
		return WT_I18N::number(get_admin_user_count());
	}

	static function totalNonAdmins() {
		return WT_I18N::number(get_non_admin_user_count());
	}

	function _totalMediaType($type='all') {
		if (!in_array($type, self::$_media_types) && $type != 'all' && $type != 'unknown') {
			return 0;
		}
		$sql="SELECT COUNT(*) AS tot FROM `##media` WHERE m_gedfile=?";
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

	function totalMedia()            {return WT_I18N::number($this->_totalMediaType('all'));}
	function totalMediaAudio()       {return WT_I18N::number($this->_totalMediaType('audio'));}
	function totalMediaBook()        {return WT_I18N::number($this->_totalMediaType('book'));}
	function totalMediaCard()        {return WT_I18N::number($this->_totalMediaType('card'));}
	function totalMediaCertificate() {return WT_I18N::number($this->_totalMediaType('certificate'));}
	function totalMediaCoatOfArms()  {return WT_I18N::number($this->_totalMediaType('coat'));}
	function totalMediaDocument()    {return WT_I18N::number($this->_totalMediaType('document'));}
	function totalMediaElectronic()  {return WT_I18N::number($this->_totalMediaType('electronic'));}
	function totalMediaMagazine()    {return WT_I18N::number($this->_totalMediaType('magazine'));}
	function totalMediaManuscript()  {return WT_I18N::number($this->_totalMediaType('manuscript'));}
	function totalMediaMap()         {return WT_I18N::number($this->_totalMediaType('map'));}
	function totalMediaFiche()       {return WT_I18N::number($this->_totalMediaType('fiche'));}
	function totalMediaFilm()        {return WT_I18N::number($this->_totalMediaType('film'));}
	function totalMediaNewspaper()   {return WT_I18N::number($this->_totalMediaType('newspaper'));}
	function totalMediaPainting()    {return WT_I18N::number($this->_totalMediaType('painting'));}
	function totalMediaPhoto()       {return WT_I18N::number($this->_totalMediaType('photo'));}
	function totalMediaTombstone()   {return WT_I18N::number($this->_totalMediaType('tombstone'));}
	function totalMediaVideo()       {return WT_I18N::number($this->_totalMediaType('video'));}
	function totalMediaOther()       {return WT_I18N::number($this->_totalMediaType('other'));}
	function totalMediaUnknown()     {return WT_I18N::number($this->_totalMediaType('unknown'));}

	function chartMedia($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
		$sizes = explode('x', $size);
		$tot = $this->_totalMediaType('all');
		// Beware divide by zero
		if ($tot==0) return WT_I18N::translate('None');
		// Build a table listing only the media types actually present in the GEDCOM
		$mediaCounts = array();
		$mediaTypes = "";
		$chart_title = "";
		$c = 0;
		$max = 0;
		$media=array();
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
		$count = $this->_totalMediaType('unknown');
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
			$mediaTypes .= WT_Gedcom_Tag::getFileFormTypeValue($type).' - '.WT_I18N::number($count).'|';
			$chart_title .= WT_Gedcom_Tag::getFileFormTypeValue($type).' ('.$count.'), ';
		}
		$chart_title = substr($chart_title,0,-2);
		$chd = self::_array_to_extended_encoding($mediaCounts);
		$chl = substr($mediaTypes,0,-1);
		return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

///////////////////////////////////////////////////////////////////////////////
// Birth & Death                                                             //
///////////////////////////////////////////////////////////////////////////////

	function _mortalityQuery($type='full', $life_dir='ASC', $birth_death='BIRT') {
		global $listDir;
		if ($birth_death == 'MARR') {
			//$query_field = "'".str_replace('|', "','", WT_EVENTS_MARR)."'";
			$query_field = "'MARR'";
		} else if ($birth_death == 'DIV') {
			//$query_field = "'".str_replace('|', "','", WT_EVENTS_DIV)."'";
			$query_field = "'DIV'";
		} else if ($birth_death == 'BIRT') {
			//$query_field = "'".str_replace('|', "','", WT_EVENTS_BIRT)."'";
			$query_field = "'BIRT'";
		} else {
			$birth_death = 'DEAT';
			//$query_field = "'".str_replace('|', "','", WT_EVENTS_DEAT)."'";
			$query_field = "'DEAT'";
		}
		if ($life_dir == 'ASC') {
			$dmod = 'MIN';
		} else {
			$dmod = 'MAX';
			$life_dir = 'DESC';
		}
		$rows=self::_runSQL(''
			."SELECT d_year, d_type, d_fact, d_gid"
			." FROM `##dates`"
			." WHERE d_file={$this->_ged_id} AND d_fact IN ({$query_field}) AND d_julianday1=("
			." SELECT {$dmod}( d_julianday1 )"
			." FROM `##dates`"
			." WHERE d_file={$this->_ged_id} AND d_fact IN ({$query_field}) AND d_julianday1<>0 )"
 			." LIMIT 1"

		/*//testing - too slow
			.' SELECT'
				.' d2.d_year,'
				.' d2.d_type,'
				.' d2.d_fact,'
				.' d2.d_gid'
			.' FROM'
				." `##dates` AS d2"
			.' WHERE'
				." d2.d_file={$this->_ged_id} AND"
				." d2.d_fact IN ({$query_field}) AND"
				.' d2.d_julianday1=('
					.' SELECT'
						." {$dmod}(d_julianday1)"
					.' FROM'
						." `##dates`"
					.' JOIN ('
						.' SELECT'
							.' d1.d_gid, MIN(d1.d_julianday1) as date'
						.' FROM'
							."  `##dates` AS d1"
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
		*/
		);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		$record=WT_GedcomRecord::getInstance($row['d_gid']);
		switch($type) {
			default:
			case 'full':
				if ($record->canDisplayDetails()) {
					$result=$record->format_list('span', false, $record->getFullName());
				} else {
					$result=WT_I18N::translate('This information is private and cannot be shown.');
				}
				break;
			case 'year':
				$date=new WT_Date($row['d_type'].' '.$row['d_year']);
				$result=$date->Display(true);
				break;
			case 'name':
				$result="<a href=\"".$record->getHtmlUrl()."\">".$record->getFullName()."</a>";
				break;
			case 'place':
				$fact=WT_GedcomRecord::getInstance($row['d_gid'])->getFactByType($row['d_fact']);
				$result=format_fact_place($fact, true, true, true);
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _statsPlaces($what='ALL', $fact=false, $parent=0, $country=false) {
		if ($fact) {
			if ($what=='INDI') {
				$rows=
					WT_DB::prepare("SELECT i_gedcom AS ged FROM `##individuals` WHERE i_file=?")
					->execute(array($this->_ged_id))
					->fetchAll();
			}
			else if ($what=='FAM') {
				$rows=
					WT_DB::prepare("SELECT f_gedcom AS ged FROM `##families` WHERE f_file=?")
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
				$join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
			}
			else if ($what=='FAM') {
				$join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
			}
			else {
				$join = "";
			}
			$rows=self::_runSQL(''
				.' SELECT'
				.' p_place AS place,'
				.' COUNT(*) AS tot'
				.' FROM'
					." `##places`"
				." JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id"
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
				$join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
			}
			else if ($what=='FAM') {
				$join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
			}
			else {
				$join = "";
			}
			$rows=self::_runSQL(''
					.' SELECT'
						.' p_place AS country,'
						.' COUNT(*) AS tot'
					.' FROM'
						." `##places`"
					." JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id"
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

	function _totalPlaces() {
		return
			WT_DB::prepare("SELECT COUNT(*) FROM `##places` WHERE p_file=?")
			->execute(array($this->_ged_id))
			->fetchOne();
	}

	function totalPlaces() {
		return WT_I18n::number($this->_totalPlaces());
	}

	function chartDistribution($params = null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_CHART_COLOR3, $WT_STATS_MAP_X, $WT_STATS_MAP_Y;
		if ($params !== null && isset($params[0])) {$chart_shows = $params[0];} else {$chart_shows='world';}
		if ($params !== null && isset($params[1])) {$chart_type = $params[1];} else {$chart_type='';}
		if ($params !== null && isset($params[2])) {$surname = $params[2];} else {$surname='';}

		if ($this->_totalPlaces()==0) {
			return '';
		}
		// Get the country names for each language
		$country_to_iso3166=array();
		foreach (WT_I18N::installed_languages() as $code=>$lang) {
			WT_I18N::init($code);
			$countries=self::get_all_countries();
			foreach (self::iso3166() as $three=>$two) {
				$country_to_iso3166[$three]=$two;
				$country_to_iso3166[$countries[$three]]=$two;
			}
		}
		WT_I18N::init(WT_LOCALE);
		switch ($chart_type) {
		case 'surname_distribution_chart':
			if ($surname=="") $surname = $this->getCommonSurname();
			$chart_title=WT_I18N::translate('Surname distribution chart').': '.$surname;
			// Count how many people are events in each country
			$surn_countries=array();
			$indis = WT_Query_Name::individuals(utf8_strtoupper($surname), '', '', false, false, WT_GED_ID);
			foreach ($indis as $person) {
				if (preg_match_all('/^2 PLAC (?:.*, *)*(.*)/m', $person->getGedcomRecord(), $matches)) {
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
			$chart_title=WT_I18N::translate('Birth by country');
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
			$chart_title=WT_I18N::translate('Death by country');
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
			$chart_title=WT_I18N::translate('Marriage by country');
			// Count how many families got marriage in each country
			$surn_countries=array();
			$m_countries=$this->_statsPlaces('FAM');
			// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			foreach ($m_countries as $place) {
				$country=trim($place['country']);
				if (!isset($surn_countries[$country_to_iso3166[$country]])) {
					$surn_countries[$country_to_iso3166[$country]]=$place['tot'];
				} else {
					$surn_countries[$country_to_iso3166[$country]]+=$place['tot'];
				}
			}
			break;
		case 'indi_distribution_chart':
		default:
			$chart_title=WT_I18N::translate('Individual distribution chart');
			// Count how many people are events in each country
			$surn_countries=array();
			$a_countries=$this->_statsPlaces('INDI');
			// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			foreach ($a_countries as $place) {
				$country=trim($place['country']);
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]]=$place['tot'];
					} else {
						$surn_countries[$country_to_iso3166[$country]]+=$place['tot'];
					}
				}
			}
			break;
		}
		$chart_url ="https://chart.googleapis.com/chart?cht=t&amp;chtm=".$chart_shows;
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
		$chart .= '<td bgcolor="#'.$WT_STATS_CHART_COLOR2.'" width="12"></td><td>'.WT_I18N::translate('Highest population').'&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#'.$WT_STATS_CHART_COLOR3.'" width="12"></td><td>'.WT_I18N::translate('Lowest population').'&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#'.$WT_STATS_CHART_COLOR1.'" width="12"></td><td>'.WT_I18N::translate('Nobody at all').'&nbsp;&nbsp;</td>';
		$chart .= '</tr></table></div></div>';
		return $chart;
	}

	function commonCountriesList() {
		$countries = $this->_statsPlaces();
		if (!is_array($countries)) return '';
		$top10 = array();
		$i = 1;
		// Get the country names for each language
		$country_names=array();
		foreach (WT_I18N::installed_languages() as $code=>$lang) {
			WT_I18N::init($code);
			$all_countries = self::get_all_countries();
			foreach ($all_countries as $country_code=>$country_name) {
				$country_names[$country_name]=$country_code;
			}
		}
		WT_I18N::init(WT_LOCALE);
		$all_db_countries=array();
		foreach ($countries as $place) {
			$country=trim($place['country']);
			if (array_key_exists($country, $country_names)) {
				if (!isset($all_db_countries[$country_names[$country]][$country])) {
					$all_db_countries[$country_names[$country]][$country]=$place['tot'];
				} else {
					$all_db_countries[$country_names[$country]][$country]+=$place['tot'];
				}
			}
		}
		// get all the user's countries names
		$all_countries = self::get_all_countries();
		foreach ($all_db_countries as $country_code=>$country) {
			$top10[]='<li>';
			foreach ($country as $country_name=>$tot) {
				$place = '<a href="'.get_place_url($country_name).'" class="list_item">'.$all_countries[$country_code].'</a>';
				$top10[].=$place.' - '.WT_I18N::number($tot);
			}
			$top10[].='</li>';
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonBirthPlacesList() {
		$places = $this->_statsPlaces('INDI', 'BIRT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.get_place_url($place).'" class="list_item">'.htmlspecialchars($place).'</a>';
			$top10[]='<li>'.$place.' - '.WT_I18N::number($count).'</li>';
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonDeathPlacesList() {
		$places = $this->_statsPlaces('INDI', 'DEAT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.get_place_url($place).'" class="list_item">'.htmlspecialchars($place).'</a>';
			$top10[]='<li>'.$place.' - '.WT_I18N::number($count).'</li>';
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function commonMarriagePlacesList() {
		$places = $this->_statsPlaces('FAM', 'MARR');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place=>$count) {
			$place = '<a href="'.get_place_url($place).'" class="list_item">'.htmlspecialchars($place).'</a>';
			$top10[]='<li>'.$place.' - '.WT_I18N::number($count).'</li>';
			if ($i++==10) break;
		}
		$top10=join("\n", $top10);
		return "<ul>\n{$top10}</ul>\n";
	}

	function _statsBirth($simple=true, $sex=false, $year1=-1, $year2=-1, $params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						.'d_year<>0 AND '
						."d_fact='BIRT' AND "
						."d_type='@#DGREGORIAN@'";
		} else if ($sex) {
			$sql = "SELECT d_month, i_sex, COUNT(*) AS total FROM `##dates` "
					."JOIN `##individuals` ON d_file = i_file AND d_gid = i_id "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='BIRT' AND "
						."d_type='@#DGREGORIAN@'";
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM `##dates` "
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
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= self::_centuryName($values['century']).' - '.WT_I18N::number($values['total']).'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = rawurlencode(substr($centuries,0,-1));
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Births by century')."\" title=\"".WT_I18N::translate('Births by century')."\" />";
		}
		if (!isset($rows)) return 0;
		return $rows;
	}

	function _statsDeath($simple=true, $sex=false, $year1=-1, $year2=-1, $params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						.'d_year<>0 AND '
						."d_fact='DEAT' AND "
						."d_type='@#DGREGORIAN@'";
		} else if ($sex) {
			$sql = "SELECT d_month, i_sex, COUNT(*) AS total FROM `##dates` "
					."JOIN `##individuals` ON d_file = i_file AND d_gid = i_id "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						."d_fact='DEAT' AND "
						."d_type='@#DGREGORIAN@'";
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM `##dates` "
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
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= self::_centuryName($values['century']).' - '.WT_I18N::number($values['total']).'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = rawurlencode(substr($centuries,0,-1));
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Deaths by century')."\" title=\"".WT_I18N::translate('Deaths by century')."\" />";
		}
		if (!isset($rows)) {return 0;}
		return $rows;
	}

	//
	// Birth
	//

	function firstBirth()      { return $this->_mortalityQuery('full',  'ASC', 'BIRT'); }
	function firstBirthYear()  { return $this->_mortalityQuery('year',  'ASC', 'BIRT'); }
	function firstBirthName()  { return $this->_mortalityQuery('name',  'ASC', 'BIRT'); }
	function firstBirthPlace() { return $this->_mortalityQuery('place', 'ASC', 'BIRT'); }

	function lastBirth()       { return $this->_mortalityQuery('full',  'DESC', 'BIRT'); }
	function lastBirthYear()   { return $this->_mortalityQuery('year',  'DESC', 'BIRT'); }
	function lastBirthName()   { return $this->_mortalityQuery('name',  'DESC', 'BIRT'); }
	function lastBirthPlace()  { return $this->_mortalityQuery('place', 'DESC', 'BIRT'); }

	function statsBirth($params=null) {return $this->_statsBirth(true, false, -1, -1, $params);}

	//
	// Death
	//

	function firstDeath()      { return $this->_mortalityQuery('full',  'ASC', 'DEAT'); }
	function firstDeathYear()  { return $this->_mortalityQuery('year',  'ASC', 'DEAT'); }
	function firstDeathName()  { return $this->_mortalityQuery('name',  'ASC', 'DEAT'); }
	function firstDeathPlace() { return $this->_mortalityQuery('place', 'ASC', 'DEAT'); }

	function lastDeath()       { return $this->_mortalityQuery('full',  'DESC', 'DEAT'); }
	function lastDeathYear()   { return $this->_mortalityQuery('year',  'DESC', 'DEAT'); }
	function lastDeathName()   { return $this->_mortalityQuery('name',  'DESC', 'DEAT'); }
	function lastDeathPlace()  { return $this->_mortalityQuery('place', 'DESC', 'DEAT'); }

	function statsDeath($params=null) { return $this->_statsDeath(true, false, -1, -1, $params); }

///////////////////////////////////////////////////////////////////////////////
// Lifespan                                                                  //
///////////////////////////////////////////////////////////////////////////////

	function _longlifeQuery($type='full', $sex='F') {
		global $listDir;

		$sex_search = ' 1=1';
		if ($sex == 'F') {
			$sex_search = " i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " i_sex='M'";
		}

		$rows=self::_runSQL(''
		/*//old
			.' SELECT'
				.' death.d_gid AS id,'
				.' death.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." `##dates` AS death,"
				." `##dates` AS birth,"
				." `##individuals` AS indi"
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
		//testing - too slow
			.' SELECT'
				.' i_id AS id,'
				.' death.death_jd-birth.birth_jd AS age'
			.' FROM'
				.' (SELECT d_gid, d_file, MIN(d_julianday2) AS death_jd'
					.' FROM `##dates`'
					." WHERE d_fact IN ('DEAT', 'BURI', 'CREM') AND d_julianday2>0"
					.' GROUP BY d_gid, d_file'
				.' ) AS death'
			.' JOIN'
				.' (SELECT d_gid, d_file, MIN(d_julianday1) AS birth_jd'
					.' FROM `##dates`'
					." WHERE d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND d_julianday1>0"
					.' GROUP BY d_gid, d_file'
				.' ) AS birth USING (d_gid, d_file)'
			.' JOIN `##individuals` ON (d_gid=i_id AND d_file=i_file)'
			.' WHERE'
				." i_file={$this->_ged_id} AND"
				.$sex_search
			.' ORDER BY'
				.' age DESC'
		*/
		// use only BIRT and DEAT
			.' SELECT'
				.' death.d_gid AS id,'
				.' death.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." `##dates` AS death,"
				." `##dates` AS birth,"
				." `##individuals` AS indi"
			.' WHERE'
				.' indi.i_id=birth.d_gid AND'
				.' birth.d_gid=death.d_gid AND'
				." death.d_file={$this->_ged_id} AND"
				.' birth.d_file=death.d_file AND'
				.' birth.d_file=indi.i_file AND'
				." birth.d_fact='BIRT' AND"
				." death.d_fact='DEAT' AND"
				.' birth.d_julianday1<>0 AND'
				.' death.d_julianday1>birth.d_julianday2 AND'
				.$sex_search
			.' ORDER BY'
				.' age DESC LIMIT 1'
		);
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$person=WT_Person::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($person->canDisplayName()) {
					$result=$person->format_list('span', false, $person->getFullName());
				} else {
					$result= WT_I18N::translate('This information is private and cannot be shown.');
				}
				break;
			case 'age':
				$result=WT_I18N::number(floor($row['age']/365.25));
				break;
			case 'name':
				$result="<a href=\"".$person->getHtmlUrl()."\">".$person->getFullName()."</a>";
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenOldest($type='list', $sex='BOTH', $params=null) {
		global $TEXT_DIRECTION;

		if ($sex == 'F') {
			$sex_search = " AND i_sex='F' ";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M' ";
		} else {
			$sex_search = '';
		}
		if ($params !== null && isset($params[0])) {$total = $params[0];} else {$total = 10;}
		$total=(int)$total;
		$rows=self::_runSQL(
			'SELECT '.
			' MAX(death.d_julianday2-birth.d_julianday1) AS age, '.
			' death.d_gid AS deathdate '.
			'FROM '.
			" `##dates` AS death, ".
			" `##dates` AS birth, ".
			" `##individuals` AS indi ".
			'WHERE '.
			' indi.i_id=birth.d_gid AND '.
			' birth.d_gid=death.d_gid AND '.
			" death.d_file={$this->_ged_id} AND ".
			' birth.d_file=death.d_file AND '.
			' birth.d_file=indi.i_file AND '.
			" birth.d_fact='BIRT' AND ". // Only use BIRT/DEAT.  Using CHR/BURI can give spurious results.
			" death.d_fact='DEAT' AND ".
			' birth.d_julianday1<>0 AND '.
			' death.d_julianday1>birth.d_julianday2 '.
			$sex_search.
			'GROUP BY deathdate '.
			'ORDER BY age DESC '.
			'LIMIT '.$total
		);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		foreach ($rows as $row) {
			$person = WT_Person::getInstance($row['deathdate']);
			$age = $row['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/30.4375)>0) {
				$age = floor($age/30.4375).'m';
			} else {
				$age = $age.'d';
			}
			$age = get_age_at_event($age, true);
			if ($person->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[]="\t<li><a href=\"".$person->getHtmlUrl()."\">".$person->getFullName()."</a> (".$age.")"."</li>\n";
				} else {
					$top10[]="<a href=\"".$person->getHtmlUrl()."\">".$person->getFullName()."</a> (".$age.")";
				}
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10=join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _topTenOldestAlive($type='list', $sex='BOTH', $params=null) {
		global $TEXT_DIRECTION;

		if (!WT_USER_CAN_ACCESS) return WT_I18N::translate('This information is private and cannot be shown.');
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		if ($params !== null && isset($params[0])) {$total = $params[0];} else {$total = 10;}
		$total=(int)$total;
		$rows=self::_runSQL(''
			." SELECT"
			." birth.d_gid AS id,"
			." MIN(birth.d_julianday1) AS age"
			." FROM"
			." `##dates` AS birth,"
			." `##individuals` AS indi"
			." WHERE"
			." indi.i_id=birth.d_gid AND"
			." indi.i_gedcom NOT REGEXP '\n1 (".WT_EVENTS_DEAT.")' AND"
			." birth.d_file={$this->_ged_id} AND"
			." birth.d_fact='BIRT' AND"
			." birth.d_file=indi.i_file AND"
			." birth.d_julianday1<>0"
			.$sex_search
			.' GROUP BY'
			.' id'
			.' ORDER BY'
			.' age ASC LIMIT '.$total
		);
		if (!isset($rows)) {return 0;}
		$top10 = array();
		foreach ($rows as $row) {
			$person=WT_Person::getInstance($row['id']);
			$age = (WT_CLIENT_JD-$row['age']);
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/30.4375)>0) {
				$age = floor($age/30.4375).'m';
			} else {
				$age = $age.'d';
			}
			$age = get_age_at_event($age, true);
			if ($type == 'list') {
				$top10[]="\t<li><a href=\"".$person->getHtmlUrl()."\">".$person->getFullName()."</a> (".$age.")"."</li>\n";
			} else {
				$top10[]="<a href=\"".$person->getHtmlUrl()."\">".$person->getFullName()."</a> (".$age.")";
			}
		}
		if ($type == 'list') {
			$top10=join("\n", $top10);
		} else {
			$top10=join(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION=='rtl') {
			$top10=str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return "<ul>\n{$top10}</ul>\n";
		}
		return $top10;
	}

	function _averageLifespanQuery($sex='BOTH', $show_years=false) {
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F' ";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M' ";
		} else {
			$sex_search = '';
		}
		$rows=self::_runSQL(
			"SELECT ".
			" AVG(death.d_julianday2-birth.d_julianday1) AS age ".
			"FROM ".
			" `##dates` AS death, ".
			" `##dates` AS birth, ".
			" `##individuals` AS indi ".
			"WHERE ".
			" indi.i_id=birth.d_gid AND ".
			" birth.d_gid=death.d_gid AND ".
			" death.d_file=".$this->_ged_id. " AND ".
			" birth.d_file=death.d_file AND ".
			" birth.d_file=indi.i_file AND ".
			" birth.d_fact='BIRT' AND ". // Use only BIRT and DEAT.  Using CHR/BURI can give spurious results.
			" death.d_fact='DEAT' AND ".
			" birth.d_julianday1<>0 AND ".
			" death.d_julianday1>birth.d_julianday2 ".
			$sex_search
		);
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$age = $row['age'];
		if ($show_years) {
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/30.4375)>0) {
				$age = floor($age/30.4375).'m';
			} else if (!empty($age)) {
				$age = $age.'d';
			}
			return get_age_at_event($age, true);
		} else {
			return WT_I18N::number($age/365.25);
		}
	}

	function _statsAge($simple=true, $related='BIRT', $sex='BOTH', $year1=-1, $year2=-1, $params=null) {
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = '230x250';}
			$sizes = explode('x', $size);
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(death.d_julianday2-birth.d_julianday1)/365.25,1) AS age,'
					.' FLOOR(death.d_year/100+1) AS century,'
					.' i_sex AS sex'
				.' FROM'
					." `##dates` AS death,"
					." `##dates` AS birth,"
					." `##individuals` AS indi"
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
			$chxl = '0:|';
			$countsm = '';
			$countsf = '';
			$countsa = '';
			foreach ($rows as $values) {
				$out[$values['century']][$values['sex']]=$values['age'];
			}
			foreach ($out as $century=>$values) {
				if ($sizes[0]<980) $sizes[0] += 50;
				$chxl .= self::_centuryName($century).'|';
				$average = 0;
				if (isset($values['F'])) {
					$countsf .= $values['F'].',';
					$average = $values['F'];
				} else {
					$countsf .= '0,';
				}
				if (isset($values['M'])) {
					$countsm .= $values['M'].',';
					if ($average==0) $countsa .= $values['M'].',';
					else $countsa .= (($values['M']+$average)/2).',';
				} else {
					$countsm .= '0,';
					if ($average==0) $countsa .= '0,';
					else $countsa .= $values['F'].',';
				}
			}
			$countsm = substr($countsm,0,-1);
			$countsf = substr($countsf,0,-1);
			$countsa = substr($countsa,0,-1);
			$chd = 't2:'.$countsm.'|'.$countsf.'|'.$countsa;
			$decades='';
			for ($i=0; $i<=100; $i+=10) {
				$decades.='|'.WT_I18N::number($i);
			}
			$chxl .= '1:||'.WT_I18N::translate('century').'|2:'.$decades.'|3:||'.WT_I18N::translate('Age').'|';
			$title = WT_I18N::translate('Average age related to death century');
			if (count($rows)>6 || utf8_strlen($title)<30) {
				$chtt = $title;
			} else {
				$offset = 0;
				$counter = array();
				while ($offset = strpos($title, ' ', $offset + 1)) {
					$counter[] = $offset;
				}
				$half = floor(count($counter)/2);
				$chtt = substr_replace($title, '|', $counter[$half], 1);
			}
			return '<img src="'."https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|N*f1*,000000,0,-1,11,1|N*f1*,000000,1,-1,11,1&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=".rawurlencode($chtt)."&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=".rawurlencode($chxl)."&amp;chdl=".rawurlencode(WT_I18N::translate('Males').'|'.WT_I18N::translate('Females').'|'.WT_I18N::translate('Average age at death'))."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Average age related to death century')."\" title=\"".WT_I18N::translate('Average age related to death century')."\" />";
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
					." `##dates` AS death,"
					." `##dates` AS birth,"
					." `##individuals` AS indi"
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
	function statsAge($params=null) {return $this->_statsAge(true, 'BIRT', 'BOTH', -1, -1, $params);}

	function longestLife()     { return $this->_longlifeQuery('full', 'BOTH'); }
	function longestLifeAge()  { return $this->_longlifeQuery('age',  'BOTH'); }
	function longestLifeName() { return $this->_longlifeQuery('name', 'BOTH'); }

	function topTenOldest($params=null)          { return $this->_topTenOldest('nolist', 'BOTH', $params); }
	function topTenOldestList($params=null)      { return $this->_topTenOldest('list',   'BOTH', $params); }

	function topTenOldestAlive($params=null)     { return $this->_topTenOldestAlive('nolist', 'BOTH', $params); }
	function topTenOldestListAlive($params=null) { return $this->_topTenOldestAlive('list',   'BOTH', $params); }

	function averageLifespan($show_years=false)  { return $this->_averageLifespanQuery('BOTH', $show_years); }

	// Female Only

	function longestLifeFemale()     { return $this->_longlifeQuery('full', 'F'); }
	function longestLifeFemaleAge()  { return $this->_longlifeQuery('age',  'F'); }
	function longestLifeFemaleName() { return $this->_longlifeQuery('name', 'F'); }

	function topTenOldestFemale($params=null)     { return $this->_topTenOldest('nolist', 'F', $params); }
	function topTenOldestFemaleList($params=null) { return $this->_topTenOldest('list',   'F', $params); }

	function topTenOldestFemaleAlive($params=null)     { return $this->_topTenOldestAlive('nolist', 'F', $params); }
	function topTenOldestFemaleListAlive($params=null) { return $this->_topTenOldestAlive('list',   'F', $params); }

	function averageLifespanFemale($show_years=false) { return $this->_averageLifespanQuery('F', $show_years); }

	// Male Only

	function longestLifeMale()     { return $this->_longlifeQuery('full', 'M'); }
	function longestLifeMaleAge()  { return $this->_longlifeQuery('age',  'M'); }
	function longestLifeMaleName() { return $this->_longlifeQuery('name', 'M'); }

	function topTenOldestMale($params=null)     { return $this->_topTenOldest('nolist', 'M', $params); }
	function topTenOldestMaleList($params=null) { return $this->_topTenOldest('list',   'M', $params); }

	function topTenOldestMaleAlive($params=null)     { return $this->_topTenOldestAlive('nolist', 'M', $params); }
	function topTenOldestMaleListAlive($params=null) { return $this->_topTenOldestAlive('list',   'M', $params); }

	function averageLifespanMale($show_years=false) {return $this->_averageLifespanQuery('M', $show_years);}

///////////////////////////////////////////////////////////////////////////////
// Events                                                                    //
///////////////////////////////////////////////////////////////////////////////

	function _eventQuery($type, $direction, $facts) {
		global $listDir;
		$eventTypes = array(
			'BIRT'=>WT_I18N::translate('birth'),
			'DEAT'=>WT_I18N::translate('death'),
			'MARR'=>WT_I18N::translate('marriage'),
			'ADOP'=>WT_I18N::translate('adoption'),
			'BURI'=>WT_I18N::translate('burial'),
			'CENS'=>WT_I18N::translate('census added')
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
				." `##dates`"
			.' WHERE'
				." d_file={$this->_ged_id} AND"
				." d_gid<>'HEAD' AND"
				." d_fact {$fact_query} AND"
				.' d_julianday1<>0'
			.' ORDER BY'
				." d_julianday1 {$direction}, d_type LIMIT 1"
		);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		$record=WT_GedcomRecord::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($record->canDisplayDetails()) {
					$result=$record->format_list('span', false, $record->getFullName());
				} else {
					$result=WT_I18N::translate('This information is private and cannot be shown.');
				}
				break;
			case 'year':
				$date=new WT_Date($row['type'].' '.$row['year']);
				$result=$date->Display(true);
				break;
			case 'type':
				if (isset($eventTypes[$row['fact']])) {
					$result=$eventTypes[$row['fact']];
				} else {
					$result=WT_Gedcom_Tag::getLabel($row['fact']);
				}
				break;
			case 'name':
				$result="<a href=\"".$record->getHtmlUrl()."\">".$record->getFullName()."</a>";
				break;
			case 'place':
				$fact=$record->getFactByType($row['fact']);
				$result=format_fact_place($fact, true, true, true);
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
		if ($sex == 'F') {$sex_field = 'f_wife';} else {$sex_field = 'f_husb';}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$rows=self::_runSQL(''
		/* //old
			.' SELECT'
				.' fam.f_id AS famid,'
				." fam.{$sex_field},"
				.' married.d_julianday2-birth.d_julianday1 AS age,'
				.' indi.i_id AS i_id'
			.' FROM'
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS birth ON birth.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##dates` AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##individuals` AS indi ON indi.i_file = {$this->_ged_id}"
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
		//testing - too slow
			. 'SELECT'
				.' fam.f_id AS famid,'
				." fam.{$sex_field},"
				.' married.d_julianday2-birth.d_julianday1 AS age,'
				.' indi.i_id AS i_id'
			.' FROM'
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS birth ON birth.d_file = {$this->_ged_id} AND birth.d_fact = 'BIRT'"
			.' LEFT JOIN'
				." `##dates` AS birth_act ON birth_act.d_file = {$this->_ged_id} AND birth_act.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM')"
			.' LEFT JOIN'
				." `##dates` AS married ON married.d_file = {$this->_ged_id} AND married.d_fact = 'MARR'"
			.' LEFT JOIN'
				." `##individuals` AS indi ON indi.i_file = {$this->_ged_id}"
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
		// use only BIRT and MARR
			.' SELECT'
				.' fam.f_id AS famid,'
				." fam.{$sex_field},"
				.' married.d_julianday2-birth.d_julianday1 AS age,'
				.' indi.i_id AS i_id'
			.' FROM'
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS birth ON birth.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##dates` AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##individuals` AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' birth.d_gid = indi.i_id AND'
				.' married.d_gid = fam.f_id AND'
				." indi.i_id = fam.{$sex_field} AND"
				." fam.f_file = {$this->_ged_id} AND"
				." birth.d_fact = 'BIRT' AND"
				." married.d_fact = 'MARR' AND"
				.' birth.d_julianday1 <> 0 AND'
				.' married.d_julianday2 > birth.d_julianday1 AND'
				." i_sex='{$sex}'"
			.' ORDER BY'
				." married.d_julianday2-birth.d_julianday1 {$age_dir} LIMIT 1"
		);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		if (isset($row['famid'])) $family=WT_Family::getInstance($row['famid']);
		if (isset($row['i_id'])) $person=WT_Person::getInstance($row['i_id']);
		switch($type) {
			default:
			case 'full':
				if ($family->canDisplayDetails()) {
					$result=$family->format_list('span', false, $person->getFullName());
				} else {
					$result=WT_I18N::translate('This information is private and cannot be shown.');
				}
				break;
			case 'name':
				$result='<a href="'.$family->getHtmlUrl().'">'.$person->getFullName().'</a>';
				break;
			case 'age':
				$age = $row['age'];
				if ($show_years) {
					if (floor($age/365.25)>0) {
						$age = floor($age/365.25).'y';
					} else if (floor($age/30.4375)>0) {
						$age = floor($age/30.4375).'m';
					} else {
						$age = $age.'d';
					}
					$result = get_age_at_event($age, true);
				} else {
					$result = floor($age/365.25);
				}
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _ageOfMarriageQuery($type='list', $age_dir='ASC', $params=null) {
		global $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];} else {$total = 10;}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$hrows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' fam.f_id AS family,'
				.' MIN(husbdeath.d_julianday2-married.d_julianday1) AS age'
			.' FROM'
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##dates` AS husbdeath ON husbdeath.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' husbdeath.d_gid = fam.f_husb AND'
				." husbdeath.d_fact = 'DEAT' AND"
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
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##dates` AS wifedeath ON wifedeath.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' wifedeath.d_gid = fam.f_wife AND'
				." wifedeath.d_fact = 'DEAT' AND"
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
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##dates` AS divorced ON divorced.d_file = {$this->_ged_id}"
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
		foreach ($rows as $fam=>$age) {
			$family = WT_Family::getInstance($fam);
			if ($type == 'name') {
				return $family->format_list('span', false, $family->getFullName());
			}
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/30.4375)>0) {
				$age = floor($age/30.4375).'m';
			} else {
				$age = $age.'d';
			}
			$age = get_age_at_event($age, true);
			if ($type == 'age') {
				return $age;
			}
			$husb = $family->getHusband();
			$wife = $family->getWife();
			if (($husb->getAllDeathDates() && $wife->getAllDeathDates()) || !$husb->isDead() || !$wife->isDead()) {
				if ($family->canDisplayDetails()) {
					if ($type == 'list') {
						$top10[] = "\t<li><a href=\"".$family->getHtmlUrl()."\">".$family->getFullName()."</a> (".$age.")"."</li>\n";
					} else {
						$top10[] = "<a href=\"".$family->getHtmlUrl()."\">".$family->getFullName()."</a> (".$age.")";
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
		global $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];} else {$total = 10;}
		if ($age_dir=='DESC') {
			$query1 = ' MIN(wifebirth.d_julianday2-husbbirth.d_julianday1) AS age';
			$query2 = ' wifebirth.d_julianday2 >= husbbirth.d_julianday1 AND'
					 .' husbbirth.d_julianday1 <> 0';
		} else {
			$query1 = ' MIN(husbbirth.d_julianday2-wifebirth.d_julianday1) AS age';
			$query2 = ' wifebirth.d_julianday1 < husbbirth.d_julianday2 AND'
					 .' wifebirth.d_julianday1 <> 0';
		}
		$total=(int)$total;
		$rows=self::_runSQL(''
			.' SELECT'
				.' fam.f_id AS family,'
				.$query1
			.' FROM'
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS wifebirth ON wifebirth.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##dates` AS husbbirth ON husbbirth.d_file = {$this->_ged_id}"
			.' WHERE'
				." fam.f_file = {$this->_ged_id} AND"
				.' husbbirth.d_gid = fam.f_husb AND'
				//." husbbirth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." husbbirth.d_fact = 'BIRT' AND"
				.' wifebirth.d_gid = fam.f_wife AND'
				//." wifebirth.d_fact IN ('BIRT', 'CHR', 'BAPM', '_BRTM') AND"
				." wifebirth.d_fact = 'BIRT' AND"
				.$query2
			.' GROUP BY'
				.' family'
			.' ORDER BY'
				." age DESC LIMIT ".$total
		);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		foreach ($rows as $fam) {
			$family=WT_Family::getInstance($fam['family']);
			if ($fam['age']<0) break;
			$age = $fam['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/30.4375)>0) {
				$age = floor($age/30.4375).'m';
			} else {
				$age = $age.'d';
			}
			$age = get_age_at_event($age, true);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".$family->getHtmlUrl()."\">".$family->getFullName()."</a> (".$age.")"."</li>\n";
				} else {
					$top10[] = "<a href=\"".$family->getHtmlUrl()."\">".$family->getFullName()."</a> (".$age.")";
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
		if ($sex == 'F') {$sex_field = 'WIFE';} else {$sex_field = 'HUSB';}
		if ($age_dir != 'ASC') {$age_dir = 'DESC';}
		$rows=self::_runSQL(''
			.' SELECT'
				.' parentfamily.l_to AS id,'
				.' childbirth.d_julianday2-birth.d_julianday1 AS age'
			.' FROM'
				." `##link` AS parentfamily"
			.' JOIN'
				." `##link` AS childfamily ON childfamily.l_file = {$this->_ged_id}"
			.' JOIN'
				." `##dates` AS birth ON birth.d_file = {$this->_ged_id}"
			.' JOIN'
				." `##dates` AS childbirth ON childbirth.d_file = {$this->_ged_id}"
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
				." age {$age_dir} LIMIT 1"
		);
		if (!isset($rows[0])) {return '';}
		$row=$rows[0];
		if (isset($row['id'])) $person=WT_Person::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($person->canDisplayDetails()) {
					$result=$person->format_list('span', false, $person->getFullName());
				} else {
					$result=WT_I18N::translate('This information is private and cannot be shown.');
				}
				break;
			case 'name':
				$result='<a href="'.$person->getHtmlUrl().'">'.$person->getFullName().'</a>';
				break;
			case 'age':
				$age = $row['age'];
				if ($show_years) {
					if (floor($age/365.25)>0) {
						$age = floor($age/365.25).'y';
					} else if (floor($age/30.4375)>0) {
						$age = floor($age/30.4375).'m';
					} else {
						$age = $age.'d';
					}
					$result = get_age_at_event($age, true);
				} else {
					$result = floor($age/365.25);
				}
				break;
		}
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _statsMarr($simple=true, $first=false, $year1=-1, $year2=-1, $params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` "
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
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS married ON married.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##individuals` AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' married.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				." married.d_fact = 'MARR' AND"
				.' married.d_julianday2 <> 0 AND'
				.$years
				.' (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)'
			.' ORDER BY fams, indi, age ASC';
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM `##dates` "
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
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			$counts=array();
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= self::_centuryName($values['century']).' - '.WT_I18N::number($values['total']).'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Marriages by century')."\" title=\"".WT_I18N::translate('Marriages by century')."\" />";
		}
		return $rows;
	}

	function _statsDiv($simple=true, $first=false, $year1=-1, $year2=-1, $params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql = "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` "
					."WHERE "
						."d_file={$this->_ged_id} AND "
						.'d_year<>0 AND '
						."d_fact = 'DIV' AND "
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
				." `##families` AS fam"
			.' LEFT JOIN'
				." `##dates` AS divorced ON divorced.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##individuals` AS indi ON indi.i_file = {$this->_ged_id}"
			.' WHERE'
				.' divorced.d_gid = fam.f_id AND'
				." fam.f_file = {$this->_ged_id} AND"
				." divorced.d_fact = 'DIV' AND"
				.' divorced.d_julianday2 <> 0 AND'
				.$years
				.' (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)'
			.' ORDER BY fams, indi, age ASC';
		} else {
			$sql = "SELECT d_month, COUNT(*) AS total FROM `##dates` "
				."WHERE "
				."d_file={$this->_ged_id} AND "
				."d_fact = 'DIV'";
				if ($year1>=0 && $year2>=0) {
					$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			$sql .= " GROUP BY d_month";
		}
		$rows=self::_runSQL($sql);
		if (!isset($rows)) {return 0;}
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$centuries = "";
			$counts=array();
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= self::_centuryName($values['century']).' - '.WT_I18N::number($values['total']).'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($centuries,0,-1);
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Divorces by century')."\" title=\"".WT_I18N::translate('Divorces by century')."\" />";
		}
		return $rows;
	}

	//
	// Marriage
	//
	function firstMarriage()      { return $this->_mortalityQuery('full',  'ASC', 'MARR'); }
	function firstMarriageYear()  { return $this->_mortalityQuery('year',  'ASC', 'MARR'); }
	function firstMarriageName()  { return $this->_mortalityQuery('name',  'ASC', 'MARR'); }
	function firstMarriagePlace() { return $this->_mortalityQuery('place', 'ASC', 'MARR'); }

	function lastMarriage()      { return $this->_mortalityQuery('full',  'DESC', 'MARR'); }
	function lastMarriageYear()  { return $this->_mortalityQuery('year',  'DESC', 'MARR'); }
	function lastMarriageName()  { return $this->_mortalityQuery('name',  'DESC', 'MARR'); }
	function lastMarriagePlace() { return $this->_mortalityQuery('place', 'DESC', 'MARR'); }

	function statsMarr($params=null) {return $this->_statsMarr(true, false, -1, -1, $params);}
	//
	// Divorce
	//
	function firstDivorce()      { return $this->_mortalityQuery('full',  'ASC', 'DIV'); }
	function firstDivorceYear()  { return $this->_mortalityQuery('year',  'ASC', 'DIV'); }
	function firstDivorceName()  { return $this->_mortalityQuery('name',  'ASC', 'DIV'); }
	function firstDivorcePlace() { return $this->_mortalityQuery('place', 'ASC', 'DIV'); }

	function lastDivorce()      { return $this->_mortalityQuery('full',  'DESC', 'DIV'); }
	function lastDivorceYear()  { return $this->_mortalityQuery('year',  'DESC', 'DIV'); }
	function lastDivorceName()  { return $this->_mortalityQuery('name',  'DESC', 'DIV'); }
	function lastDivorcePlace() { return $this->_mortalityQuery('place', 'DESC', 'DIV'); }
	
	function statsDiv($params=null) {return $this->_statsDiv(true, false, -1, -1, $params);}

	function _statsMarrAge($simple=true, $sex='M', $year1=-1, $year2=-1, $params=null) {
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = '200x250';}
			$sizes = explode('x', $size);
			$rows=self::_runSQL(
				"SELECT ".
				" ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, ".
				" FLOOR(married.d_year/100+1) AS century, ".
				" 'M' AS sex ".
				"FROM `wt_dates` AS married ".
				"JOIN `wt_families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) ".
				"JOIN `wt_dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) ".
				"WHERE ".
				" '{$sex}' IN ('M', 'BOTH') AND ".
				" married.d_file={$this->_ged_id} AND married.d_type='@#DGREGORIAN@' AND married.d_fact='MARR' AND ".
				" birth.d_type='@#DGREGORIAN@' AND birth.d_fact='BIRT' AND ".
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 ".
				"GROUP BY century, sex ".
				"UNION ALL ".
				"SELECT ".
				" ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, ".
				" FLOOR(married.d_year/100+1) AS century, ".
				" 'F' AS sex ".
				"FROM `wt_dates` AS married ".
				"JOIN `wt_families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) ".
				"JOIN `wt_dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) ".
				"WHERE ".
				" '{$sex}' IN ('F', 'BOTH') AND ".
				" married.d_file={$this->_ged_id} AND married.d_type='@#DGREGORIAN@' AND married.d_fact='MARR' AND ".
				" birth.d_type='@#DGREGORIAN@' AND birth.d_fact='BIRT' AND ".
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 ".
				" GROUP BY century, sex ORDER BY century"
			);
			if (empty($rows)) return'';
			$max = 0;
			foreach ($rows as $values) {
				if ($max<$values['age']) $max = $values['age'];
			}
			$chxl = '0:|';
			$chmm = '';
			$chmf = '';
			$i = 0;
			$countsm = '';
			$countsf = '';
			$countsa = '';
			foreach ($rows as $values) {
				$out[$values['century']][$values['sex']]=$values['age'];
			}
			foreach ($out as $century=>$values) {
				if ($sizes[0]<1000) $sizes[0] += 50;
				$chxl .= self::_centuryName($century).'|';
				$average = 0;
				if (isset($values['F'])) {
					if ($max<=50) $value = $values['F']*2;
					else $value = $values['F'];
					$countsf .= $value.',';
					$average = $value;
					$chmf .= 't'.$values['F'].',000000,1,'.$i.',11,1|';
				} else {
					$countsf .= '0,';
					$chmf .= 't0,000000,1,'.$i.',11,1|';
				}
				if (isset($values['M'])) {
					if ($max<=50) $value = $values['M']*2;
					else $value = $values['M'];
					$countsm .= $value.',';
					if ($average==0) $countsa .= $value.',';
					else $countsa .= (($value+$average)/2).',';
					$chmm .= 't'.$values['M'].',000000,0,'.$i.',11,1|';
				} else {
					$countsm .= '0,';
					if ($average==0) $countsa .= '0,';
					else $countsa .= $value.',';
					$chmm .= 't0,000000,0,'.$i.',11,1|';
				}
				$i++;
			}
			$countsm = substr($countsm,0,-1);
			$countsf = substr($countsf,0,-1);
			$countsa = substr($countsa,0,-1);
			$chmf = substr($chmf,0,-1);
			$chd = 't2:'.$countsm.'|'.$countsf.'|'.$countsa;
			if ($max<=50) $chxl .= '1:||'.WT_I18N::translate('century').'|2:|0|10|20|30|40|50|3:||'.WT_I18N::translate('Age').'|';
			else $chxl .= '1:||'.WT_I18N::translate('century').'|2:|0|10|20|30|40|50|60|70|80|90|100|3:||'.WT_I18N::translate('Age').'|';
			if (count($rows)>4 || utf8_strlen(WT_I18N::translate('Average age in century of marriage'))<30) {
				$chtt = WT_I18N::translate('Average age in century of marriage');
			} else {
				$offset = 0;
				$counter = array();
				while ($offset = strpos(WT_I18N::translate('Average age in century of marriage'), ' ', $offset + 1)) {
					$counter[] = $offset;
				}
				$half = floor(count($counter)/2);
				$chtt = substr_replace(WT_I18N::translate('Average age in century of marriage'), '|', $counter[$half], 1);
			}
			return "<img src=\""."https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|{$chmm}{$chmf}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=".rawurlencode($chtt)."&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=".rawurlencode($chxl)."&amp;chdl=".rawurlencode(WT_I18N::translate('Males')."|".WT_I18N::translate('Females')."|".WT_I18N::translate('Average age'))."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Average age in century of marriage')."\" title=\"".WT_I18N::translate('Average age in century of marriage')."\" />";
		} else {
			if ($year1>=0 && $year2>=0) {
				$years=" married.d_year BETWEEN {$year1} AND {$year2} AND ";
			} else {
				$years='';
			}
			$rows=self::_runSQL(
				"SELECT ".
				" fam.f_id, ".
				" birth.d_gid, ".
				" married.d_julianday2-birth.d_julianday1 AS age ".
				"FROM `wt_dates` AS married ".
				"JOIN `wt_families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) ".
				"JOIN `wt_dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) ".
				"WHERE ".
				" '{$sex}' IN ('M', 'BOTH') AND {$years} ".
				" married.d_file={$this->_ged_id} AND married.d_type='@#DGREGORIAN@' AND married.d_fact='MARR' AND ".
				" birth.d_type='@#DGREGORIAN@' AND birth.d_fact='BIRT' AND ".
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 ".
				"UNION ALL ".
				"SELECT ".
				" ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, ".
				" FLOOR(married.d_year/100+1) AS century, ".
				" 'F' AS sex ".
				"FROM `wt_dates` AS married ".
				"JOIN `wt_families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) ".
				"JOIN `wt_dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) ".
				"WHERE ".
				" '{$sex}' IN ('F', 'BOTH') AND {$years} ".
				" married.d_file={$this->_ged_id} AND married.d_type='@#DGREGORIAN@' AND married.d_fact='MARR' AND ".
				" birth.d_type='@#DGREGORIAN@' AND birth.d_fact='BIRT' AND ".
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 "
			);
			return $rows;
		}
	}

	//
	// Female only
	//
	function youngestMarriageFemale()                     { return $this->_marriageQuery('full', 'ASC', 'F'); }
	function youngestMarriageFemaleName()                 { return $this->_marriageQuery('name', 'ASC', 'F'); }
	function youngestMarriageFemaleAge($show_years=false) { return $this->_marriageQuery('age',  'ASC', 'F', $show_years); }

	function oldestMarriageFemale()                     { return $this->_marriageQuery('full', 'DESC', 'F'); }
	function oldestMarriageFemaleName()                 { return $this->_marriageQuery('name', 'DESC', 'F'); }
	function oldestMarriageFemaleAge($show_years=false) { return $this->_marriageQuery('age',  'DESC', 'F', $show_years); }

	//
	// Male only
	//
	function youngestMarriageMale()                     { return $this->_marriageQuery('full', 'ASC', 'M'); }
	function youngestMarriageMaleName()                 { return $this->_marriageQuery('name', 'ASC', 'M'); }
	function youngestMarriageMaleAge($show_years=false) { return $this->_marriageQuery('age',  'ASC', 'M', $show_years); }

	function oldestMarriageMale()                     { return $this->_marriageQuery('full', 'DESC', 'M'); }
	function oldestMarriageMaleName()                 { return $this->_marriageQuery('name', 'DESC', 'M'); }
	function oldestMarriageMaleAge($show_years=false) { return $this->_marriageQuery('age',  'DESC', 'M', $show_years); }
	
	function statsMarrAge($params=null) { return $this->_statsMarrAge(true, 'BOTH', -1, -1, $params); }

	function ageBetweenSpousesMF    ($params=null) { return $this->_ageBetweenSpousesQuery($type='nolist', $age_dir='DESC', $params=null); }
	function ageBetweenSpousesMFList($params=null) { return $this->_ageBetweenSpousesQuery($type='list',   $age_dir='DESC', $params=null); }
	function ageBetweenSpousesFM    ($params=null) { return $this->_ageBetweenSpousesQuery($type='nolist', $age_dir='ASC',  $params=null); }
	function ageBetweenSpousesFMList($params=null) { return $this->_ageBetweenSpousesQuery($type='list',   $age_dir='ASC',  $params=null); }

	function topAgeOfMarriageFamily()                   { return $this->_ageOfMarriageQuery('name',   'DESC', array('1')); }
	function topAgeOfMarriage()                         { return $this->_ageOfMarriageQuery('age',    'DESC', array('1')); }
	function topAgeOfMarriageFamilies($params=null)     { return $this->_ageOfMarriageQuery('nolist', 'DESC', $params);    }
	function topAgeOfMarriageFamiliesList($params=null) { return $this->_ageOfMarriageQuery('list',   'DESC', $params);    }

	function minAgeOfMarriageFamily()                   { return $this->_ageOfMarriageQuery('name',   'ASC', array('1')); }
	function minAgeOfMarriage()                         { return $this->_ageOfMarriageQuery('age',    'ASC', array('1')); }
	function minAgeOfMarriageFamilies    ($params=null) { return $this->_ageOfMarriageQuery('nolist', 'ASC', $params); }
	function minAgeOfMarriageFamiliesList($params=null) { return $this->_ageOfMarriageQuery('list',   'ASC', $params); }

	//
	// Mother only
	//
	function youngestMother()                     { return $this->_parentsQuery('full', 'ASC',  'F'); }
	function youngestMotherName()                 { return $this->_parentsQuery('name', 'ASC',  'F'); }
	function youngestMotherAge($show_years=false) { return $this->_parentsQuery('age',  'ASC',  'F', $show_years); }
	function oldestMother()                       { return $this->_parentsQuery('full', 'DESC', 'F'); }
	function oldestMotherName()                   { return $this->_parentsQuery('name', 'DESC', 'F'); }
	function oldestMotherAge($show_years=false)   { return $this->_parentsQuery('age',  'DESC', 'F', $show_years); }

	//
	// Father only
	//
	function youngestFather()                     { return $this->_parentsQuery('full', 'ASC',  'M'); }
	function youngestFatherName()                 { return $this->_parentsQuery('name', 'ASC',  'M'); }
	function youngestFatherAge($show_years=false) { return $this->_parentsQuery('age',  'ASC',  'M', $show_years); }
	function oldestFather()                       { return $this->_parentsQuery('full', 'DESC', 'M'); }
	function oldestFatherName()                   { return $this->_parentsQuery('name', 'DESC', 'M'); }
	function oldestFatherAge($show_years=false)   { return $this->_parentsQuery('age',  'DESC', 'M', $show_years); }

	function totalMarriedMales() {
		$rows = WT_DB::prepare("SELECT f_gedcom AS ged, f_husb AS husb FROM `##families` WHERE f_file=?")
				->execute(array($this->_ged_id))
				->fetchAll();
		$husb = array();
		foreach ($rows as $row) {
			$factrec = trim(get_sub_record(1, "1 MARR", $row->ged, 1));
			if (!empty($factrec)) {
				$husb[] = $row->husb."<br />";
			}
		}
		return WT_I18N::number(count(array_unique($husb)));
	}

	function totalMarriedFemales() {
		$rows = WT_DB::prepare("SELECT f_gedcom AS ged, f_wife AS wife FROM `##families` WHERE f_file=?")
				->execute(array($this->_ged_id))
				->fetchAll();
		$wife = array();
		foreach ($rows as $row) {
			$factrec = trim(get_sub_record(1, "1 MARR", $row->ged, 1));
			if (!empty($factrec)) {
				$wife[] = $row->wife."<br />";
			}
		}
		return WT_I18N::number(count(array_unique($wife)));
	}

///////////////////////////////////////////////////////////////////////////////
// Family Size                                                               //
///////////////////////////////////////////////////////////////////////////////

	function _familyQuery($type='full') {
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." `##families`"
			.' WHERE'
				." f_file={$this->_ged_id}"
				.' AND f_numchil = ('
				.' SELECT max( f_numchil )'
				." FROM `##families`" 
				." WHERE f_file ={$this->_ged_id})" 
				.' LIMIT 1'
		);
		if (!isset($rows[0])) {return '';}
		$row = $rows[0];
		$family=WT_Family::getInstance($row['id']);
		switch($type) {
			default:
			case 'full':
				if ($family->canDisplayDetails()) {
					$result=$family->format_list('span', false, $family->getFullName());
				} else {
					$result = WT_I18N::translate('This information is private and cannot be shown.');
				}
				break;
			case 'size':
				$result=WT_I18N::number($row['tot']);
				break;
			case 'name':
				$result="<a href=\"".$family->getHtmlUrl()."\">".$family->getFullName().'</a>';
				break;
		}
		// Statistics are used by RSS feeds, etc., so need absolute URLs.
		return str_replace('<a href="', '<a href="'.$this->_server_url, $result);
	}

	function _topTenFamilyQuery($type='list', $params=null) {
		global $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];} else {$total = 10;}
		$total=(int)$total;
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." `##families`"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC LIMIT '.$total
		);
		if (!isset($rows[0])) {return '';}
		if (count($rows) < $total) {$total = count($rows);}
		$top10 = array();
		for ($c = 0; $c < $total; $c++) {
			$family=WT_Family::getInstance($rows[$c]['id']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[]=
						'<li><a href="'.$family->getHtmlUrl().'">'.$family->getFullName().'</a> - '.
						WT_I18N::plural('%s child', '%s children', $rows[$c]['tot'], WT_I18N::number($rows[$c]['tot']));
				} else {
					$top10[]=
						'<a href="'.$family->getHtmlUrl().'">'.$family->getFullName().'</a> - '.
						WT_I18N::plural('%s child', '%s children', $rows[$c]['tot'], WT_I18N::number($rows[$c]['tot']));
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
		global $TEXT_DIRECTION;
		if ($params === null) {$params = array();}
		if (isset($params[0])) {$total = $params[0];} else {$total = 10;}
		if (isset($params[1])) {$one = $params[1];} else {$one = false;} // each family only once if true
		$total=(int)$total;
		$rows=self::_runSQL(''
			.' SELECT DISTINCT'
				.' link1.l_from AS family,'
				.' link1.l_to AS ch1,'
				.' link2.l_to AS ch2,'
				.' child1.d_julianday2-child2.d_julianday2 AS age'
			.' FROM'
				." `##link` AS link1"
			.' LEFT JOIN'
				." `##dates` AS child1 ON child1.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##dates` AS child2 ON child2.d_file = {$this->_ged_id}"
			.' LEFT JOIN'
				." `##link` AS link2 ON link2.l_file = {$this->_ged_id}"
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
				." age DESC LIMIT ".$total
		);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		if ($one) $dist = array();
		foreach ($rows as $fam) {
			$family = WT_Family::getInstance($fam['family']);
			$child1 = WT_Person::getInstance($fam['ch1']);
			$child2 = WT_Person::getInstance($fam['ch2']);
			if ($type == 'name') {
				if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = '<a href="'.$child2->getHtmlUrl().'">'.$child2->getFullName().'</a> ';
					$return .= WT_I18N::translate('and').' ';
					$return .= '<a href="'.$child1->getHtmlUrl().'">'.$child1->getFullName().'</a>';
					$return .= ' <a href="'.$family->getHtmlUrl().'">['.WT_I18N::translate('View Family').']</a>';
				} else {
					$return = WT_I18N::translate('This information is private and cannot be shown.');
				}
				return $return;
			}
			$age = $fam['age'];
			if (floor($age/365.25)>0) {
				$age = floor($age/365.25).'y';
			} else if (floor($age/30.4375)>0) {
				$age = floor($age/30.4375).'m';
			} else {
				$age = $age.'d';
			}
			$age = get_age_at_event($age, true);
			if ($type == 'age') {
				return $age;
			}
			if ($type == 'list') {
				if ($one && !in_array($fam['family'], $dist)) {
					if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
						$return = "\t<li>";
						$return .= "<a href=\"".$child2->getHtmlUrl()."\">".$child2->getFullName()."</a> ";
						$return .= WT_I18N::translate('and')." ";
						$return .= "<a href=\"".$child1->getHtmlUrl()."\">".$child1->getFullName()."</a>";
						$return .= " (".$age.")";
						$return .= " <a href=\"".$family->getHtmlUrl()."\">[".WT_I18N::translate('View Family')."]</a>";
						$return .= "\t</li>\n";
						$top10[] = $return;
						$dist[] = $fam['family'];
					}
				} else if (!$one && $child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = "\t<li>";
					$return .= "<a href=\"".$child2->getHtmlUrl()."\">".$child2->getFullName()."</a> ";
					$return .= WT_I18N::translate('and')." ";
					$return .= "<a href=\"".$child1->getHtmlUrl()."\">".$child1->getFullName()."</a>";
					$return .= " (".$age.")";
					$return .= " <a href=\"".$family->getHtmlUrl()."\">[".WT_I18N::translate('View Family')."]</a>";
					$return .= "\t</li>\n";
					$top10[] = $return;
				}
			} else {
				if ($child1->canDisplayDetails() && $child2->canDisplayDetails()) {
					$return = $child2->format_list('span', false, $child2->getFullName());
					$return .= "<br />".WT_I18N::translate('and')."<br />";
					$return .= $child1->format_list('span', false, $child1->getFullName());
					//$return .= "<br />(".$age.")";
					$return .= "<br /><a href=\"".$family->getHtmlUrl()."\">[".WT_I18N::translate('View Family')."]</a>\n";
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
	
	function _monthFirstChildQuery($simple=true, $sex=false, $year1=-1, $year2=-1, $params=null) {
		global $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2;
		if ($params === null) {$params = array();}
		if (isset($params[0])) {$total = $params[0];} else {$total = 10;}
		if (isset($params[1])) {$one = $params[1];} else {$one = false;} // each family only once if true
		$total=(int)$total;
		if ($year1>=0 && $year2>=0) {
			$sql_years = " AND (d_year BETWEEN '{$year1}' AND '{$year2}')";
		} else {
			$sql_years = '';
		}
		if ($sex) {
			$sql_sex1 = ', i_sex';
			$sql_sex2 = " JOIN `##individuals` AS child ON child1.d_file = i_file AND child1.d_gid = child.i_id ";
		} else {
			$sql_sex1 = '';
			$sql_sex2 = '';
		}
		$sql = "SELECT d_month{$sql_sex1}, COUNT(*) AS total"
			.' FROM ('
				." SELECT family{$sql_sex1}, MIN(date) AS d_date, d_month"
					.' FROM ('
						.' SELECT'
							.' link1.l_from AS family,'
							.' link1.l_to AS child,'
							.' child1.d_julianday2 as date,'
							.' child1.d_month as d_month'
							.$sql_sex1
						.' FROM'
							." `##link` AS link1"
						.' LEFT JOIN'
							." `##dates` AS child1 ON child1.d_file = {$this->_ged_id}"
						.$sql_sex2
						.' WHERE'
							." link1.l_file = {$this->_ged_id} AND"
							." link1.l_type = 'CHIL' AND"
							.' child1.d_gid = link1.l_to AND'
							." child1.d_fact = 'BIRT' AND"
							." d_type='@#DGREGORIAN@' AND"
							.' child1.d_month <> ""'
							.$sql_years
						.' ORDER BY'
							.' date'
					.') AS children'
				.' GROUP BY'
					.' family'
			.') AS first_child'
			.' GROUP BY'
				.' d_month'
		;
		if ($sex) $sql .= ', i_sex';
		$rows=self::_runSQL($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X.'x'.$WT_STATS_S_CHART_Y;}
			if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
			if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot==0) return '';
			$text = '';
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				switch ($values['d_month']) {
				default:
				case 'JAN':
					$values['d_month'] = 1;
					break;
				case 'FEB':
					$values['d_month'] = 2;
					break;
				case 'MAR':
					$values['d_month'] = 3;
					break;
				case 'APR':
					$values['d_month'] = 4;
					break;
				case 'MAY':
					$values['d_month'] = 5;
					break;
				case 'JUN':
					$values['d_month'] = 6;
					break;
				case 'JUL':
					$values['d_month'] = 7;
					break;
				case 'AUG':
					$values['d_month'] = 8;
					break;
				case 'SEP':
					$values['d_month'] = 9;
					break;
				case 'OCT':
					$values['d_month'] = 10;
					break;
				case 'NOV':
					$values['d_month'] = 11;
					break;
				case 'DEC':
					$values['d_month'] = 12;
					break;
				}
				$text .= WT_I18N::translate(ucfirst(strtolower(($values['d_month'])))).' - '.$values['total'].'|';
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chl = substr($text,0,-1);
			return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:'.$chd.'&amp;chs='.$size.'&amp;chco='.$color_from.','.$color_to.'&amp;chf=bg,s,ffffff00&amp;chl='.$chl.'" width="'.$sizes[0].'" height="'.$sizes[1].'" alt="'.WT_I18N::translate('Month of birth of first child in a relation').'" title="'.WT_I18N::translate('Month of birth of first child in a relation').'" />';
		}
		if (!isset($rows)) return 0;
		return $rows;
	}

	function largestFamily()     { return $this->_familyQuery('full'); }
	function largestFamilySize() { return $this->_familyQuery('size'); }
	function largestFamilyName() { return $this->_familyQuery('name'); }

	function topTenLargestFamily    ($params=null) { return $this->_topTenFamilyQuery('nolist', $params); }
	function topTenLargestFamilyList($params=null) { return $this->_topTenFamilyQuery('list',   $params); }

	function chartLargestFamilies($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_L_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_L_CHART_X.'x'.$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$total = strtolower($params[3]);} else {$total = 10;}
		$sizes = explode('x', $size);
		$total=(int)$total;
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_numchil AS tot,'
				.' f_id AS id'
			.' FROM'
				." `##families`"
			.' WHERE'
				." f_file={$this->_ged_id}"
			.' ORDER BY'
				.' tot DESC LIMIT '.$total
		);
		if (!isset($rows[0])) {return '';}
		$tot = 0;
		foreach ($rows as $row) {$tot += $row['tot'];}
		$chd = '';
		$chl = array();
		foreach ($rows as $row) {
			$family=WT_Family::getInstance($row['id']);
			if ($family->canDisplayDetails()) {
				if ($tot==0) {
					$per = 0;
				} else {
					$per = round(100 * $row['tot'] / $tot, 0);
				}
				$chd .= self::_array_to_extended_encoding(array($per));
				$chl[] = strip_tags(unhtmlentities($family->getFullName())).' - '.WT_I18N::number($row['tot']);
			}
		}
		$chl = rawurlencode(join('|', $chl));

		return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Largest families')."\" title=\"".WT_I18N::translate('Largest families')."\" />";
	}

	function totalChildren() {
		$rows=self::_runSQL("SELECT SUM(f_numchil) AS tot FROM `##families` WHERE f_file={$this->_ged_id}");
		$row=$rows[0];
		return WT_I18N::number($row['tot']);
	}

	function averageChildren() {
		$rows=self::_runSQL("SELECT AVG(f_numchil) AS tot FROM `##families` WHERE f_file={$this->_ged_id}");
		$row=$rows[0];
		return WT_I18N::number($row['tot'], 2);
	}

	function _statsChildren($simple=true, $sex='BOTH', $year1=-1, $year2=-1, $params=null) {
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = '220x200';}
			$sizes = explode('x', $size);
			$max = 0;
			$rows=self::_runSQL(''
				.' SELECT'
					.' ROUND(AVG(f_numchil),2) AS num,'
					.' FLOOR(married.d_year/100+1) AS century'
				.' FROM'
					." `##families` AS fam"
				.' LEFT JOIN'
					." `##dates` AS married ON married.d_file = {$this->_ged_id}"
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
			$counts=array();
			foreach ($rows as $values) {
				if ($sizes[0]<980) $sizes[0] += 38;
				$chxl .= self::_centuryName($values['century'])."|";
				if ($max<=5) $counts[] = round($values['num']*819.2-1, 1);
				else $counts[] = round($values['num']*409.6, 1);
				$chm .= 't'.$values['num'].',000000,0,'.$i.',11,1|';
				$i++;
			}
			$chd = self::_array_to_extended_encoding($counts);
			$chm = substr($chm,0,-1);
			if ($max<=5) $chxl .= "1:||".WT_I18N::translate('century')."|2:|0|1|2|3|4|5|3:||".WT_I18N::translate('Number of children')."|";
			else $chxl .= "1:||".WT_I18N::translate('century')."|2:|0|1|2|3|4|5|6|7|8|9|10|3:||".WT_I18N::translate('Number of children')."|";
			return "<img src=\"https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0,3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl=".rawurlencode($chxl)."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Average number of children per family')."\" title=\"".WT_I18N::translate('Average number of children per family')."\" />";
		} else {
			if ($sex=='M') {
				$sql = "SELECT num, COUNT(*) AS total FROM "
						."(SELECT count(i_sex) AS num FROM `##link` "
							."LEFT OUTER JOIN `##individuals` "
							."ON l_from=i_id AND l_file=i_file AND i_sex='M' AND l_type='FAMC' "
							."JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->_ged_id} GROUP BY l_to"
						.") boys"
						." GROUP BY num ORDER BY num ASC";
			}
			else if ($sex=='F') {
				$sql = "SELECT num, COUNT(*) AS total FROM "
						."(SELECT count(i_sex) AS num FROM `##link` "
							."LEFT OUTER JOIN `##individuals` "
							."ON l_from=i_id AND l_file=i_file AND i_sex='F' AND l_type='FAMC' "
							."JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->_ged_id} GROUP BY l_to"
						.") girls"
						." GROUP BY num ORDER BY num ASC";
			}
			else {
				$sql = "SELECT f_numchil, COUNT(*) AS total FROM `##families` ";
				if ($year1>=0 && $year2>=0) {
					$sql .= "AS fam LEFT JOIN `##dates` AS married ON married.d_file = {$this->_ged_id}"
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

	function statsChildren($params=null) {return $this->_statsChildren($simple=true, $sex='BOTH', $year1=-1, $year2=-1, $params=null);}

	function topAgeBetweenSiblingsName    ($params=null) { return $this->_ageBetweenSiblingsQuery($type='name',   $params=null); }
	function topAgeBetweenSiblings        ($params=null) { return $this->_ageBetweenSiblingsQuery($type='age',    $params=null); }
	function topAgeBetweenSiblingsFullName($params=null) { return $this->_ageBetweenSiblingsQuery($type='nolist', $params=null); }
	function topAgeBetweenSiblingsList    ($params=null) { return $this->_ageBetweenSiblingsQuery($type='list',   $params=null); }

	function noChildrenFamilies() {
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*) AS tot'
			.' FROM'
				." `##families` AS fam"
			.' WHERE'
				.' f_numchil = 0 AND'
				." fam.f_file = {$this->_ged_id}");
		$row=$rows[0];
		return WT_I18N::number($row['tot']);
	}


	function noChildrenFamiliesList($params = null) {
		global $TEXT_DIRECTION;
		if (isset($params[0]) && $params[0] != '') {$type = strtolower($params[0]);} else {$type = 'list';}
		$rows=self::_runSQL(''
			.' SELECT'
				.' f_id AS family'
			.' FROM'
				." `##families` AS fam"
			.' WHERE'
				.' f_numchil = 0 AND'
				." fam.f_file = {$this->_ged_id}");
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		foreach ($rows as $row) {
			$family=WT_Family::getInstance($row['family']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[] = "\t<li><a href=\"".$family->getHtmlUrl()."\">".$family->getFullName()."</a></li>\n";
				} else {
					$top10[] = "<a href=\"".$family->getHtmlUrl()."\">".$family->getFullName()."</a>";
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

	function chartNoChildrenFamilies($params=null) {
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = '220x200';}
		if (isset($params[1]) && $params[1] != '') {$year1 = $params[1];} else {$year1 = -1;}
		if (isset($params[2]) && $params[2] != '') {$year2 = $params[2];} else {$year2 = -1;}
		$sizes = explode('x', $size);
		if ($year1>=0 && $year2>=0) {
			$years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
		} else {
			$years = "";
		}
		$max = 0;
		$tot = 0;
		$rows=self::_runSQL(
			"SELECT".
			" COUNT(*) AS count,".
			" FLOOR(married.d_year/100+1) AS century".
			" FROM".
			" `##families` AS fam".
			" JOIN".
			" `##dates` AS married ON (married.d_file = fam.f_file AND married.d_gid = fam.f_id)".
			" WHERE".
			" f_numchil = 0 AND".
			" fam.f_file = {$this->_ged_id} AND".
			$years.
			" married.d_fact = 'MARR' AND".
			" married.d_type = '@#DGREGORIAN@'".
			" GROUP BY century ORDER BY century"
		);
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
		foreach ($rows as $values) {
			if ($sizes[0]<980) $sizes[0] += 38;
			$chxl .= self::_centuryName($values['century'])."|";
			$counts[] = round(4095*$values['count']/($max+1));
			$chm .= 't'.$values['count'].',000000,0,'.$i.',11,1|';
			$i++;
		}
		$counts[] = round(4095*$unknown/($max+1));
		$chd = self::_array_to_extended_encoding($counts);
		$chm .= 't'.$unknown.',000000,0,'.$i.',11,1';
		$chxl .= WT_I18N::translate_c('unknown century', 'Unknown')."|1:||".WT_I18N::translate('century')."|2:|0|";
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
		$chxl .= "3:||".WT_I18N::translate('Total families')."|";
		return "<img src=\"https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0:".($i-1).",3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF,ffffff00&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl=".rawurlencode($chxl)."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".WT_I18N::translate('Number of families without children')."\" title=\"".WT_I18N::translate('Number of families without children')."\" />";
	}

	function _topTenGrandFamilyQuery($type='list', $params=null) {
		global $TEXT_DIRECTION;
		if ($params !== null && isset($params[0])) {$total = $params[0];} else {$total = 10;}
		$total=(int)$total;
		$rows=self::_runSQL(''
			.' SELECT'
				.' COUNT(*) AS tot,'
				.' f_id AS id'
			.' FROM'
				." `##families`"
			.' JOIN'
				." `##link` AS children ON children.l_file = {$this->_ged_id}"
			.' JOIN'
				." `##link` AS mchildren ON mchildren.l_file = {$this->_ged_id}"
			.' JOIN'
				." `##link` AS gchildren ON gchildren.l_file = {$this->_ged_id}"
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
				.' tot DESC LIMIT '.$total
		);
		if (!isset($rows[0])) {return '';}
		$top10 = array();
		foreach ($rows as $row) {
			$family=WT_Family::getInstance($row['id']);
			if ($family->canDisplayDetails()) {
				if ($type == 'list') {
					$top10[]=
						'<li><a href="'.$family->getHtmlUrl().'">'.$family->getFullName().'</a> - '.
						WT_I18N::plural('%s grandchild', '%s grandchildren', $row['tot'], WT_I18N::number($row['tot']));
				} else {
					$top10[]=
						'<a href="'.$family->getHtmlUrl().'">'.$family->getFullName().'</a> - '.
						WT_I18N::plural('%s grandchild', '%s grandchildren', $row['tot'], WT_I18N::number($row['tot']));
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
		global $SURNAME_LIST_STYLE, $GEDCOM;

		$ged_id=get_id_from_gedcom($GEDCOM);
		if (is_array($params) && isset($params[0]) && $params[0] != '') {$threshold = strtolower($params[0]);} else {$threshold = get_gedcom_setting($ged_id, 'COMMON_NAMES_THRESHOLD');}
		if (is_array($params) && isset($params[1]) && $params[1] != '' && $params[1] >= 0) {$maxtoshow = strtolower($params[1]);} else {$maxtoshow = false;}
		if (is_array($params) && isset($params[2]) && $params[2] != '') {$sorting = strtolower($params[2]);} else {$sorting = 'alpha';}
		$surname_list = get_common_surnames($threshold);
		if (count($surname_list) == 0) return '';
		uasort($surname_list, array('WT_Stats', '_name_total_rsort'));
		if ($maxtoshow>0) $surname_list = array_slice($surname_list, 0, $maxtoshow);

		switch($sorting) {
			default:
			case 'alpha':
				uksort($surname_list, 'utf8_strcasecmp');
				break;
			case 'count':
				uasort($surname_list, array('WT_Stats', '_name_total_sort'));
				break;
			case 'rcount':
				uasort($surname_list, array('WT_Stats', '_name_total_rsort'));
				break;
		}

		// Note that we count/display SPFX SURN, but sort/group under just SURN
		$surnames=array();
		foreach (array_keys($surname_list) as $surname) {
			$surnames=array_merge($surnames, WT_Query_Name::surnames($surname, '', false, false, WT_GED_ID));
		}
		return format_surname_list($surnames, ($type=='list' ? 1 : 2), $show_tot, 'indilist.php');
	}

	function getCommonSurname() {
		$surnames=array_keys(get_top_surnames($this->_ged_id, 1, 1));
		return array_shift($surnames);
	}

	static function commonSurnames          ($params=array('','','alpha' )) { return self::_commonSurnamesQuery('nolist', false, $params); }
	static function commonSurnamesTotals    ($params=array('','','rcount')) { return self::_commonSurnamesQuery('nolist', true,  $params); }
	static function commonSurnamesList      ($params=array('','','alpha' )) { return self::_commonSurnamesQuery('list',   false, $params); }
	static function commonSurnamesListTotals($params=array('','','rcount')) { return self::_commonSurnamesQuery('list',   true,  $params); }

	function chartCommonSurnames($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$threshold = strtolower($params[3]);} else {$threshold = get_gedcom_setting($this->_ged_id, 'COMMON_NAMES_THRESHOLD');}
		if (isset($params[4]) && $params[4] != '') {$maxtoshow = strtolower($params[4]);} else {$maxtoshow = 7;}
		$sizes = explode('x', $size);
		$tot_indi = $this->_totalIndividuals();
		$surnames = get_common_surnames($threshold);
		if (count($surnames) <= 0) {return '';}
		$SURNAME_TRADITION=get_gedcom_setting(WT_GED_ID, 'SURNAME_TRADITION');
		uasort($surnames, array('WT_Stats', '_name_total_rsort'));
		$surnames = array_slice($surnames, 0, $maxtoshow);
		$all_surnames = array();
		foreach (array_keys($surnames) as $n=>$surname) {
			if ($n>=$maxtoshow) {
				break;
			}
			$all_surnames = array_merge($all_surnames, WT_Query_Name::surnames(utf8_strtoupper($surname), '', false, false, WT_GED_ID));
		}
		$tot = 0;
		$per = 0;
		foreach ($surnames as $indexval=>$surname) {$tot += $surname['match'];}
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
			switch ($SURNAME_TRADITION) {
			case 'polish':
				// most common surname should be in male variant (Kowalski, not Kowalska)
				$top_name=preg_replace(array('/ska$/', '/cka$/', '/dzka$/', '/żka$/'), array('ski', 'cki', 'dzki', 'żki'), $top_name);
			}
			$per = round(100 * $count_per / $tot_indi, 0);
			$chd .= self::_array_to_extended_encoding($per);
			//ToDo: RTL names are often printed LTR when also LTR names are present
			$chl[] = $top_name.' - '.WT_I18N::number($count_per);

		}
		$per = round(100 * ($tot_indi-$tot) / $tot_indi, 0);
		$chd .= self::_array_to_extended_encoding($per);
		$chl[] = WT_I18N::translate('Other').' - '.WT_I18N::number($tot_indi-$tot);

		$chart_title=implode(WT_I18N::$list_separator, $chl);
		$chl=implode('|', $chl);
		return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:'.$chd.'&amp;chs='.$size.'&amp;chco='.$color_from.','.$color_to.'&amp;chf=bg,s,ffffff00&amp;chl='.rawurlencode($chl).'" width="'.$sizes[0].'" height="'.$sizes[1].'" alt="'.$chart_title.'" title="'.$chart_title.'" />';
	}


///////////////////////////////////////////////////////////////////////////////
// Given Names                                                               //
///////////////////////////////////////////////////////////////////////////////

	/*
	* Most Common Given Names Block
	* Original block created by kiwi
	*/
	static function _commonGivenQuery($sex='B', $type='list', $show_tot=false, $params=null) {
		global $TEXT_DIRECTION, $GEDCOM;
		static $sort_types = array('count'=>'asort', 'rcount'=>'arsort', 'alpha'=>'ksort', 'ralpha'=>'krsort');
		static $sort_flags = array('count'=>SORT_NUMERIC, 'rcount'=>SORT_NUMERIC, 'alpha'=>SORT_STRING, 'ralpha'=>SORT_STRING);

		if (is_array($params) && isset($params[0]) && $params[0] != '' && $params[0] >= 0) {$threshold = strtolower($params[0]);} else {$threshold = 1;}
		if (is_array($params) && isset($params[1]) && $params[1] != '' && $params[1] >= 0) {$maxtoshow = strtolower($params[1]);} else {$maxtoshow = 10;}
		if (is_array($params) && isset($params[2]) && $params[2] != '' && isset($sort_types[strtolower($params[2])])) {$sorting = strtolower($params[2]);} else {$sorting = 'rcount';}

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

		$rows=WT_DB::prepare("SELECT n_givn, COUNT(*) AS num FROM `##name` JOIN `##individuals` ON (n_id=i_id AND n_file=i_file) WHERE n_file={$ged_id} AND n_type<>'_MARNM' AND n_givn NOT IN ('@P.N.', '') AND LENGTH(n_givn)>1 AND {$sex_sql} GROUP BY n_id, n_givn")
			->fetchAll();
		$nameList=array();
		foreach ($rows as $row) {
			// Split "John Thomas" into "John" and "Thomas" and count against both totals
			foreach (explode(' ', $row->n_givn) as $given) {
				if (utf8_strlen($given)>1) {
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
			if ($maxtoshow !== -1) {if ($maxtoshow-- <= 0) {break;}}
			if ($total < $threshold) {break;}
			if ($show_tot) {
				$tot = '&nbsp;('.WT_I18N::number($total).')';
			} else {
				$tot = '';
			}
			switch ($type) {
			case 'table':
				$common[] = '<tr><td>'.$given.'</td><td>'.WT_I18N::number($total).'</td><td>'.$total.'</td></tr>';
				break;
			case 'list':
				$common[] = '<li><span dir="auto">'.$given.'</span>'.$tot.'</li>';
				break;
			case 'nolist':
				$common[] = '<span dir="auto">'.$given.'</span>'.$tot;
				break;
			}
		}
		if ($common) {
			switch ($type) {
			case 'table':
			global $controller;
				$table_id = 'ID'.floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
				$controller
				->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
				->addInlineJavaScript('
					jQuery("#'.$table_id.'").dataTable({
						"sDom": \'t\',
						"bAutoWidth":false,
						"bPaginate": false,
						"bLengthChange": false,
						"bFilter": false,
						"bInfo": false,
						"bJQueryUI": true,
						"aaSorting": [[1,"desc"]],
						"aoColumns": [
							/* 0-name */ {},
							/* 1-count */ { sClass:"center", iDataSort:2},
							/* 2-COUNT */ { bVisible:false}
						]
					});
					jQuery("#'.$table_id.'").css("visibility", "visible");
				');
				$lookup=array('M'=>WT_I18N::translate('Male'), 'F'=>WT_I18N::translate('Female'), 'U'=>WT_I18N::translate_c('unknown gender', 'Unknown'), 'B'=>WT_I18N::translate('All'));
				return '<table id="'.$table_id.'" class="givn-list"><thead><tr><th class="ui-state-default" colspan="3">'.$lookup[$sex].'</th></tr><tr><th>'.WT_I18N::translate('Name').'</th><th>'.WT_I18N::translate('Count').'</th><th>COUNT</th></tr></thead><tbody>'.join('', $common).'</tbody></table>';
			case 'list':
				return '<ul>\n'.join("\n", $common).'</ul>\n';
			case 'nolist':
				return join(WT_I18N::$list_separator, $common);
			}
		} else {
			return '';
		}
	}

	static function commonGiven                 ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('B', 'nolist', false, $params); }
	static function commonGivenTotals           ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('B', 'nolist', true,  $params); }
	static function commonGivenList             ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('B', 'list',   false, $params); }
	static function commonGivenListTotals       ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('B', 'list',   true,  $params); }
	static function commonGivenTable            ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('B', 'table',  false, $params); }

	static function commonGivenFemale           ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('F', 'nolist', false, $params); }
	static function commonGivenFemaleTotals     ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('F', 'nolist', true,  $params); }
	static function commonGivenFemaleList       ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('F', 'list',   false, $params); }
	static function commonGivenFemaleListTotals ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('F', 'list',   true,  $params); }
	static function commonGivenFemaleTable      ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('F', 'table',  false, $params); }

	static function commonGivenMale             ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('M', 'nolist', false, $params); }
	static function commonGivenMaleTotals       ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('M', 'nolist', true,  $params); }
	static function commonGivenMaleList         ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('M', 'list',   false, $params); }
	static function commonGivenMaleListTotals   ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('M', 'list',   true,  $params); }
	static function commonGivenMaleTable        ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('M', 'table',  false, $params); }

	static function commonGivenUnknown          ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('U', 'nolist', false, $params); }
	static function commonGivenUnknownTotals    ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('U', 'nolist', true,  $params); }
	static function commonGivenUnknownList      ($params=array(1,10,'alpha' )) { return self::_commonGivenQuery('U', 'list',   false, $params); }
	static function commonGivenUnknownListTotals($params=array(1,10,'rcount')) { return self::_commonGivenQuery('U', 'list',   true,  $params); }
	static function commonGivenUnknownTable     ($params=array(1,10,'rcount')) { return self::_commonGivenQuery('U', 'table',  false, $params); }

	function chartCommonGiven($params=null) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;
		if ($params === null) {$params = array();}
		if (isset($params[0]) && $params[0] != '') {$size = strtolower($params[0]);} else {$size = $WT_STATS_S_CHART_X."x".$WT_STATS_S_CHART_Y;}
		if (isset($params[1]) && $params[1] != '') {$color_from = strtolower($params[1]);} else {$color_from = $WT_STATS_CHART_COLOR1;}
		if (isset($params[2]) && $params[2] != '') {$color_to = strtolower($params[2]);} else {$color_to = $WT_STATS_CHART_COLOR2;}
		if (isset($params[3]) && $params[3] != '') {$threshold = strtolower($params[3]);} else {$threshold = get_gedcom_setting($this->_ged_id, 'COMMON_NAMES_THRESHOLD');}
		if (isset($params[4]) && $params[4] != '') {$maxtoshow = strtolower($params[4]);} else {$maxtoshow = 7;}
		$sizes = explode('x', $size);
		$tot_indi = $this->_totalIndividuals();
		$given = self::_commonGivenQuery('B', 'chart');
		if (!is_array($given)) return '';
		$given = array_slice($given, 0, $maxtoshow);
		if (count($given) <= 0) {return '';}
		$tot = 0;
		foreach ($given as $givn=>$count) {$tot += $count;}
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
			$chl[] = $givn.' - '.WT_I18N::number($count);
		}
		$per = round(100 * ($tot_indi-$tot) / $tot_indi, 0);
		$chd .= self::_array_to_extended_encoding($per);
		$chl[] = WT_I18N::translate('Other').' - '.WT_I18N::number($tot_indi-$tot);

		$chart_title=implode(WT_I18N::$list_separator, $chl);
		$chl=implode('|', $chl);
		return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl=".rawurlencode($chl)."\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"".$chart_title."\" title=\"".$chart_title."\" />";
	}

///////////////////////////////////////////////////////////////////////////////
// Users                                                                     //
///////////////////////////////////////////////////////////////////////////////

	static function _usersLoggedIn($type='nolist') {
		$content = '';
		// List active users
		$NumAnonymous = 0;
		$loggedusers = array ();
		$x = get_logged_in_users();
		foreach ($x as $user_id=>$user_name) {
			if (WT_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline')) {
				$loggedusers[$user_id] = $user_name;
			} else {
				$NumAnonymous++;
			}
		}
		$LoginUsers = count($loggedusers);
		if (($LoginUsers == 0) and ($NumAnonymous == 0)) {
			return WT_I18N::translate('No logged-in and no anonymous users');
		}
		if ($NumAnonymous > 0) {
			$content.='<b>'.WT_I18N::plural('%d anonymous logged-in user', '%d anonymous logged-in users', $NumAnonymous, $NumAnonymous).'</b>';
		}
		if ($LoginUsers > 0) {
			if ($NumAnonymous) {
				if ($type == 'list') {
					$content .= "<br /><br />\n";
				} else {
					$content .= " ".WT_I18N::translate('and')." ";
				}
			}
			$content.='<b>'.WT_I18N::plural('%d logged-in user', '%d logged-in users', $LoginUsers, $LoginUsers).'</b>';
			if ($type == 'list') {
				$content .= '<ul>';
			} else {
				$content .= ': ';
			}
		}
		if (WT_USER_ID) {
			foreach ($loggedusers as $user_id=>$user_name) {
				if ($type == 'list') {
					$content .= "\t<li>".htmlspecialchars(getUserFullName($user_id))." - {$user_name}";
				} else {
					$content .= htmlspecialchars(getUserFullName($user_id))." - {$user_name}";
				}
				if (WT_USER_ID != $user_id && get_user_setting($user_id, 'contactmethod') != 'none') {
					if ($type == 'list') {
						$content .= "<br /><a href=\"#\" onclick=\"return message('{$user_id}');\">".WT_I18N::translate('Send Message')."</a>";
					} else {
						$content .= " <a href=\"#\" onclick=\"return message('{$user_id}');\">".WT_I18N::translate('Send Message')."</a>";
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
		$anon = 0;
		$visible = 0;
		$x = get_logged_in_users();
		foreach ($x as $user_id=>$user_name) {
			if (WT_USER_IS_ADMIN || get_user_setting($user_id, 'visibleonline')) {$visible++;} else {$anon++;}
		}
		if ($type == 'anon') {return $anon;}
		elseif ($type == 'visible') {return $visible;}
		else {return $visible + $anon;}
	}

	static function usersLoggedIn    () { return self::_usersLoggedIn('nolist'); }
	static function usersLoggedInList() { return self::_usersLoggedIn('list'  ); }

	static function usersLoggedInTotal       () { return self::_usersLoggedInTotal('all'    ); }
	static function usersLoggedInTotalAnon   () { return self::_usersLoggedInTotal('anon'   ); }
	static function usersLoggedInTotalVisible() { return self::_usersLoggedInTotal('visible'); }

	static function userID() {return getUserId();}
	static function userName($params=null) {
		if (getUserID()) {
			return getUserName();
		} else {
			if (is_array($params) && isset($params[0]) && $params[0] != '') {
				# if #username:visitor# was specified, then "visitor" will be returned when the user is not logged in 
				return $params[0]; 
			}
			else return null;
		}
	}
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
				if (is_array($params) && isset($params[0]) && $params[0] != '') {$datestamp = $params[0];} else {$datestamp = $DATE_FORMAT;}
				return timestamp_to_gedcom_date(get_user_setting($user_id, 'reg_timestamp'))->Display(false, $datestamp);
			case 'regtime':
				if (is_array($params) && isset($params[0]) && $params[0] != '') {$datestamp = $params[0];} else {$datestamp = str_replace('%', '', $TIME_FORMAT);}
				return date($datestamp, get_user_setting($user_id, 'reg_timestamp'));
			case 'loggedin':
				if (is_array($params) && isset($params[0]) && $params[0] != '') {$yes = $params[0];} else {$yes = WT_I18N::translate('yes');}
				if (is_array($params) && isset($params[1]) && $params[1] != '') {$no = $params[1];} else {$no = WT_I18N::translate('no');}
				return WT_DB::prepare("SELECT 1 FROM `##session` WHERE user_id=? LIMIT 1")->execute(array($user_id))->fetchOne() ? $yes : $no;
		}
	}

	static function latestUserId      ()             { return self::_getLatestUserData('userid'           ); }
	static function latestUserName    ()             { return self::_getLatestUserData('username'         ); }
	static function latestUserFullName()             { return self::_getLatestUserData('fullname'         ); }
	static function latestUserRegDate ($params=null) { return self::_getLatestUserData('regdate',  $params); }
	static function latestUserRegTime ($params=null) { return self::_getLatestUserData('regtime',  $params); }
	static function latestUserLoggedin($params=null) { return self::_getLatestUserData('loggedin', $params); }

///////////////////////////////////////////////////////////////////////////////
// Contact                                                                   //
///////////////////////////////////////////////////////////////////////////////

	function contactWebmaster() { return user_contact_link(get_gedcom_setting($this->_ged_id, 'WEBMASTER_USER_ID')); }
	function contactGedcom   () { return user_contact_link(get_gedcom_setting($this->_ged_id, 'CONTACT_USER_ID'  )); }

///////////////////////////////////////////////////////////////////////////////
// Date & Time                                                               //
///////////////////////////////////////////////////////////////////////////////

	static function serverDate     () { return timestamp_to_gedcom_date(time())->Display(false);}

	static function serverTime     () { return date('g:i a');}
	static function serverTime24   () { return date('G:i');}
	static function serverTimezone () { return date('T');}

	static function browserDate    () { return timestamp_to_gedcom_date(client_time())->Display(false);}

	static function browserTime    () { return date('g:i a', client_time());}
	static function browserTime24  () { return date('G:i',   client_time());}
	static function browserTimezone() { return date('T',     client_time());}

///////////////////////////////////////////////////////////////////////////////
// Tools                                                                     //
///////////////////////////////////////////////////////////////////////////////

	// Older versions of webtrees allowed access to all constants and globals.
	// Newer version just allow access to these values:
	public static function WT_VERSION()      { return WT_VERSION; }
	public static function WT_VERSION_TEXT() { return WT_VERSION_TEXT; }

	// These functions provide access to hitcounter
	// for use in the HTML block.

	static private function _getHitCount($page_name, $params) {
		if (is_array($params) && isset($params[0]) && $params[0] != '') {
			$page_parameter = $params[0];
		} else {
			$page_parameter = '';
		}

		if ($page_name===null) {
			// index.php?ctype=gedcom
			$page_name='index.php';
			$page_parameter='gedcom:'.get_id_from_gedcom($page_parameter ? $page_parameter : WT_GEDCOM);
		} elseif ($page_name=='index.php') {
			// index.php?ctype=user
			$page_parameter='user:'.($page_parameter ? get_user_id($page_parameter) : WT_USER_ID);
		} else {
			// indi/fam/sour/etc.
		}
		
		$count=WT_DB::prepare(
			"SELECT page_count FROM `##hit_counter`".
			" WHERE gedcom_id=? AND page_name=? AND page_parameter=?"
		)->execute(array(WT_GED_ID, $page_name, $page_parameter))->fetchOne();
		return '<span class="hit-counter">'.WT_I18N::number($count).'</span>';
	}

	static function hitCount    ($params=null) {return self::_getHitCount(null,             $params);}
	static function hitCountUser($params=null) {return self::_getHitCount('index.php',      $params);}
	static function hitCountIndi($params=null) {return self::_getHitCount('individual.php', $params);}
	static function hitCountFam ($params=null) {return self::_getHitCount('family.php',     $params);}
	static function hitCountSour($params=null) {return self::_getHitCount('source.php',     $params);}
	static function hitCountRepo($params=null) {return self::_getHitCount('repo.php',       $params);}
	static function hitCountNote($params=null) {return self::_getHitCount('note.php',       $params);}
	static function hitCountObje($params=null) {return self::_getHitCount('mediaviewer.php',$params);}

	/*
	* Leave for backwards compatability? Anybody using this?
	*/
	static function _getEventType($type) {
		$eventTypes=array(
			'BIRT'=>WT_I18N::translate('birth'),
			'DEAT'=>WT_I18N::translate('death'),
			'MARR'=>WT_I18N::translate('marriage'),
			'ADOP'=>WT_I18N::translate('adoption'),
			'BURI'=>WT_I18N::translate('burial'),
			'CENS'=>WT_I18N::translate('census added')
		);
		if (isset($eventTypes[$type])) {
			return $eventTypes[$type];
		}
		return false;
	}

	// http://bendodson.com/news/google-extended-encoding-made-easy/
	static function _array_to_extended_encoding($a) {
		if (!is_array($a)) {
			$a = array($a);
		}
		$encoding = '';
		foreach ($a as $value) {
			if ($value<0) $value = 0;
			$first = floor($value / 64);
			$second = $value % 64;
			$encoding .= self::$_xencoding[$first].self::$_xencoding[$second];
		}
		return $encoding;
	}

	static function _name_total_sort($a, $b) {
		return $a['match']-$b['match'];
	}

	static function _name_total_rsort($a, $b) {
		return $b['match']-$a['match'];
	}

	static function _runSQL($sql) {
		static $cache = array();
		$id = md5($sql);
		if (isset($cache[$id])) {
			return $cache[$id];
		}
		$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
		$cache[$id]=$rows;
		return $rows;
	}

	// These functions provide access to additional non-stats features of webtrees
	// for use in the HTML block.

	static function _getFavorites($isged=true) {
		global $GEDCOM;

		ob_start();
		if ($isged) {
			$class_name = 'gedcom_favorites_WT_Module';
			$block = new $class_name;
			$content = $block->getBlock($GEDCOM);
		}
		else if (WT_USER_ID) {
			$class_name = 'user_favorites_WT_Module';
			$block = new $class_name;
			$content = $block->getBlock($GEDCOM);
		}
		return ob_get_clean();
	}

	static function gedcomFavorites() {return self::_getFavorites(true);}
	static function userFavorites() {return self::_getFavorites(false);}

	static function totalGedcomFavorites() {return count(gedcom_favorites_WT_Module::getFavorites(WT_GED_ID));}
	static function totalUserFavorites() {return count(user_favorites_WT_Module::getFavorites(WT_USER_ID));}

	///////////////////////////////////////////////////////////////////////////////
	// Other blocks                                                              //
	// example of use: #callBlock:block_name#                                    //
	///////////////////////////////////////////////////////////////////////////////

	static function callBlock($params=null) {
		global $ctype;
		if ($params === null) {return '';}
		if (isset($params[0]) && $params[0] != '') {$block = $params[0];} else {return '';}
		$all_blocks=array();
		foreach (WT_Module::getActiveBlocks() as $name=>$active_block) {
			if ($ctype=='user' && $active_block->isUserBlock() || $ctype=='gedcom' && $active_block->isGedcomBlock()) {
				$all_blocks[$name]=$active_block;
			}
		}
		if (!array_key_exists($block, $all_blocks) || $block=='html') return '';
		$class_name = $block.'_WT_Module';
		// Build the config array
		array_shift($params);
		$cfg = array();
		foreach ($params as $config) {
			$bits = explode('=', $config);
			if (count($bits) < 2) {continue;}
			$v = array_shift($bits);
			$cfg[$v] = join('=', $bits);
		}
		$block = new $class_name;
		$block_id=safe_GET('block_id');
		$content = $block->getBlock($block_id, false, $cfg);
		return $content;
	}

	function totalUserMessages() { return WT_I18N::number(count(getUserMessages(WT_USER_NAME))); }

	function totalUserJournal()  { return WT_I18N::number(count(getUserNews(WT_USER_ID))); }
	function totalGedcomNews()   { return WT_I18N::number(count(getUserNews(WT_GEDCOM)));  }

	//////////////////////////////////////////////////////////////////////////////
	// Country lookup data
	//////////////////////////////////////////////////////////////////////////////

	// ISO3166 3 letter codes, with their 2 letter equivalent.
	// NOTE: this is not 1:1.  ENG/SCO/WAL/NIR => GB
	// NOTE: this also includes champman codes and others.  Should it?
	public static function iso3166() {
		return array(
			'ABW'=>'AW', 'AFG'=>'AF', 'AGO'=>'AO', 'AIA'=>'AI', 'ALA'=>'AX', 'ALB'=>'AL',
			'AND'=>'AD', 'ANT'=>'AN', 'ARE'=>'AE', 'ARG'=>'AR', 'ARM'=>'AM', 'ASM'=>'AS',
			'ATA'=>'AQ', 'ATF'=>'TF', 'ATG'=>'AG', 'AUS'=>'AU', 'AUT'=>'AT', 'AZE'=>'AZ',
			'BDI'=>'BI', 'BEL'=>'BE', 'BEN'=>'BJ', 'BFA'=>'BF', 'BGD'=>'BD', 'BGR'=>'BG',
			'BHR'=>'BH', 'BHS'=>'BS', 'BIH'=>'BA', 'BLR'=>'BY', 'BLZ'=>'BZ', 'BMU'=>'BM',
			'BOL'=>'BO', 'BRA'=>'BR', 'BRB'=>'BB', 'BRN'=>'BN', 'BTN'=>'BT', 'BVT'=>'BV',
			'BWA'=>'BW', 'CAF'=>'CF', 'CAN'=>'CA', 'CCK'=>'CC', 'CHE'=>'CH', 'CHL'=>'CL',
			'CHN'=>'CN', 'CHI'=>'JE', 'CIV'=>'CI', 'CMR'=>'CM', 'COD'=>'CD', 'COG'=>'CG',
			'COK'=>'CK', 'COL'=>'CO', 'COM'=>'KM', 'CPV'=>'CV', 'CRI'=>'CR', 'CUB'=>'CU',
			'CXR'=>'CX', 'CYM'=>'KY', 'CYP'=>'CY', 'CZE'=>'CZ', 'DEU'=>'DE', 'DJI'=>'DJ',
			'DMA'=>'DM', 'DNK'=>'DK', 'DOM'=>'DO', 'DZA'=>'DZ', 'ECU'=>'EC', 'EGY'=>'EG',
			'ENG'=>'GB', 'ERI'=>'ER', 'ESH'=>'EH', 'ESP'=>'ES', 'EST'=>'EE', 'ETH'=>'ET',
			'FIN'=>'FI', 'FJI'=>'FJ', 'FLK'=>'FK', 'FRA'=>'FR', 'FRO'=>'FO', 'FSM'=>'FM',
			'GAB'=>'GA', 'GBR'=>'GB', 'GEO'=>'GE', 'GHA'=>'GH', 'GIB'=>'GI', 'GIN'=>'GN',
			'GLP'=>'GP', 'GMB'=>'GM', 'GNB'=>'GW', 'GNQ'=>'GQ', 'GRC'=>'GR', 'GRD'=>'GD',
			'GRL'=>'GL', 'GTM'=>'GT', 'GUF'=>'GF', 'GUM'=>'GU', 'GUY'=>'GY', 'HKG'=>'HK',
			'HMD'=>'HM', 'HND'=>'HN', 'HRV'=>'HR', 'HTI'=>'HT', 'HUN'=>'HU', 'IDN'=>'ID',
			'IND'=>'IN', 'IOT'=>'IO', 'IRL'=>'IE', 'IRN'=>'IR', 'IRQ'=>'IQ', 'ISL'=>'IS',
			'ISR'=>'IL', 'ITA'=>'IT', 'JAM'=>'JM', 'JOR'=>'JO', 'JPN'=>'JA', 'KAZ'=>'KZ',
			'KEN'=>'KE', 'KGZ'=>'KG', 'KHM'=>'KH', 'KIR'=>'KI', 'KNA'=>'KN', 'KOR'=>'KO',
			'KWT'=>'KW', 'LAO'=>'LA', 'LBN'=>'LB', 'LBR'=>'LR', 'LBY'=>'LY', 'LCA'=>'LC',
			'LIE'=>'LI', 'LKA'=>'LK', 'LSO'=>'LS', 'LTU'=>'LT', 'LUX'=>'LU', 'LVA'=>'LV',
			'MAC'=>'MO', 'MAR'=>'MA', 'MCO'=>'MC', 'MDA'=>'MD', 'MDG'=>'MG', 'MDV'=>'MV',
			'MEX'=>'MX', 'MHL'=>'MH', 'MKD'=>'MK', 'MLI'=>'ML', 'MLT'=>'MT', 'MMR'=>'MM',
			'MNG'=>'MN', 'MNP'=>'MP', 'MNT'=>'ME', 'MOZ'=>'MZ', 'MRT'=>'MR', 'MSR'=>'MS',
			'MTQ'=>'MQ', 'MUS'=>'MU', 'MWI'=>'MW', 'MYS'=>'MY', 'MYT'=>'YT', 'NAM'=>'NA',
			'NCL'=>'NC', 'NER'=>'NE', 'NFK'=>'NF', 'NGA'=>'NG', 'NIC'=>'NI', 'NIR'=>'GB',
			'NIU'=>'NU', 'NLD'=>'NL', 'NOR'=>'NO', 'NPL'=>'NP', 'NRU'=>'NR', 'NZL'=>'NZ',
			'OMN'=>'OM', 'PAK'=>'PK', 'PAN'=>'PA', 'PCN'=>'PN', 'PER'=>'PE', 'PHL'=>'PH',
			'PLW'=>'PW', 'PNG'=>'PG', 'POL'=>'PL', 'PRI'=>'PR', 'PRK'=>'KP', 'PRT'=>'PO',
			'PRY'=>'PY', 'PSE'=>'PS', 'PYF'=>'PF', 'QAT'=>'QA', 'REU'=>'RE', 'ROM'=>'RO',
			'RUS'=>'RU', 'RWA'=>'RW', 'SAU'=>'SA', 'SCT'=>'GB', 'SDN'=>'SD', 'SEN'=>'SN',
			'SER'=>'RS', 'SGP'=>'SG', 'SGS'=>'GS', 'SHN'=>'SH', 'SIC'=>'IT', 'SJM'=>'SJ',
			'SLB'=>'SB', 'SLE'=>'SL', 'SLV'=>'SV', 'SMR'=>'SM', 'SOM'=>'SO', 'SPM'=>'PM',
			'STP'=>'ST', 'SUN'=>'RU', 'SUR'=>'SR', 'SVK'=>'SK', 'SVN'=>'SI', 'SWE'=>'SE',
			'SWZ'=>'SZ', 'SYC'=>'SC', 'SYR'=>'SY', 'TCA'=>'TC', 'TCD'=>'TD', 'TGO'=>'TG',
			'THA'=>'TH', 'TJK'=>'TJ', 'TKL'=>'TK', 'TKM'=>'TM', 'TLS'=>'TL', 'TON'=>'TO',
			'TTO'=>'TT', 'TUN'=>'TN', 'TUR'=>'TR', 'TUV'=>'TV', 'TWN'=>'TW', 'TZA'=>'TZ',
			'UGA'=>'UG', 'UKR'=>'UA', 'UMI'=>'UM', 'URY'=>'UY', 'USA'=>'US', 'UZB'=>'UZ',
			'VAT'=>'VA', 'VCT'=>'VC', 'VEN'=>'VE', 'VGB'=>'VG', 'VIR'=>'VI', 'VNM'=>'VN',
			'VUT'=>'VU', 'WLF'=>'WF', 'WLS'=>'GB', 'WSM'=>'WS', 'YEM'=>'YE', 'ZAF'=>'ZA',
			'ZMB'=>'ZM', 'ZWE'=>'ZW',
		);
	}

	public static function get_all_countries() {
		return array(
			'???'=>WT_I18N::translate('Unknown'),
			'ABW'=>WT_I18N::translate('Aruba'),
			'ACA'=>WT_I18N::translate('Acadia'),
			'AFG'=>WT_I18N::translate('Afghanistan'),
			'AGO'=>WT_I18N::translate('Angola'),
			'AIA'=>WT_I18N::translate('Anguilla'),
			'ALA'=>WT_I18N::translate('Aland Islands'),
			'ALB'=>WT_I18N::translate('Albania'),
			'AND'=>WT_I18N::translate('Andorra'),
			'ANT'=>WT_I18N::translate('Netherlands Antilles'),
			'ARE'=>WT_I18N::translate('United Arab Emirates'),
			'ARG'=>WT_I18N::translate('Argentina'),
			'ARM'=>WT_I18N::translate('Armenia'),
			'ASM'=>WT_I18N::translate('American Samoa'),
			'ATA'=>WT_I18N::translate('Antarctica'),
			'ATF'=>WT_I18N::translate('French Southern Territories'),
			'ATG'=>WT_I18N::translate('Antigua and Barbuda'),
			'AUS'=>WT_I18N::translate('Australia'),
			'AUT'=>WT_I18N::translate('Austria'),
			'AZE'=>WT_I18N::translate('Azerbaijan'),
			'AZR'=>WT_I18N::translate('Azores'),
			'BDI'=>WT_I18N::translate('Burundi'),
			'BEL'=>WT_I18N::translate('Belgium'),
			'BEN'=>WT_I18N::translate('Benin'),
			'BFA'=>WT_I18N::translate('Burkina Faso'),
			'BGD'=>WT_I18N::translate('Bangladesh'),
			'BGR'=>WT_I18N::translate('Bulgaria'),
			'BHR'=>WT_I18N::translate('Bahrain'),
			'BHS'=>WT_I18N::translate('Bahamas'),
			'BIH'=>WT_I18N::translate('Bosnia and Herzegovina'),
			'BLR'=>WT_I18N::translate('Belarus'),
			'BLZ'=>WT_I18N::translate('Belize'),
			'BMU'=>WT_I18N::translate('Bermuda'),
			'BOL'=>WT_I18N::translate('Bolivia'),
			'BRA'=>WT_I18N::translate('Brazil'),
			'BRB'=>WT_I18N::translate('Barbados'),
			'BRN'=>WT_I18N::translate('Brunei Darussalam'),
			'BTN'=>WT_I18N::translate('Bhutan'),
			'BVT'=>WT_I18N::translate('Bouvet Island'),
			'BWA'=>WT_I18N::translate('Botswana'),
			'BWI'=>WT_I18N::translate('British West Indies'),
			'CAF'=>WT_I18N::translate('Central African Republic'),
			'CAN'=>WT_I18N::translate('Canada'),
			'CAP'=>WT_I18N::translate('Cape Colony'),
			'CAT'=>WT_I18N::translate('Catalonia'),
			'CCK'=>WT_I18N::translate('Cocos (Keeling) Islands'),
			'CHE'=>WT_I18N::translate('Switzerland'),
			'CHI'=>WT_I18N::translate('Channel Islands'),
			'CHL'=>WT_I18N::translate('Chile'),
			'CHN'=>WT_I18N::translate('China'),
			'CIV'=>WT_I18N::translate('Cote d\'Ivoire'),
			'CMR'=>WT_I18N::translate('Cameroon'),
			'COD'=>WT_I18N::translate('Congo (Kinshasa)'),
			'COG'=>WT_I18N::translate('Congo (Brazzaville)'),
			'COK'=>WT_I18N::translate('Cook Islands'),
			'COL'=>WT_I18N::translate('Colombia'),
			'COM'=>WT_I18N::translate('Comoros'),
			'CPV'=>WT_I18N::translate('Cape Verde'),
			'CRI'=>WT_I18N::translate('Costa Rica'),
			'CSK'=>WT_I18N::translate('Czechoslovakia'),
			'CUB'=>WT_I18N::translate('Cuba'),
			'CXR'=>WT_I18N::translate('Christmas Island'),
			'CYM'=>WT_I18N::translate('Cayman Islands'),
			'CYP'=>WT_I18N::translate('Cyprus'),
			'CZE'=>WT_I18N::translate('Czech Republic'),
			'DEU'=>WT_I18N::translate('Germany'),
			'DJI'=>WT_I18N::translate('Djibouti'),
			'DMA'=>WT_I18N::translate('Dominica'),
			'DNK'=>WT_I18N::translate('Denmark'),
			'DOM'=>WT_I18N::translate('Dominican Republic'),
			'DZA'=>WT_I18N::translate('Algeria'),
			'ECU'=>WT_I18N::translate('Ecuador'),
			'EGY'=>WT_I18N::translate('Egypt'),
			'EIR'=>WT_I18N::translate('Eire'),
			'ENG'=>WT_I18N::translate('England'),
			'ERI'=>WT_I18N::translate('Eritrea'),
			'ESH'=>WT_I18N::translate('Western Sahara'),
			'ESP'=>WT_I18N::translate('Spain'),
			'EST'=>WT_I18N::translate('Estonia'),
			'ETH'=>WT_I18N::translate('Ethiopia'),
			'FIN'=>WT_I18N::translate('Finland'),
			'FJI'=>WT_I18N::translate('Fiji'),
			'FLD'=>WT_I18N::translate('Flanders'),
			'FLK'=>WT_I18N::translate('Falkland Islands'),
			'FRA'=>WT_I18N::translate('France'),
			'FRO'=>WT_I18N::translate('Faeroe Islands'),
			'FSM'=>WT_I18N::translate('Micronesia'),
			'GAB'=>WT_I18N::translate('Gabon'),
			'GBR'=>WT_I18N::translate('United Kingdom'),
			'GEO'=>WT_I18N::translate('Georgia'),
			'GGY'=>WT_I18N::translate('Guernsey'),
			'GHA'=>WT_I18N::translate('Ghana'),
			'GIB'=>WT_I18N::translate('Gibraltar'),
			'GIN'=>WT_I18N::translate('Guinea'),
			'GLP'=>WT_I18N::translate('Guadeloupe'),
			'GMB'=>WT_I18N::translate('Gambia'),
			'GNB'=>WT_I18N::translate('Guinea-Bissau'),
			'GNQ'=>WT_I18N::translate('Equatorial Guinea'),
			'GRC'=>WT_I18N::translate('Greece'),
			'GRD'=>WT_I18N::translate('Grenada'),
			'GRL'=>WT_I18N::translate('Greenland'),
			'GTM'=>WT_I18N::translate('Guatemala'),
			'GUF'=>WT_I18N::translate('French Guiana'),
			'GUM'=>WT_I18N::translate('Guam'),
			'GUY'=>WT_I18N::translate('Guyana'),
			'HKG'=>WT_I18N::translate('Hong Kong'),
			'HMD'=>WT_I18N::translate('Heard Island and McDonald Islands'),
			'HND'=>WT_I18N::translate('Honduras'),
			'HRV'=>WT_I18N::translate('Croatia'),
			'HTI'=>WT_I18N::translate('Haiti'),
			'HUN'=>WT_I18N::translate('Hungary'),
			'IDN'=>WT_I18N::translate('Indonesia'),
			'IND'=>WT_I18N::translate('India'),
			'IOM'=>WT_I18N::translate('Isle of Man'),
			'IOT'=>WT_I18N::translate('British Indian Ocean Territory'),
			'IRL'=>WT_I18N::translate('Ireland'),
			'IRN'=>WT_I18N::translate('Iran'),
			'IRQ'=>WT_I18N::translate('Iraq'),
			'ISL'=>WT_I18N::translate('Iceland'),
			'ISR'=>WT_I18N::translate('Israel'),
			'ITA'=>WT_I18N::translate('Italy'),
			'JAM'=>WT_I18N::translate('Jamaica'),
			'JOR'=>WT_I18N::translate('Jordan'),
			'JPN'=>WT_I18N::translate('Japan'),
			'KAZ'=>WT_I18N::translate('Kazakhstan'),
			'KEN'=>WT_I18N::translate('Kenya'),
			'KGZ'=>WT_I18N::translate('Kyrgyzstan'),
			'KHM'=>WT_I18N::translate('Cambodia'),
			'KIR'=>WT_I18N::translate('Kiribati'),
			'KNA'=>WT_I18N::translate('Saint Kitts and Nevis'),
			'KOR'=>WT_I18N::translate('Korea'),
			'KWT'=>WT_I18N::translate('Kuwait'),
			'LAO'=>WT_I18N::translate('Laos'),
			'LBN'=>WT_I18N::translate('Lebanon'),
			'LBR'=>WT_I18N::translate('Liberia'),
			'LBY'=>WT_I18N::translate('Libya'),
			'LCA'=>WT_I18N::translate('Saint Lucia'),
			'LIE'=>WT_I18N::translate('Liechtenstein'),
			'LKA'=>WT_I18N::translate('Sri Lanka'),
			'LSO'=>WT_I18N::translate('Lesotho'),
			'LTU'=>WT_I18N::translate('Lithuania'),
			'LUX'=>WT_I18N::translate('Luxembourg'),
			'LVA'=>WT_I18N::translate('Latvia'),
			'MAC'=>WT_I18N::translate('Macau'),
			'MAR'=>WT_I18N::translate('Morocco'),
			'MCO'=>WT_I18N::translate('Monaco'),
			'MDA'=>WT_I18N::translate('Moldova'),
			'MDG'=>WT_I18N::translate('Madagascar'),
			'MDV'=>WT_I18N::translate('Maldives'),
			'MEX'=>WT_I18N::translate('Mexico'),
			'MHL'=>WT_I18N::translate('Marshall Islands'),
			'MKD'=>WT_I18N::translate('Macedonia'),
			'MLI'=>WT_I18N::translate('Mali'),
			'MLT'=>WT_I18N::translate('Malta'),
			'MMR'=>WT_I18N::translate('Myanmar'),
			'MNG'=>WT_I18N::translate('Mongolia'),
			'MNP'=>WT_I18N::translate('Northern Mariana Islands'),
			'MNT'=>WT_I18N::translate('Montenegro'),
			'MOZ'=>WT_I18N::translate('Mozambique'),
			'MRT'=>WT_I18N::translate('Mauritania'),
			'MSR'=>WT_I18N::translate('Montserrat'),
			'MTQ'=>WT_I18N::translate('Martinique'),
			'MUS'=>WT_I18N::translate('Mauritius'),
			'MWI'=>WT_I18N::translate('Malawi'),
			'MYS'=>WT_I18N::translate('Malaysia'),
			'MYT'=>WT_I18N::translate('Mayotte'),
			'NAM'=>WT_I18N::translate('Namibia'),
			'NCL'=>WT_I18N::translate('New Caledonia'),
			'NER'=>WT_I18N::translate('Niger'),
			'NFK'=>WT_I18N::translate('Norfolk Island'),
			'NGA'=>WT_I18N::translate('Nigeria'),
			'NIC'=>WT_I18N::translate('Nicaragua'),
			'NIR'=>WT_I18N::translate('Northern Ireland'),
			'NIU'=>WT_I18N::translate('Niue'),
			'NLD'=>WT_I18N::translate('Netherlands'),
			'NOR'=>WT_I18N::translate('Norway'),
			'NPL'=>WT_I18N::translate('Nepal'),
			'NRU'=>WT_I18N::translate('Nauru'),
			'NTZ'=>WT_I18N::translate('Neutral Zone'),
			'NZL'=>WT_I18N::translate('New Zealand'),
			'OMN'=>WT_I18N::translate('Oman'),
			'PAK'=>WT_I18N::translate('Pakistan'),
			'PAN'=>WT_I18N::translate('Panama'),
			'PCN'=>WT_I18N::translate('Pitcairn'),
			'PER'=>WT_I18N::translate('Peru'),
			'PHL'=>WT_I18N::translate('Philippines'),
			'PLW'=>WT_I18N::translate('Palau'),
			'PNG'=>WT_I18N::translate('Papua New Guinea'),
			'POL'=>WT_I18N::translate('Poland'),
			'PRI'=>WT_I18N::translate('Puerto Rico'),
			'PRK'=>WT_I18N::translate('North Korea'),
			'PRT'=>WT_I18N::translate('Portugal'),
			'PRY'=>WT_I18N::translate('Paraguay'),
			'PSE'=>WT_I18N::translate('Occupied Palestinian Territory'),
			'PYF'=>WT_I18N::translate('French Polynesia'),
			'QAT'=>WT_I18N::translate('Qatar'),
			'REU'=>WT_I18N::translate('Reunion'),
			'ROM'=>WT_I18N::translate('Romania'),
			'RUS'=>WT_I18N::translate('Russia'),
			'RWA'=>WT_I18N::translate('Rwanda'),
			'SAU'=>WT_I18N::translate('Saudi Arabia'),
			'SCG'=>WT_I18N::translate('Serbia and Montenegro'),
			'SCT'=>WT_I18N::translate('Scotland'),
			'SDN'=>WT_I18N::translate('Sudan'),
			'SEA'=>WT_I18N::translate('At Sea'),
			'SEN'=>WT_I18N::translate('Senegal'),
			'SER'=>WT_I18N::translate('Serbia'),
			'SGP'=>WT_I18N::translate('Singapore'),
			'SGS'=>WT_I18N::translate('South Georgia and the South Sandwich Islands'),
			'SHN'=>WT_I18N::translate('Saint Helena'),
			'SIC'=>WT_I18N::translate('Sicily'),
			'SJM'=>WT_I18N::translate('Svalbard and Jan Mayen Islands'),
			'SLB'=>WT_I18N::translate('Solomon Islands'),
			'SLE'=>WT_I18N::translate('Sierra Leone'),
			'SLV'=>WT_I18N::translate('El Salvador'),
			'SMR'=>WT_I18N::translate('San Marino'),
			'SOM'=>WT_I18N::translate('Somalia'),
			'SPM'=>WT_I18N::translate('Saint Pierre and Miquelon'),
			'SSD'=>WT_I18N::translate('South Sudan'),
			'STP'=>WT_I18N::translate('Sao Tome and Principe'),
			'SUN'=>WT_I18N::translate('USSR'),
			'SUR'=>WT_I18N::translate('Suriname'),
			'SVK'=>WT_I18N::translate('Slovakia'),
			'SVN'=>WT_I18N::translate('Slovenia'),
			'SWE'=>WT_I18N::translate('Sweden'),
			'SWZ'=>WT_I18N::translate('Swaziland'),
			'SYC'=>WT_I18N::translate('Seychelles'),
			'SYR'=>WT_I18N::translate('Syrian Arab Republic'),
			'TCA'=>WT_I18N::translate('Turks and Caicos Islands'),
			'TCD'=>WT_I18N::translate('Chad'),
			'TGO'=>WT_I18N::translate('Togo'),
			'THA'=>WT_I18N::translate('Thailand'),
			'TJK'=>WT_I18N::translate('Tajikistan'),
			'TKL'=>WT_I18N::translate('Tokelau'),
			'TKM'=>WT_I18N::translate('Turkmenistan'),
			'TLS'=>WT_I18N::translate('Timor-Leste'),
			'TON'=>WT_I18N::translate('Tonga'),
			'TRN'=>WT_I18N::translate('Transylvania'),
			'TTO'=>WT_I18N::translate('Trinidad and Tobago'),
			'TUN'=>WT_I18N::translate('Tunisia'),
			'TUR'=>WT_I18N::translate('Turkey'),
			'TUV'=>WT_I18N::translate('Tuvalu'),
			'TWN'=>WT_I18N::translate('Taiwan'),
			'TZA'=>WT_I18N::translate('Tanzania'),
			'UGA'=>WT_I18N::translate('Uganda'),
			'UKR'=>WT_I18N::translate('Ukraine'),
			'UMI'=>WT_I18N::translate('US Minor Outlying Islands'),
			'URY'=>WT_I18N::translate('Uruguay'),
			'USA'=>WT_I18N::translate('USA'),
			'UZB'=>WT_I18N::translate('Uzbekistan'),
			'VAT'=>WT_I18N::translate('Vatican City'),
			'VCT'=>WT_I18N::translate('Saint Vincent and the Grenadines'),
			'VEN'=>WT_I18N::translate('Venezuela'),
			'VGB'=>WT_I18N::translate('British Virgin Islands'),
			'VIR'=>WT_I18N::translate('US Virgin Islands'),
			'VNM'=>WT_I18N::translate('Viet Nam'),
			'VUT'=>WT_I18N::translate('Vanuatu'),
			'WAF'=>WT_I18N::translate('West Africa'),
			'WLF'=>WT_I18N::translate('Wallis and Futuna Islands'),
			'WLS'=>WT_I18N::translate('Wales'),
			'WSM'=>WT_I18N::translate('Samoa'),
			'YEM'=>WT_I18N::translate('Yemen'),
			'YUG'=>WT_I18N::translate('Yugoslavia'),
			'ZAF'=>WT_I18N::translate('South Africa'),
			'ZAR'=>WT_I18N::translate('Zaire'),
			'ZMB'=>WT_I18N::translate('Zambia'),
			'ZWE'=>WT_I18N::translate('Zimbabwe'),
		);
	}

	// century name, English => 21st, Polish => XXI, etc.
	private static function _centuryName($century) {
		if ($century<0) {
			return str_replace(-$century, WT_I18N::_centuryName(-$century), WT_I18N::translate('%s&nbsp;BCE', WT_I18N::number(-$century)));
		}
		switch ($century) {
		case 21: return WT_I18N::translate_c('CENTURY', '21st');
		case 20: return WT_I18N::translate_c('CENTURY', '20th');
		case 19: return WT_I18N::translate_c('CENTURY', '19th');
		case 18: return WT_I18N::translate_c('CENTURY', '18th');
		case 17: return WT_I18N::translate_c('CENTURY', '17th');
		case 16: return WT_I18N::translate_c('CENTURY', '16th');
		case 15: return WT_I18N::translate_c('CENTURY', '15th');
		case 14: return WT_I18N::translate_c('CENTURY', '14th');
		case 13: return WT_I18N::translate_c('CENTURY', '13th');
		case 12: return WT_I18N::translate_c('CENTURY', '12th');
		case 11: return WT_I18N::translate_c('CENTURY', '11th');
		case 10: return WT_I18N::translate_c('CENTURY', '10th');
		case  9: return WT_I18N::translate_c('CENTURY', '9th');
		case  8: return WT_I18N::translate_c('CENTURY', '8th');
		case  7: return WT_I18N::translate_c('CENTURY', '7th');
		case  6: return WT_I18N::translate_c('CENTURY', '6th');
		case  5: return WT_I18N::translate_c('CENTURY', '5th');
		case  4: return WT_I18N::translate_c('CENTURY', '4th');
		case  3: return WT_I18N::translate_c('CENTURY', '3rd');
		case  2: return WT_I18N::translate_c('CENTURY', '2nd');
		case  1: return WT_I18N::translate_c('CENTURY', '1st');
		default: return ($century-1).'01-'.$century.'00';
		}
	}

}
