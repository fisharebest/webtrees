<?php
// Creates some statistics out of the GEDCOM information.
// We will start with the following possibilities
// number of persons -> periods of 50 years from 1700-2000
// age -> periods of 10 years (different for 0-1,1-5,5-10,10-20 etc)
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

define('WT_SCRIPT_NAME', 'statisticsplot.php');
require './includes/session.php';

$controller = new WT_Controller_Ajax();

$stats = new WT_Stats($GEDCOM);

// Month of birth
function month_of_birth() {
	global $z_axis, $months, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->statsBirthQuery(false);
		foreach ($num as $values) {
			foreach ($months as $key => $month) {
				if ($month == $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$n1 += $values['total'];
				}
			}
		}
	} else if ($z_axis == 301) {
		$num = $stats->statsBirthQuery(false, true);
		foreach ($num as $values) {
			foreach ($months as $key => $month) {
				if ($month == $values['d_month']) {
					if ($values['i_sex'] === 'M') {
						fill_y_data(0, $key, $values['total']);
						$n1 += $values['total'];
					} else if ($values['i_sex'] === 'F') {
						fill_y_data(1, $key, $values['total']);
						$n1 += $values['total'];
					}
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsBirthQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($months as $key => $month) {
					if ($month == $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$n1 += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}
}

//Month of birth of first child in a relation
function month_of_birth_of_first_child() {
	global $z_axis, $months, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->monthFirstChildQuery(false);
		foreach ($num as $values) {
			foreach ($months as $key => $month) {
				if ($month == $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$n1 += $values['total'];
				}
			}
		}
	} else if ($z_axis == 301) {
		$num = $stats->monthFirstChildQuery(false, true);
		foreach ($num as $values) {
			foreach ($months as $key => $month) {
				if ($month == $values['d_month']) {
					if ($values['i_sex'] === 'M') {
						fill_y_data(0, $key, $values['total']);
						$n1 += $values['total'];
					} else if ($values['i_sex'] === 'F') {
						fill_y_data(1, $key, $values['total']);
						$n1 += $values['total'];
					}
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->monthFirstChildQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($months as $key => $month) {
					if ($month == $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$n1 += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}
}

//Month of death
function month_of_death() {
	global $z_axis, $months, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->statsDeathQuery(false);
		foreach ($num as $values) {
			foreach ($months as $key => $month) {
				if ($month == $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$n1 += $values['total'];
				}
			}
		}
	} else if ($z_axis == 301) {
		$num = $stats->statsDeathQuery(false, true);
		foreach ($num as $values) {
			foreach ($months as $key => $month) {
				if ($month == $values['d_month']) {
					if ($values['i_sex'] === 'M') {
						fill_y_data(0, $key, $values['total']);
						$n1 += $values['total'];
					} else if ($values['i_sex'] === 'F') {
						fill_y_data(1, $key, $values['total']);
						$n1 += $values['total'];
					}
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsDeathQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($months as $key => $month) {
					if ($month == $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$n1 += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}
}

//Month of marriage
function month_of_marriage() {
	global $z_axis, $months, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->statsMarrQuery(false, false);
		foreach ($num as $values) {
			foreach ($months as $key => $month) {
				if ($month == $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$n1 += $values['total'];
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsMarrQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($months as $key => $month) {
					if ($month == $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$n1 += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}
}

//Month of first marriage
function month_of_first_marriage() {
	global $z_axis, $months, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num  = $stats->statsMarrQuery(false, true);
		$indi = array();
		$fam  = array();
		foreach ($num as $values) {
			if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
				foreach ($months as $key => $month) {
					if ($month == $values['month']) {
						fill_y_data(0, $key, 1);
						$n1++;
					}
				}
				$indi[] = $values['indi'];
				$fam[]  = $values['fams'];
			}
		}
	} else {
		$zstart = 0;
		$indi   = array();
		$fam    = array();
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsMarrQuery(false, true, $zstart, $boundary);
			foreach ($num as $values) {
				if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
					foreach ($months as $key => $month) {
						if ($month == $values['month']) {
							fill_y_data($boundary, $key, 1);
							$n1++;
						}
					}
					$indi[] = $values['indi'];
					$fam[]  = $values['fams'];
				}
			}
			$zstart = $boundary + 1;
		}
	}
	unset($indi, $fam);
}

// Months between marriage and first child
function months_between_marriage_and_first_child() {
	echo 'not working yet';
}

// Age related to birth year
function lifespan_by_birth_year() {
	global $z_axis, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->statsAgeQuery(false, 'BIRT');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int)($age_value / 365.25), 1);
				$n1++;
			}
		}
	} else if ($z_axis == 301) {
		$num = $stats->statsAgeQuery(false, 'BIRT', 'M');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int)($age_value / 365.25), 1);
				$n1++;
			}
		}
		$num = $stats->statsAgeQuery(false, 'BIRT', 'F');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(1, (int)($age_value / 365.25), 1);
				$n1++;
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsAgeQuery(false, 'BIRT', 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					fill_y_data($boundary, (int)($age_value / 365.25), 1);
					$n1++;
				}
			}
			$zstart = $boundary + 1;
		}
	}
}

//Age related to death year
function lifespan_by_death_year() {
	global $z_axis, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->statsAgeQuery(false, 'DEAT');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int)($age_value / 365.25), 1);
				$n1++;
			}
		}
	} else if ($z_axis == 301) {
		$num = $stats->statsAgeQuery(false, 'DEAT', 'M');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int)($age_value / 365.25), 1);
				$n1++;
			}
		}
		$num = $stats->statsAgeQuery(false, 'DEAT', 'F');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(1, (int)($age_value / 365.25), 1);
				$n1++;
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsAgeQuery(false, 'DEAT', 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					fill_y_data($boundary, (int)($age_value / 365.25), 1);
					$n1++;
				}
			}
			$zstart = $boundary + 1;
		}
	}
}

//Age in year of marriage
function age_at_marriage() {
	global $z_axis, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->statsMarrAgeQuery(false, 'M');
		foreach ($num as $values) {
			fill_y_data(0, (int)($values['age'] / 365.25), 1);
			$n1++;
		}
		$num = $stats->statsMarrAgeQuery(false, 'F');
		foreach ($num as $values) {
			fill_y_data(0, (int)($values['age'] / 365.25), 1);
			$n1++;
		}
	} else if ($z_axis == 301) {
		$num = $stats->statsMarrAgeQuery(false, 'M');
		foreach ($num as $values) {
			fill_y_data(0, (int)($values['age'] / 365.25), 1);
			$n1++;
		}
		$num = $stats->statsMarrAgeQuery(false, 'F');
		foreach ($num as $values) {
			fill_y_data(1, (int)($values['age'] / 365.25), 1);
			$n1++;
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsMarrAgeQuery(false, 'M', $zstart, $boundary);
			foreach ($num as $values) {
				fill_y_data($boundary, (int)($values['age'] / 365.25), 1);
				$n1++;
			}
			$num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
			foreach ($num as $values) {
				fill_y_data($boundary, (int)($values['age'] / 365.25), 1);
				$n1++;
			}
			$zstart = $boundary + 1;
		}
	}
}

//Age in year of first marriage
function age_at_first_marriage() {
	global $z_axis, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num  = $stats->statsMarrAgeQuery(false, 'M');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(0, (int)($values['age'] / 365.25), 1);
				$n1++;
				$indi[] = $values['d_gid'];
			}
		}
		$num  = $stats->statsMarrAgeQuery(false, 'F');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(0, (int)($values['age'] / 365.25), 1);
				$n1++;
				$indi[] = $values['d_gid'];
			}
		}
	} else if ($z_axis == 301) {
		$num  = $stats->statsMarrAgeQuery(false, 'M');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(0, (int)($values['age'] / 365.25), 1);
				$n1++;
				$indi[] = $values['d_gid'];
			}
		}
		$num  = $stats->statsMarrAgeQuery(false, 'F');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(1, (int)($values['age'] / 365.25), 1);
				$n1++;
				$indi[] = $values['d_gid'];
			}
		}
	} else {
		$zstart = 0;
		$indi   = array();
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsMarrAgeQuery(false, 'M', $zstart, $boundary);
			foreach ($num as $values) {
				if (!in_array($values['d_gid'], $indi)) {
					fill_y_data($boundary, (int)($values['age'] / 365.25), 1);
					$n1++;
					$indi[] = $values['d_gid'];
				}
			}
			$num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
			foreach ($num as $values) {
				if (!in_array($values['d_gid'], $indi)) {
					fill_y_data($boundary, (int)($values['age'] / 365.25), 1);
					$n1++;
					$indi[] = $values['d_gid'];
				}
			}
			$zstart = $boundary + 1;
		}
	}
	unset($indi);
}

//Number of children
function number_of_children() {
	global $z_axis, $z_boundaries, $stats, $n1;

	if ($z_axis == 300) {
		$num = $stats->statsChildrenQuery(false);
		foreach ($num as $values) {
			fill_y_data(0, $values['f_numchil'], $values['total']);
			$n1 += $values['f_numchil'] * $values['total'];
		}
	} else if ($z_axis == 301) {
		$num = $stats->statsChildrenQuery(false, 'M');
		foreach ($num as $values) {
			fill_y_data(0, $values['num'], $values['total']);
			$n1 += $values['num'] * $values['total'];
		}
		$num = $stats->statsChildrenQuery(false, 'F');
		foreach ($num as $values) {
			fill_y_data(1, $values['num'], $values['total']);
			$n1 += $values['num'] * $values['total'];
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsChildrenQuery(false, 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				fill_y_data($boundary, $values['f_numchil'], $values['total']);
				$n1 += $values['f_numchil'] * $values['total'];
			}
			$zstart = $boundary + 1;
		}
	}
}

/**
 * @param integer $z
 * @param integer $x
 * @param integer $val
 */
function fill_y_data($z, $x, $val) {
	global $ydata, $xmax, $x_boundaries, $zmax, $z_boundaries, $xgiven, $zgiven;
	//-- calculate index $i out of given z value
	//-- calculate index $j out of given x value
	if ($xgiven) {
		$j = $x;
	} else {
		$j = 0;
		while (($x > $x_boundaries[$j]) && ($j < $xmax)) {
			$j++;
		}
	}
	if ($zgiven) {
		$i = $z;
	} else {
		$i = 0;
		while (($z > $z_boundaries[$i]) && ($i < $zmax)) {
			$i++;
		}
	}
	if (isset($ydata[$i][$j])) {
		$ydata[$i][$j] += $val;
	} else {
		$ydata[$i][$j] = $val;
	}
}

/**
 * @param string      $mytitle
 * @param integer[][] $xdata
 * @param string      $xtitle
 * @param integer[][] $ydata
 * @param string      $ytitle
 * @param string[]    $legend
 */
function my_plot($mytitle, $xdata, $xtitle, $ydata, $ytitle, $legend) {
	global $percentage, $male_female, $ymax, $scalefactor, $datastring, $imgurl;

	// Google Chart API only allows text encoding for numbers less than 100
	// and it does not allow adjusting the y-axis range, so we must find the maximum y-value
	// in order to adjust beforehand by changing the numbers

	if ($male_female == 1) {
		$stop = 2;
	} else {
		$stop = count($ydata);
	}
	$yprocentmax = 0;
	if ($percentage) {
		$yt = array();
		for ($i = 0; $i < $stop; $i++) {
			$ytotal   = 0;
			$ymax     = 0;
			$yprocent = 0;
			if (isset($ydata[$i])) {
				for ($j = 0; $j < count($ydata[$i]); $j++) {
					if ($ydata[$i][$j] > $ymax) {
						$ymax = $ydata[$i][$j];
					}
					$ytotal += $ydata[$i][$j];
				}
				$yt[$i] = $ytotal;
				if ($ytotal > 0) {
					$yprocent = round($ymax / $ytotal * 100, 1);
				}
				if ($yprocentmax < $yprocent) {
					$yprocentmax = $yprocent;
				}
			}
		}
		$ymax = $yprocentmax;
		if ($ymax > 0) {
			$scalefactor = 100.0 / $ymax;
		} else {
			$scalefactor = 0;
		}
		$datastring = 'chd=t:';
		for ($i = 0; $i < $stop; $i++) {
			if (isset($ydata[$i])) {
				for ($j = 0; $j < count($ydata[$i]); $j++) {
					if ($yt[$i] > 0) {
						$datastring .= round($ydata[$i][$j] / $yt[$i] * 100 * $scalefactor, 1);
					} else {
						$datastring .= '0';
					}
					if (!($j == (count($ydata[$i]) - 1))) {
						$datastring .= ',';
					}
				}
				if (!($i == ($stop - 1))) {
					$datastring .= '|';
				}
			}
		}
	} else {
		for ($i = 0; $i < $stop; $i++) {
			for ($j = 0; $j < count($ydata[$i]); $j++) {
				if ($ydata[$i][$j] > $ymax) {
					$ymax = $ydata[$i][$j];
				}
			}
		}
		if ($ymax > 0) {
			$scalefactor = 100.0 / $ymax;
		} else {
			$scalefactor = 0;
		}
		$datastring = 'chd=t:';
		for ($i = 0; $i < $stop; $i++) {
			for ($j = 0; $j < count($ydata[$i]); $j++) {
				$datastring .= round($ydata[$i][$j] * $scalefactor, 1);
				if (!($j == (count($ydata[$i]) - 1))) {
					$datastring .= ',';
				}
			}
			if (!($i == ($stop - 1))) {
				$datastring .= '|';
			}
		}
	}
	$colors      = array('0000FF', 'FFA0CB', '9F00FF', 'FF7000', '905030', 'FF0000', '00FF00', 'F0F000');
	$colorstring = 'chco=';
	for ($i = 0; $i < $stop; $i++) {
		if (isset($colors[$i])) {
			$colorstring .= $colors[$i];
			if ($i != ($stop - 1)) {
				$colorstring .= ',';
			}
		}
	}

	$titleLength = strpos($mytitle . "\n", "\n");
	$title       = substr($mytitle, 0, $titleLength);

	$imgurl = 'https://chart.googleapis.com/chart?cht=bvg&amp;chs=950x300&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=' . rawurlencode($title) . '&amp;' . $datastring . '&amp;' . $colorstring . '&amp;chbh=';
	if (count($ydata) > 3) {
		$imgurl .= '5,1';
	} else if (count($ydata) < 2) {
		$imgurl .= '45,1';
	} else {
		$imgurl .= '20,3';
	}
	$imgurl .= '&amp;chxt=x,x,y,y&amp;chxl=0:|';
	for ($i = 0; $i < count($xdata); $i++) {
		$imgurl .= $xdata[$i] . '|';
	}

	$imgurl .= '1:||||' . rawurlencode($xtitle) . '|2:|';
	$imgurl .= '0|';
	if ($percentage) {
		for ($i = 1; $i < 11; $i++) {
			if ($ymax < 11) {
				$imgurl .= round($ymax * $i / 10, 1) . '|';
			} else {
				$imgurl .= round($ymax * $i / 10, 0) . '|';
			}
		}
		$imgurl .= '3:||%|';
	} else {
		if ($ymax < 11) {
			for ($i = 1; $i < $ymax + 1; $i++) {
				$imgurl .= round($ymax * $i / ($ymax), 0) . '|';
			}
		} else {
			for ($i = 1; $i < 11; $i++) {
				$imgurl .= round($ymax * $i / 10, 0) . '|';
			}
		}
		$imgurl .= '3:||' . rawurlencode($ytitle) . '|';
	}
	//only show legend if y-data is non-2-dimensional
	if (count($ydata) > 1) {
		$imgurl .= '&amp;chdl=';
		for ($i = 0; $i < count($legend); $i++) {
			$imgurl .= rawurlencode($legend[$i]);
			if (!($i == (count($legend) - 1))) {
				$imgurl .= '|';
			}
		}
	}
	$title = strstr($mytitle, '|', true);
	echo '<img src="', $imgurl, '" width="950" height="300" alt="', WT_Filter::escapeHtml($title), '" title="', WT_Filter::escapeHtml($title), '">';
}

/**
 * @param string $x_axis_boundaries
 */
function calculate_axis($x_axis_boundaries) {
	global $x_axis, $xdata, $xmax, $x_boundaries;

	//calculate xdata and zdata elements out of given POST values
	$hulpar = explode(',', $x_axis_boundaries);
	$i      = 1;
	if ($x_axis == 21 && $hulpar[0] == 1) {
		$xdata[0] = 0;
	} else if ($x_axis == 16 && $hulpar[0] == 0) {
		$xdata[0] = WT_I18N::translate('before');
	} else if ($x_axis == 16 && $hulpar[0] < 0) {
		$xdata[0] = WT_I18N::translate('over') . ' ' . $hulpar[0];
	} else {
		$xdata[0] = WT_I18N::translate('less than') . ' ' . $hulpar[0];
	}
	$x_boundaries[0] = $hulpar[0] - 1;
	while (isset($hulpar[$i])) {
		$i1 = $i - 1;
		if (($hulpar[$i] - $hulpar[$i1]) == 1) {
			$xdata[$i]        = $hulpar[$i1];
			$x_boundaries[$i] = $hulpar[$i1];
		} else if ($hulpar[$i1] == $hulpar[0]) {
			$xdata[$i]        = $hulpar[$i1] . '-' . $hulpar[$i];
			$x_boundaries[$i] = $hulpar[$i];
		} else {
			$xdata[$i]        = ($hulpar[$i1] + 1) . '-' . $hulpar[$i];
			$x_boundaries[$i] = $hulpar[$i];
		}
		$i++;
	}
	$xdata[$i]        = $hulpar[$i - 1];
	$x_boundaries[$i] = $hulpar[$i - 1];
	if ($hulpar[$i - 1] == $i) {
		$xmax = $i + 1;
	} else {
		$xmax = $i;
	}
	$xdata[$xmax]        = WT_I18N::translate('over') . ' ' . $hulpar[$i - 1];
	$x_boundaries[$xmax] = 10000;
	$xmax                = $xmax + 1;
	if ($xmax > 20) {
		$xmax = 20;
	}
}

/**
 * @param string $boundaries_z_axis
 */
function calculate_legend($boundaries_z_axis) {
	global $legend, $zmax, $z_boundaries;

	// calculate the legend values
	$hulpar = explode(',', $boundaries_z_axis);
	$i      = 1;
	// I18N: %d is a year
	$date            = new WT_Date('BEF ' . $hulpar[0]);
	$legend[0]       = strip_tags($date->display());
	$z_boundaries[0] = $hulpar[0] - 1;
	while (isset($hulpar[$i])) {
		$i1               = $i - 1;
		$date             = new WT_Date('BET ' . $hulpar[$i1] . ' AND ' . ($hulpar[$i] - 1));
		$legend[$i]       = strip_tags($date->display());
		$z_boundaries[$i] = $hulpar[$i] - 1;
		$i++;
	}
	$zmax  = $i;
	$zmax1 = $zmax - 1;
	// I18N: %d is a year
	$date                = new WT_Date('AFT ' . $hulpar[$zmax1]);
	$legend[$zmax]       = strip_tags($date->display());
	$z_boundaries[$zmax] = 10000;
	$zmax                = $zmax + 1;
	if ($zmax > 8) {
		$zmax = 8;
	}
}

/**
 * @param integer $current
 * @param string  $indfam
 * @param boolean $xg
 * @param boolean $zg
 * @param string  $titstr
 * @param string  $xt
 * @param string  $gx
 * @param string  $gz
 * @param string  $myfunc
 */
function set_parameters($current, $indfam, $xg, $zg, $titstr, $xt, $gx, $gz, $myfunc) {
	global $x_axis, $y_axis, $z_axis, $n1, $months;
	global $legend, $xdata, $ydata, $xmax, $zmax, $z_boundaries, $xgiven, $zgiven, $percentage, $male_female;
	global $stats;

	if (!function_exists($myfunc)) {
		echo WT_I18N::translate('%s not implemented', $myfunc);
		exit;
	}

	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = WT_Date_Gregorian::monthNameNominativeCase($i + 1, false);
	}

	$months = array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC');

	if ($x_axis == $current) {
		if (($x_axis == 13 || $x_axis == 15) && $z_axis == 301) {
			$z_axis = 300;
		}
		$xgiven            = $xg;
		$zgiven            = $zg;
		$title             = $titstr;
		$xtitle            = $xt;
		$ytitle            = WT_I18N::translate('numbers');
		$boundaries_x_axis = $gx;
		$boundaries_z_axis = $gz;
		if ($xg == true) {
			$xdata = $monthdata;
			$xmax  = 12;
		} else {
			calculate_axis($boundaries_x_axis);
		}
		if ($z_axis != 300 && $z_axis != 301) {
			calculate_legend($boundaries_z_axis);
		}
		$percentage = false;
		if ($y_axis == 201) {
			$percentage = false;
			if ($current == 13 || $current == 15 || $current == 16 || $current == 21) {
				$ytitle = WT_I18N::translate('Families');
			} elseif ($current == 14) {
				$ytitle = WT_I18N::translate('Children');
			} else {
				$ytitle = WT_I18N::translate('Individuals');
			}
		} elseif ($y_axis == 202) {
			$percentage = true;
			$ytitle     = WT_I18N::translate('percentage');
		}
		$male_female = false;
		if ($z_axis == 300) {
			$zgiven          = false;
			$legend[0]       = 'all';
			$zmax            = 1;
			$z_boundaries[0] = 100000;
		} elseif ($z_axis == 301) {
			$male_female = true;
			$zgiven      = true;
			$legend[0]   = WT_I18N::translate('Male');
			$legend[1]   = WT_I18N::translate('Female');
			$zmax        = 2;
			$xtitle      = $xtitle . WT_I18N::translate(' per gender');
		} elseif ($z_axis == 302) {
			$xtitle = $xtitle . WT_I18N::translate(' per time period');
		}
		//-- reset the data array
		for ($i = 0; $i < $zmax; $i++) {
			for ($j = 0; $j < $xmax; $j++) {
				$ydata[$i][$j] = 0;
			}
		}
		$myfunc();
		if ($indfam === 'IND') {
			$hstr = $title . '|' . WT_I18N::translate('Counts ') . ' ' . WT_I18N::number($n1) . ' ' . WT_I18N::translate('of') . ' ' . $stats->totalIndividuals();
		} elseif ($x_axis == 21) {
			$hstr = $title . '|' . WT_I18N::translate('Counts ') . ' ' . WT_I18N::number($n1) . ' ' . WT_I18N::translate('of') . ' ' . $stats->totalChildren();
		} else {
			$hstr = $title . '|' . WT_I18N::translate('Counts ') . ' ' . WT_I18N::number($n1) . ' ' . WT_I18N::translate('of') . ' ' . $stats->totalFamilies();
		}
		my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	}
}

//-- ========= start of main program =========
$action = WT_Filter::post('action');

if ($action === 'update') {
	$x_axis = $_POST['x-as'];
	$y_axis = $_POST['y-as'];
	if (isset($_POST['z-as'])) {
		$z_axis = $_POST['z-as'];
	} else {
		$z_axis = 300;
	}
	$xgl  = $_POST['x-axis-boundaries-ages'];
	$xglm = $_POST['x-axis-boundaries-ages_m'];
	$xgm  = $_POST['x-axis-boundaries-months'];
	$xga  = $_POST['x-axis-boundaries-numbers'];
	if (isset($_POST['z-axis-boundaries-periods'])) {
		$zgp = $_POST['z-axis-boundaries-periods'];
	} else {
		$zgp = 0;
	}
	$chart_shows = $_POST['chart_shows'];
	$chart_type  = $_POST['chart_type'];
	$surname     = $_POST['SURN'];

	$WT_SESSION->statTicks[$GEDCOM]['x_axis_boundary_ages']          = $xgl;
	$WT_SESSION->statTicks[$GEDCOM]['x_axis_boundary_ages_marriage'] = $xglm;
	$WT_SESSION->statTicks[$GEDCOM]['x_axis_boundary_months']        = $xgm;
	$WT_SESSION->statTicks[$GEDCOM]['x_axis_boundary_numbers']       = $xga;
	$WT_SESSION->statTicks[$GEDCOM]['z_axis_boundary_periods']       = $zgp;
	$WT_SESSION->statTicks[$GEDCOM]['chart_shows']                   = $chart_shows;
	$WT_SESSION->statTicks[$GEDCOM]['chart_type']                    = $chart_type;
	$WT_SESSION->statTicks[$GEDCOM]['SURN']                          = $surname;

	// Save the input variables
	$savedInput                          = array();
	$savedInput['x_axis']                = $x_axis;
	$savedInput['y_axis']                = $y_axis;
	$savedInput['z_axis']                = $z_axis;
	$savedInput['xgl']                   = $xgl;
	$savedInput['xglm']                  = $xglm;
	$savedInput['xgm']                   = $xgm;
	$savedInput['xga']                   = $xga;
	$savedInput['zgp']                   = $zgp;
	$savedInput['chart_shows']           = $chart_shows;
	$savedInput['chart_type']            = $chart_type;
	$savedInput['SURN']                  = $surname;
	$WT_SESSION->statisticsplot[$GEDCOM] = $savedInput;
	unset($savedInput);
} else {
	// Recover the saved input variables
	$savedInput  = $WT_SESSION->statisticsplot[$GEDCOM];
	$x_axis      = $savedInput['x_axis'];
	$y_axis      = $savedInput['y_axis'];
	$z_axis      = $savedInput['z_axis'];
	$xgl         = $savedInput['xgl'];
	$xglm        = $savedInput['xglm'];
	$xgm         = $savedInput['xgm'];
	$xga         = $savedInput['xga'];
	$zgp         = $savedInput['zgp'];
	$chart_shows = $savedInput['chart_shows'];
	$chart_type  = $savedInput['chart_type'];
	$surname     = $savedInput['SURN'];
	unset($savedInput);
}
Zend_Session::writeClose();

echo '<div class="statistics_chart" title="', WT_I18N::translate('Statistics plot'), '">';

//-- Set params for request out of the information for plot
$g_xas = '1,2,3,4,5,6,7,8,9,10,11,12'; //should not be needed. but just for month

switch ($x_axis) {
case '11':
	//--------- nr, type, xgiven, zgiven, title, xtitle, ytitle, boundaries_x, boundaries-z, function
	set_parameters(11, 'IND', true, false, WT_I18N::translate('Month of birth'), WT_I18N::translate('month'), $g_xas, $zgp, 'month_of_birth');
	break;
case '12':
	set_parameters(12, 'IND', true, false, WT_I18N::translate('Month of death'), WT_I18N::translate('month'), $g_xas, $zgp, 'month_of_death');
	break;
case '13':
	set_parameters(13, 'FAM', true, false, WT_I18N::translate('Month of marriage'), WT_I18N::translate('month'), $g_xas, $zgp, 'month_of_marriage');
	break;
case '14':
	set_parameters(14, 'FAM', true, false, WT_I18N::translate('Month of birth of first child in a relation'), WT_I18N::translate('month'), $g_xas, $zgp, 'month_of_birth_of_first_child');
	break;
case '15':
	set_parameters(15, 'FAM', true, false, WT_I18N::translate('Month of first marriage'), WT_I18N::translate('month'), $g_xas, $zgp, 'month_of_first_marriage');
	break;
case '16':
	set_parameters(16, 'FAM', false, false, WT_I18N::translate('Months between marriage and first child'), WT_I18N::translate('Months between marriage and birth of first child'), $xgm, $zgp, 'months_between_marriage_and_first_child');
	break;
case '17':
	set_parameters(17, 'IND', false, false, WT_I18N::translate('Age related to birth year'), WT_I18N::translate('age'), $xgl, $zgp, 'lifespan_by_birth_year');
	break;
case '18':
	set_parameters(18, 'IND', false, false, WT_I18N::translate('Age related to death year'), WT_I18N::translate('age'), $xgl, $zgp, 'lifespan_by_death_year');
	break;
case '19':
	set_parameters(19, 'IND', false, false, WT_I18N::translate('Age in year of marriage'), WT_I18N::translate('age'), $xglm, $zgp, 'age_at_marriage');
	break;
case '20':
	set_parameters(20, 'IND', false, false, WT_I18N::translate('Age in year of first marriage'), WT_I18N::translate('age'), $xglm, $zgp, 'age_at_first_marriage');
	break;
case '21':
	set_parameters(21, 'FAM', false, false, WT_I18N::translate('Number of children'), WT_I18N::translate('children'), $xga, $zgp, 'number_of_children');
	break;
case '1':
	echo $stats->chartDistribution(array($chart_shows, $chart_type, $surname));
	break;
case '2':
	echo $stats->chartDistribution(array($chart_shows, 'birth_distribution_chart'));
	break;
case '3':
	echo $stats->chartDistribution(array($chart_shows, 'death_distribution_chart'));
	break;
case '4':
	echo $stats->chartDistribution(array($chart_shows, 'marriage_distribution_chart'));
	break;
default:
	echo '<i class="icon-loading-large"></i>';
	exit;
}
echo '</div>';
