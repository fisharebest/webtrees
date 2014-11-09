<?php
// Family tree Statistics Class
//
// This class provides a quick & easy method for accessing statistics
// about the family tree.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

require_once WT_ROOT . 'includes/functions/functions_print_lists.php';

use Rhumsaa\Uuid\Uuid;
use WT\Auth;
use WT\User;

/**
 * Class WT_Stats A selection of pre-formatted statistical queries.  These are primarily
 *                used for embedded keywords on HTML blocks, but are also used elsewhere in
 *                the code.
 */
class WT_Stats {
	/** @var WT_Tree  */
	private $tree;

	/** @var string[] All public functions are available as keywords - except these ones */
	private $public_but_not_allowed = array(
		'__construct', 'embedTags', 'iso3166', 'getAllCountries', 'getAllTagsTable', 'getAllTagsText', 'statsPlaces', 'statsBirthQuery', 'statsDeathQuery', 'statsMarrQuery', 'statsAgeQuery', 'monthFirstChildQuery', 'statsChildrenQuery', 'statsMarrAgeQuery'
	);

	/** @var string[] List of GEDCOM media types */
	private $_media_types = array('audio', 'book', 'card', 'certificate', 'coat', 'document', 'electronic', 'magazine', 'manuscript', 'map', 'fiche', 'film', 'newspaper', 'painting', 'photo', 'tombstone', 'video', 'other');

	/**
	 * @param string $gedcom
	 */
	public function __construct($gedcom) {
		$this->tree = WT_Tree::get(get_id_from_gedcom($gedcom));
	}

	/**
	 * Return a string of all supported tags and an example of its output in table row form.
	 *
	 * @return string
	 */
	public function getAllTagsTable() {
		$examples = array();
		foreach (get_class_methods($this) as $method) {
			$reflection = new ReflectionMethod($this, $method);
			if ($reflection->isPublic() && !in_array($method, $this->public_but_not_allowed)) {
				$examples[$method] = $this->$method();
				if (stristr($method, 'highlight')) {
					$examples[$method] = str_replace(array(' align="left"', ' align="right"'), '', $examples[$method]);
				}
			}
		}
		ksort($examples);

		$html = '';
		foreach ($examples as $tag => $value) {
			$html .= '<tr>';
			$html .= '<td class="list_value_wrap">' . $tag . '</td>';
			$html .= '<td class="list_value_wrap">' . $value . '</td>';
			$html .= '</tr>';
		}

		return
			'<table id="keywords"><thead>' .
			'<tr>' .
			'<th class="list_label_wrap">' .
			WT_I18N::translate('Embedded variable') .
			'</th>' .
			'<th class="list_label_wrap">' .
			WT_I18N::translate('Resulting value') .
			'</th>' .
			'</tr>' .
			'</thead><tbody>' .
			$html .
			'</tbody></table>';
	}

	/**
	 * Return a string of all supported tags in plain text.
	 *
	 * @return string
	 */
	public function getAllTagsText() {
		$examples = array();
		foreach (get_class_methods($this) as $method) {
			$reflection = new ReflectionMethod($this, $method);
			if ($reflection->isPublic() && !in_array($method, $this->public_but_not_allowed)) {
				$examples[$method] = $method;
			}
		}
		ksort($examples);

		return implode('<br>', $examples);
	}

	/**
	 * Get tags and their parsed results.
	 *
	 * @param string $text
	 *
	 * @return string[][]
	 */
	private function getTags($text) {
		static $funcs;

		// Retrive all class methods
		isset($funcs) or $funcs = get_class_methods($this);

		// Extract all tags from the provided text
		preg_match_all("/#([^#]+)(?=#)/", (string)$text, $match);
		$tags       = $match[1];
		$c          = count($tags);
		$new_tags   = array(); // tag to replace
		$new_values = array(); // value to replace it with

		/*
		 * Parse block tags.
		 */
		for ($i = 0; $i < $c; $i++) {
			$full_tag = $tags[$i];
			// Added for new parameter support
			$params = explode(':', $tags[$i]);
			if (count($params) > 1) {
				$tags[$i] = array_shift($params);
			} else {
				$params = array();
			}

			// Generate the replacement value for the tag
			if (method_exists($this, $tags[$i])) {
				$new_tags[]   = "#{$full_tag}#";
				$new_values[] = call_user_func_array(array($this, $tags[$i]), array($params));
			} elseif ($tags[$i] == 'help') {
				// re-merge, just in case
				$new_tags[]   = "#{$full_tag}#";
				$new_values[] = help_link(implode(':', $params));
			}
		}

		return array($new_tags, $new_values);
	}

	/**
	 * Embed tags in text
	 *
	 * @param $text
	 *
	 * @return mixed
	 */
	public function embedTags($text) {
		if (strpos($text, '#') !== false) {
			list($new_tags, $new_values) = $this->getTags($text);
			$text = str_replace($new_tags, $new_values, $text);
		}

		return $text;
	}

	/**
	 * @return string
	 */
	public function gedcomFilename() {
		return $this->tree->tree_name;
	}

	/**
	 * @return string
	 */
	public function gedcomID() {
		return $this->tree->tree_id;
	}

	/**
	 * @return string
	 */
	public function gedcomTitle() {
		return $this->tree->tree_title_html;
	}

	/**
	 * @return string[]
	 */
	private function gedcomHead() {
		$title   = '';
		$version = '';
		$source  = '';

		$head = WT_GedcomRecord::getInstance('HEAD');
		$sour = $head->getFirstFact('SOUR');
		if ($sour) {
			$source  = $sour->getValue();
			$title   = $sour->getAttribute('NAME');
			$version = $sour->getAttribute('VERS');
		}

		return array($title, $version, $source);
	}

	/**
	 * @return string
	 */
	public function gedcomCreatedSoftware() {
		$head = $this->gedcomHead();

		return $head[0];
	}

	/**
	 * @return string
	 */
	public function gedcomCreatedVersion() {
		$head = $this->gedcomHead();
		// fix broken version string in Family Tree Maker
		if (strstr($head[1], 'Family Tree Maker ')) {
			$p       = strpos($head[1], '(') + 1;
			$p2      = strpos($head[1], ')');
			$head[1] = substr($head[1], $p, ($p2 - $p));
		}
		// Fix EasyTree version
		if ($head[2] == 'EasyTree') {
			$head[1] = substr($head[1], 1);
		}

		return $head[1];
	}

	/**
	 * @return string
	 */
	public function gedcomDate() {
		$head = WT_GedcomRecord::getInstance('HEAD');
		$fact = $head->getFirstFact('DATE');
		if ($fact) {
			$date = new WT_Date($fact->getValue());

			return $date->display();
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function gedcomUpdated() {
		$row = WT_DB::prepare(
			"SELECT SQL_CACHE d_year, d_month, d_day FROM `##dates` WHERE d_julianday1 = (SELECT MAX(d_julianday1) FROM `##dates` WHERE d_file =? AND d_fact='CHAN') LIMIT 1"
		)->execute(array($this->tree->tree_id))->fetchOneRow();
		if ($row) {
			$date = new WT_Date("{$row->d_day} {$row->d_month} {$row->d_year}");

			return $date->display();
		} else {
			return $this->gedcomDate();
		}
	}

	/**
	 * @return string
	 */
	public function gedcomRootID() {
		return $this->tree->getPreference('PEDIGREE_ROOT_ID');
	}

	/**
	 * @param string $total
	 * @param string $type
	 *
	 * @return string
	 */
	private function getPercentage($total, $type) {
		switch ($type) {
		case 'individual':
			$type = $this->totalIndividualsQuery();
			break;
		case 'family':
			$type = $this->totalFamiliesQuery();
			break;
		case 'source':
			$type = $this->totalSourcesQuery();
			break;
		case 'note':
			$type = $this->totalNotesQuery();
			break;
		case 'all':
		default:
			$type = $this->totalIndividualsQuery() + $this->totalFamiliesQuery() + $this->totalSourcesQuery();
			break;
		}
		if ($type == 0) {
			return WT_I18N::percentage(0, 1);
		} else {
			return WT_I18N::percentage($total / $type, 1);
		}
	}

	/**
	 * @return string
	 */
	public function totalRecords() {
		return WT_I18N::number($this->totalIndividualsQuery() + $this->totalFamiliesQuery() + $this->totalSourcesQuery());
	}

	/**
	 * @return integer
	 */
	private function totalIndividualsQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##individuals` WHERE i_file = ?")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalIndividuals() {
		return WT_I18N::number($this->totalIndividualsQuery());
	}

	/**
	 * @return integer
	 */
	private function totalIndisWithSourcesQuery() {
		$rows = $this->runSql("SELECT SQL_CACHE COUNT(DISTINCT i_id) AS tot FROM `##link`, `##individuals` WHERE i_id=l_from AND i_file=l_file AND l_file=" . $this->tree->tree_id . " AND l_type='SOUR'");

		return (int)$rows[0]['tot'];
	}

	/**
	 * @return string
	 */
	public function totalIndisWithSources() {
		return WT_I18N::number($this->totalIndisWithSourcesQuery());
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartIndisWithSources($params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_from = strtolower($params[1]);
		} else {
			$color_from = $WT_STATS_CHART_COLOR1;
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_to = strtolower($params[2]);
		} else {
			$color_to = $WT_STATS_CHART_COLOR2;
		}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividualsQuery();
		if ($tot_indi == 0) {
			return '';
		} else {
			$tot_sindi_per = round($this->totalIndisWithSourcesQuery() / $tot_indi, 3);
			$chd = $this->arrayToExtendedEncoding(array(100 - 100 * $tot_sindi_per, 100 * $tot_sindi_per));
			$chl = WT_I18N::translate('Without sources') . ' - ' . WT_I18N::percentage(1 - $tot_sindi_per, 1) . '|' .
				WT_I18N::translate('With sources') . ' - ' . WT_I18N::percentage($tot_sindi_per, 1);
			$chart_title = WT_I18N::translate('Individuals with sources');
			return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:' . $chd . '&amp;chs=' . $size . '&amp;chco=' . $color_from . ',' . $color_to . '&amp;chf=bg,s,ffffff00&amp;chl=' . rawurlencode($chl) . '" width="' . $sizes[0] . '" height="' . $sizes[1] . '" alt="' . $chart_title . '" title="' . $chart_title . '">';
		}
	}

	/**
	 * @return string
	 */
	public function totalIndividualsPercentage() {
		return $this->getPercentage($this->totalIndividualsQuery(), 'all');
	}

	/**
	 * @return integer
	 */
	private function totalFamiliesQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##families` WHERE f_file=?")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalFamilies() {
		return WT_I18N::number($this->totalFamiliesQuery());
	}

	/**
	 * @return integer
	 */
	private function totalFamsWithSourcesQuery() {
		$rows = $this->runSql("SELECT SQL_CACHE COUNT(DISTINCT f_id) AS tot FROM `##link`, `##families` WHERE f_id=l_from AND f_file=l_file AND l_file=" . $this->tree->tree_id . " AND l_type='SOUR'");

		return (int)$rows[0]['tot'];
	}

	/**
	 * @return string
	 */
	public function totalFamsWithSources() {
		return WT_I18N::number($this->totalFamsWithSourcesQuery());
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartFamsWithSources($params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_from = strtolower($params[1]);
		} else {
			$color_from = $WT_STATS_CHART_COLOR1;
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_to = strtolower($params[2]);
		} else {
			$color_to = $WT_STATS_CHART_COLOR2;
		}
		$sizes = explode('x', $size);
		$tot_fam = $this->totalFamiliesQuery();
		if ($tot_fam == 0) {
			return '';
		} else {
			$tot_sfam_per = round($this->totalFamsWithSourcesQuery() / $tot_fam, 3);
			$chd = $this->arrayToExtendedEncoding(array(100 - 100 * $tot_sfam_per, 100 * $tot_sfam_per));
			$chl = WT_I18N::translate('Without sources') . ' - ' . WT_I18N::percentage(1 - $tot_sfam_per, 1) . '|' .
				WT_I18N::translate('With sources') . ' - ' . WT_I18N::percentage($tot_sfam_per, 1);
			$chart_title = WT_I18N::translate('Families with sources');
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . "\" title=\"" . $chart_title . "\" />";
		}
	}

	/**
	 * @return string
	 */
	public function totalFamiliesPercentage() {
		return $this->getPercentage($this->totalFamiliesQuery(), 'all');
	}

	/**
	 * @return integer
	 */
	private function totalSourcesQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##sources` WHERE s_file=?")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalSources() {
		return WT_I18N::number($this->totalSourcesQuery());
	}

	/**
	 * @return string
	 */
	public function totalSourcesPercentage() {
		return $this->getPercentage($this->totalSourcesQuery(), 'all');
	}

	/**
	 * @return integer
	 */
	private function totalNotesQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##other` WHERE o_type='NOTE' AND o_file=?")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalNotes() {
		return WT_I18N::number($this->totalNotesQuery());
	}

	/**
	 * @return string
	 */
	public function totalNotesPercentage() {
		return $this->getPercentage($this->totalNotesQuery(), 'all');
	}

	/**
	 * @return integer
	 */
	private function totalRepositoriesQuery() {
		return
			(Int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##other` WHERE o_type='REPO' AND o_file=?")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalRepositories() {
		return WT_I18N::number($this->totalRepositoriesQuery());
	}

	/**
	 * @return string
	 */
	public function totalRepositoriesPercentage() {
		return $this->getPercentage($this->totalRepositoriesQuery(), 'all');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function totalSurnames($params = array()) {
		if ($params) {
			$qs = implode(',', array_fill(0, count($params), '?'));
			$opt = "IN ({$qs})";
			$vars = $params;
			$distinct = '';
		} else {
			$opt = "IS NOT NULL";
			$vars = '';
			$distinct = 'DISTINCT';
		}
		$vars[] = $this->tree->tree_id;
		$total =
			WT_DB::prepare(
				"SELECT SQL_CACHE COUNT({$distinct} n_surn COLLATE '" . WT_I18N::$collation . "')" .
				" FROM `##name`" .
				" WHERE n_surn COLLATE '" . WT_I18N::$collation . "' {$opt} AND n_file=?")
				->execute($vars)
				->fetchOne();

		return WT_I18N::number($total);
	}

	/**
	 * Count the number of distinct given names, or count the number of
	 * occurrences of a specific name or names.
	 *
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function totalGivennames($params = array()) {
		if ($params) {
			$qs = implode(',', array_fill(0, count($params), '?'));
			$params[] = $this->tree->tree_id;
			$total =
				WT_DB::prepare("SELECT SQL_CACHE COUNT( n_givn) FROM `##name` WHERE n_givn IN ({$qs}) AND n_file=?")
					->execute($params)
					->fetchOne();
		} else {
			$total =
				WT_DB::prepare("SELECT SQL_CACHE COUNT(DISTINCT n_givn) FROM `##name` WHERE n_givn IS NOT NULL AND n_file=?")
					->execute(array($this->tree->tree_id))
					->fetchOne();
		}

		return WT_I18N::number($total);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function totalEvents($params = array()) {
		$sql = "SELECT SQL_CACHE COUNT(*) AS tot FROM `##dates` WHERE d_file=?";
		$vars = array($this->tree->tree_id);

		$no_types = array('HEAD', 'CHAN');
		if ($params) {
			$types = array();
			foreach ($params as $type) {
				if (substr($type, 0, 1) == '!') {
					$no_types[] = substr($type, 1);
				} else {
					$types[] = $type;
				}
			}
			if ($types) {
				$sql .= ' AND d_fact IN (' . implode(', ', array_fill(0, count($types), '?')) . ')';
				$vars = array_merge($vars, $types);
			}
		}
		$sql .= ' AND d_fact NOT IN (' . implode(', ', array_fill(0, count($no_types), '?')) . ')';
		$vars = array_merge($vars, $no_types);

		return WT_I18N::number(WT_DB::prepare($sql)->execute($vars)->fetchOne());
	}

	/**
	 * @return string
	 */
	public function totalEventsBirth() {
		return $this->totalEvents(explode('|', WT_EVENTS_BIRT));
	}

	/**
	 * @return string
	 */
	public function totalBirths() {
		return $this->totalEvents(array('BIRT'));
	}

	/**
	 * @return string
	 */
	public function totalEventsDeath() {
		return $this->totalEvents(explode('|', WT_EVENTS_DEAT));
	}

	/**
	 * @return string
	 */
	public function totalDeaths() {
		return $this->totalEvents(array('DEAT'));
	}

	/**
	 * @return string
	 */
	public function totalEventsMarriage() {
		return $this->totalEvents(explode('|', WT_EVENTS_MARR));
	}

	/**
	 * @return string
	 */
	public function totalMarriages() {
		return $this->totalEvents(array('MARR'));
	}

	/**
	 * @return string
	 */
	public function totalEventsDivorce() {
		return $this->totalEvents(explode('|', WT_EVENTS_DIV));
	}

	/**
	 * @return string
	 */
	public function totalDivorces() {
		return $this->totalEvents(array('DIV'));
	}

	/**
	 * @return string
	 */
	public function totalEventsOther() {
		$facts = array_merge(explode('|', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT));
		$no_facts = array();
		foreach ($facts as $fact) {
			$fact = '!' . str_replace('\'', '', $fact);
			$no_facts[] = $fact;
		}

		return $this->totalEvents($no_facts);
	}

	/**
	 * @return integer
	 */
	private function totalSexMalesQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##individuals` WHERE i_file=? AND i_sex=?")
				->execute(array($this->tree->tree_id, 'M'))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalSexMales() {
		return WT_I18N::number($this->totalSexMalesQuery());
	}

	/**
	 * @return string
	 */
	public function totalSexMalesPercentage() {
		return $this->getPercentage($this->totalSexMalesQuery(), 'individual');
	}

	/**
	 * @return integer
	 */
	private function totalSexFemalesQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##individuals` WHERE i_file=? AND i_sex=?")
				->execute(array($this->tree->tree_id, 'F'))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalSexFemales() {
		return WT_I18N::number($this->totalSexFemalesQuery());
	}

	/**
	 * @return string
	 */
	public function totalSexFemalesPercentage() {
		return $this->getPercentage($this->totalSexFemalesQuery(), 'individual');
	}

	/**
	 * @return integer
	 */
	private function totalSexUnknownQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##individuals` WHERE i_file=? AND i_sex=?")
				->execute(array($this->tree->tree_id, 'U'))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalSexUnknown() {
		return WT_I18N::number($this->totalSexUnknownQuery());
	}

	/**
	 * @return string
	 */
	public function totalSexUnknownPercentage() {
		return $this->getPercentage($this->totalSexUnknownQuery(), 'individual');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartSex($params = array()) {
		global $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_female = strtolower($params[1]);
		} else {
			$color_female = 'ffd1dc';
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_male = strtolower($params[2]);
		} else {
			$color_male = '84beff';
		}
		if (isset($params[3]) && $params[3] != '') {
			$color_unknown = strtolower($params[3]);
		} else {
			$color_unknown = '777777';
		}
		$sizes = explode('x', $size);
		// Raw data - for calculation
		$tot_f = $this->totalSexFemalesQuery();
		$tot_m = $this->totalSexMalesQuery();
		$tot_u = $this->totalSexUnknownQuery();
		$tot = $tot_f + $tot_m + $tot_u;
		// I18N data - for display
		$per_f = $this->totalSexFemalesPercentage();
		$per_m = $this->totalSexMalesPercentage();
		$per_u = $this->totalSexUnknownPercentage();
		if ($tot == 0) {
			return '';
		} elseif ($tot_u > 0) {
			$chd = $this->arrayToExtendedEncoding(array(4095 * $tot_u / $tot, 4095 * $tot_f / $tot, 4095 * $tot_m / $tot));
			$chl =
				WT_I18N::translate_c('unknown people', 'Unknown') . ' - ' . $per_u . '|' .
				WT_I18N::translate('Females') . ' - ' . $per_f . '|' .
				WT_I18N::translate('Males') . ' - ' . $per_m;
			$chart_title =
				WT_I18N::translate('Males') . ' - ' . $per_m . WT_I18N::$list_separator .
				WT_I18N::translate('Females') . ' - ' . $per_f . WT_I18N::$list_separator .
				WT_I18N::translate_c('unknown people', 'Unknown') . ' - ' . $per_u;
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_unknown},{$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . "\" title=\"" . $chart_title . "\" />";
		} else {
			$chd = $this->arrayToExtendedEncoding(array(4095 * $tot_f / $tot, 4095 * $tot_m / $tot));
			$chl =
				WT_I18N::translate('Females') . ' - ' . $per_f . '|' .
				WT_I18N::translate('Males') . ' - ' . $per_m;
			$chart_title = WT_I18N::translate('Males') . ' - ' . $per_m . WT_I18N::$list_separator .
				WT_I18N::translate('Females') . ' - ' . $per_f;
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_female},{$color_male}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . "\" title=\"" . $chart_title . "\" />";
		}
	}

	/**
	 * The totalLiving/totalDeceased queries assume that every dead person will
	 * have a DEAT record.  It will not include individuals who were born more
	 * than MAX_ALIVE_AGE years ago, and who have no DEAT record.
	 * A good reason to run the “Add missing DEAT records” batch-update!
	 * However, SQL cannot provide the same logic used by Person::isDead().
	 *
	 * @return integer
	 */
	private function totalLivingQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##individuals` WHERE i_file=? AND i_gedcom NOT REGEXP '\\n1 (" . WT_EVENTS_DEAT . ")'")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalLiving() {
		return WT_I18N::number($this->totalLivingQuery());
	}

	/**
	 * @return string
	 */
	public function totalLivingPercentage() {
		return $this->getPercentage($this->totalLivingQuery(), 'individual');
	}

	/**
	 * @return integer
	 */
	private function totalDeceasedQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##individuals` WHERE i_file=? AND i_gedcom REGEXP '\\n1 (" . WT_EVENTS_DEAT . ")'")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalDeceased() {
		return WT_I18N::number($this->totalDeceasedQuery());
	}

	/**
	 * @return string
	 */
	public function totalDeceasedPercentage() {
		return $this->getPercentage($this->totalDeceasedQuery(), 'individual');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartMortality($params = array()) {
		global $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_living = strtolower($params[1]);
		} else {
			$color_living = 'ffffff';
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_dead = strtolower($params[2]);
		} else {
			$color_dead = 'cccccc';
		}
		$sizes = explode('x', $size);
		// Raw data - for calculation
		$tot_l = $this->totalLivingQuery();
		$tot_d = $this->totalDeceasedQuery();
		$tot = $tot_l + $tot_d;
		// I18N data - for display
		$per_l = $this->totalLivingPercentage();
		$per_d = $this->totalDeceasedPercentage();
		if ($tot == 0) {
			return '';
		} else {
			$chd = $this->arrayToExtendedEncoding(array(4095 * $tot_l / $tot, 4095 * $tot_d / $tot));
			$chl =
				WT_I18N::translate('Living') . ' - ' . $per_l . '|' .
				WT_I18N::translate('Dead') . ' - ' . $per_d . '|';
			$chart_title = WT_I18N::translate('Living') . ' - ' . $per_l . WT_I18N::$list_separator .
				WT_I18N::translate('Dead') . ' - ' . $per_d;

			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . "\" title=\"" . $chart_title . "\" />";
		}
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function totalUsers($params = array()) {
		if (isset($params[0])) {
			$total = count(User::all()) + (int)$params[0];
		} else {
			$total = count(User::all());
		}

		return WT_I18N::number($total);
	}

	/**
	 * @return string
	 */
	public function totalAdmins() {
		return WT_I18N::number(count(User::allAdmins()));
	}

	/**
	 * @return string
	 */
	public function totalNonAdmins() {
		return WT_I18N::number(count(User::all()) - count(User::allAdmins()));
	}

	/**
	 * @param string $type
	 *
	 * @return integer
	 */
	private function totalMediaType($type = 'all') {
		if (!in_array($type, $this->_media_types) && $type != 'all' && $type != 'unknown') {
			return 0;
		}
		$sql = "SELECT SQL_CACHE COUNT(*) AS tot FROM `##media` WHERE m_file=?";
		$vars = array($this->tree->tree_id);

		if ($type != 'all') {
			if ($type == 'unknown') {
				// There has to be a better way then this :(
				foreach ($this->_media_types as $t) {
					$sql .= " AND (m_gedcom NOT LIKE ? AND m_gedcom NOT LIKE ?)";
					$vars[] = "%3 TYPE {$t}%";
					$vars[] = "%1 _TYPE {$t}%";
				}
			} else {
				$sql .= " AND (m_gedcom LIKE ? OR m_gedcom LIKE ?)";
				$vars[] = "%3 TYPE {$type}%";
				$vars[] = "%1 _TYPE {$type}%";
			}
		}

		return (int)WT_DB::prepare($sql)->execute($vars)->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalMedia() {
		return WT_I18N::number($this->totalMediaType('all'));
	}

	/**
	 * @return string
	 */
	public function totalMediaAudio() {
		return WT_I18N::number($this->totalMediaType('audio'));
	}

	/**
	 * @return string
	 */
	public function totalMediaBook() {
		return WT_I18N::number($this->totalMediaType('book'));
	}

	/**
	 * @return string
	 */
	public function totalMediaCard() {
		return WT_I18N::number($this->totalMediaType('card'));
	}

	/**
	 * @return string
	 */
	public function totalMediaCertificate() {
		return WT_I18N::number($this->totalMediaType('certificate'));
	}

	/**
	 * @return string
	 */
	public function totalMediaCoatOfArms() {
		return WT_I18N::number($this->totalMediaType('coat'));
	}

	/**
	 * @return string
	 */
	public function totalMediaDocument() {
		return WT_I18N::number($this->totalMediaType('document'));
	}

	/**
	 * @return string
	 */
	public function totalMediaElectronic() {
		return WT_I18N::number($this->totalMediaType('electronic'));
	}

	/**
	 * @return string
	 */
	public function totalMediaMagazine() {
		return WT_I18N::number($this->totalMediaType('magazine'));
	}

	/**
	 * @return string
	 */
	public function totalMediaManuscript() {
		return WT_I18N::number($this->totalMediaType('manuscript'));
	}

	/**
	 * @return string
	 */
	public function totalMediaMap() {
		return WT_I18N::number($this->totalMediaType('map'));
	}

	/**
	 * @return string
	 */
	public function totalMediaFiche() {
		return WT_I18N::number($this->totalMediaType('fiche'));
	}

	/**
	 * @return string
	 */
	public function totalMediaFilm() {
		return WT_I18N::number($this->totalMediaType('film'));
	}

	/**
	 * @return string
	 */
	public function totalMediaNewspaper() {
		return WT_I18N::number($this->totalMediaType('newspaper'));
	}

	/**
	 * @return string
	 */
	public function totalMediaPainting() {
		return WT_I18N::number($this->totalMediaType('painting'));
	}

	/**
	 * @return string
	 */
	public function totalMediaPhoto() {
		return WT_I18N::number($this->totalMediaType('photo'));
	}

	/**
	 * @return string
	 */
	public function totalMediaTombstone() {
		return WT_I18N::number($this->totalMediaType('tombstone'));
	}

	/**
	 * @return string
	 */
	public function totalMediaVideo() {
		return WT_I18N::number($this->totalMediaType('video'));
	}

	/**
	 * @return string
	 */
	public function totalMediaOther() {
		return WT_I18N::number($this->totalMediaType('other'));
	}

	/**
	 * @return string
	 */
	public function totalMediaUnknown() {
		return WT_I18N::number($this->totalMediaType('unknown'));
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartMedia($params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_from = strtolower($params[1]);
		} else {
			$color_from = $WT_STATS_CHART_COLOR1;
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_to = strtolower($params[2]);
		} else {
			$color_to = $WT_STATS_CHART_COLOR2;
		}
		$sizes = explode('x', $size);
		$tot = $this->totalMediaType('all');
		// Beware divide by zero
		if ($tot == 0) {
			return WT_I18N::translate('None');
		}
		// Build a table listing only the media types actually present in the GEDCOM
		$mediaCounts = array();
		$mediaTypes = "";
		$chart_title = "";
		$c = 0;
		$max = 0;
		$media = array();
		foreach ($this->_media_types as $type) {
			$count = $this->totalMediaType($type);
			if ($count > 0) {
				$media[$type] = $count;
				if ($count > $max) {
					$max = $count;
				}
				$c += $count;
			}
		}
		$count = $this->totalMediaType('unknown');
		if ($count > 0) {
			$media['unknown'] = $tot - $c;
			if ($tot - $c > $max) {
				$max = $count;
			}
		}
		if (($max / $tot) > 0.6 && count($media) > 10) {
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
		foreach ($media as $type => $count) {
			$mediaCounts[] = round(100 * $count / $tot, 0);
			$mediaTypes .= WT_Gedcom_Tag::getFileFormTypeValue($type) . ' - ' . WT_I18N::number($count) . '|';
			$chart_title .= WT_Gedcom_Tag::getFileFormTypeValue($type) . ' (' . $count . '), ';
		}
		$chart_title = substr($chart_title, 0, -2);
		$chd = $this->arrayToExtendedEncoding($mediaCounts);
		$chl = substr($mediaTypes, 0, -1);

		return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . "\" title=\"" . $chart_title . "\" />";
	}

	/**
	 * Birth & Death
	 *
	 * @param string $type
	 * @param string $life_dir
	 * @param string $birth_death
	 *
	 * @return string
	 */
	private function mortalityQuery($type = 'full', $life_dir = 'ASC', $birth_death = 'BIRT') {
		if ($birth_death == 'MARR') {
			$query_field = "'MARR'";
		} elseif ($birth_death == 'DIV') {
			$query_field = "'DIV'";
		} elseif ($birth_death == 'BIRT') {
			$query_field = "'BIRT'";
		} else {
			$query_field = "'DEAT'";
		}
		if ($life_dir == 'ASC') {
			$dmod = 'MIN';
		} else {
			$dmod = 'MAX';
		}
		$rows = $this->runSql(
			"SELECT SQL_CACHE d_year, d_type, d_fact, d_gid" .
			" FROM `##dates`" .
			" WHERE d_file={$this->tree->tree_id} AND d_fact IN ({$query_field}) AND d_julianday1=(" .
			" SELECT {$dmod}( d_julianday1 )" .
			" FROM `##dates`" .
			" WHERE d_file={$this->tree->tree_id} AND d_fact IN ({$query_field}) AND d_julianday1<>0 )" .
			" LIMIT 1"
		);
		if (!isset($rows[0])) {
			return '';
		}
		$row = $rows[0];
		$record = WT_GedcomRecord::getInstance($row['d_gid']);
		switch ($type) {
		default:
		case 'full':
			if ($record->canShow()) {
				$result = $record->format_list('span', false, $record->getFullName());
			} else {
				$result = WT_I18N::translate('This information is private and cannot be shown.');
			}
			break;
		case 'year':
			$date = new WT_Date($row['d_type'] . ' ' . $row['d_year']);
			$result = $date->display();
			break;
		case 'name':
			$result = "<a href=\"" . $record->getHtmlUrl() . "\">" . $record->getFullName() . "</a>";
			break;
		case 'place':
			$fact = WT_GedcomRecord::getInstance($row['d_gid'])->getFirstFact($row['d_fact']);
			if ($fact) {
				$result = format_fact_place($fact, true, true, true);
			} else {
				$result = WT_I18N::translate('Private');
			}
			break;
		}

		return $result;
	}

	/**
	 * @param string  $what
	 * @param string  $fact
	 * @param integer $parent
	 * @param boolean $country
	 *
	 * @return integer[]|string[][]
	 */
	public function statsPlaces($what = 'ALL', $fact = '', $parent = 0, $country = false) {
		if ($fact) {
			if ($what == 'INDI') {
				$rows =
					WT_DB::prepare("SELECT i_gedcom AS ged FROM `##individuals` WHERE i_file=?")
						->execute(array($this->tree->tree_id))
						->fetchAll();
			} elseif ($what == 'FAM') {
				$rows =
					WT_DB::prepare("SELECT f_gedcom AS ged FROM `##families` WHERE f_file=?")
						->execute(array($this->tree->tree_id))
						->fetchAll();
			}
			$placelist = array();
			foreach ($rows as $row) {
				if (preg_match('/\n1 ' . $fact . '(?:\n[2-9].*)*\n2 PLAC (.+)/', $row->ged, $match)) {
					if ($country) {
						$tmp = explode(WT_Place::GEDCOM_SEPARATOR, $match[1]);
						$place = end($tmp);
					} else {
						$place = $match[1];
					}
					if (!isset($placelist[$place])) {
						$placelist[$place] = 1;
					} else {
						$placelist[$place]++;
					}
				}
			}

			return $placelist;
		} elseif ($parent > 0) {
			// used by placehierarchy googlemap module
			if ($what == 'INDI') {
				$join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
			} elseif ($what == 'FAM') {
				$join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
			} else {
				$join = "";
			}
			$rows = $this->runSql(
				" SELECT SQL_CACHE" .
				" p_place AS place," .
				" COUNT(*) AS tot" .
				" FROM" .
				" `##places`" .
				" JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
				$join .
				" WHERE" .
				" p_id={$parent} AND" .
				" p_file={$this->tree->tree_id}" .
				" GROUP BY place"
			);

			return $rows;
		} else {
			if ($what == 'INDI') {
				$join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
			} elseif ($what == 'FAM') {
				$join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
			} else {
				$join = "";
			}
			$rows = $this->runSql(
				" SELECT SQL_CACHE" .
				" p_place AS country," .
				" COUNT(*) AS tot" .
				" FROM" .
				" `##places`" .
				" JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
				$join .
				" WHERE" .
				" p_file={$this->tree->tree_id}" .
				" AND p_parent_id='0'" .
				" GROUP BY country ORDER BY tot DESC, country ASC"
			);

			return $rows;
		}
	}

	/**
	 * @return integer
	 */
	private function totalPlacesQuery() {
		return
			(int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##places` WHERE p_file=?")
				->execute(array($this->tree->tree_id))
				->fetchOne();
	}

	/**
	 * @return string
	 */
	public function totalPlaces() {
		return WT_I18n::number($this->totalPlacesQuery());
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartDistribution($params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_CHART_COLOR3, $WT_STATS_MAP_X, $WT_STATS_MAP_Y;

		if (isset($params[0])) {
			$chart_shows = $params[0];
		} else {
			$chart_shows = 'world';
		}
		if (isset($params[1])) {
			$chart_type = $params[1];
		} else {
			$chart_type = '';
		}
		if (isset($params[2])) {
			$surname = $params[2];
		} else {
			$surname = '';
		}

		if ($this->totalPlacesQuery() == 0) {
			return '';
		}
		// Get the country names for each language
		$country_to_iso3166 = array();
		foreach (WT_I18N::installed_languages() as $code => $lang) {
			WT_I18N::init($code);
			$countries = $this->getAllCountries();
			foreach ($this->iso3166() as $three => $two) {
				$country_to_iso3166[$three] = $two;
				$country_to_iso3166[$countries[$three]] = $two;
			}
		}
		WT_I18N::init(WT_LOCALE);
		switch ($chart_type) {
		case 'surname_distribution_chart':
			if ($surname == "") {
				$surname = $this->getCommonSurname();
			}
			$chart_title = WT_I18N::translate('Surname distribution chart') . ': ' . $surname;
			// Count how many people are events in each country
			$surn_countries = array();
			$indis = WT_Query_Name::individuals(WT_I18N::strtoupper($surname), '', '', false, false, WT_GED_ID);
			foreach ($indis as $person) {
				if (preg_match_all('/^2 PLAC (?:.*, *)*(.*)/m', $person->getGedcom(), $matches)) {
					// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
					foreach ($matches[1] as $country) {
						if (array_key_exists($country, $country_to_iso3166)) {
							if (array_key_exists($country_to_iso3166[$country], $surn_countries)) {
								$surn_countries[$country_to_iso3166[$country]]++;
							} else {
								$surn_countries[$country_to_iso3166[$country]] = 1;
							}
						}
					}
				}
			};
			break;
		case 'birth_distribution_chart':
			$chart_title = WT_I18N::translate('Birth by country');
			// Count how many people were born in each country
			$surn_countries = array();
			$b_countries = $this->statsPlaces('INDI', 'BIRT', 0, true);
			foreach ($b_countries as $place => $count) {
				$country = $place;
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]] = $count;
					} else {
						$surn_countries[$country_to_iso3166[$country]] += $count;
					}
				}
			}
			break;
		case 'death_distribution_chart':
			$chart_title = WT_I18N::translate('Death by country');
			// Count how many people were death in each country
			$surn_countries = array();
			$d_countries = $this->statsPlaces('INDI', 'DEAT', 0, true);
			foreach ($d_countries as $place => $count) {
				$country = $place;
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]] = $count;
					} else {
						$surn_countries[$country_to_iso3166[$country]] += $count;
					}
				}
			}
			break;
		case 'marriage_distribution_chart':
			$chart_title = WT_I18N::translate('Marriage by country');
			// Count how many families got marriage in each country
			$surn_countries = array();
			$m_countries = $this->statsPlaces('FAM');
			// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			foreach ($m_countries as $place) {
				$country = $place['country'];
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]] = $place['tot'];
					} else {
						$surn_countries[$country_to_iso3166[$country]] += $place['tot'];
					}
				}
			}
			break;
		case 'indi_distribution_chart':
		default:
			$chart_title = WT_I18N::translate('Individual distribution chart');
			// Count how many people have events in each country
			$surn_countries = array();
			$a_countries = $this->statsPlaces('INDI');
			// webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
			foreach ($a_countries as $place) {
				$country = $place['country'];
				if (array_key_exists($country, $country_to_iso3166)) {
					if (!isset($surn_countries[$country_to_iso3166[$country]])) {
						$surn_countries[$country_to_iso3166[$country]] = $place['tot'];
					} else {
						$surn_countries[$country_to_iso3166[$country]] += $place['tot'];
					}
				}
			}
			break;
		}
		$chart_url = "https://chart.googleapis.com/chart?cht=t&amp;chtm=" . $chart_shows;
		$chart_url .= "&amp;chco=" . $WT_STATS_CHART_COLOR1 . "," . $WT_STATS_CHART_COLOR3 . "," . $WT_STATS_CHART_COLOR2; // country colours
		$chart_url .= "&amp;chf=bg,s,ECF5FF"; // sea colour
		$chart_url .= "&amp;chs=" . $WT_STATS_MAP_X . "x" . $WT_STATS_MAP_Y;
		$chart_url .= "&amp;chld=" . implode('', array_keys($surn_countries)) . "&amp;chd=s:";
		foreach ($surn_countries as $count) {
			$chart_url .= substr(WT_GOOGLE_CHART_ENCODING, (int)($count / max($surn_countries) * 61), 1);
		}
		$chart = '<div id="google_charts" class="center">';
		$chart .= '<b>' . $chart_title . '</b><br><br>';
		$chart .= '<div align="center"><img src="' . $chart_url . '" alt="' . $chart_title . '" title="' . $chart_title . '" class="gchart" /><br>';
		$chart .= '<table class="center"><tr>';
		$chart .= '<td bgcolor="#' . $WT_STATS_CHART_COLOR2 . '" width="12"></td><td>' . WT_I18N::translate('Highest population') . '&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#' . $WT_STATS_CHART_COLOR3 . '" width="12"></td><td>' . WT_I18N::translate('Lowest population') . '&nbsp;&nbsp;</td>';
		$chart .= '<td bgcolor="#' . $WT_STATS_CHART_COLOR1 . '" width="12"></td><td>' . WT_I18N::translate('Nobody at all') . '&nbsp;&nbsp;</td>';
		$chart .= '</tr></table></div></div>';

		return $chart;
	}

	/**
	 * @return string
	 */
	public function commonCountriesList() {
		$countries = $this->statsPlaces();
		if (empty($countries)) {
			return '';
		}
		$top10 = array();
		$i = 1;
		// Get the country names for each language
		$country_names = array();
		foreach (WT_I18N::installed_languages() as $code => $lang) {
			WT_I18N::init($code);
			$all_countries = $this->getAllCountries();
			foreach ($all_countries as $country_code => $country_name) {
				$country_names[$country_name] = $country_code;
			}
		}
		WT_I18N::init(WT_LOCALE);
		$all_db_countries = array();
		foreach ($countries as $place) {
			$country = trim($place['country']);
			if (array_key_exists($country, $country_names)) {
				if (!isset($all_db_countries[$country_names[$country]][$country])) {
					$all_db_countries[$country_names[$country]][$country] = $place['tot'];
				} else {
					$all_db_countries[$country_names[$country]][$country] += $place['tot'];
				}
			}
		}
		// get all the user’s countries names
		$all_countries = $this->getAllCountries();
		foreach ($all_db_countries as $country_code => $country) {
			$top10[] = '<li>';
			foreach ($country as $country_name => $tot) {
				$tmp = new WT_Place($country_name, $this->tree->tree_id);
				$place = '<a href="' . $tmp->getURL() . '" class="list_item">' . $all_countries[$country_code] . '</a>';
				$top10[] .= $place . ' - ' . WT_I18N::number($tot);
			}
			$top10[] .= '</li>';
			if ($i++ == 10) {
				break;
			}
		}
		$top10 = implode('', $top10);
		return '<ul>' . $top10 . '</ul>';
	}

	/**
	 * @return string
	 */
	public function commonBirthPlacesList() {
		$places = $this->statsPlaces('INDI', 'BIRT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place => $count) {
			$tmp = new WT_Place($place, $this->tree->tree_id);
			$place = '<a href="' . $tmp->getURL() . '" class="list_item">' . $tmp->getFullName() . '</a>';
			$top10[] = '<li>' . $place . ' - ' . WT_I18N::number($count) . '</li>';
			if ($i++ == 10) {
				break;
			}
		}
		$top10 = implode('', $top10);

		return '<ul>' . $top10 . '</ul>';
	}

	/**
	 * @return string
	 */
	public function commonDeathPlacesList() {
		$places = $this->statsPlaces('INDI', 'DEAT');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place => $count) {
			$tmp = new WT_Place($place, $this->tree->tree_id);
			$place = '<a href="' . $tmp->getURL() . '" class="list_item">' . $tmp->getFullName() . '</a>';
			$top10[] = '<li>' . $place . ' - ' . WT_I18N::number($count) . '</li>';
			if ($i++ == 10) {
				break;
			}
		}
		$top10 = implode('', $top10);

		return '<ul>' . $top10 . '</ul>';
	}

	/**
	 * @return string
	 */
	public function commonMarriagePlacesList() {
		$places = $this->statsPlaces('FAM', 'MARR');
		$top10 = array();
		$i = 1;
		arsort($places);
		foreach ($places as $place => $count) {
			$tmp = new WT_Place($place, $this->tree->tree_id);
			$place = '<a href="' . $tmp->getURL() . '" class="list_item">' . $tmp->getFullName() . '</a>';
			$top10[] = '<li>' . $place . ' - ' . WT_I18N::number($count) . '</li>';
			if ($i++ == 10) {
				break;
			}
		}
		$top10 = implode('', $top10);

		return '<ul>' . $top10 . '</ul>';
	}

	/**
	 * @param boolean $simple
	 * @param boolean $sex
	 * @param integer $year1
	 * @param integer $year2
	 * @param array   $params
	 *
	 * @return array|string
	 */
	public function statsBirthQuery($simple = true, $sex = false, $year1 = -1, $year2 = -1, $params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql =
				"SELECT SQL_CACHE FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` " .
				"WHERE " .
				"d_file={$this->tree->tree_id} AND " .
				"d_year<>0 AND " .
				"d_fact='BIRT' AND " .
				"d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
		} elseif ($sex) {
			$sql =
				"SELECT SQL_CACHE d_month, i_sex, COUNT(*) AS total FROM `##dates` " .
				"JOIN `##individuals` ON d_file = i_file AND d_gid = i_id " .
				"WHERE " .
				"d_file={$this->tree->tree_id} AND " .
				"d_fact='BIRT' AND " .
				"d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
		} else {
			$sql =
				"SELECT SQL_CACHE d_month, COUNT(*) AS total FROM `##dates` " .
				"WHERE " .
				"d_file={$this->tree->tree_id} AND " .
				"d_fact='BIRT' AND " .
				"d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
		}
		if ($year1 >= 0 && $year2 >= 0) {
			$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
		}
		if ($simple) {
			$sql .= " GROUP BY century ORDER BY century";
		} else {
			$sql .= " GROUP BY d_month";
			if ($sex) {
				$sql .= ", i_sex";
			}
		}
		$rows = $this->runSql($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
			}
			if (isset($params[1]) && $params[1] != '') {
				$color_from = strtolower($params[1]);
			} else {
				$color_from = $WT_STATS_CHART_COLOR1;
			}
			if (isset($params[2]) && $params[2] != '') {
				$color_to = strtolower($params[2]);
			} else {
				$color_to = $WT_STATS_CHART_COLOR2;
			}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot == 0) {
				return '';
			}
			$centuries = "";
			$counts = array();
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $this->centuryName($values['century']) . ' - ' . WT_I18N::number($values['total']) . '|';
			}
			$chd = $this->arrayToExtendedEncoding($counts);
			$chl = rawurlencode(substr($centuries, 0, -1));

			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Births by century') . "\" title=\"" . WT_I18N::translate('Births by century') . "\" />";
		} else {
			return $rows;
		}
	}

	/**
	 * @param boolean $simple
	 * @param boolean $sex
	 * @param integer $year1
	 * @param integer $year2
	 * @param array   $params
	 *
	 * @return array|string
	 */
	public function statsDeathQuery($simple = true, $sex = false, $year1 = -1, $year2 = -1, $params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql =
				"SELECT SQL_CACHE FLOOR(d_year/100+1) AS century, COUNT(*) AS total FROM `##dates` " .
				"WHERE " .
				"d_file={$this->tree->tree_id} AND " .
				'd_year<>0 AND ' .
				"d_fact='DEAT' AND " .
				"d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
		} elseif ($sex) {
			$sql =
				"SELECT SQL_CACHE d_month, i_sex, COUNT(*) AS total FROM `##dates` " .
				"JOIN `##individuals` ON d_file = i_file AND d_gid = i_id " .
				"WHERE " .
				"d_file={$this->tree->tree_id} AND " .
				"d_fact='DEAT' AND " .
				"d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
		} else {
			$sql =
				"SELECT SQL_CACHE d_month, COUNT(*) AS total FROM `##dates` " .
				"WHERE " .
				"d_file={$this->tree->tree_id} AND " .
				"d_fact='DEAT' AND " .
				"d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
		}
		if ($year1 >= 0 && $year2 >= 0) {
			$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
		}
		if ($simple) {
			$sql .= " GROUP BY century ORDER BY century";
		} else {
			$sql .= " GROUP BY d_month";
			if ($sex) {
				$sql .= ", i_sex";
			}
		}
		$rows = $this->runSql($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
			}
			if (isset($params[1]) && $params[1] != '') {
				$color_from = strtolower($params[1]);
			} else {
				$color_from = $WT_STATS_CHART_COLOR1;
			}
			if (isset($params[2]) && $params[2] != '') {
				$color_to = strtolower($params[2]);
			} else {
				$color_to = $WT_STATS_CHART_COLOR2;
			}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot == 0) {
				return '';
			}
			$centuries = "";
			$counts = array();
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $this->centuryName($values['century']) . ' - ' . WT_I18N::number($values['total']) . '|';
			}
			$chd = $this->arrayToExtendedEncoding($counts);
			$chl = rawurlencode(substr($centuries, 0, -1));

			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Deaths by century') . "\" title=\"" . WT_I18N::translate('Deaths by century') . "\" />";
		}
		return $rows;
	}

	/**
	 * @return string
	 */
	public function firstBirth() {
		return $this->mortalityQuery('full', 'ASC', 'BIRT');
	}

	/**
	 * @return string
	 */
	public function firstBirthYear() {
		return $this->mortalityQuery('year', 'ASC', 'BIRT');
	}

	/**
	 * @return string
	 */
	public function firstBirthName() {
		return $this->mortalityQuery('name', 'ASC', 'BIRT');
	}

	/**
	 * @return string
	 */
	public function firstBirthPlace() {
		return $this->mortalityQuery('place', 'ASC', 'BIRT');
	}

	/**
	 * @return string
	 */
	public function lastBirth() {
		return $this->mortalityQuery('full', 'DESC', 'BIRT');
	}

	/**
	 * @return string
	 */
	public function lastBirthYear() {
		return $this->mortalityQuery('year', 'DESC', 'BIRT');
	}

	/**
	 * @return string
	 */
	public function lastBirthName() {
		return $this->mortalityQuery('name', 'DESC', 'BIRT');
	}

	/**
	 * @return string
	 */
	public function lastBirthPlace() {
		return $this->mortalityQuery('place', 'DESC', 'BIRT');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function statsBirth($params = array()) {
		return $this->statsBirthQuery(true, false, -1, -1, $params);
	}

	/**
	 * @return string
	 */
	public function firstDeath() {
		return $this->mortalityQuery('full', 'ASC', 'DEAT');
	}

	/**
	 * @return string
	 */
	public function firstDeathYear() {
		return $this->mortalityQuery('year', 'ASC', 'DEAT');
	}

	/**
	 * @return string
	 */
	public function firstDeathName() {
		return $this->mortalityQuery('name', 'ASC', 'DEAT');
	}

	/**
	 * @return string
	 */
	public function firstDeathPlace() {
		return $this->mortalityQuery('place', 'ASC', 'DEAT');
	}

	/**
	 * @return string
	 */
	public function lastDeath() {
		return $this->mortalityQuery('full', 'DESC', 'DEAT');
	}

	/**
	 * @return string
	 */
	public function lastDeathYear() {
		return $this->mortalityQuery('year', 'DESC', 'DEAT');
	}

	/**
	 * @return string
	 */
	public function lastDeathName() {
		return $this->mortalityQuery('name', 'DESC', 'DEAT');
	}

	/**
	 * @return string
	 */
	public function lastDeathPlace() {
		return $this->mortalityQuery('place', 'DESC', 'DEAT');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function statsDeath($params = array()) {
		return $this->statsDeathQuery(true, false, -1, -1, $params);
	}

	/**
	 * Lifespan
	 *
	 * @param string $type
	 * @param string $sex
	 *
	 * @return string
	 */
	private function longlifeQuery($type = 'full', $sex = 'F') {
		$sex_search = ' 1=1';
		if ($sex == 'F') {
			$sex_search = " i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " i_sex='M'";
		}

		$rows = $this->runSql(
			" SELECT SQL_CACHE" .
			" death.d_gid AS id," .
			" death.d_julianday2-birth.d_julianday1 AS age" .
			" FROM" .
			" `##dates` AS death," .
			" `##dates` AS birth," .
			" `##individuals` AS indi" .
			" WHERE" .
			" indi.i_id=birth.d_gid AND" .
			" birth.d_gid=death.d_gid AND" .
			" death.d_file={$this->tree->tree_id} AND" .
			" birth.d_file=death.d_file AND" .
			" birth.d_file=indi.i_file AND" .
			" birth.d_fact='BIRT' AND" .
			" death.d_fact='DEAT' AND" .
			" birth.d_julianday1<>0 AND" .
			" death.d_julianday1>birth.d_julianday2 AND" .
			$sex_search .
			" ORDER BY" .
			" age DESC LIMIT 1"
		);
		if (!isset($rows[0])) {
			return '';
		}
		$row = $rows[0];
		$person = WT_Individual::getInstance($row['id']);
		switch ($type) {
		default:
		case 'full':
			if ($person->canShowName()) {
				$result = $person->format_list('span', false, $person->getFullName());
			} else {
				$result = WT_I18N::translate('This information is private and cannot be shown.');
			}
			break;
		case 'age':
			$result = WT_I18N::number((int)($row['age'] / 365.25));
			break;
		case 'name':
			$result = "<a href=\"" . $person->getHtmlUrl() . "\">" . $person->getFullName() . "</a>";
			break;
		}
		return $result;
	}

	/**
	 * @param string   $type
	 * @param string   $sex
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function topTenOldestQuery($type = 'list', $sex = 'BOTH', $params = array()) {
		global $TEXT_DIRECTION;

		if ($sex == 'F') {
			$sex_search = " AND i_sex='F' ";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M' ";
		} else {
			$sex_search = '';
		}
		if (isset($params[0])) {
			$total = (int)$params[0];
		} else {
			$total = 10;
		}
		$rows = $this->runSql(
			"SELECT SQL_CACHE " .
			" MAX(death.d_julianday2-birth.d_julianday1) AS age, " .
			" death.d_gid AS deathdate " .
			"FROM " .
			" `##dates` AS death, " .
			" `##dates` AS birth, " .
			" `##individuals` AS indi " .
			"WHERE " .
			" indi.i_id=birth.d_gid AND " .
			" birth.d_gid=death.d_gid AND " .
			" death.d_file={$this->tree->tree_id} AND " .
			" birth.d_file=death.d_file AND " .
			" birth.d_file=indi.i_file AND " .
			" birth.d_fact='BIRT' AND " .
			" death.d_fact='DEAT' AND " .
			" birth.d_julianday1<>0 AND " .
			" death.d_julianday1>birth.d_julianday2 " .
			$sex_search .
			"GROUP BY deathdate " .
			"ORDER BY age DESC " .
			"LIMIT " . $total
		);
		if (!isset($rows[0])) {
			return '';
		}
		$top10 = array();
		foreach ($rows as $row) {
			$person = WT_Individual::getInstance($row['deathdate']);
			$age = $row['age'];
			if ((int)($age / 365.25) > 0) {
				$age = (int)($age / 365.25) . 'y';
			} elseif ((int)($age / 30.4375) > 0) {
				$age = (int)($age / 30.4375) . 'm';
			} else {
				$age = $age . 'd';
			}
			$age = get_age_at_event($age, true);
			if ($person->canShow()) {
				if ($type == 'list') {
					$top10[] = "<li><a href=\"" . $person->getHtmlUrl() . "\">" . $person->getFullName() . "</a> (" . $age . ")" . "</li>";
				} else {
					$top10[] = "<a href=\"" . $person->getHtmlUrl() . "\">" . $person->getFullName() . "</a> (" . $age . ")";
				}
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		} else {
			$top10 = implode(' ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}
		return $top10;
	}

	/**
	 * @param string   $type
	 * @param string   $sex
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function topTenOldestAliveQuery($type = 'list', $sex = 'BOTH', $params = array()) {
		global $TEXT_DIRECTION;

		if (!WT_USER_CAN_ACCESS) {
			return WT_I18N::translate('This information is private and cannot be shown.');
		}
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F'";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M'";
		} else {
			$sex_search = '';
		}
		if (isset($params[0])) {
			$total = (int)$params[0];
		} else {
			$total = 10;
		}
		$rows = $this->runSql(
			"SELECT SQL_CACHE" .
			" birth.d_gid AS id," .
			" MIN(birth.d_julianday1) AS age" .
			" FROM" .
			" `##dates` AS birth," .
			" `##individuals` AS indi" .
			" WHERE" .
			" indi.i_id=birth.d_gid AND" .
			" indi.i_gedcom NOT REGEXP '\\n1 (" . WT_EVENTS_DEAT . ")' AND" .
			" birth.d_file={$this->tree->tree_id} AND" .
			" birth.d_fact='BIRT' AND" .
			" birth.d_file=indi.i_file AND" .
			" birth.d_julianday1<>0" .
			$sex_search .
			" GROUP BY id" .
			" ORDER BY age" .
			" ASC LIMIT " . $total
		);
		$top10 = array();
		foreach ($rows as $row) {
			$person = WT_Individual::getInstance($row['id']);
			$age = (WT_CLIENT_JD - $row['age']);
			if ((int)($age / 365.25) > 0) {
				$age = (int)($age / 365.25) . 'y';
			} elseif ((int)($age / 30.4375) > 0) {
				$age = (int)($age / 30.4375) . 'm';
			} else {
				$age = $age . 'd';
			}
			$age = get_age_at_event($age, true);
			if ($type == 'list') {
				$top10[] = "<li><a href=\"" . $person->getHtmlUrl() . "\">" . $person->getFullName() . "</a> (" . $age . ")" . "</li>";
			} else {
				$top10[] = "<a href=\"" . $person->getHtmlUrl() . "\">" . $person->getFullName() . "</a> (" . $age . ")";
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		} else {
			$top10 = implode(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}
		return $top10;
	}

	/**
	 * @param string $sex
	 * @param bool   $show_years
	 *
	 * @return string
	 */
	private function averageLifespanQuery($sex = 'BOTH', $show_years = false) {
		if ($sex == 'F') {
			$sex_search = " AND i_sex='F' ";
		} elseif ($sex == 'M') {
			$sex_search = " AND i_sex='M' ";
		} else {
			$sex_search = '';
		}
		$rows = $this->runSql(
			"SELECT SQL_CACHE " .
			" AVG(death.d_julianday2-birth.d_julianday1) AS age " .
			"FROM " .
			" `##dates` AS death, " .
			" `##dates` AS birth, " .
			" `##individuals` AS indi " .
			"WHERE " .
			" indi.i_id=birth.d_gid AND " .
			" birth.d_gid=death.d_gid AND " .
			" death.d_file=" . $this->tree->tree_id . " AND " .
			" birth.d_file=death.d_file AND " .
			" birth.d_file=indi.i_file AND " .
			" birth.d_fact='BIRT' AND " .
			" death.d_fact='DEAT' AND " .
			" birth.d_julianday1<>0 AND " .
			" death.d_julianday1>birth.d_julianday2 " .
			$sex_search
		);
		if (!isset($rows[0])) {
			return '';
		}
		$row = $rows[0];
		$age = $row['age'];
		if ($show_years) {
			if ((int)($age / 365.25) > 0) {
				$age = (int)($age / 365.25) . 'y';
			} elseif ((int)($age / 30.4375) > 0) {
				$age = (int)($age / 30.4375) . 'm';
			} elseif (!empty($age)) {
				$age = $age . 'd';
			}
			return get_age_at_event($age, true);
		} else {
			return WT_I18N::number($age / 365.25);
		}
	}

	/**
	 * @param boolean  $simple
	 * @param string   $related
	 * @param string   $sex
	 * @param integer  $year1
	 * @param integer  $year2
	 * @param string[] $params
	 *
	 * @return array|string
	 */
	public function statsAgeQuery($simple = true, $related = 'BIRT', $sex = 'BOTH', $year1 = -1, $year2 = -1, $params = array()) {
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = '230x250';
			}
			$sizes = explode('x', $size);
			$rows = $this->runSql(
				"SELECT SQL_CACHE" .
				" ROUND(AVG(death.d_julianday2-birth.d_julianday1)/365.25,1) AS age," .
				" FLOOR(death.d_year/100+1) AS century," .
				" i_sex AS sex" .
				" FROM" .
				" `##dates` AS death," .
				" `##dates` AS birth," .
				" `##individuals` AS indi" .
				" WHERE" .
				" indi.i_id=birth.d_gid AND" .
				" birth.d_gid=death.d_gid AND" .
				" death.d_file={$this->tree->tree_id} AND" .
				" birth.d_file=death.d_file AND" .
				" birth.d_file=indi.i_file AND" .
				" birth.d_fact='BIRT' AND" .
				" death.d_fact='DEAT' AND" .
				" birth.d_julianday1<>0 AND" .
				" birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
				" death.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
				" death.d_julianday1>birth.d_julianday2" .
				" GROUP BY century, sex ORDER BY century, sex");
			if (empty($rows)) {
				return '';
			}
			$chxl = '0:|';
			$countsm = '';
			$countsf = '';
			$countsa = '';
			$out = array();
			foreach ($rows as $values) {
				$out[$values['century']][$values['sex']] = $values['age'];
			}
			foreach ($out as $century => $values) {
				if ($sizes[0] < 980) {
					$sizes[0] += 50;
				}
				$chxl .= $this->centuryName($century) . '|';
				$average = 0;
				if (isset($values['F'])) {
					$countsf .= $values['F'] . ',';
					$average = $values['F'];
				} else {
					$countsf .= '0,';
				}
				if (isset($values['M'])) {
					$countsm .= $values['M'] . ',';
					if ($average == 0) {
						$countsa .= $values['M'] . ',';
					} else {
						$countsa .= (($values['M'] + $average) / 2) . ',';
					}
				} else {
					$countsm .= '0,';
					if ($average == 0) {
						$countsa .= '0,';
					} else {
						$countsa .= $values['F'] . ',';
					}
				}
			}
			$countsm = substr($countsm, 0, -1);
			$countsf = substr($countsf, 0, -1);
			$countsa = substr($countsa, 0, -1);
			$chd = 't2:' . $countsm . '|' . $countsf . '|' . $countsa;
			$decades = '';
			for ($i = 0; $i <= 100; $i += 10) {
				$decades .= '|' . WT_I18N::number($i);
			}
			$chxl .= '1:||' . WT_I18N::translate('century') . '|2:' . $decades . '|3:||' . WT_I18N::translate('Age') . '|';
			$title = WT_I18N::translate('Average age related to death century');
			if (count($rows) > 6 || mb_strlen($title) < 30) {
				$chtt = $title;
			} else {
				$offset = 0;
				$counter = array();
				while ($offset = strpos($title, ' ', $offset + 1)) {
					$counter[] = $offset;
				}
				$half = (int)(count($counter) / 2);
				$chtt = substr_replace($title, '|', $counter[$half], 1);
			}
			return '<img src="' . "https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|N*f1*,000000,0,-1,11,1|N*f1*,000000,1,-1,11,1&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=" . rawurlencode($chtt) . "&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . "&amp;chdl=" . rawurlencode(WT_I18N::translate('Males') . '|' . WT_I18N::translate('Females') . '|' . WT_I18N::translate('Average age at death')) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Average age related to death century') . "\" title=\"" . WT_I18N::translate('Average age related to death century') . "\" />";
		} else {
			$sex_search = '';
			$years = '';
			if ($sex == 'F') {
				$sex_search = " AND i_sex='F'";
			} elseif ($sex == 'M') {
				$sex_search = " AND i_sex='M'";
			}
			if ($year1 >= 0 && $year2 >= 0) {
				if ($related == 'BIRT') {
					$years = " AND birth.d_year BETWEEN '{$year1}' AND '{$year2}'";
				} elseif ($related == 'DEAT') {
					$years = " AND death.d_year BETWEEN '{$year1}' AND '{$year2}'";
				}
			}
			$rows = $this->runSql(
				"SELECT SQL_CACHE" .
				" death.d_julianday2-birth.d_julianday1 AS age" .
				" FROM" .
				" `##dates` AS death," .
				" `##dates` AS birth," .
				" `##individuals` AS indi" .
				" WHERE" .
				" indi.i_id=birth.d_gid AND" .
				" birth.d_gid=death.d_gid AND" .
				" death.d_file={$this->tree->tree_id} AND" .
				" birth.d_file=death.d_file AND" .
				" birth.d_file=indi.i_file AND" .
				" birth.d_fact='BIRT' AND" .
				" death.d_fact='DEAT' AND" .
				" birth.d_julianday1<>0 AND" .
				" birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
				" death.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
				" death.d_julianday1>birth.d_julianday2" .
				$years .
				$sex_search .
				" ORDER BY age DESC");

			return $rows;
		}
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function statsAge($params = array()) {
		return $this->statsAgeQuery(true, 'BIRT', 'BOTH', -1, -1, $params);
	}

	/**
	 * @return string
	 */
	public function longestLife() {
		return $this->longlifeQuery('full', 'BOTH');
	}

	/**
	 * @return string
	 */
	public function longestLifeAge() {
		return $this->longlifeQuery('age', 'BOTH');
	}

	/**
	 * @return string
	 */
	public function longestLifeName() {
		return $this->longlifeQuery('name', 'BOTH');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldest($params = array()) {
		return $this->topTenOldestQuery('nolist', 'BOTH', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestList($params = array()) {
		return $this->topTenOldestQuery('list', 'BOTH', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestAlive($params = array()) {
		return $this->topTenOldestAliveQuery('nolist', 'BOTH', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestListAlive($params = array()) {
		return $this->topTenOldestAliveQuery('list', 'BOTH', $params);
	}

	/**
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	public function averageLifespan($show_years = false) {
		return $this->averageLifespanQuery('BOTH', $show_years);
	}

	/**
	 * @return string
	 */
	public function longestLifeFemale() {
		return $this->longlifeQuery('full', 'F');
	}

	/**
	 * @return string
	 */
	public function longestLifeFemaleAge() {
		return $this->longlifeQuery('age', 'F');
	}

	/**
	 * @return string
	 */
	public function longestLifeFemaleName() {
		return $this->longlifeQuery('name', 'F');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestFemale($params = array()) {
		return $this->topTenOldestQuery('nolist', 'F', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestFemaleList($params = array()) {
		return $this->topTenOldestQuery('list', 'F', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestFemaleAlive($params = array()) {
		return $this->topTenOldestAliveQuery('nolist', 'F', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestFemaleListAlive($params = array()) {
		return $this->topTenOldestAliveQuery('list', 'F', $params);
	}

	/**
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	public function averageLifespanFemale($show_years = false) {
		return $this->averageLifespanQuery('F', $show_years);
	}

	/**
	 * @return string
	 */
	public function longestLifeMale() {
		return $this->longlifeQuery('full', 'M');
	}

	/**
	 * @return string
	 */
	public function longestLifeMaleAge() {
		return $this->longlifeQuery('age', 'M');
	}

	/**
	 * @return string
	 */
	public function longestLifeMaleName() {
		return $this->longlifeQuery('name', 'M');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestMale($params = array()) {
		return $this->topTenOldestQuery('nolist', 'M', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestMaleList($params = array()) {
		return $this->topTenOldestQuery('list', 'M', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestMaleAlive($params = array()) {
		return $this->topTenOldestAliveQuery('nolist', 'M', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenOldestMaleListAlive($params = array()) {
		return $this->topTenOldestAliveQuery('list', 'M', $params);
	}

	/**
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	public function averageLifespanMale($show_years = false) {
		return $this->averageLifespanQuery('M', $show_years);
	}

	/**
	 * Events
	 *
	 * @param string $type
	 * @param string $direction
	 * @param string $facts
	 *
	 * @return string
	 */
	private function eventQuery($type, $direction, $facts) {
		$eventTypes = array(
			'BIRT' => WT_I18N::translate('birth'),
			'DEAT' => WT_I18N::translate('death'),
			'MARR' => WT_I18N::translate('marriage'),
			'ADOP' => WT_I18N::translate('adoption'),
			'BURI' => WT_I18N::translate('burial'),
			'CENS' => WT_I18N::translate('census added')
		);

		$fact_query = "IN ('" . str_replace('|', "','", $facts) . "')";

		if ($direction != 'ASC') {
			$direction = 'DESC';
		}
		$rows = $this->runSql(''
			. ' SELECT SQL_CACHE'
			. ' d_gid AS id,'
			. ' d_year AS year,'
			. ' d_fact AS fact,'
			. ' d_type AS type'
			. ' FROM'
			. " `##dates`"
			. ' WHERE'
			. " d_file={$this->tree->tree_id} AND"
			. " d_gid<>'HEAD' AND"
			. " d_fact {$fact_query} AND"
			. ' d_julianday1<>0'
			. ' ORDER BY'
			. " d_julianday1 {$direction}, d_type LIMIT 1"
		);
		if (!isset($rows[0])) {
			return '';
		}
		$row = $rows[0];
		$record = WT_GedcomRecord::getInstance($row['id']);
		switch ($type) {
		default:
		case 'full':
			if ($record->canShow()) {
				$result = $record->format_list('span', false, $record->getFullName());
			} else {
				$result = WT_I18N::translate('This information is private and cannot be shown.');
			}
			break;
		case 'year':
			$date = new WT_Date($row['type'] . ' ' . $row['year']);
			$result = $date->display();
			break;
		case 'type':
			if (isset($eventTypes[$row['fact']])) {
				$result = $eventTypes[$row['fact']];
			} else {
				$result = WT_Gedcom_Tag::getLabel($row['fact']);
			}
			break;
		case 'name':
			$result = "<a href=\"" . $record->getHtmlUrl() . "\">" . $record->getFullName() . "</a>";
			break;
		case 'place':
			$fact = $record->getFirstFact($row['fact']);
			if ($fact) {
				$result = format_fact_place($fact, true, true, true);
			} else {
				$result = WT_I18N::translate('Private');
			}
			break;
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function firstEvent() {
		return $this->eventQuery('full', 'ASC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function firstEventYear() {
		return $this->eventQuery('year', 'ASC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function firstEventType() {
		return $this->eventQuery('type', 'ASC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function firstEventName() {
		return $this->eventQuery('name', 'ASC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function firstEventPlace() {
		return $this->eventQuery('place', 'ASC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function lastEvent() {
		return $this->eventQuery('full', 'DESC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function lastEventYear() {
		return $this->eventQuery('year', 'DESC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function lastEventType() {
		return $this->eventQuery('type', 'DESC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function lastEventName() {
		return $this->eventQuery('name', 'DESC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * @return string
	 */
	public function lastEventPlace() {
		return $this->eventQuery('place', 'DESC', WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV . '|' . WT_EVENTS_DEAT);
	}

	/**
	 * Query the database for marriage tags.
	 *
	 * @param string  $type
	 * @param string  $age_dir
	 * @param string  $sex
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	private function marriageQuery($type = 'full', $age_dir = 'ASC', $sex = 'F', $show_years = false) {
		if ($sex == 'F') {
			$sex_field = 'f_wife';
		} else {
			$sex_field = 'f_husb';
		}
		if ($age_dir != 'ASC') {
			$age_dir = 'DESC';
		}
		$rows = $this->runSql(
			" SELECT SQL_CACHE fam.f_id AS famid, fam.{$sex_field}, married.d_julianday2-birth.d_julianday1 AS age, indi.i_id AS i_id" .
			" FROM `##families` AS fam" .
			" LEFT JOIN `##dates` AS birth ON birth.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->tree_id}" .
			" WHERE" .
			" birth.d_gid = indi.i_id AND" .
			" married.d_gid = fam.f_id AND" .
			" indi.i_id = fam.{$sex_field} AND" .
			" fam.f_file = {$this->tree->tree_id} AND" .
			" birth.d_fact = 'BIRT' AND" .
			" married.d_fact = 'MARR' AND" .
			" birth.d_julianday1 <> 0 AND" .
			" married.d_julianday2 > birth.d_julianday1 AND" .
			" i_sex='{$sex}'" .
			" ORDER BY" .
			" married.d_julianday2-birth.d_julianday1 {$age_dir} LIMIT 1"
		);
		if (!isset($rows[0])) {
			return '';
		}
		$row = $rows[0];
		if (isset($row['famid'])) {
			$family = WT_Family::getInstance($row['famid']);
		}
		if (isset($row['i_id'])) {
			$person = WT_Individual::getInstance($row['i_id']);
		}
		switch ($type) {
		default:
		case 'full':
			if ($family->canShow()) {
				$result = $family->format_list('span', false, $person->getFullName());
			} else {
				$result = WT_I18N::translate('This information is private and cannot be shown.');
			}
			break;
		case 'name':
			$result = '<a href="' . $family->getHtmlUrl() . '">' . $person->getFullName() . '</a>';
			break;
		case 'age':
			$age = $row['age'];
			if ($show_years) {
				if ((int)($age / 365.25) > 0) {
					$age = (int)($age / 365.25) . 'y';
				} elseif ((int)($age / 30.4375) > 0) {
					$age = (int)($age / 30.4375) . 'm';
				} else {
					$age = $age . 'd';
				}
				$result = get_age_at_event($age, true);
			} else {
				$result = WT_I18N::number((int)($age / 365.25));
			}
			break;
		}
		return $result;
	}

	/**
	 * @param string   $type
	 * @param string   $age_dir
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function ageOfMarriageQuery($type = 'list', $age_dir = 'ASC', $params = array()) {
		global $TEXT_DIRECTION;

		if (isset($params[0])) {
			$total = (int)$params[0];
		} else {
			$total = 10;
		}
		if ($age_dir != 'ASC') {
			$age_dir = 'DESC';
		}
		$hrows = $this->runSql(
			" SELECT SQL_CACHE DISTINCT fam.f_id AS family, MIN(husbdeath.d_julianday2-married.d_julianday1) AS age" .
			" FROM `##families` AS fam" .
			" LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##dates` AS husbdeath ON husbdeath.d_file = {$this->tree->tree_id}" .
			" WHERE" .
			" fam.f_file = {$this->tree->tree_id} AND" .
			" husbdeath.d_gid = fam.f_husb AND" .
			" husbdeath.d_fact = 'DEAT' AND" .
			" married.d_gid = fam.f_id AND" .
			" married.d_fact = 'MARR' AND" .
			" married.d_julianday1 < husbdeath.d_julianday2 AND" .
			" married.d_julianday1 <> 0" .
			" GROUP BY family" .
			" ORDER BY age {$age_dir}");
		$wrows = $this->runSql(
			" SELECT SQL_CACHE DISTINCT fam.f_id AS family, MIN(wifedeath.d_julianday2-married.d_julianday1) AS age" .
			" FROM `##families` AS fam" .
			" LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##dates` AS wifedeath ON wifedeath.d_file = {$this->tree->tree_id}" .
			" WHERE" .
			" fam.f_file = {$this->tree->tree_id} AND" .
			" wifedeath.d_gid = fam.f_wife AND" .
			" wifedeath.d_fact = 'DEAT' AND" .
			" married.d_gid = fam.f_id AND" .
			" married.d_fact = 'MARR' AND" .
			" married.d_julianday1 < wifedeath.d_julianday2 AND" .
			" married.d_julianday1 <> 0" .
			" GROUP BY family" .
			" ORDER BY age {$age_dir}");
		$drows = $this->runSql(
			" SELECT SQL_CACHE DISTINCT fam.f_id AS family, MIN(divorced.d_julianday2-married.d_julianday1) AS age" .
			" FROM `##families` AS fam" .
			" LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##dates` AS divorced ON divorced.d_file = {$this->tree->tree_id}" .
			" WHERE" .
			" fam.f_file = {$this->tree->tree_id} AND" .
			" married.d_gid = fam.f_id AND" .
			" married.d_fact = 'MARR' AND" .
			" divorced.d_gid = fam.f_id AND" .
			" divorced.d_fact IN ('DIV', 'ANUL', '_SEPR', '_DETS') AND" .
			" married.d_julianday1 < divorced.d_julianday2 AND" .
			" married.d_julianday1 <> 0" .
			" GROUP BY family" .
			" ORDER BY age {$age_dir}");
		if (!isset($hrows) && !isset($wrows) && !isset($drows)) {
			return '';
		}
		$rows = array();
		foreach ($drows as $family) {
			$rows[$family['family']] = $family['age'];
		}
		foreach ($hrows as $family) {
			if (!isset($rows[$family['family']])) {
				$rows[$family['family']] = $family['age'];
			}
		}
		foreach ($wrows as $family) {
			if (!isset($rows[$family['family']])) {
				$rows[$family['family']] = $family['age'];
			} elseif ($rows[$family['family']] > $family['age']) {
				$rows[$family['family']] = $family['age'];
			}
		}
		if ($age_dir == 'DESC') {
			arsort($rows);
		} else {
			asort($rows);
		}
		$top10 = array();
		$i = 0;
		foreach ($rows as $fam => $age) {
			$family = WT_Family::getInstance($fam);
			if ($type == 'name') {
				return $family->format_list('span', false, $family->getFullName());
			}
			if ((int)($age / 365.25) > 0) {
				$age = (int)($age / 365.25) . 'y';
			} elseif ((int)($age / 30.4375) > 0) {
				$age = (int)($age / 30.4375) . 'm';
			} else {
				$age = $age . 'd';
			}
			$age = get_age_at_event($age, true);
			if ($type == 'age') {
				return $age;
			}
			$husb = $family->getHusband();
			$wife = $family->getWife();
			if (($husb->getAllDeathDates() && $wife->getAllDeathDates()) || !$husb->isDead() || !$wife->isDead()) {
				if ($family->canShow()) {
					if ($type == 'list') {
						$top10[] = "<li><a href=\"" . $family->getHtmlUrl() . "\">" . $family->getFullName() . "</a> (" . $age . ")" . "</li>";
					} else {
						$top10[] = "<a href=\"" . $family->getHtmlUrl() . "\">" . $family->getFullName() . "</a> (" . $age . ")";
					}
				}
				if (++$i == $total) {
					break;
				}
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		} else {
			$top10 = implode(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}
		return $top10;
	}

	/**
	 * @param string   $type
	 * @param string   $age_dir
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function ageBetweenSpousesQuery($type = 'list', $age_dir = 'DESC', $params = array()) {
		global $TEXT_DIRECTION;

		if (isset($params[0])) {
			$total = (int)$params[0];
		} else {
			$total = 10;
		}
		if ($age_dir == 'DESC') {
			$query1 = ' MIN(wifebirth.d_julianday2-husbbirth.d_julianday1) AS age';
			$query2 = ' wifebirth.d_julianday2 >= husbbirth.d_julianday1 AND husbbirth.d_julianday1 <> 0';
		} else {
			$query1 = ' MIN(husbbirth.d_julianday2-wifebirth.d_julianday1) AS age';
			$query2 = ' wifebirth.d_julianday1 < husbbirth.d_julianday2 AND wifebirth.d_julianday1 <> 0';
		}
		$rows = $this->runSql(
			" SELECT SQL_CACHE fam.f_id AS family," . $query1 .
			" FROM `##families` AS fam" .
			" LEFT JOIN `##dates` AS wifebirth ON wifebirth.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##dates` AS husbbirth ON husbbirth.d_file = {$this->tree->tree_id}" .
			" WHERE" .
			" fam.f_file = {$this->tree->tree_id} AND" .
			" husbbirth.d_gid = fam.f_husb AND" .
			" husbbirth.d_fact = 'BIRT' AND" .
			" wifebirth.d_gid = fam.f_wife AND" .
			" wifebirth.d_fact = 'BIRT' AND" .
			$query2 .
			" GROUP BY family" .
			" ORDER BY age DESC LIMIT " . $total
		);
		if (!isset($rows[0])) {
			return '';
		}
		$top10 = array();
		foreach ($rows as $fam) {
			$family = WT_Family::getInstance($fam['family']);
			if ($fam['age'] < 0) {
				break;
			}
			$age = $fam['age'];
			if ((int)($age / 365.25) > 0) {
				$age = (int)($age / 365.25) . 'y';
			} elseif ((int)($age / 30.4375) > 0) {
				$age = (int)($age / 30.4375) . 'm';
			} else {
				$age = $age . 'd';
			}
			$age = get_age_at_event($age, true);
			if ($family->canShow()) {
				if ($type == 'list') {
					$top10[] = "<li><a href=\"" . $family->getHtmlUrl() . "\">" . $family->getFullName() . "</a> (" . $age . ")" . "</li>";
				} else {
					$top10[] = "<a href=\"" . $family->getHtmlUrl() . "\">" . $family->getFullName() . "</a> (" . $age . ")";
				}
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		} else {
			$top10 = implode(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}
		return $top10;
	}

	/**
	 * @param string  $type
	 * @param string  $age_dir
	 * @param string  $sex
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	private function parentsQuery($type = 'full', $age_dir = 'ASC', $sex = 'F', $show_years = false) {
		if ($sex == 'F') {
			$sex_field = 'WIFE';
		} else {
			$sex_field = 'HUSB';
		}
		if ($age_dir != 'ASC') {
			$age_dir = 'DESC';
		}
		$rows = $this->runSql(
			" SELECT SQL_CACHE" .
			" parentfamily.l_to AS id," .
			" childbirth.d_julianday2-birth.d_julianday1 AS age" .
			" FROM `##link` AS parentfamily" .
			" JOIN `##link` AS childfamily ON childfamily.l_file = {$this->tree->tree_id}" .
			" JOIN `##dates` AS birth ON birth.d_file = {$this->tree->tree_id}" .
			" JOIN `##dates` AS childbirth ON childbirth.d_file = {$this->tree->tree_id}" .
			" WHERE" .
			" birth.d_gid = parentfamily.l_to AND" .
			" childfamily.l_to = childbirth.d_gid AND" .
			" childfamily.l_type = 'CHIL' AND" .
			" parentfamily.l_type = '{$sex_field}' AND" .
			" childfamily.l_from = parentfamily.l_from AND" .
			" parentfamily.l_file = {$this->tree->tree_id} AND" .
			" birth.d_fact = 'BIRT' AND" .
			" childbirth.d_fact = 'BIRT' AND" .
			" birth.d_julianday1 <> 0 AND" .
			" childbirth.d_julianday2 > birth.d_julianday1" .
			" ORDER BY age {$age_dir} LIMIT 1"
		);
		if (!isset($rows[0])) {
			return '';
		}
		$row = $rows[0];
		if (isset($row['id'])) {
			$person = WT_Individual::getInstance($row['id']);
		}
		switch ($type) {
		default:
		case 'full':
			if ($person->canShow()) {
				$result = $person->format_list('span', false, $person->getFullName());
			} else {
				$result = WT_I18N::translate('This information is private and cannot be shown.');
			}
			break;
		case 'name':
			$result = '<a href="' . $person->getHtmlUrl() . '">' . $person->getFullName() . '</a>';
			break;
		case 'age':
			$age = $row['age'];
			if ($show_years) {
				if ((int)($age / 365.25) > 0) {
					$age = (int)($age / 365.25) . 'y';
				} elseif ((int)($age / 30.4375) > 0) {
					$age = (int)($age / 30.4375) . 'm';
				} else {
					$age = $age . 'd';
				}
				$result = get_age_at_event($age, true);
			} else {
				$result = (int)($age / 365.25);
			}
			break;
		}
		return $result;
	}

	/**
	 * @param boolean  $simple
	 * @param boolean  $first
	 * @param integer  $year1
	 * @param integer  $year2
	 * @param string[] $params
	 *
	 * @return string|array
	 */
	public function statsMarrQuery($simple = true, $first = false, $year1 = -1, $year2 = -1, $params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql =
				"SELECT SQL_CACHE FLOOR(d_year/100+1) AS century, COUNT(*) AS total" .
				" FROM `##dates`" .
				" WHERE d_file={$this->tree->tree_id} AND d_year<>0 AND d_fact='MARR' AND d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
			if ($year1 >= 0 && $year2 >= 0) {
				$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
			}
			$sql .= " GROUP BY century ORDER BY century";
		} elseif ($first) {
			$years = '';
			if ($year1 >= 0 && $year2 >= 0) {
				$years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
			}
			$sql =
				" SELECT SQL_CACHE fam.f_id AS fams, fam.f_husb, fam.f_wife, married.d_julianday2 AS age, married.d_month AS month, indi.i_id AS indi" .
				" FROM `##families` AS fam" .
				" LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->tree_id}" .
				" LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->tree_id}" .
				" WHERE" .
				" married.d_gid = fam.f_id AND" .
				" fam.f_file = {$this->tree->tree_id} AND" .
				" married.d_fact = 'MARR' AND" .
				" married.d_julianday2 <> 0 AND" .
				$years .
				" (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)" .
				" ORDER BY fams, indi, age ASC";
		} else {
			$sql =
				"SELECT SQL_CACHE d_month, COUNT(*) AS total" .
				" FROM `##dates`" .
				" WHERE d_file={$this->tree->tree_id} AND d_fact='MARR'";
			if ($year1 >= 0 && $year2 >= 0) {
				$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
			}
			$sql .= " GROUP BY d_month";
		}
		$rows = $this->runSql($sql);
		if (!isset($rows)) {
			return '';
		}
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
			}
			if (isset($params[1]) && $params[1] != '') {
				$color_from = strtolower($params[1]);
			} else {
				$color_from = $WT_STATS_CHART_COLOR1;
			}
			if (isset($params[2]) && $params[2] != '') {
				$color_to = strtolower($params[2]);
			} else {
				$color_to = $WT_STATS_CHART_COLOR2;
			}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot == 0) {
				return '';
			}
			$centuries = "";
			$counts = array();
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $this->centuryName($values['century']) . ' - ' . WT_I18N::number($values['total']) . '|';
			}
			$chd = $this->arrayToExtendedEncoding($counts);
			$chl = substr($centuries, 0, -1);
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Marriages by century') . "\" title=\"" . WT_I18N::translate('Marriages by century') . "\" />";
		}
		return $rows;
	}

	/**
	 * @param boolean  $simple
	 * @param boolean  $first
	 * @param integer  $year1
	 * @param integer  $year2
	 * @param string[] $params
	 *
	 * @return string|array
	 */
	private function statsDivQuery($simple = true, $first = false, $year1 = -1, $year2 = -1, $params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if ($simple) {
			$sql =
				"SELECT SQL_CACHE FLOOR(d_year/100+1) AS century, COUNT(*) AS total" .
				" FROM `##dates`" .
				" WHERE d_file={$this->tree->tree_id} AND d_year<>0 AND d_fact = 'DIV' AND d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";
			if ($year1 >= 0 && $year2 >= 0) {
				$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
			}
			$sql .= " GROUP BY century ORDER BY century";
		} elseif ($first) {
			$years = '';
			if ($year1 >= 0 && $year2 >= 0) {
				$years = " divorced.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
			}
			$sql =
				" SELECT SQL_CACHE fam.f_id AS fams, fam.f_husb, fam.f_wife, divorced.d_julianday2 AS age, divorced.d_month AS month, indi.i_id AS indi" .
				" FROM `##families` AS fam" .
				" LEFT JOIN `##dates` AS divorced ON divorced.d_file = {$this->tree->tree_id}" .
				" LEFT JOIN `##individuals` AS indi ON indi.i_file = {$this->tree->tree_id}" .
				" WHERE" .
				" divorced.d_gid = fam.f_id AND" .
				" fam.f_file = {$this->tree->tree_id} AND" .
				" divorced.d_fact = 'DIV' AND" .
				" divorced.d_julianday2 <> 0 AND" .
				$years .
				" (indi.i_id = fam.f_husb OR indi.i_id = fam.f_wife)" .
				" ORDER BY fams, indi, age ASC";
		} else {
			$sql =
				"SELECT SQL_CACHE d_month, COUNT(*) AS total FROM `##dates` " .
				"WHERE d_file={$this->tree->tree_id} AND d_fact = 'DIV'";
			if ($year1 >= 0 && $year2 >= 0) {
				$sql .= " AND d_year BETWEEN '{$year1}' AND '{$year2}'";
			}
			$sql .= " GROUP BY d_month";
		}
		$rows = $this->runSql($sql);
		if (!isset($rows)) {
			return '';
		}
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
			}
			if (isset($params[1]) && $params[1] != '') {
				$color_from = strtolower($params[1]);
			} else {
				$color_from = $WT_STATS_CHART_COLOR1;
			}
			if (isset($params[2]) && $params[2] != '') {
				$color_to = strtolower($params[2]);
			} else {
				$color_to = $WT_STATS_CHART_COLOR2;
			}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot == 0) {
				return '';
			}
			$centuries = "";
			$counts = array();
			foreach ($rows as $values) {
				$counts[] = round(100 * $values['total'] / $tot, 0);
				$centuries .= $this->centuryName($values['century']) . ' - ' . WT_I18N::number($values['total']) . '|';
			}
			$chd = $this->arrayToExtendedEncoding($counts);
			$chl = substr($centuries, 0, -1);
			return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Divorces by century') . "\" title=\"" . WT_I18N::translate('Divorces by century') . "\" />";
		}
		return $rows;
	}

	/**
	 * @return string
	 */
	public function firstMarriage() {
		return $this->mortalityQuery('full', 'ASC', 'MARR');
	}

	/**
	 * @return string
	 */
	public function firstMarriageYear() {
		return $this->mortalityQuery('year', 'ASC', 'MARR');
	}

	/**
	 * @return string
	 */
	public function firstMarriageName() {
		return $this->mortalityQuery('name', 'ASC', 'MARR');
	}

	/**
	 * @return string
	 */
	public function firstMarriagePlace() {
		return $this->mortalityQuery('place', 'ASC', 'MARR');
	}

	/**
	 * @return string
	 */
	public function lastMarriage() {
		return $this->mortalityQuery('full', 'DESC', 'MARR');
	}

	/**
	 * @return string
	 */
	public function lastMarriageYear() {
		return $this->mortalityQuery('year', 'DESC', 'MARR');
	}

	/**
	 * @return string
	 */
	public function lastMarriageName() {
		return $this->mortalityQuery('name', 'DESC', 'MARR');
	}

	/**
	 * @return string
	 */
	public function lastMarriagePlace() {
		return $this->mortalityQuery('place', 'DESC', 'MARR');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function statsMarr($params = array()) {
		return $this->statsMarrQuery(true, false, -1, -1, $params);
	}

	/**
	 * @return string
	 */
	public function firstDivorce() {
		return $this->mortalityQuery('full', 'ASC', 'DIV');
	}

	/**
	 * @return string
	 */
	public function firstDivorceYear() {
		return $this->mortalityQuery('year', 'ASC', 'DIV');
	}

	/**
	 * @return string
	 */
	public function firstDivorceName() {
		return $this->mortalityQuery('name', 'ASC', 'DIV');
	}

	/**
	 * @return string
	 */
	public function firstDivorcePlace() {
		return $this->mortalityQuery('place', 'ASC', 'DIV');
	}

	/**
	 * @return string
	 */
	public function lastDivorce() {
		return $this->mortalityQuery('full', 'DESC', 'DIV');
	}

	/**
	 * @return string
	 */
	public function lastDivorceYear() {
		return $this->mortalityQuery('year', 'DESC', 'DIV');
	}

	/**
	 * @return string
	 */
	public function lastDivorceName() {
		return $this->mortalityQuery('name', 'DESC', 'DIV');
	}

	/**
	 * @return string
	 */
	public function lastDivorcePlace() {
		return $this->mortalityQuery('place', 'DESC', 'DIV');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function statsDiv($params = array()) {
		return $this->statsDivQuery(true, false, -1, -1, $params);
	}

	/**
	 * @param boolean  $simple
	 * @param string   $sex
	 * @param integer  $year1
	 * @param integer  $year2
	 * @param string[] $params
	 *
	 * @return array|string
	 */
	public function statsMarrAgeQuery($simple = true, $sex = 'M', $year1 = -1, $year2 = -1, $params = array()) {
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = '200x250';
			}
			$sizes = explode('x', $size);
			$rows = $this->runSql(
				"SELECT SQL_CACHE " .
				" ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, " .
				" FLOOR(married.d_year/100+1) AS century, " .
				" 'M' AS sex " .
				"FROM `##dates` AS married " .
				"JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
				"JOIN `##dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) " .
				"WHERE " .
				" '{$sex}' IN ('M', 'BOTH') AND " .
				" married.d_file={$this->tree->tree_id} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
				" birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
				"GROUP BY century, sex " .
				"UNION ALL " .
				"SELECT " .
				" ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, " .
				" FLOOR(married.d_year/100+1) AS century, " .
				" 'F' AS sex " .
				"FROM `##dates` AS married " .
				"JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
				"JOIN `##dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) " .
				"WHERE " .
				" '{$sex}' IN ('F', 'BOTH') AND " .
				" married.d_file={$this->tree->tree_id} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
				" birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
				" GROUP BY century, sex ORDER BY century"
			);
			if (empty($rows)) {
				return '';
			}
			$max = 0;
			foreach ($rows as $values) {
				if ($max < $values['age']) {
					$max = $values['age'];
				}
			}
			$chxl = '0:|';
			$chmm = '';
			$chmf = '';
			$i = 0;
			$countsm = '';
			$countsf = '';
			$countsa = '';
			$out = array();
			foreach ($rows as $values) {
				$out[$values['century']][$values['sex']] = $values['age'];
			}
			foreach ($out as $century => $values) {
				if ($sizes[0] < 1000) {
					$sizes[0] += 50;
				}
				$chxl .= $this->centuryName($century) . '|';
				$average = 0;
				if (isset($values['F'])) {
					if ($max <= 50) {
						$value = $values['F'] * 2;
					} else {
						$value = $values['F'];
					}
					$countsf .= $value . ',';
					$average = $value;
					$chmf .= 't' . $values['F'] . ',000000,1,' . $i . ',11,1|';
				} else {
					$countsf .= '0,';
					$chmf .= 't0,000000,1,' . $i . ',11,1|';
				}
				if (isset($values['M'])) {
					if ($max <= 50) {
						$value = $values['M'] * 2;
					} else {
						$value = $values['M'];
					}
					$countsm .= $value . ',';
					if ($average == 0) {
						$countsa .= $value . ',';
					} else {
						$countsa .= (($value + $average) / 2) . ',';
					}
					$chmm .= 't' . $values['M'] . ',000000,0,' . $i . ',11,1|';
				} else {
					$countsm .= '0,';
					if ($average == 0) {
						$countsa .= '0,';
					} else {
						$countsa .= $value . ',';
					}
					$chmm .= 't0,000000,0,' . $i . ',11,1|';
				}
				$i++;
			}
			$countsm = substr($countsm, 0, -1);
			$countsf = substr($countsf, 0, -1);
			$countsa = substr($countsa, 0, -1);
			$chmf = substr($chmf, 0, -1);
			$chd = 't2:' . $countsm . '|' . $countsf . '|' . $countsa;
			if ($max <= 50) {
				$chxl .= '1:||' . WT_I18N::translate('century') . '|2:|0|10|20|30|40|50|3:||' . WT_I18N::translate('Age') . '|';
			} else {
				$chxl .= '1:||' . WT_I18N::translate('century') . '|2:|0|10|20|30|40|50|60|70|80|90|100|3:||' . WT_I18N::translate('Age') . '|';
			}
			if (count($rows) > 4 || mb_strlen(WT_I18N::translate('Average age in century of marriage')) < 30) {
				$chtt = WT_I18N::translate('Average age in century of marriage');
			} else {
				$offset = 0;
				$counter = array();
				while ($offset = strpos(WT_I18N::translate('Average age in century of marriage'), ' ', $offset + 1)) {
					$counter[] = $offset;
				}
				$half = (int)(count($counter) / 2);
				$chtt = substr_replace(WT_I18N::translate('Average age in century of marriage'), '|', $counter[$half], 1);
			}
			return "<img src=\"" . "https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|{$chmm}{$chmf}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=" . rawurlencode($chtt) . "&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . "&amp;chdl=" . rawurlencode(WT_I18N::translate('Males') . "|" . WT_I18N::translate('Females') . "|" . WT_I18N::translate('Average age')) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Average age in century of marriage') . "\" title=\"" . WT_I18N::translate('Average age in century of marriage') . "\" />";
		} else {
			if ($year1 >= 0 && $year2 >= 0) {
				$years = " married.d_year BETWEEN {$year1} AND {$year2} AND ";
			} else {
				$years = '';
			}
			$rows = $this->runSql(
				"SELECT SQL_CACHE " .
				" fam.f_id, " .
				" birth.d_gid, " .
				" married.d_julianday2-birth.d_julianday1 AS age " .
				"FROM `##dates` AS married " .
				"JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
				"JOIN `##dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) " .
				"WHERE " .
				" '{$sex}' IN ('M', 'BOTH') AND {$years} " .
				" married.d_file={$this->tree->tree_id} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
				" birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
				"UNION ALL " .
				"SELECT " .
				" fam.f_id, " .
				" birth.d_gid, " .
				" married.d_julianday2-birth.d_julianday1 AS age " .
				"FROM `##dates` AS married " .
				"JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
				"JOIN `##dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) " .
				"WHERE " .
				" '{$sex}' IN ('F', 'BOTH') AND {$years} " .
				" married.d_file={$this->tree->tree_id} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
				" birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
				" married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 "
			);
			return $rows;
		}
	}

	/**
	 * @return string
	 */
	public function youngestMarriageFemale() {
		return $this->marriageQuery('full', 'ASC', 'F', false);
	}

	/**
	 * @return string
	 */
	public function youngestMarriageFemaleName() {
		return $this->marriageQuery('name', 'ASC', 'F', false);
	}

	/**
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	public function youngestMarriageFemaleAge($show_years = false) {
		return $this->marriageQuery('age', 'ASC', 'F', $show_years);
	}

	/**
	 * @return string
	 */
	public function oldestMarriageFemale() {
		return $this->marriageQuery('full', 'DESC', 'F', false);
	}

	/**
	 * @return string
	 */
	public function oldestMarriageFemaleName() {
		return $this->marriageQuery('name', 'DESC', 'F', false);
	}

	/**
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	public function oldestMarriageFemaleAge($show_years = false) {
		return $this->marriageQuery('age', 'DESC', 'F', $show_years);
	}

	/**
	 * @return string
	 */
	public function youngestMarriageMale() {
		return $this->marriageQuery('full', 'ASC', 'M', false);
	}

	/**
	 * @return string
	 */
	public function youngestMarriageMaleName() {
		return $this->marriageQuery('name', 'ASC', 'M', false);
	}

	/**
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	public function youngestMarriageMaleAge($show_years = false) {
		return $this->marriageQuery('age', 'ASC', 'M', $show_years);
	}

	/**
	 * @return string
	 */
	public function oldestMarriageMale() {
		return $this->marriageQuery('full', 'DESC', 'M', false);
	}

	/**
	 * @return string
	 */
	public function oldestMarriageMaleName() {
		return $this->marriageQuery('name', 'DESC', 'M', false);
	}

	/**
	 * @param boolean $show_years
	 *
	 * @return string
	 */
	public function oldestMarriageMaleAge($show_years = false) {
		return $this->marriageQuery('age', 'DESC', 'M', $show_years);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function statsMarrAge($params = array()) {
		return $this->statsMarrAgeQuery(true, 'BOTH', -1, -1, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function ageBetweenSpousesMF($params = array()) {
		return $this->ageBetweenSpousesQuery($type = 'nolist', $age_dir = 'DESC', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function ageBetweenSpousesMFList($params = array()) {
		return $this->ageBetweenSpousesQuery($type = 'list', $age_dir = 'DESC', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function ageBetweenSpousesFM($params = array()) {
		return $this->ageBetweenSpousesQuery($type = 'nolist', $age_dir = 'ASC', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function ageBetweenSpousesFMList($params = array()) {
		return $this->ageBetweenSpousesQuery($type = 'list', $age_dir = 'ASC', $params);
	}

	/**
	 * @return string
	 */
	public function topAgeOfMarriageFamily() {
		return $this->ageOfMarriageQuery('name', 'DESC', array('1'));
	}

	/**
	 * @return string
	 */
	public function topAgeOfMarriage() {
		return $this->ageOfMarriageQuery('age', 'DESC', array('1'));
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topAgeOfMarriageFamilies($params = array()) {
		return $this->ageOfMarriageQuery('nolist', 'DESC', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topAgeOfMarriageFamiliesList($params = array()) {
		return $this->ageOfMarriageQuery('list', 'DESC', $params);
	}

	/**
	 * @return string
	 */
	public function minAgeOfMarriageFamily() {
		return $this->ageOfMarriageQuery('name', 'ASC', array('1'));
	}

	/**
	 * @return string
	 */
	public function minAgeOfMarriage() {
		return $this->ageOfMarriageQuery('age', 'ASC', array('1'));
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function minAgeOfMarriageFamilies($params = array()) {
		return $this->ageOfMarriageQuery('nolist', 'ASC', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function minAgeOfMarriageFamiliesList($params = array()) {
		return $this->ageOfMarriageQuery('list', 'ASC', $params);
	}

	/**
	 * @return string
	 */
	public function youngestMother() {
		return $this->parentsQuery('full', 'ASC', 'F');
	}

	/**
	 * @return string
	 */
	public function youngestMotherName() {
		return $this->parentsQuery('name', 'ASC', 'F');
	}

	/**
	 * @param bool $show_years
	 *
	 * @return string
	 */
	public function youngestMotherAge($show_years = false) {
		return $this->parentsQuery('age', 'ASC', 'F', $show_years);
	}

	/**
	 * @return string
	 */
	public function oldestMother() {
		return $this->parentsQuery('full', 'DESC', 'F');
	}

	/**
	 * @return string
	 */
	public function oldestMotherName() {
		return $this->parentsQuery('name', 'DESC', 'F');
	}

	/**
	 * @param bool $show_years
	 *
	 * @return string
	 */
	public function oldestMotherAge($show_years = false) {
		return $this->parentsQuery('age', 'DESC', 'F', $show_years);
	}

	/**
	 * @return string
	 */
	public function youngestFather() {
		return $this->parentsQuery('full', 'ASC', 'M');
	}

	/**
	 * @return string
	 */
	public function youngestFatherName() {
		return $this->parentsQuery('name', 'ASC', 'M');
	}

	/**
	 * @param bool $show_years
	 *
	 * @return string
	 */
	public function youngestFatherAge($show_years = false) {
		return $this->parentsQuery('age', 'ASC', 'M', $show_years);
	}

	/**
	 * @return string
	 */
	public function oldestFather() {
		return $this->parentsQuery('full', 'DESC', 'M');
	}

	/**
	 * @return string
	 */
	public function oldestFatherName() {
		return $this->parentsQuery('name', 'DESC', 'M');
	}

	/**
	 * @param bool $show_years
	 *
	 * @return string
	 */
	public function oldestFatherAge($show_years = false) {
		return $this->parentsQuery('age', 'DESC', 'M', $show_years);
	}

	/**
	 * @return string
	 */
	public function totalMarriedMales() {
		$n = WT_DB::prepare("SELECT SQL_CACHE COUNT(DISTINCT f_husb) FROM `##families` WHERE f_file=? AND f_gedcom LIKE '%\\n1 MARR%'")
			->execute(array($this->tree->tree_id))
			->fetchOne();
		return WT_I18N::number($n);
	}

	/**
	 * @return string
	 */
	public function totalMarriedFemales() {
		$n = WT_DB::prepare("SELECT SQL_CACHE COUNT(DISTINCT f_wife) FROM `##families` WHERE f_file=? AND f_gedcom LIKE '%\\n1 MARR%'")
			->execute(array($this->tree->tree_id))
			->fetchOne();
		return WT_I18N::number($n);
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	private function familyQuery($type = 'full') {
		$rows = $this->runSql(
			" SELECT SQL_CACHE f_numchil AS tot, f_id AS id" .
			" FROM `##families`" .
			" WHERE" .
			" f_file={$this->tree->tree_id}" .
			" AND f_numchil = (" .
			"  SELECT max( f_numchil )" .
			"  FROM `##families`" .
			"  WHERE f_file ={$this->tree->tree_id}" .
			" )" .
			" LIMIT 1"
		);
		if (!isset($rows[0])) {
			return '';
		}
		$row = $rows[0];
		$family = WT_Family::getInstance($row['id']);
		switch ($type) {
		default:
		case 'full':
			if ($family->canShow()) {
				$result = $family->format_list('span', false, $family->getFullName());
			} else {
				$result = WT_I18N::translate('This information is private and cannot be shown.');
			}
			break;
		case 'size':
			$result = WT_I18N::number($row['tot']);
			break;
		case 'name':
			$result = "<a href=\"" . $family->getHtmlUrl() . "\">" . $family->getFullName() . '</a>';
			break;
		}
		return $result;
	}

	/**
	 * @param string   $type
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function topTenFamilyQuery($type = 'list', $params = array()) {
		global $TEXT_DIRECTION;

		if (isset($params[0])) {
			$total = (int)$params[0];
		} else {
			$total = 10;
		}
		$rows = $this->runSql(
			"SELECT SQL_CACHE f_numchil AS tot, f_id AS id" .
			" FROM `##families`" .
			" WHERE" .
			" f_file={$this->tree->tree_id}" .
			" ORDER BY tot DESC" .
			" LIMIT " . $total
		);
		if (!isset($rows[0])) {
			return '';
		}
		if (count($rows) < $total) {
			$total = count($rows);
		}
		$top10 = array();
		for ($c = 0; $c < $total; $c++) {
			$family = WT_Family::getInstance($rows[$c]['id']);
			if ($family->canShow()) {
				if ($type == 'list') {
					$top10[] =
						'<li><a href="' . $family->getHtmlUrl() . '">' . $family->getFullName() . '</a> - ' .
						WT_I18N::plural('%s child', '%s children', $rows[$c]['tot'], WT_I18N::number($rows[$c]['tot']));
				} else {
					$top10[] =
						'<a href="' . $family->getHtmlUrl() . '">' . $family->getFullName() . '</a> - ' .
						WT_I18N::plural('%s child', '%s children', $rows[$c]['tot'], WT_I18N::number($rows[$c]['tot']));
				}
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		} else {
			$top10 = implode(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}
		return $top10;
	}

	/**
	 * @param string   $type
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function ageBetweenSiblingsQuery($type = 'list', $params = array()) {
		global $TEXT_DIRECTION;

		if (isset($params[0])) {
			$total = (int)$params[0];
		} else {
			$total = 10;
		}
		if (isset($params[1])) {
			$one = $params[1];
		} else {
			$one = false;
		} // each family only once if true
		$rows = $this->runSql(
			" SELECT SQL_CACHE DISTINCT" .
			" link1.l_from AS family," .
			" link1.l_to AS ch1," .
			" link2.l_to AS ch2," .
			" child1.d_julianday2-child2.d_julianday2 AS age" .
			" FROM `##link` AS link1" .
			" LEFT JOIN `##dates` AS child1 ON child1.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##dates` AS child2 ON child2.d_file = {$this->tree->tree_id}" .
			" LEFT JOIN `##link` AS link2 ON link2.l_file = {$this->tree->tree_id}" .
			" WHERE" .
			" link1.l_file = {$this->tree->tree_id} AND" .
			" link1.l_from = link2.l_from AND" .
			" link1.l_type = 'CHIL' AND" .
			" child1.d_gid = link1.l_to AND" .
			" child1.d_fact = 'BIRT' AND" .
			" link2.l_type = 'CHIL' AND" .
			" child2.d_gid = link2.l_to AND" .
			" child2.d_fact = 'BIRT' AND" .
			" child1.d_julianday2 > child2.d_julianday2 AND" .
			" child2.d_julianday2 <> 0 AND" .
			" child1.d_gid <> child2.d_gid" .
			" ORDER BY age DESC" .
			" LIMIT " . $total
		);
		if (!isset($rows[0])) {
			return '';
		}
		$top10 = array();
		$dist  = array();
		foreach ($rows as $fam) {
			$family = WT_Family::getInstance($fam['family']);
			$child1 = WT_Individual::getInstance($fam['ch1']);
			$child2 = WT_Individual::getInstance($fam['ch2']);
			if ($type == 'name') {
				if ($child1->canShow() && $child2->canShow()) {
					$return = '<a href="' . $child2->getHtmlUrl() . '">' . $child2->getFullName() . '</a> ';
					$return .= WT_I18N::translate('and') . ' ';
					$return .= '<a href="' . $child1->getHtmlUrl() . '">' . $child1->getFullName() . '</a>';
					$return .= ' <a href="' . $family->getHtmlUrl() . '">[' . WT_I18N::translate('View family') . ']</a>';
				} else {
					$return = WT_I18N::translate('This information is private and cannot be shown.');
				}
				return $return;
			}
			$age = $fam['age'];
			if ((int)($age / 365.25) > 0) {
				$age = (int)($age / 365.25) . 'y';
			} elseif ((int)($age / 30.4375) > 0) {
				$age = (int)($age / 30.4375) . 'm';
			} else {
				$age = $age . 'd';
			}
			$age = get_age_at_event($age, true);
			if ($type == 'age') {
				return $age;
			}
			if ($type == 'list') {
				if ($one && !in_array($fam['family'], $dist)) {
					if ($child1->canShow() && $child2->canShow()) {
						$return = "<li>";
						$return .= "<a href=\"" . $child2->getHtmlUrl() . "\">" . $child2->getFullName() . "</a> ";
						$return .= WT_I18N::translate('and') . " ";
						$return .= "<a href=\"" . $child1->getHtmlUrl() . "\">" . $child1->getFullName() . "</a>";
						$return .= " (" . $age . ")";
						$return .= " <a href=\"" . $family->getHtmlUrl() . "\">[" . WT_I18N::translate('View family') . "]</a>";
						$return .= '</li>';
						$top10[] = $return;
						$dist[] = $fam['family'];
					}
				} elseif (!$one && $child1->canShow() && $child2->canShow()) {
					$return = "<li>";
					$return .= "<a href=\"" . $child2->getHtmlUrl() . "\">" . $child2->getFullName() . "</a> ";
					$return .= WT_I18N::translate('and') . " ";
					$return .= "<a href=\"" . $child1->getHtmlUrl() . "\">" . $child1->getFullName() . "</a>";
					$return .= " (" . $age . ")";
					$return .= " <a href=\"" . $family->getHtmlUrl() . "\">[" . WT_I18N::translate('View family') . "]</a>";
					$return .= '</li>';
					$top10[] = $return;
				}
			} else {
				if ($child1->canShow() && $child2->canShow()) {
					$return = $child2->format_list('span', false, $child2->getFullName());
					$return .= "<br>" . WT_I18N::translate('and') . "<br>";
					$return .= $child1->format_list('span', false, $child1->getFullName());
					$return .= "<br><a href=\"" . $family->getHtmlUrl() . "\">[" . WT_I18N::translate('View family') . "]</a>";
					return $return;
				} else {
					return WT_I18N::translate('This information is private and cannot be shown.');
				}
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}
		return $top10;
	}

	/**
	 * @param boolean  $simple
	 * @param boolean  $sex
	 * @param integer  $year1
	 * @param integer  $year2
	 * @param string[] $params
	 *
	 * @return string|string[][]
	 */
	public function monthFirstChildQuery($simple = true, $sex = false, $year1 = -1, $year2 = -1, $params = array()) {
		global $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y, $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2;

		if ($year1 >= 0 && $year2 >= 0) {
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
		$sql =
			"SELECT SQL_CACHE d_month{$sql_sex1}, COUNT(*) AS total " .
			"FROM (" .
			" SELECT family{$sql_sex1}, MIN(date) AS d_date, d_month" .
			" FROM (" .
			"  SELECT" .
			"  link1.l_from AS family," .
			"  link1.l_to AS child," .
			"  child1.d_julianday2 as date," .
			"  child1.d_month as d_month" .
			$sql_sex1 .
			"  FROM `##link` AS link1" .
			"  LEFT JOIN `##dates` AS child1 ON child1.d_file = {$this->tree->tree_id}" .
			$sql_sex2 .
			"  WHERE" .
			"  link1.l_file = {$this->tree->tree_id} AND" .
			"  link1.l_type = 'CHIL' AND" .
			"  child1.d_gid = link1.l_to AND" .
			"  child1.d_fact = 'BIRT' AND" .
			"  d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
			"  child1.d_month <> ''" .
			$sql_years .
			"  ORDER BY date" .
			" ) AS children" .
			" GROUP BY family" .
			") AS first_child " .
			"GROUP BY d_month";
		if ($sex) {
			$sql .= ', i_sex';
		}
		$rows = $this->runSQL($sql);
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = $WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y;
			}
			if (isset($params[1]) && $params[1] != '') {
				$color_from = strtolower($params[1]);
			} else {
				$color_from = $WT_STATS_CHART_COLOR1;
			}
			if (isset($params[2]) && $params[2] != '') {
				$color_to = strtolower($params[2]);
			} else {
				$color_to = $WT_STATS_CHART_COLOR2;
			}
			$sizes = explode('x', $size);
			$tot = 0;
			foreach ($rows as $values) {
				$tot += $values['total'];
			}
			// Beware divide by zero
			if ($tot == 0) {
				return '';
			}
			$text = '';
			$counts = array();
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
				$text .= WT_I18N::translate(ucfirst(strtolower(($values['d_month'])))) . ' - ' . $values['total'] . '|';
			}
			$chd = $this->arrayToExtendedEncoding($counts);
			$chl = substr($text, 0, -1);
			return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:' . $chd . '&amp;chs=' . $size . '&amp;chco=' . $color_from . ',' . $color_to . '&amp;chf=bg,s,ffffff00&amp;chl=' . $chl . '" width="' . $sizes[0] . '" height="' . $sizes[1] . '" alt="' . WT_I18N::translate('Month of birth of first child in a relation') . '" title="' . WT_I18N::translate('Month of birth of first child in a relation') . '" />';
		}
		return $rows;
	}

	/**
	 * @return string
	 */
	public function largestFamily() {
		return $this->familyQuery('full');
	}

	/**
	 * @return string
	 */
	public function largestFamilySize() {
		return $this->familyQuery('size');
	}

	/**
	 * @return string
	 */
	public function largestFamilyName() {
		return $this->familyQuery('name');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenLargestFamily($params = array()) {
		return $this->topTenFamilyQuery('nolist', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenLargestFamilyList($params = array()) {
		return $this->topTenFamilyQuery('list', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartLargestFamilies($params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_L_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_L_CHART_X . 'x' . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_from = strtolower($params[1]);
		} else {
			$color_from = $WT_STATS_CHART_COLOR1;
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_to = strtolower($params[2]);
		} else {
			$color_to = $WT_STATS_CHART_COLOR2;
		}
		if (isset($params[3]) && $params[3] != '') {
			$total = strtolower($params[3]);
		} else {
			$total = 10;
		}
		$sizes = explode('x', $size);
		$total = (int)$total;
		$rows = $this->runSql(
			" SELECT SQL_CACHE f_numchil AS tot, f_id AS id" .
			" FROM `##families`" .
			" WHERE f_file={$this->tree->tree_id}" .
			" ORDER BY tot DESC" .
			" LIMIT " . $total
		);
		if (!isset($rows[0])) {
			return '';
		}
		$tot = 0;
		foreach ($rows as $row) {
			$tot += $row['tot'];
		}
		$chd = '';
		$chl = array();
		foreach ($rows as $row) {
			$family = WT_Family::getInstance($row['id']);
			if ($family->canShow()) {
				if ($tot == 0) {
					$per = 0;
				} else {
					$per = round(100 * $row['tot'] / $tot, 0);
				}
				$chd .= $this->arrayToExtendedEncoding(array($per));
				$chl[] = htmlspecialchars_decode(strip_tags($family->getFullName())) . ' - ' . WT_I18N::number($row['tot']);
			}
		}
		$chl = rawurlencode(implode('|', $chl));

		return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Largest families') . "\" title=\"" . WT_I18N::translate('Largest families') . "\" />";
	}

	/**
	 * @return string
	 */
	public function totalChildren() {
		$rows = $this->runSql("SELECT SQL_CACHE SUM(f_numchil) AS tot FROM `##families` WHERE f_file={$this->tree->tree_id}");

		return WT_I18N::number($rows[0]['tot']);
	}

	/**
	 * @return string
	 */
	public function averageChildren() {
		$rows = $this->runSql("SELECT SQL_CACHE AVG(f_numchil) AS tot FROM `##families` WHERE f_file={$this->tree->tree_id}");

		return WT_I18N::number($rows[0]['tot'], 2);
	}

	/**
	 * @param boolean $simple
	 * @param string  $sex
	 * @param integer $year1
	 * @param integer $year2
	 * @param array   $params
	 *
	 * @return string|string[][]
	 */
	public function statsChildrenQuery($simple = true, $sex = 'BOTH', $year1 = -1, $year2 = -1, $params = array()) {
		if ($simple) {
			if (isset($params[0]) && $params[0] != '') {
				$size = strtolower($params[0]);
			} else {
				$size = '220x200';
			}
			$sizes = explode('x', $size);
			$max = 0;
			$rows = $this->runSql(
				" SELECT SQL_CACHE ROUND(AVG(f_numchil),2) AS num, FLOOR(d_year/100+1) AS century" .
				" FROM  `##families`" .
				" JOIN  `##dates` ON (d_file = f_file AND d_gid=f_id)" .
				" WHERE f_file = {$this->tree->tree_id}" .
				" AND   d_julianday1<>0" .
				" AND   d_fact = 'MARR'" .
				" AND   d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')" .
				" GROUP BY century" .
				" ORDER BY century");
			if (empty($rows)) {
				return '';
			}
			foreach ($rows as $values) {
				if ($max < $values['num']) {
					$max = $values['num'];
				}
			}
			$chm = "";
			$chxl = "0:|";
			$i = 0;
			$counts = array();
			foreach ($rows as $values) {
				if ($sizes[0] < 980) {
					$sizes[0] += 38;
				}
				$chxl .= $this->centuryName($values['century']) . "|";
				if ($max <= 5) {
					$counts[] = round($values['num'] * 819.2 - 1, 1);
				} else {
					$counts[] = round($values['num'] * 409.6, 1);
				}
				$chm .= 't' . $values['num'] . ',000000,0,' . $i . ',11,1|';
				$i++;
			}
			$chd = $this->arrayToExtendedEncoding($counts);
			$chm = substr($chm, 0, -1);
			if ($max <= 5) {
				$chxl .= "1:||" . WT_I18N::translate('century') . "|2:|0|1|2|3|4|5|3:||" . WT_I18N::translate('Number of children') . "|";
			} else {
				$chxl .= "1:||" . WT_I18N::translate('century') . "|2:|0|1|2|3|4|5|6|7|8|9|10|3:||" . WT_I18N::translate('Number of children') . "|";
			}

			return "<img src=\"https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0,3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Average number of children per family') . "\" title=\"" . WT_I18N::translate('Average number of children per family') . "\" />";
		} else {
			if ($sex == 'M') {
				$sql =
					"SELECT SQL_CACHE num, COUNT(*) AS total FROM " .
					"(SELECT count(i_sex) AS num FROM `##link` " .
					"LEFT OUTER JOIN `##individuals` " .
					"ON l_from=i_id AND l_file=i_file AND i_sex='M' AND l_type='FAMC' " .
					"JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->tree->tree_id} GROUP BY l_to" .
					") boys" .
					" GROUP BY num" .
					" ORDER BY num";
			} elseif ($sex == 'F') {
				$sql =
					"SELECT SQL_CACHE num, COUNT(*) AS total FROM " .
					"(SELECT count(i_sex) AS num FROM `##link` " .
					"LEFT OUTER JOIN `##individuals` " .
					"ON l_from=i_id AND l_file=i_file AND i_sex='F' AND l_type='FAMC' " .
					"JOIN `##families` ON f_file=l_file AND f_id=l_to WHERE f_file={$this->tree->tree_id} GROUP BY l_to" .
					") girls" .
					" GROUP BY num" .
					" ORDER BY num";
			} else {
				$sql = "SELECT SQL_CACHE f_numchil, COUNT(*) AS total FROM `##families` ";
				if ($year1 >= 0 && $year2 >= 0) {
					$sql .=
						"AS fam LEFT JOIN `##dates` AS married ON married.d_file = {$this->tree->tree_id}"
						. " WHERE"
						. " married.d_gid = fam.f_id AND"
						. " fam.f_file = {$this->tree->tree_id} AND"
						. " married.d_fact = 'MARR' AND"
						. " married.d_year BETWEEN '{$year1}' AND '{$year2}'";
				} else {
					$sql .= "WHERE f_file={$this->tree->tree_id}";
				}
				$sql .= " GROUP BY f_numchil";
			}
			$rows = $this->runSql($sql);

			return $rows;
		}
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function statsChildren($params = array()) {
		return $this->statsChildrenQuery($simple = true, $sex = 'BOTH', $year1 = -1, $year2 = -1, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topAgeBetweenSiblingsName($params = array()) {
		return $this->ageBetweenSiblingsQuery($type = 'name', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topAgeBetweenSiblings($params = array()) {
		return $this->ageBetweenSiblingsQuery($type = 'age', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topAgeBetweenSiblingsFullName($params = array()) {
		return $this->ageBetweenSiblingsQuery($type = 'nolist', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topAgeBetweenSiblingsList($params = array()) {
		return $this->ageBetweenSiblingsQuery($type = 'list', $params);
	}

	/**
	 * @return string
	 */
	private function noChildrenFamiliesQuery() {
		$rows = $this->runSql(
			" SELECT SQL_CACHE COUNT(*) AS tot" .
			" FROM  `##families`" .
			" WHERE f_numchil = 0 AND f_file = {$this->tree->tree_id}");

		return $rows[0]['tot'];
	}

	/**
	 * @return string
	 */
	public function noChildrenFamilies() {
		return WT_I18N::number($this->noChildrenFamiliesQuery());
	}

	/**
	 * @param array $params
	 *
	 * @return array|mixed|string
	 * @throws Exception
	 */
	public function noChildrenFamiliesList($params = array()) {
		global $TEXT_DIRECTION;

		if (isset($params[0]) && $params[0] != '') {
			$type = strtolower($params[0]);
		} else {
			$type = 'list';
		}
		$rows = $this->runSql(
			" SELECT SQL_CACHE f_id AS family" .
			" FROM `##families` AS fam" .
			" WHERE f_numchil = 0 AND fam.f_file = {$this->tree->tree_id}");
		if (!isset($rows[0])) {
			return '';
		}
		$top10 = array();
		foreach ($rows as $row) {
			$family = WT_Family::getInstance($row['family']);
			if ($family->canShow()) {
				if ($type == 'list') {
					$top10[] = "<li><a href=\"" . $family->getHtmlUrl() . "\">" . $family->getFullName() . "</a></li>";
				} else {
					$top10[] = "<a href=\"" . $family->getHtmlUrl() . "\">" . $family->getFullName() . "</a>";
				}
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		} else {
			$top10 = implode(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}

		return $top10;
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartNoChildrenFamilies($params = array()) {
		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = '220x200';
		}
		if (isset($params[1]) && $params[1] != '') {
			$year1 = $params[1];
		} else {
			$year1 = -1;
		}
		if (isset($params[2]) && $params[2] != '') {
			$year2 = $params[2];
		} else {
			$year2 = -1;
		}
		$sizes = explode('x', $size);
		if ($year1 >= 0 && $year2 >= 0) {
			$years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
		} else {
			$years = "";
		}
		$max = 0;
		$tot = 0;
		$rows = $this->runSql(
			"SELECT SQL_CACHE" .
			" COUNT(*) AS count," .
			" FLOOR(married.d_year/100+1) AS century" .
			" FROM" .
			" `##families` AS fam" .
			" JOIN" .
			" `##dates` AS married ON (married.d_file = fam.f_file AND married.d_gid = fam.f_id)" .
			" WHERE" .
			" f_numchil = 0 AND" .
			" fam.f_file = {$this->tree->tree_id} AND" .
			$years .
			" married.d_fact = 'MARR' AND" .
			" married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')" .
			" GROUP BY century ORDER BY century"
		);
		if (empty($rows)) {
			return '';
		}
		foreach ($rows as $values) {
			if ($max < $values['count']) {
				$max = $values['count'];
			}
			$tot += $values['count'];
		}
		$unknown = $this->noChildrenFamiliesQuery() - $tot;
		if ($unknown > $max) {
			$max = $unknown;
		}
		$chm = "";
		$chxl = "0:|";
		$i = 0;
		$counts = array();
		foreach ($rows as $values) {
			if ($sizes[0] < 980) {
				$sizes[0] += 38;
			}
			$chxl .= $this->centuryName($values['century']) . "|";
			$counts[] = round(4095 * $values['count'] / ($max + 1));
			$chm .= 't' . $values['count'] . ',000000,0,' . $i . ',11,1|';
			$i++;
		}
		$counts[] = round(4095 * $unknown / ($max + 1));
		$chd = $this->arrayToExtendedEncoding($counts);
		$chm .= 't' . $unknown . ',000000,0,' . $i . ',11,1';
		$chxl .= WT_I18N::translate_c('unknown century', 'Unknown') . "|1:||" . WT_I18N::translate('century') . "|2:|0|";
		$step = $max + 1;
		for ($d = (int)($max + 1); $d > 0; $d--) {
			if (($max + 1) < ($d * 10 + 1) && fmod(($max + 1), $d) == 0) {
				$step = $d;
			}
		}
		if ($step == (int)($max + 1)) {
			for ($d = (int)($max); $d > 0; $d--) {
				if ($max < ($d * 10 + 1) && fmod($max, $d) == 0) {
					$step = $d;
				}
			}
		}
		for ($n = $step; $n <= ($max + 1); $n += $step) {
			$chxl .= $n . "|";
		}
		$chxl .= "3:||" . WT_I18N::translate('Total families') . "|";
		return "<img src=\"https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0:" . ($i - 1) . ",3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF,ffffff00&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . WT_I18N::translate('Number of families without children') . "\" title=\"" . WT_I18N::translate('Number of families without children') . "\" />";
	}

	/**
	 * @param string   $type
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function topTenGrandFamilyQuery($type = 'list', $params = array()) {
		global $TEXT_DIRECTION;

		if (isset($params[0])) {
			$total = (int)$params[0];
		} else {
			$total = 10;
		}
		$rows = $this->runSql(
			"SELECT SQL_CACHE COUNT(*) AS tot, f_id AS id" .
			" FROM `##families`" .
			" JOIN `##link` AS children ON children.l_file = {$this->tree->tree_id}" .
			" JOIN `##link` AS mchildren ON mchildren.l_file = {$this->tree->tree_id}" .
			" JOIN `##link` AS gchildren ON gchildren.l_file = {$this->tree->tree_id}" .
			" WHERE" .
			" f_file={$this->tree->tree_id} AND" .
			" children.l_from=f_id AND" .
			" children.l_type='CHIL' AND" .
			" children.l_to=mchildren.l_from AND" .
			" mchildren.l_type='FAMS' AND" .
			" mchildren.l_to=gchildren.l_from AND" .
			" gchildren.l_type='CHIL'" .
			" GROUP BY id" .
			" ORDER BY tot DESC" .
			" LIMIT " . $total
		);
		if (!isset($rows[0])) {
			return '';
		}
		$top10 = array();
		foreach ($rows as $row) {
			$family = WT_Family::getInstance($row['id']);
			if ($family->canShow()) {
				if ($type == 'list') {
					$top10[] =
						'<li><a href="' . $family->getHtmlUrl() . '">' . $family->getFullName() . '</a> - ' .
						WT_I18N::plural('%s grandchild', '%s grandchildren', $row['tot'], WT_I18N::number($row['tot']));
				} else {
					$top10[] =
						'<a href="' . $family->getHtmlUrl() . '">' . $family->getFullName() . '</a> - ' .
						WT_I18N::plural('%s grandchild', '%s grandchildren', $row['tot'], WT_I18N::number($row['tot']));
				}
			}
		}
		if ($type == 'list') {
			$top10 = implode('', $top10);
		} else {
			$top10 = implode(';&nbsp; ', $top10);
		}
		if ($TEXT_DIRECTION == 'rtl') {
			$top10 = str_replace(array('[', ']', '(', ')', '+'), array('&rlm;[', '&rlm;]', '&rlm;(', '&rlm;)', '&rlm;+'), $top10);
		}
		if ($type == 'list') {
			return '<ul>' . $top10 . '</ul>';
		}
		return $top10;
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenLargestGrandFamily($params = array()) {
		return $this->topTenGrandFamilyQuery('nolist', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function topTenLargestGrandFamilyList($params = array()) {
		return $this->topTenGrandFamilyQuery('list', $params);
	}

	/**
	 * @param string   $type
	 * @param boolean  $show_tot
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function commonSurnamesQuery($type = 'list', $show_tot = false, $params = array()) {
		if (isset($params[0]) && $params[0] > 0) {
			$threshold = (int)$params[0];
		} else {
			$threshold = $this->tree->getPreference('COMMON_NAMES_THRESHOLD');
		}
		if (isset($params[1])) {
			$maxtoshow = (int)$params[1];
		} else {
			$maxtoshow = 0;
		}
		if (isset($params[2])) {
			$sorting = $params[2];
		} else {
			$sorting = 'alpha';
		}
		$surname_list = get_common_surnames($threshold);
		if (count($surname_list) == 0) {
			return '';
		}
		uasort($surname_list, array('WT_Stats', 'nameTotalReverseSort'));
		if ($maxtoshow > 0) {
			$surname_list = array_slice($surname_list, 0, $maxtoshow);
		}

		switch ($sorting) {
		default:
		case 'alpha':
			uksort($surname_list, array('WT_I18N', 'strcasecmp'));
			break;
		case 'count':
			uasort($surname_list, array('WT_Stats', 'nameTotalSort'));
			break;
		case 'rcount':
			uasort($surname_list, array('WT_Stats', 'nameTotalReverseSort'));
			break;
		}

		// Note that we count/display SPFX SURN, but sort/group under just SURN
		$surnames = array();
		foreach (array_keys($surname_list) as $surname) {
			$surnames = array_merge($surnames, WT_Query_Name::surnames($surname, '', false, false, WT_GED_ID));
		}
		return format_surname_list($surnames, ($type == 'list' ? 1 : 2), $show_tot, 'indilist.php');
	}

	/**
	 * @return string
	 */
	public function getCommonSurname() {
		$surnames = array_keys(get_top_surnames($this->tree->tree_id, 1, 1));
		return array_shift($surnames);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonSurnames($params = array('', '', 'alpha')) {
		return $this->commonSurnamesQuery('nolist', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonSurnamesTotals($params = array('', '', 'rcount')) {
		return $this->commonSurnamesQuery('nolist', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonSurnamesList($params = array('', '', 'alpha')) {
		return $this->commonSurnamesQuery('list', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonSurnamesListTotals($params = array('', '', 'rcount')) {
		return $this->commonSurnamesQuery('list', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartCommonSurnames($params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_from = strtolower($params[1]);
		} else {
			$color_from = $WT_STATS_CHART_COLOR1;
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_to = strtolower($params[2]);
		} else {
			$color_to = $WT_STATS_CHART_COLOR2;
		}
		if (isset($params[3]) && $params[3] != '') {
			$threshold = strtolower($params[3]);
		} else {
			$threshold = $this->tree->getPreference('COMMON_NAMES_THRESHOLD');
		}
		if (isset($params[4]) && $params[4] != '') {
			$maxtoshow = strtolower($params[4]);
		} else {
			$maxtoshow = 7;
		}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividualsQuery();
		$surnames = get_common_surnames($threshold);
		if (count($surnames) <= 0) {
			return '';
		}
		$SURNAME_TRADITION = $this->tree->getPreference('SURNAME_TRADITION');
		uasort($surnames, array('WT_Stats', 'nameTotalReverseSort'));
		$surnames = array_slice($surnames, 0, $maxtoshow);
		$all_surnames = array();
		foreach (array_keys($surnames) as $n => $surname) {
			if ($n >= $maxtoshow) {
				break;
			}
			$all_surnames = array_merge($all_surnames, WT_Query_Name::surnames(WT_I18N::strtoupper($surname), '', false, false, WT_GED_ID));
		}
		$tot = 0;
		foreach ($surnames as $surname) {
			$tot += $surname['match'];
		}
		$chd = '';
		$chl = array();
		foreach ($all_surnames as $surns) {
			$count_per = 0;
			$max_name  = 0;
			$top_name  = '';
			foreach ($surns as $spfxsurn => $indis) {
				$per = count($indis);
				$count_per += $per;
				// select most common surname from all variants
				if ($per > $max_name) {
					$max_name = $per;
					$top_name = $spfxsurn;
				}
			}
			switch ($SURNAME_TRADITION) {
			case 'polish':
				// most common surname should be in male variant (Kowalski, not Kowalska)
				$top_name = preg_replace(array('/ska$/', '/cka$/', '/dzka$/', '/żka$/'), array('ski', 'cki', 'dzki', 'żki'), $top_name);
			}
			$per = round(100 * $count_per / $tot_indi, 0);
			$chd .= $this->arrayToExtendedEncoding(array($per));
			//ToDo: RTL names are often printed LTR when also LTR names are present
			$chl[] = $top_name . ' - ' . WT_I18N::number($count_per);

		}
		$per = round(100 * ($tot_indi - $tot) / $tot_indi, 0);
		$chd .= $this->arrayToExtendedEncoding(array($per));
		$chl[] = WT_I18N::translate('Other') . ' - ' . WT_I18N::number($tot_indi - $tot);

		$chart_title = implode(WT_I18N::$list_separator, $chl);
		$chl = implode('|', $chl);
		return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:' . $chd . '&amp;chs=' . $size . '&amp;chco=' . $color_from . ',' . $color_to . '&amp;chf=bg,s,ffffff00&amp;chl=' . rawurlencode($chl) . '" width="' . $sizes[0] . '" height="' . $sizes[1] . '" alt="' . $chart_title . '" title="' . $chart_title . '" />';
	}

	/**
	 * @param string   $sex
	 * @param string   $type
	 * @param boolean  $show_tot
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function commonGivenQuery($sex = 'B', $type = 'list', $show_tot = false, $params = array()) {
		global $GEDCOM;

		if (isset($params[0]) && $params[0] != '' && $params[0] >= 0) {
			$threshold = (int)$params[0];
		} else {
			$threshold = 1;
		}
		if (isset($params[1]) && $params[1] != '' && $params[1] >= 0) {
			$maxtoshow = (int)$params[1];
		} else {
			$maxtoshow = 10;
		}

		switch ($sex) {
		case 'M':
			$sex_sql = "i_sex='M'";
			break;
		case 'F':
			$sex_sql = "i_sex='F'";
			break;
		case 'U':
			$sex_sql = "i_sex='U'";
			break;
		case 'B':
		default:
			$sex_sql = "i_sex<>'U'";
			break;
		}
		$ged_id = get_id_from_gedcom($GEDCOM);

		$rows = WT_DB::prepare("SELECT SQL_CACHE n_givn, COUNT(*) AS num FROM `##name` JOIN `##individuals` ON (n_id=i_id AND n_file=i_file) WHERE n_file={$ged_id} AND n_type<>'_MARNM' AND n_givn NOT IN ('@P.N.', '') AND LENGTH(n_givn)>1 AND {$sex_sql} GROUP BY n_id, n_givn")
			->fetchAll();
		$nameList = array();
		foreach ($rows as $row) {
			// Split “John Thomas” into “John” and “Thomas” and count against both totals
			foreach (explode(' ', $row->n_givn) as $given) {
				// Exclude initials and particles.
				if (!preg_match('/^([A-Z]|[a-z]{1,3})$/', $given)) {
					if (array_key_exists($given, $nameList)) {
						$nameList[$given] += $row->num;
					} else {
						$nameList[$given] = $row->num;
					}
				}
			}
		}
		arsort($nameList, SORT_NUMERIC);
		$nameList = array_slice($nameList, 0, $maxtoshow);

		if (count($nameList) == 0) {
			return '';
		}
		if ($type == 'chart') {
			return $nameList;
		}
		$common = array();
		foreach ($nameList as $given => $total) {
			if ($maxtoshow !== -1) {
				if ($maxtoshow-- <= 0) {
					break;
				}
			}
			if ($total < $threshold) {
				break;
			}
			if ($show_tot) {
				$tot = '&nbsp;(' . WT_I18N::number($total) . ')';
			} else {
				$tot = '';
			}
			switch ($type) {
			case 'table':
				$common[] = '<tr><td>' . $given . '</td><td>' . WT_I18N::number($total) . '</td><td>' . $total . '</td></tr>';
				break;
			case 'list':
				$common[] = '<li><span dir="auto">' . $given . '</span>' . $tot . '</li>';
				break;
			case 'nolist':
				$common[] = '<span dir="auto">' . $given . '</span>' . $tot;
				break;
			}
		}
		if ($common) {
			switch ($type) {
			case 'table':
				global $controller;
				$table_id = Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
				$controller
					->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
					->addInlineJavascript('
					jQuery("#' . $table_id . '").dataTable({
						dom: \'t\',
						autoWidth: false,
						paging: false,
						lengthChange: false,
						filter: false,
						info: false,
						jQueryUI: true,
						sorting: [[1,"desc"]],
						columns: [
							/* 0-name */ {},
							/* 1-count */ { class: "center", dataSort: 2},
							/* 2-COUNT */ { visible: false}
						]
					});
					jQuery("#' . $table_id . '").css("visibility", "visible");
				');
				$lookup = array('M' => WT_I18N::translate('Male'), 'F' => WT_I18N::translate('Female'), 'U' => WT_I18N::translate_c('unknown gender', 'Unknown'), 'B' => WT_I18N::translate('All'));
				return '<table id="' . $table_id . '" class="givn-list"><thead><tr><th class="ui-state-default" colspan="3">' . $lookup[$sex] . '</th></tr><tr><th>' . WT_I18N::translate('Name') . '</th><th>' . WT_I18N::translate('Count') . '</th><th>COUNT</th></tr></thead><tbody>' . implode('', $common) . '</tbody></table>';
			case 'list':
				return '<ul>' . implode('', $common) . '</ul>';
			case 'nolist':
				return implode(WT_I18N::$list_separator, $common);
			default:
				return '';
			}
		} else {
			return '';
		}
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGiven($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('B', 'nolist', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('B', 'nolist', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenList($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('B', 'list', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenListTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('B', 'list', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenTable($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('B', 'table', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenFemale($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('F', 'nolist', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenFemaleTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('F', 'nolist', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenFemaleList($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('F', 'list', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenFemaleListTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('F', 'list', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenFemaleTable($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('F', 'table', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenMale($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('M', 'nolist', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenMaleTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('M', 'nolist', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenMaleList($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('M', 'list', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenMaleListTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('M', 'list', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenMaleTable($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('M', 'table', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenUnknown($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('U', 'nolist', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenUnknownTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('U', 'nolist', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenUnknownList($params = array(1, 10, 'alpha')) {
		return $this->commonGivenQuery('U', 'list', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenUnknownListTotals($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('U', 'list', true, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function commonGivenUnknownTable($params = array(1, 10, 'rcount')) {
		return $this->commonGivenQuery('U', 'table', false, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function chartCommonGiven($params = array()) {
		global $WT_STATS_CHART_COLOR1, $WT_STATS_CHART_COLOR2, $WT_STATS_S_CHART_X, $WT_STATS_S_CHART_Y;

		if (isset($params[0]) && $params[0] != '') {
			$size = strtolower($params[0]);
		} else {
			$size = $WT_STATS_S_CHART_X . "x" . $WT_STATS_S_CHART_Y;
		}
		if (isset($params[1]) && $params[1] != '') {
			$color_from = strtolower($params[1]);
		} else {
			$color_from = $WT_STATS_CHART_COLOR1;
		}
		if (isset($params[2]) && $params[2] != '') {
			$color_to = strtolower($params[2]);
		} else {
			$color_to = $WT_STATS_CHART_COLOR2;
		}
		if (isset($params[4]) && $params[4] != '') {
			$maxtoshow = strtolower($params[4]);
		} else {
			$maxtoshow = 7;
		}
		$sizes = explode('x', $size);
		$tot_indi = $this->totalIndividualsQuery();
		$given = $this->commonGivenQuery('B', 'chart');
		if (!is_array($given)) {
			return '';
		}
		$given = array_slice($given, 0, $maxtoshow);
		if (count($given) <= 0) {
			return '';
		}
		$tot = 0;
		foreach ($given as $count) {
			$tot += $count;
		}
		$chd = '';
		$chl = array();
		foreach ($given as $givn => $count) {
			if ($tot == 0) {
				$per = 0;
			} else {
				$per = round(100 * $count / $tot_indi, 0);
			}
			$chd .= $this->arrayToExtendedEncoding(array($per));
			//ToDo: RTL names are often printed LTR when also LTR names are present
			$chl[] = $givn . ' - ' . WT_I18N::number($count);
		}
		$per = round(100 * ($tot_indi - $tot) / $tot_indi, 0);
		$chd .= $this->arrayToExtendedEncoding(array($per));
		$chl[] = WT_I18N::translate('Other') . ' - ' . WT_I18N::number($tot_indi - $tot);

		$chart_title = implode(WT_I18N::$list_separator, $chl);
		$chl = implode('|', $chl);
		return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl=" . rawurlencode($chl) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . "\" title=\"" . $chart_title . "\" />";
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	private function usersLoggedInQuery($type = 'nolist') {
		$content = '';
		// List active users
		$NumAnonymous = 0;
		$loggedusers = array();
		foreach (User::allLoggedIn() as $user) {
			if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
				$loggedusers[] = $user;
			} else {
				$NumAnonymous++;
			}
		}
		$LoginUsers = count($loggedusers);
		if ($LoginUsers == 0 && $NumAnonymous == 0) {
			return WT_I18N::translate('No logged-in and no anonymous users');
		}
		if ($NumAnonymous > 0) {
			$content .= '<b>' . WT_I18N::plural('%d anonymous logged-in user', '%d anonymous logged-in users', $NumAnonymous, $NumAnonymous) . '</b>';
		}
		if ($LoginUsers > 0) {
			if ($NumAnonymous) {
				if ($type == 'list') {
					$content .= "<br><br>";
				} else {
					$content .= " " . WT_I18N::translate('and') . " ";
				}
			}
			$content .= '<b>' . WT_I18N::plural('%d logged-in user', '%d logged-in users', $LoginUsers, $LoginUsers) . '</b>';
			if ($type == 'list') {
				$content .= '<ul>';
			} else {
				$content .= ': ';
			}
		}
		if (Auth::check()) {
			foreach ($loggedusers as $user) {
				if ($type == 'list') {
					$content .= '<li>' . WT_Filter::escapeHtml($user->getRealName()) . ' - ' . WT_Filter::escapeHtml($user->getUserName());
				} else {
					$content .= WT_Filter::escapeHtml($user->getRealName()) . ' - ' . WT_Filter::escapeHtml($user->getUserName());
				}
				if (Auth::id() != $user->getUserId() && $user->getPreference('contactmethod') != 'none') {
					if ($type == 'list') {
						$content .= '<br><a class="icon-email" href="#" onclick="return message(\'' . $user->getUserId() . '\', \'\', \'' . WT_Filter::escapeJs(get_query_url()) . '\');" title="' . WT_I18N::translate('Send a message') . '"></a>';
					} else {
						$content .= ' <a class="icon-email" href="#" onclick="return message(\'' . $user->getUserId() . '\', \'\', \'' . WT_Filter::escapeJs(get_query_url()) . '\');" title="' . WT_I18N::translate('Send a message') . '"></a>';
					}
				}
				if ($type == 'list') {
					$content .= '</li>';
				}
			}
		}
		if ($type == 'list') {
			$content .= '</ul>';
		}
		return $content;
	}

	/**
	 * @param string $type
	 *
	 * @return integer
	 */
	private function usersLoggedInTotalQuery($type = 'all') {
		$anon = 0;
		$visible = 0;
		foreach (User::allLoggedIn() as $user) {
			if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
				$visible++;
			} else {
				$anon++;
			}
		}
		if ($type == 'anon') {
			return $anon;
		} elseif ($type == 'visible') {
			return $visible;
		} else {
			return $visible + $anon;
		}
	}

	/**
	 * @return string
	 */
	public function usersLoggedIn() {
		return $this->usersLoggedInQuery('nolist');
	}

	/**
	 * @return string
	 */
	public function usersLoggedInList() {
		return $this->usersLoggedInQuery('list');
	}

	/**
	 * @return integer
	 */
	public function usersLoggedInTotal() {
		return $this->usersLoggedInTotalQuery('all');
	}

	/**
	 * @return integer
	 */
	public function usersLoggedInTotalAnon() {
		return $this->usersLoggedInTotalQuery('anon');
	}

	/**
	 * @return integer
	 */
	public function usersLoggedInTotalVisible() {
		return $this->usersLoggedInTotalQuery('visible');
	}

	/**
	 * @return null|string
	 */
	public function userID() {
		return Auth::id();
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function userName($params = array()) {
		if (Auth::check()) {
			return Auth::user()->getUserName();
		} elseif (isset($params[0]) && $params[0] != '') {
			// if #username:visitor# was specified, then "visitor" will be returned when the user is not logged in
			return $params[0];
		} else {
			return '';
		}
	}

	/**
	 * @return string
	 */
	public function userFullName() {
		return Auth::check() ? Auth::user()->getRealName() : '';
	}

	/**
	 * @param string   $type
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function getLatestUserData($type = 'userid', $params = array()) {
		global $DATE_FORMAT, $TIME_FORMAT;

		static $user_id = null;

		if ($user_id === null) {
			$user = User::findLatestToRegister();
		} else {
			$user = User::find($user_id);
		}

		switch ($type) {
		default:
		case 'userid':
			return $user->getUserId();
		case 'username':
			return $user->getUserName();
		case 'fullname':
			return $user->getRealName();
		case 'regdate':
			if (is_array($params) && isset($params[0]) && $params[0] != '') {
				$datestamp = $params[0];
			} else {
				$datestamp = $DATE_FORMAT;
			}
			return timestamp_to_gedcom_date($user->getPreference('reg_timestamp'))->display(false, $datestamp);
		case 'regtime':
			if (is_array($params) && isset($params[0]) && $params[0] != '') {
				$datestamp = $params[0];
			} else {
				$datestamp = str_replace('%', '', $TIME_FORMAT);
			}
			return date($datestamp, $user->getPreference('reg_timestamp'));
		case 'loggedin':
			if (is_array($params) && isset($params[0]) && $params[0] != '') {
				$yes = $params[0];
			} else {
				$yes = WT_I18N::translate('yes');
			}
			if (is_array($params) && isset($params[1]) && $params[1] != '') {
				$no = $params[1];
			} else {
				$no = WT_I18N::translate('no');
			}
			return WT_DB::prepare("SELECT SQL_NO_CACHE 1 FROM `##session` WHERE user_id=? LIMIT 1")->execute(array($user->getUserId()))->fetchOne() ? $yes : $no;
		}
	}

	/**
	 * @return string
	 */
	public function latestUserId() {
		return $this->getLatestUserData('userid');
	}

	/**
	 * @return string
	 */
	public function latestUserName() {
		return $this->getLatestUserData('username');
	}

	/**
	 * @return string
	 */
	public function latestUserFullName() {
		return $this->getLatestUserData('fullname');
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function latestUserRegDate($params = array()) {
		return $this->getLatestUserData('regdate', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function latestUserRegTime($params = array()) {
		return $this->getLatestUserData('regtime', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function latestUserLoggedin($params = array()) {
		return $this->getLatestUserData('loggedin', $params);
	}

	/**
	 * @return string
	 */
	public function contactWebmaster() {
		return user_contact_link($this->tree->getPreference('WEBMASTER_USER_ID'));
	}

	/**
	 * @return string
	 */
	public function contactGedcom() {
		return user_contact_link($this->tree->getPreference('CONTACT_USER_ID'));
	}

	/**
	 * @return string
	 */
	public function serverDate() {
		return timestamp_to_gedcom_date(WT_TIMESTAMP)->display();
	}

	/**
	 * @return string
	 */
	public function serverTime() {
		return date('g:i a');
	}

	/**
	 * @return string
	 */
	public function serverTime24() {
		return date('G:i');
	}

	/**
	 * @return string
	 */
	/**
	 * @return string
	 */
	public function serverTimezone() {
		return date('T');
	}

	/**
	 * @return string
	 */
	public function browserDate() {
		return timestamp_to_gedcom_date(WT_CLIENT_TIMESTAMP)->display();
	}

	/**
	 * @return string
	 */
	public function browserTime() {
		return date('g:i a', WT_CLIENT_TIMESTAMP);
	}

	/**
	 * @return string
	 */
	public function browserTime24() {
		return date('G:i', WT_CLIENT_TIMESTAMP);
	}

	/**
	 * @return string
	 */
	public function browserTimezone() {
		return date('T', WT_CLIENT_TIMESTAMP);
	}

	/**
	 * @return string
	 */
	public function WT_VERSION() {
		return WT_VERSION;
	}

	/**
	 * These functions provide access to hitcounter for use in the HTML block.
	 *
	 * @param string   $page_name
	 * @param string[] $params
	 *
	 * @return string
	 */
	private function hitCountQuery($page_name, $params) {
		if (is_array($params) && isset($params[0]) && $params[0] != '') {
			$page_parameter = $params[0];
		} else {
			$page_parameter = '';
		}

		if ($page_name === null) {
			// index.php?ctype=gedcom
			$page_name = 'index.php';
			$page_parameter = 'gedcom:' . get_id_from_gedcom($page_parameter ? $page_parameter : WT_GEDCOM);
		} elseif ($page_name == 'index.php') {
			// index.php?ctype=user
			$user = User::findByIdentifier($page_parameter);
			$page_parameter = 'user:' . ($user ? $user->getUserId() : Auth::id());
		} else {
			// indi/fam/sour/etc.
		}

		$count = WT_DB::prepare(
			"SELECT SQL_NO_CACHE page_count FROM `##hit_counter`" .
			" WHERE gedcom_id=? AND page_name=? AND page_parameter=?"
		)->execute(array(WT_GED_ID, $page_name, $page_parameter))->fetchOne();
		return '<span class="hit-counter">' . WT_I18N::number($count) . '</span>';
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCount($params = array()) {
		return $this->hitCountQuery(null, $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCountUser($params = array()) {
		return $this->hitCountQuery('index.php', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCountIndi($params = array()) {
		return $this->hitCountQuery('individual.php', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCountFam($params = array()) {
		return $this->hitCountQuery('family.php', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCountSour($params = array()) {
		return $this->hitCountQuery('source.php', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCountRepo($params = array()) {
		return $this->hitCountQuery('repo.php', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCountNote($params = array()) {
		return $this->hitCountQuery('note.php', $params);
	}

	/**
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function hitCountObje($params = array()) {
		return $this->hitCountQuery('mediaviewer.php', $params);
	}

	/**
	 * @link http://bendodson.com/news/google-extended-encoding-made-easy
	 *
	 * @param integer[] $a
	 *
	 * @return string
	 */
	private function arrayToExtendedEncoding($a) {
		$xencoding = WT_GOOGLE_CHART_ENCODING;

		$encoding = '';
		foreach ($a as $value) {
			if ($value < 0) {
				$value = 0;
			}
			$first = (int)($value / 64);
			$second = $value % 64;
			$encoding .= $xencoding[(int)$first] . $xencoding[(int)$second];
		}
		return $encoding;
	}

	/**
	 * @param array $a
	 * @param array $b
	 *
	 * @return integer
	 */
	private function nameTotalSort($a, $b) {
		return $a['match'] - $b['match'];
	}

	/**
	 * @param array $a
	 * @param array $b
	 *
	 * @return integer
	 */
	private function nameTotalReverseSort($a, $b) {
		return $b['match'] - $a['match'];
	}

	/**
	 * @param string $sql
	 *
	 * @return string[][]
	 */
	private function runSql($sql) {
		static $cache = array();

		$id = md5($sql);
		if (isset($cache[$id])) {
			return $cache[$id];
		}
		$rows = WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
		$cache[$id] = $rows;

		return $rows;
	}

	/**
	 * @return string
	 */
	public function gedcomFavorites() {
		if (array_key_exists('gedcom_favorites', WT_Module::getActiveModules())) {
			$block = new gedcom_favorites_WT_Module;

			return $block->getBlock(0, false);
		} else {
			return '';
		}
	}

	/**
	 * @return string
	 */
	public function userFavorites() {
		if (Auth::check() && array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$block = new user_favorites_WT_Module;

			return $block->getBlock(0, false);
		} else {
			return '';
		}
	}

	/**
	 * @return integer
	 */
	public function totalGedcomFavorites() {
		if (array_key_exists('gedcom_favorites', WT_Module::getActiveModules())) {
			return count(gedcom_favorites_WT_Module::getFavorites(WT_GED_ID));
		} else {
			return 0;
		}
	}

	/**
	 * @return integer
	 */
	public function totalUserFavorites() {
		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			return count(user_favorites_WT_Module::getFavorites(Auth::id()));
		} else {
			return 0;
		}
	}

	/**
	 * Other blocks
	 * example of use: #callBlock:block_name#
	 *
	 * @param string[] $params
	 *
	 * @return string
	 */
	public function callBlock($params = array()) {
		global $ctype;

		if (isset($params[0]) && $params[0] != '') {
			$block = $params[0];
		} else {
			return '';
		}
		$all_blocks = array();
		foreach (WT_Module::getActiveBlocks() as $name => $active_block) {
			if ($ctype == 'user' && $active_block->isUserBlock() || $ctype == 'gedcom' && $active_block->isGedcomBlock()) {
				$all_blocks[$name] = $active_block;
			}
		}
		if (!array_key_exists($block, $all_blocks) || $block == 'html') {
			return '';
		}
		$class_name = $block . '_WT_Module';
		// Build the config array
		array_shift($params);
		$cfg = array();
		foreach ($params as $config) {
			$bits = explode('=', $config);
			if (count($bits) < 2) {
				continue;
			}
			$v = array_shift($bits);
			$cfg[$v] = implode('=', $bits);
		}
		$block = new $class_name;
		$block_id = WT_Filter::getInteger('block_id');
		$content = $block->getBlock($block_id, false, $cfg);
		return $content;
	}

	/**
	 * @return string
	 */
	public function totalUserMessages() {
		$total = (int)WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##message` WHERE user_id = ?")
			->execute(array(Auth::id()))
			->fetchOne();

		return WT_I18N::number($total);
	}

	/**
	 * How many blog entries exist for this user.
	 *
	 * @return string
	 */
	public function totalUserJournal() {
		try {
			$number = (int)WT_DB::prepare("SELECT COUNT(*) FROM `##news` WHERE user_id = ?")
				->execute(array(Auth::id()))
				->fetchOne();
		} catch (PDOException $ex) {
			// The module may not be installed, so the table may not exist.
			$number = 0;
		}

		return WT_I18N::number($number);
	}

	/**
	 * How many news items exist for this tree.
	 *
	 * @return string
	 */
	public function totalGedcomNews() {
		try {
			$number = (int)WT_DB::prepare("SELECT COUNT(*) FROM `##news` WHERE gedcom_id = ?")
				->execute(array($this->tree->tree_id))
				->fetchOne();
		} catch (PDOException $ex) {
			// The module may not be installed, so the table may not exist.
			$number = 0;
		}

		return WT_I18N::number($number);
	}

	/**
	 * ISO3166 3 letter codes, with their 2 letter equivalent.
	 * NOTE: this is not 1:1.  ENG/SCO/WAL/NIR => GB
	 * NOTE: this also includes champman codes and others.  Should it?
	 *
	 * @return string[]
	 */
	public function iso3166() {
		return array(
			'ABW' => 'AW', 'AFG' => 'AF', 'AGO' => 'AO', 'AIA' => 'AI', 'ALA' => 'AX', 'ALB' => 'AL',
			'AND' => 'AD', 'ANT' => 'AN', 'ARE' => 'AE', 'ARG' => 'AR', 'ARM' => 'AM', 'ASM' => 'AS',
			'ATA' => 'AQ', 'ATF' => 'TF', 'ATG' => 'AG', 'AUS' => 'AU', 'AUT' => 'AT', 'AZE' => 'AZ',
			'BDI' => 'BI', 'BEL' => 'BE', 'BEN' => 'BJ', 'BFA' => 'BF', 'BGD' => 'BD', 'BGR' => 'BG',
			'BHR' => 'BH', 'BHS' => 'BS', 'BIH' => 'BA', 'BLR' => 'BY', 'BLZ' => 'BZ', 'BMU' => 'BM',
			'BOL' => 'BO', 'BRA' => 'BR', 'BRB' => 'BB', 'BRN' => 'BN', 'BTN' => 'BT', 'BVT' => 'BV',
			'BWA' => 'BW', 'CAF' => 'CF', 'CAN' => 'CA', 'CCK' => 'CC', 'CHE' => 'CH', 'CHL' => 'CL',
			'CHN' => 'CN', 'CHI' => 'JE', 'CIV' => 'CI', 'CMR' => 'CM', 'COD' => 'CD', 'COG' => 'CG',
			'COK' => 'CK', 'COL' => 'CO', 'COM' => 'KM', 'CPV' => 'CV', 'CRI' => 'CR', 'CUB' => 'CU',
			'CXR' => 'CX', 'CYM' => 'KY', 'CYP' => 'CY', 'CZE' => 'CZ', 'DEU' => 'DE', 'DJI' => 'DJ',
			'DMA' => 'DM', 'DNK' => 'DK', 'DOM' => 'DO', 'DZA' => 'DZ', 'ECU' => 'EC', 'EGY' => 'EG',
			'ENG' => 'GB', 'ERI' => 'ER', 'ESH' => 'EH', 'ESP' => 'ES', 'EST' => 'EE', 'ETH' => 'ET',
			'FIN' => 'FI', 'FJI' => 'FJ', 'FLK' => 'FK', 'FRA' => 'FR', 'FRO' => 'FO', 'FSM' => 'FM',
			'GAB' => 'GA', 'GBR' => 'GB', 'GEO' => 'GE', 'GHA' => 'GH', 'GIB' => 'GI', 'GIN' => 'GN',
			'GLP' => 'GP', 'GMB' => 'GM', 'GNB' => 'GW', 'GNQ' => 'GQ', 'GRC' => 'GR', 'GRD' => 'GD',
			'GRL' => 'GL', 'GTM' => 'GT', 'GUF' => 'GF', 'GUM' => 'GU', 'GUY' => 'GY', 'HKG' => 'HK',
			'HMD' => 'HM', 'HND' => 'HN', 'HRV' => 'HR', 'HTI' => 'HT', 'HUN' => 'HU', 'IDN' => 'ID',
			'IND' => 'IN', 'IOT' => 'IO', 'IRL' => 'IE', 'IRN' => 'IR', 'IRQ' => 'IQ', 'ISL' => 'IS',
			'ISR' => 'IL', 'ITA' => 'IT', 'JAM' => 'JM', 'JOR' => 'JO', 'JPN' => 'JA', 'KAZ' => 'KZ',
			'KEN' => 'KE', 'KGZ' => 'KG', 'KHM' => 'KH', 'KIR' => 'KI', 'KNA' => 'KN', 'KOR' => 'KO',
			'KWT' => 'KW', 'LAO' => 'LA', 'LBN' => 'LB', 'LBR' => 'LR', 'LBY' => 'LY', 'LCA' => 'LC',
			'LIE' => 'LI', 'LKA' => 'LK', 'LSO' => 'LS', 'LTU' => 'LT', 'LUX' => 'LU', 'LVA' => 'LV',
			'MAC' => 'MO', 'MAR' => 'MA', 'MCO' => 'MC', 'MDA' => 'MD', 'MDG' => 'MG', 'MDV' => 'MV',
			'MEX' => 'MX', 'MHL' => 'MH', 'MKD' => 'MK', 'MLI' => 'ML', 'MLT' => 'MT', 'MMR' => 'MM',
			'MNG' => 'MN', 'MNP' => 'MP', 'MNT' => 'ME', 'MOZ' => 'MZ', 'MRT' => 'MR', 'MSR' => 'MS',
			'MTQ' => 'MQ', 'MUS' => 'MU', 'MWI' => 'MW', 'MYS' => 'MY', 'MYT' => 'YT', 'NAM' => 'NA',
			'NCL' => 'NC', 'NER' => 'NE', 'NFK' => 'NF', 'NGA' => 'NG', 'NIC' => 'NI', 'NIR' => 'GB',
			'NIU' => 'NU', 'NLD' => 'NL', 'NOR' => 'NO', 'NPL' => 'NP', 'NRU' => 'NR', 'NZL' => 'NZ',
			'OMN' => 'OM', 'PAK' => 'PK', 'PAN' => 'PA', 'PCN' => 'PN', 'PER' => 'PE', 'PHL' => 'PH',
			'PLW' => 'PW', 'PNG' => 'PG', 'POL' => 'PL', 'PRI' => 'PR', 'PRK' => 'KP', 'PRT' => 'PO',
			'PRY' => 'PY', 'PSE' => 'PS', 'PYF' => 'PF', 'QAT' => 'QA', 'REU' => 'RE', 'ROM' => 'RO',
			'RUS' => 'RU', 'RWA' => 'RW', 'SAU' => 'SA', 'SCT' => 'GB', 'SDN' => 'SD', 'SEN' => 'SN',
			'SER' => 'RS', 'SGP' => 'SG', 'SGS' => 'GS', 'SHN' => 'SH', 'SIC' => 'IT', 'SJM' => 'SJ',
			'SLB' => 'SB', 'SLE' => 'SL', 'SLV' => 'SV', 'SMR' => 'SM', 'SOM' => 'SO', 'SPM' => 'PM',
			'STP' => 'ST', 'SUN' => 'RU', 'SUR' => 'SR', 'SVK' => 'SK', 'SVN' => 'SI', 'SWE' => 'SE',
			'SWZ' => 'SZ', 'SYC' => 'SC', 'SYR' => 'SY', 'TCA' => 'TC', 'TCD' => 'TD', 'TGO' => 'TG',
			'THA' => 'TH', 'TJK' => 'TJ', 'TKL' => 'TK', 'TKM' => 'TM', 'TLS' => 'TL', 'TON' => 'TO',
			'TTO' => 'TT', 'TUN' => 'TN', 'TUR' => 'TR', 'TUV' => 'TV', 'TWN' => 'TW', 'TZA' => 'TZ',
			'UGA' => 'UG', 'UKR' => 'UA', 'UMI' => 'UM', 'URY' => 'UY', 'USA' => 'US', 'UZB' => 'UZ',
			'VAT' => 'VA', 'VCT' => 'VC', 'VEN' => 'VE', 'VGB' => 'VG', 'VIR' => 'VI', 'VNM' => 'VN',
			'VUT' => 'VU', 'WLF' => 'WF', 'WLS' => 'GB', 'WSM' => 'WS', 'YEM' => 'YE', 'ZAF' => 'ZA',
			'ZMB' => 'ZM', 'ZWE' => 'ZW',
		);
	}

	/**
	 * Country codes and names
	 *
	 * @return string[]
	 */
	public function getAllCountries() {
		return array(
			'???' => WT_I18N::translate('Unknown'),
			'ABW' => WT_I18N::translate('Aruba'),
			'ACA' => WT_I18N::translate('Acadia'),
			'AFG' => WT_I18N::translate('Afghanistan'),
			'AGO' => WT_I18N::translate('Angola'),
			'AIA' => WT_I18N::translate('Anguilla'),
			'ALA' => WT_I18N::translate('Aland Islands'),
			'ALB' => WT_I18N::translate('Albania'),
			'AND' => WT_I18N::translate('Andorra'),
			'ANT' => WT_I18N::translate('Netherlands Antilles'),
			'ARE' => WT_I18N::translate('United Arab Emirates'),
			'ARG' => WT_I18N::translate('Argentina'),
			'ARM' => WT_I18N::translate('Armenia'),
			'ASM' => WT_I18N::translate('American Samoa'),
			'ATA' => WT_I18N::translate('Antarctica'),
			'ATF' => WT_I18N::translate('French Southern Territories'),
			'ATG' => WT_I18N::translate('Antigua and Barbuda'),
			'AUS' => WT_I18N::translate('Australia'),
			'AUT' => WT_I18N::translate('Austria'),
			'AZE' => WT_I18N::translate('Azerbaijan'),
			'AZR' => WT_I18N::translate('Azores'),
			'BDI' => WT_I18N::translate('Burundi'),
			'BEL' => WT_I18N::translate('Belgium'),
			'BEN' => WT_I18N::translate('Benin'),
			'BFA' => WT_I18N::translate('Burkina Faso'),
			'BGD' => WT_I18N::translate('Bangladesh'),
			'BGR' => WT_I18N::translate('Bulgaria'),
			'BHR' => WT_I18N::translate('Bahrain'),
			'BHS' => WT_I18N::translate('Bahamas'),
			'BIH' => WT_I18N::translate('Bosnia and Herzegovina'),
			'BLR' => WT_I18N::translate('Belarus'),
			'BLZ' => WT_I18N::translate('Belize'),
			'BMU' => WT_I18N::translate('Bermuda'),
			'BOL' => WT_I18N::translate('Bolivia'),
			'BRA' => WT_I18N::translate('Brazil'),
			'BRB' => WT_I18N::translate('Barbados'),
			'BRN' => WT_I18N::translate('Brunei Darussalam'),
			'BTN' => WT_I18N::translate('Bhutan'),
			'BVT' => WT_I18N::translate('Bouvet Island'),
			'BWA' => WT_I18N::translate('Botswana'),
			'BWI' => WT_I18N::translate('British West Indies'),
			'CAF' => WT_I18N::translate('Central African Republic'),
			'CAN' => WT_I18N::translate('Canada'),
			'CAP' => WT_I18N::translate('Cape Colony'),
			'CAT' => WT_I18N::translate('Catalonia'),
			'CCK' => WT_I18N::translate('Cocos (Keeling) Islands'),
			'CHE' => WT_I18N::translate('Switzerland'),
			'CHI' => WT_I18N::translate('Channel Islands'),
			'CHL' => WT_I18N::translate('Chile'),
			'CHN' => WT_I18N::translate('China'),
			'CIV' => WT_I18N::translate('Cote d’Ivoire'),
			'CMR' => WT_I18N::translate('Cameroon'),
			'COD' => WT_I18N::translate('Congo (Kinshasa)'),
			'COG' => WT_I18N::translate('Congo (Brazzaville)'),
			'COK' => WT_I18N::translate('Cook Islands'),
			'COL' => WT_I18N::translate('Colombia'),
			'COM' => WT_I18N::translate('Comoros'),
			'CPV' => WT_I18N::translate('Cape Verde'),
			'CRI' => WT_I18N::translate('Costa Rica'),
			'CSK' => WT_I18N::translate('Czechoslovakia'),
			'CUB' => WT_I18N::translate('Cuba'),
			'CXR' => WT_I18N::translate('Christmas Island'),
			'CYM' => WT_I18N::translate('Cayman Islands'),
			'CYP' => WT_I18N::translate('Cyprus'),
			'CZE' => WT_I18N::translate('Czech Republic'),
			'DEU' => WT_I18N::translate('Germany'),
			'DJI' => WT_I18N::translate('Djibouti'),
			'DMA' => WT_I18N::translate('Dominica'),
			'DNK' => WT_I18N::translate('Denmark'),
			'DOM' => WT_I18N::translate('Dominican Republic'),
			'DZA' => WT_I18N::translate('Algeria'),
			'ECU' => WT_I18N::translate('Ecuador'),
			'EGY' => WT_I18N::translate('Egypt'),
			'EIR' => WT_I18N::translate('Eire'),
			'ENG' => WT_I18N::translate('England'),
			'ERI' => WT_I18N::translate('Eritrea'),
			'ESH' => WT_I18N::translate('Western Sahara'),
			'ESP' => WT_I18N::translate('Spain'),
			'EST' => WT_I18N::translate('Estonia'),
			'ETH' => WT_I18N::translate('Ethiopia'),
			'FIN' => WT_I18N::translate('Finland'),
			'FJI' => WT_I18N::translate('Fiji'),
			'FLD' => WT_I18N::translate('Flanders'),
			'FLK' => WT_I18N::translate('Falkland Islands'),
			'FRA' => WT_I18N::translate('France'),
			'FRO' => WT_I18N::translate('Faeroe Islands'),
			'FSM' => WT_I18N::translate('Micronesia'),
			'GAB' => WT_I18N::translate('Gabon'),
			'GBR' => WT_I18N::translate('United Kingdom'),
			'GEO' => WT_I18N::translate('Georgia'),
			'GGY' => WT_I18N::translate('Guernsey'),
			'GHA' => WT_I18N::translate('Ghana'),
			'GIB' => WT_I18N::translate('Gibraltar'),
			'GIN' => WT_I18N::translate('Guinea'),
			'GLP' => WT_I18N::translate('Guadeloupe'),
			'GMB' => WT_I18N::translate('Gambia'),
			'GNB' => WT_I18N::translate('Guinea-Bissau'),
			'GNQ' => WT_I18N::translate('Equatorial Guinea'),
			'GRC' => WT_I18N::translate('Greece'),
			'GRD' => WT_I18N::translate('Grenada'),
			'GRL' => WT_I18N::translate('Greenland'),
			'GTM' => WT_I18N::translate('Guatemala'),
			'GUF' => WT_I18N::translate('French Guiana'),
			'GUM' => WT_I18N::translate('Guam'),
			'GUY' => WT_I18N::translate('Guyana'),
			'HKG' => WT_I18N::translate('Hong Kong'),
			'HMD' => WT_I18N::translate('Heard Island and McDonald Islands'),
			'HND' => WT_I18N::translate('Honduras'),
			'HRV' => WT_I18N::translate('Croatia'),
			'HTI' => WT_I18N::translate('Haiti'),
			'HUN' => WT_I18N::translate('Hungary'),
			'IDN' => WT_I18N::translate('Indonesia'),
			'IND' => WT_I18N::translate('India'),
			'IOM' => WT_I18N::translate('Isle of Man'),
			'IOT' => WT_I18N::translate('British Indian Ocean Territory'),
			'IRL' => WT_I18N::translate('Ireland'),
			'IRN' => WT_I18N::translate('Iran'),
			'IRQ' => WT_I18N::translate('Iraq'),
			'ISL' => WT_I18N::translate('Iceland'),
			'ISR' => WT_I18N::translate('Israel'),
			'ITA' => WT_I18N::translate('Italy'),
			'JAM' => WT_I18N::translate('Jamaica'),
			'JOR' => WT_I18N::translate('Jordan'),
			'JPN' => WT_I18N::translate('Japan'),
			'KAZ' => WT_I18N::translate('Kazakhstan'),
			'KEN' => WT_I18N::translate('Kenya'),
			'KGZ' => WT_I18N::translate('Kyrgyzstan'),
			'KHM' => WT_I18N::translate('Cambodia'),
			'KIR' => WT_I18N::translate('Kiribati'),
			'KNA' => WT_I18N::translate('Saint Kitts and Nevis'),
			'KOR' => WT_I18N::translate('Korea'),
			'KWT' => WT_I18N::translate('Kuwait'),
			'LAO' => WT_I18N::translate('Laos'),
			'LBN' => WT_I18N::translate('Lebanon'),
			'LBR' => WT_I18N::translate('Liberia'),
			'LBY' => WT_I18N::translate('Libya'),
			'LCA' => WT_I18N::translate('Saint Lucia'),
			'LIE' => WT_I18N::translate('Liechtenstein'),
			'LKA' => WT_I18N::translate('Sri Lanka'),
			'LSO' => WT_I18N::translate('Lesotho'),
			'LTU' => WT_I18N::translate('Lithuania'),
			'LUX' => WT_I18N::translate('Luxembourg'),
			'LVA' => WT_I18N::translate('Latvia'),
			'MAC' => WT_I18N::translate('Macau'),
			'MAR' => WT_I18N::translate('Morocco'),
			'MCO' => WT_I18N::translate('Monaco'),
			'MDA' => WT_I18N::translate('Moldova'),
			'MDG' => WT_I18N::translate('Madagascar'),
			'MDV' => WT_I18N::translate('Maldives'),
			'MEX' => WT_I18N::translate('Mexico'),
			'MHL' => WT_I18N::translate('Marshall Islands'),
			'MKD' => WT_I18N::translate('Macedonia'),
			'MLI' => WT_I18N::translate('Mali'),
			'MLT' => WT_I18N::translate('Malta'),
			'MMR' => WT_I18N::translate('Myanmar'),
			'MNG' => WT_I18N::translate('Mongolia'),
			'MNP' => WT_I18N::translate('Northern Mariana Islands'),
			'MNT' => WT_I18N::translate('Montenegro'),
			'MOZ' => WT_I18N::translate('Mozambique'),
			'MRT' => WT_I18N::translate('Mauritania'),
			'MSR' => WT_I18N::translate('Montserrat'),
			'MTQ' => WT_I18N::translate('Martinique'),
			'MUS' => WT_I18N::translate('Mauritius'),
			'MWI' => WT_I18N::translate('Malawi'),
			'MYS' => WT_I18N::translate('Malaysia'),
			'MYT' => WT_I18N::translate('Mayotte'),
			'NAM' => WT_I18N::translate('Namibia'),
			'NCL' => WT_I18N::translate('New Caledonia'),
			'NER' => WT_I18N::translate('Niger'),
			'NFK' => WT_I18N::translate('Norfolk Island'),
			'NGA' => WT_I18N::translate('Nigeria'),
			'NIC' => WT_I18N::translate('Nicaragua'),
			'NIR' => WT_I18N::translate('Northern Ireland'),
			'NIU' => WT_I18N::translate('Niue'),
			'NLD' => WT_I18N::translate('Netherlands'),
			'NOR' => WT_I18N::translate('Norway'),
			'NPL' => WT_I18N::translate('Nepal'),
			'NRU' => WT_I18N::translate('Nauru'),
			'NTZ' => WT_I18N::translate('Neutral Zone'),
			'NZL' => WT_I18N::translate('New Zealand'),
			'OMN' => WT_I18N::translate('Oman'),
			'PAK' => WT_I18N::translate('Pakistan'),
			'PAN' => WT_I18N::translate('Panama'),
			'PCN' => WT_I18N::translate('Pitcairn'),
			'PER' => WT_I18N::translate('Peru'),
			'PHL' => WT_I18N::translate('Philippines'),
			'PLW' => WT_I18N::translate('Palau'),
			'PNG' => WT_I18N::translate('Papua New Guinea'),
			'POL' => WT_I18N::translate('Poland'),
			'PRI' => WT_I18N::translate('Puerto Rico'),
			'PRK' => WT_I18N::translate('North Korea'),
			'PRT' => WT_I18N::translate('Portugal'),
			'PRY' => WT_I18N::translate('Paraguay'),
			'PSE' => WT_I18N::translate('Occupied Palestinian Territory'),
			'PYF' => WT_I18N::translate('French Polynesia'),
			'QAT' => WT_I18N::translate('Qatar'),
			'REU' => WT_I18N::translate('Reunion'),
			'ROM' => WT_I18N::translate('Romania'),
			'RUS' => WT_I18N::translate('Russia'),
			'RWA' => WT_I18N::translate('Rwanda'),
			'SAU' => WT_I18N::translate('Saudi Arabia'),
			'SCG' => WT_I18N::translate('Serbia and Montenegro'),
			'SCT' => WT_I18N::translate('Scotland'),
			'SDN' => WT_I18N::translate('Sudan'),
			'SEA' => WT_I18N::translate('At sea'),
			'SEN' => WT_I18N::translate('Senegal'),
			'SER' => WT_I18N::translate('Serbia'),
			'SGP' => WT_I18N::translate('Singapore'),
			'SGS' => WT_I18N::translate('South Georgia and the South Sandwich Islands'),
			'SHN' => WT_I18N::translate('Saint Helena'),
			'SIC' => WT_I18N::translate('Sicily'),
			'SJM' => WT_I18N::translate('Svalbard and Jan Mayen Islands'),
			'SLB' => WT_I18N::translate('Solomon Islands'),
			'SLE' => WT_I18N::translate('Sierra Leone'),
			'SLV' => WT_I18N::translate('El Salvador'),
			'SMR' => WT_I18N::translate('San Marino'),
			'SOM' => WT_I18N::translate('Somalia'),
			'SPM' => WT_I18N::translate('Saint Pierre and Miquelon'),
			'SSD' => WT_I18N::translate('South Sudan'),
			'STP' => WT_I18N::translate('Sao Tome and Principe'),
			'SUN' => WT_I18N::translate('USSR'),
			'SUR' => WT_I18N::translate('Suriname'),
			'SVK' => WT_I18N::translate('Slovakia'),
			'SVN' => WT_I18N::translate('Slovenia'),
			'SWE' => WT_I18N::translate('Sweden'),
			'SWZ' => WT_I18N::translate('Swaziland'),
			'SYC' => WT_I18N::translate('Seychelles'),
			'SYR' => WT_I18N::translate('Syrian Arab Republic'),
			'TCA' => WT_I18N::translate('Turks and Caicos Islands'),
			'TCD' => WT_I18N::translate('Chad'),
			'TGO' => WT_I18N::translate('Togo'),
			'THA' => WT_I18N::translate('Thailand'),
			'TJK' => WT_I18N::translate('Tajikistan'),
			'TKL' => WT_I18N::translate('Tokelau'),
			'TKM' => WT_I18N::translate('Turkmenistan'),
			'TLS' => WT_I18N::translate('Timor-Leste'),
			'TON' => WT_I18N::translate('Tonga'),
			'TRN' => WT_I18N::translate('Transylvania'),
			'TTO' => WT_I18N::translate('Trinidad and Tobago'),
			'TUN' => WT_I18N::translate('Tunisia'),
			'TUR' => WT_I18N::translate('Turkey'),
			'TUV' => WT_I18N::translate('Tuvalu'),
			'TWN' => WT_I18N::translate('Taiwan'),
			'TZA' => WT_I18N::translate('Tanzania'),
			'UGA' => WT_I18N::translate('Uganda'),
			'UKR' => WT_I18N::translate('Ukraine'),
			'UMI' => WT_I18N::translate('US Minor Outlying Islands'),
			'URY' => WT_I18N::translate('Uruguay'),
			'USA' => WT_I18N::translate('USA'),
			'UZB' => WT_I18N::translate('Uzbekistan'),
			'VAT' => WT_I18N::translate('Vatican City'),
			'VCT' => WT_I18N::translate('Saint Vincent and the Grenadines'),
			'VEN' => WT_I18N::translate('Venezuela'),
			'VGB' => WT_I18N::translate('British Virgin Islands'),
			'VIR' => WT_I18N::translate('US Virgin Islands'),
			'VNM' => WT_I18N::translate('Viet Nam'),
			'VUT' => WT_I18N::translate('Vanuatu'),
			'WAF' => WT_I18N::translate('West Africa'),
			'WLF' => WT_I18N::translate('Wallis and Futuna Islands'),
			'WLS' => WT_I18N::translate('Wales'),
			'WSM' => WT_I18N::translate('Samoa'),
			'YEM' => WT_I18N::translate('Yemen'),
			'YUG' => WT_I18N::translate('Yugoslavia'),
			'ZAF' => WT_I18N::translate('South Africa'),
			'ZAR' => WT_I18N::translate('Zaire'),
			'ZMB' => WT_I18N::translate('Zambia'),
			'ZWE' => WT_I18N::translate('Zimbabwe'),
		);
	}

	/**
	 * century name, English => 21st, Polish => XXI, etc.
	 *
	 * @param integer $century
	 *
	 * @return string
	 */
	private function centuryName($century) {
		if ($century < 0) {
			return str_replace(-$century, WT_Stats::centuryName(-$century), /* I18N: BCE=Before the Common Era, for Julian years < 0.  See http://en.wikipedia.org/wiki/Common_Era */
				WT_I18N::translate('%s BCE', WT_I18N::number(-$century)));
		}
		// The current chart engine (Google charts) can't handle <sup></sup> markup
		switch ($century) {
		case 21:
			return strip_tags(WT_I18N::translate_c('CENTURY', '21st'));
		case 20:
			return strip_tags(WT_I18N::translate_c('CENTURY', '20th'));
		case 19:
			return strip_tags(WT_I18N::translate_c('CENTURY', '19th'));
		case 18:
			return strip_tags(WT_I18N::translate_c('CENTURY', '18th'));
		case 17:
			return strip_tags(WT_I18N::translate_c('CENTURY', '17th'));
		case 16:
			return strip_tags(WT_I18N::translate_c('CENTURY', '16th'));
		case 15:
			return strip_tags(WT_I18N::translate_c('CENTURY', '15th'));
		case 14:
			return strip_tags(WT_I18N::translate_c('CENTURY', '14th'));
		case 13:
			return strip_tags(WT_I18N::translate_c('CENTURY', '13th'));
		case 12:
			return strip_tags(WT_I18N::translate_c('CENTURY', '12th'));
		case 11:
			return strip_tags(WT_I18N::translate_c('CENTURY', '11th'));
		case 10:
			return strip_tags(WT_I18N::translate_c('CENTURY', '10th'));
		case  9:
			return strip_tags(WT_I18N::translate_c('CENTURY', '9th'));
		case  8:
			return strip_tags(WT_I18N::translate_c('CENTURY', '8th'));
		case  7:
			return strip_tags(WT_I18N::translate_c('CENTURY', '7th'));
		case  6:
			return strip_tags(WT_I18N::translate_c('CENTURY', '6th'));
		case  5:
			return strip_tags(WT_I18N::translate_c('CENTURY', '5th'));
		case  4:
			return strip_tags(WT_I18N::translate_c('CENTURY', '4th'));
		case  3:
			return strip_tags(WT_I18N::translate_c('CENTURY', '3rd'));
		case  2:
			return strip_tags(WT_I18N::translate_c('CENTURY', '2nd'));
		case  1:
			return strip_tags(WT_I18N::translate_c('CENTURY', '1st'));
		default:
			return ($century - 1) . '01-' . $century . '00';
		}
	}
}
