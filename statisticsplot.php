<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Date\GregorianDate;

define('WT_SCRIPT_NAME', 'statisticsplot.php');
require './includes/session.php';

/**
 * Month of birth
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function month_of_birth($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->statsBirthQuery(false);
		foreach ($num as $values) {
			foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
				if ($month === $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$total += $values['total'];
				}
			}
		}
	} elseif ($z_axis === 301) {
		$num = $stats->statsBirthQuery(false, true);
		foreach ($num as $values) {
			foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
				if ($month === $values['d_month']) {
					if ($values['i_sex'] === 'M') {
						fill_y_data(0, $key, $values['total']);
						$total += $values['total'];
					} elseif ($values['i_sex'] === 'F') {
						fill_y_data(1, $key, $values['total']);
						$total += $values['total'];
					}
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsBirthQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
					if ($month === $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$total += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Month of birth of first child in a relation
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function month_of_birth_of_first_child($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->monthFirstChildQuery(false);
		foreach ($num as $values) {
			foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
				if ($month === $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$total += $values['total'];
				}
			}
		}
	} elseif ($z_axis === 301) {
		$num = $stats->monthFirstChildQuery(false, true);
		foreach ($num as $values) {
			foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
				if ($month === $values['d_month']) {
					if ($values['i_sex'] === 'M') {
						fill_y_data(0, $key, $values['total']);
						$total += $values['total'];
					} elseif ($values['i_sex'] === 'F') {
						fill_y_data(1, $key, $values['total']);
						$total += $values['total'];
					}
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->monthFirstChildQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
					if ($month === $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$total += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Month of death
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function month_of_death($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->statsDeathQuery(false);
		foreach ($num as $values) {
			foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
				if ($month === $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$total += $values['total'];
				}
			}
		}
	} elseif ($z_axis === 301) {
		$num = $stats->statsDeathQuery(false, true);
		foreach ($num as $values) {
			foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
				if ($month === $values['d_month']) {
					if ($values['i_sex'] === 'M') {
						fill_y_data(0, $key, $values['total']);
						$total += $values['total'];
					} elseif ($values['i_sex'] === 'F') {
						fill_y_data(1, $key, $values['total']);
						$total += $values['total'];
					}
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsDeathQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
					if ($month === $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$total += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Month of marriage
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function month_of_marriage($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->statsMarrQuery(false, false);
		foreach ($num as $values) {
			foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
				if ($month === $values['d_month']) {
					fill_y_data(0, $key, $values['total']);
					$total += $values['total'];
				}
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsMarrQuery(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
					if ($month === $values['d_month']) {
						fill_y_data($boundary, $key, $values['total']);
						$total += $values['total'];
					}
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Month of first marriage
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function month_of_first_marriage($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num  = $stats->statsMarrQuery(false, true);
		$indi = array();
		$fam  = array();
		foreach ($num as $values) {
			if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
				foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
					if ($month === $values['month']) {
						fill_y_data(0, $key, 1);
						$total++;
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
					foreach (array('JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC') as $key => $month) {
						if ($month === $values['month']) {
							fill_y_data($boundary, $key, 1);
							$total++;
						}
					}
					$indi[] = $values['indi'];
					$fam[]  = $values['fams'];
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Age related to birth year
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function lifespan_by_birth_year($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->statsAgeQuery(false, 'BIRT');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int) ($age_value / 365.25), 1);
				$total++;
			}
		}
	} elseif ($z_axis === 301) {
		$num = $stats->statsAgeQuery(false, 'BIRT', 'M');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int) ($age_value / 365.25), 1);
				$total++;
			}
		}
		$num = $stats->statsAgeQuery(false, 'BIRT', 'F');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(1, (int) ($age_value / 365.25), 1);
				$total++;
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsAgeQuery(false, 'BIRT', 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					fill_y_data($boundary, (int) ($age_value / 365.25), 1);
					$total++;
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Age related to death year
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function lifespan_by_death_year($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->statsAgeQuery(false, 'DEAT');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int) ($age_value / 365.25), 1);
				$total++;
			}
		}
	} elseif ($z_axis === 301) {
		$num = $stats->statsAgeQuery(false, 'DEAT', 'M');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(0, (int) ($age_value / 365.25), 1);
				$total++;
			}
		}
		$num = $stats->statsAgeQuery(false, 'DEAT', 'F');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_y_data(1, (int) ($age_value / 365.25), 1);
				$total++;
			}
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsAgeQuery(false, 'DEAT', 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					fill_y_data($boundary, (int) ($age_value / 365.25), 1);
					$total++;
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Age in year of marriage
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function age_at_marriage($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->statsMarrAgeQuery(false, 'M');
		foreach ($num as $values) {
			fill_y_data(0, (int) ($values['age'] / 365.25), 1);
			$total++;
		}
		$num = $stats->statsMarrAgeQuery(false, 'F');
		foreach ($num as $values) {
			fill_y_data(0, (int) ($values['age'] / 365.25), 1);
			$total++;
		}
	} elseif ($z_axis === 301) {
		$num = $stats->statsMarrAgeQuery(false, 'M');
		foreach ($num as $values) {
			fill_y_data(0, (int) ($values['age'] / 365.25), 1);
			$total++;
		}
		$num = $stats->statsMarrAgeQuery(false, 'F');
		foreach ($num as $values) {
			fill_y_data(1, (int) ($values['age'] / 365.25), 1);
			$total++;
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsMarrAgeQuery(false, 'M', $zstart, $boundary);
			foreach ($num as $values) {
				fill_y_data($boundary, (int) ($values['age'] / 365.25), 1);
				$total++;
			}
			$num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
			foreach ($num as $values) {
				fill_y_data($boundary, (int) ($values['age'] / 365.25), 1);
				$total++;
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Age in year of first marriage
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function age_at_first_marriage($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num  = $stats->statsMarrAgeQuery(false, 'M');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(0, (int) ($values['age'] / 365.25), 1);
				$total++;
				$indi[] = $values['d_gid'];
			}
		}
		$num  = $stats->statsMarrAgeQuery(false, 'F');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(0, (int) ($values['age'] / 365.25), 1);
				$total++;
				$indi[] = $values['d_gid'];
			}
		}
	} elseif ($z_axis === 301) {
		$num  = $stats->statsMarrAgeQuery(false, 'M');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(0, (int) ($values['age'] / 365.25), 1);
				$total++;
				$indi[] = $values['d_gid'];
			}
		}
		$num  = $stats->statsMarrAgeQuery(false, 'F');
		$indi = array();
		foreach ($num as $values) {
			if (!in_array($values['d_gid'], $indi)) {
				fill_y_data(1, (int) ($values['age'] / 365.25), 1);
				$total++;
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
					fill_y_data($boundary, (int) ($values['age'] / 365.25), 1);
					$total++;
					$indi[] = $values['d_gid'];
				}
			}
			$num = $stats->statsMarrAgeQuery(false, 'F', $zstart, $boundary);
			foreach ($num as $values) {
				if (!in_array($values['d_gid'], $indi)) {
					fill_y_data($boundary, (int) ($values['age'] / 365.25), 1);
					$total++;
					$indi[] = $values['d_gid'];
				}
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Number of children
 *
 * @param int   $z_axis
 * @param int[] $z_boundaries
 * @param Stats $stats
 *
 * @return int
 */
function number_of_children($z_axis, array $z_boundaries, Stats $stats) {
	$total = 0;

	if ($z_axis === 300) {
		$num = $stats->statsChildrenQuery(false);
		foreach ($num as $values) {
			fill_y_data(0, $values['f_numchil'], $values['total']);
			$total += $values['f_numchil'] * $values['total'];
		}
	} elseif ($z_axis === 301) {
		$num = $stats->statsChildrenQuery(false, 'M');
		foreach ($num as $values) {
			fill_y_data(0, $values['num'], $values['total']);
			$total += $values['num'] * $values['total'];
		}
		$num = $stats->statsChildrenQuery(false, 'F');
		foreach ($num as $values) {
			fill_y_data(1, $values['num'], $values['total']);
			$total += $values['num'] * $values['total'];
		}
	} else {
		$zstart = 0;
		foreach ($z_boundaries as $boundary) {
			$num = $stats->statsChildrenQuery(false, 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				fill_y_data($boundary, $values['f_numchil'], $values['total']);
				$total += $values['f_numchil'] * $values['total'];
			}
			$zstart = $boundary + 1;
		}
	}

	return $total;
}

/**
 * Calculate the Y axis.
 *
 * @param int $z
 * @param int $x
 * @param int $val
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
 * Plot the data.
 *
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

	if ($male_female) {
		$stop = 2;
	} else {
		$stop = count($ydata);
	}
	if ($percentage) {
		$ypercentmax = 0;
		$yt          = array();
		for ($i = 0; $i < $stop; $i++) {
			if (isset($ydata[$i])) {
				$ymax   = max($ydata[$i]);
				$yt[$i] = array_sum($ydata[$i]);
				if ($yt[$i] > 0) {
					$ypercent    = round($ymax / $yt[$i] * 100, 1);
					$ypercentmax = max($ypercentmax, $ypercent);
				}
			}
		}
		$ymax = $ypercentmax;
		if ($ymax > 0) {
			$scalefactor = 100.0 / $ymax;
		} else {
			$scalefactor = 0;
		}
		$datastring = 'chd=t:';
		for ($i = 0; $i < $stop; $i++) {
			if (isset($ydata[$i])) {
				foreach ($ydata[$i] as $j => $data) {
					if ($j > 0) {
						$datastring .= ',';
					}
					if ($yt[$i] > 0) {
						$datastring .= round($data / $yt[$i] * 100 * $scalefactor, 1);
					} else {
						$datastring .= '0';
					}
				}
				if ($i !== $stop - 1) {
					$datastring .= '|';
				}
			}
		}
	} else {
		for ($i = 0; $i < $stop; $i++) {
			$ymax = max($ymax, max($ydata[$i]));
		}
		if ($ymax > 0) {
			$scalefactor = 100.0 / $ymax;
		} else {
			$scalefactor = 0;
		}
		$datastring = 'chd=t:';
		for ($i = 0; $i < $stop; $i++) {
			foreach ($ydata[$i] as $j => $data) {
				if ($j > 0) {
					$datastring .= ',';
				}
				$datastring .= round($data * $scalefactor, 1);
			}
			if ($i !== $stop - 1) {
				$datastring .= '|';
			}
		}
	}
	$colors      = array('0000FF', 'FFA0CB', '9F00FF', 'FF7000', '905030', 'FF0000', '00FF00', 'F0F000');
	$colorstring = 'chco=';
	for ($i = 0; $i < $stop; $i++) {
		if (isset($colors[$i])) {
			$colorstring .= $colors[$i];
			if ($i !== ($stop - 1)) {
				$colorstring .= ',';
			}
		}
	}

	$titleLength = strpos($mytitle . "\n", "\n");
	$title       = substr($mytitle, 0, $titleLength);

	$imgurl = 'https://chart.googleapis.com/chart?cht=bvg&amp;chs=950x300&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=' . rawurlencode($title) . '&amp;' . $datastring . '&amp;' . $colorstring . '&amp;chbh=';
	if (count($ydata) > 3) {
		$imgurl .= '5,1';
	} elseif (count($ydata) < 2) {
		$imgurl .= '45,1';
	} else {
		$imgurl .= '20,3';
	}
	$imgurl .= '&amp;chxt=x,x,y,y&amp;chxl=0:|';
	foreach ($xdata as $data) {
		$imgurl .= rawurlencode($data) . '|';
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
	// Only show legend if y-data is non-2-dimensional
	if (count($ydata) > 1) {
		$imgurl .= '&amp;chdl=';
		foreach ($legend as $i => $data) {
			if ($i > 0) {
				$imgurl .= '|';
			}
			$imgurl .= rawurlencode($data);
		}
	}
	$title = strstr($mytitle, '|', true);
	echo '<img src="', $imgurl, '" width="950" height="300" alt="', Filter::escapeHtml($title), '" title="', Filter::escapeHtml($title), '">';
}

/**
 * Create the X azxs.
 *
 * @param string $x_axis_boundaries
 */
function calculate_axis($x_axis_boundaries) {
	global $x_axis, $xdata, $xmax, $x_boundaries;

	// Calculate xdata and zdata elements out of chart values
	$hulpar = explode(',', $x_axis_boundaries);
	$i      = 1;
	if ($x_axis === 21 && $hulpar[0] == 1) {
		$xdata[0] = 0;
	} else {
		$xdata[0] = I18N::translate('less than') . ' ' . $hulpar[0];
	}
	$x_boundaries[0] = $hulpar[0] - 1;
	while (isset($hulpar[$i])) {
		$i1 = $i - 1;
		if (($hulpar[$i] - $hulpar[$i1]) === 1) {
			$xdata[$i]        = $hulpar[$i1];
			$x_boundaries[$i] = $hulpar[$i1];
		} elseif ($hulpar[$i1] === $hulpar[0]) {
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
	if ($hulpar[$i - 1] === $i) {
		$xmax = $i + 1;
	} else {
		$xmax = $i;
	}
	$xdata[$xmax]        = I18N::translate('over') . ' ' . $hulpar[$i - 1];
	$x_boundaries[$xmax] = 10000;
	$xmax                = $xmax + 1;
	if ($xmax > 20) {
		$xmax = 20;
	}
}

/**
 * Calculate the Z axis.
 *
 * @param string $boundaries_z_axis
 */
function calculate_legend($boundaries_z_axis) {
	global $legend, $zmax, $z_boundaries;

	// calculate the legend values
	$hulpar          = explode(',', $boundaries_z_axis);
	$i               = 1;
	$date            = new Date('BEF ' . $hulpar[0]);
	$legend[0]       = strip_tags($date->display());
	$z_boundaries[0] = $hulpar[0] - 1;
	while (isset($hulpar[$i])) {
		$i1               = $i - 1;
		$date             = new Date('BET ' . $hulpar[$i1] . ' AND ' . ($hulpar[$i] - 1));
		$legend[$i]       = strip_tags($date->display());
		$z_boundaries[$i] = $hulpar[$i] - 1;
		$i++;
	}
	$zmax                = $i;
	$zmax1               = $zmax - 1;
	$date                = new Date('AFT ' . $hulpar[$zmax1]);
	$legend[$zmax]       = strip_tags($date->display());
	$z_boundaries[$zmax] = 10000;
	$zmax                = $zmax + 1;
	if ($zmax > 8) {
		$zmax = 8;
	}
}

global $legend, $xdata, $ydata, $xmax, $zmax, $z_boundaries, $xgiven, $zgiven, $percentage, $male_female;

$x_axis       = Filter::getInteger('x-as', 1, 21, 11);
$y_axis       = Filter::getInteger('y-as', 201, 202, 201);
$z_axis       = Filter::getInteger('z-as', 300, 302, 302);
$stats        = new Stats($WT_TREE);
$z_boundaries = array();

echo '<div class="statistics_chart" title="', I18N::translate('Statistics chart'), '">';

switch ($x_axis) {
case '1':
	echo $stats->chartDistribution(array(Filter::get('chart_shows'), Filter::get('chart_type'), Filter::get('SURN')));
	break;
case '2':
	echo $stats->chartDistribution(array(Filter::get('chart_shows'), 'birth_distribution_chart'));
	break;
case '3':
	echo $stats->chartDistribution(array(Filter::get('chart_shows'), 'death_distribution_chart'));
	break;
case '4':
	echo $stats->chartDistribution(array(Filter::get('chart_shows'), 'marriage_distribution_chart'));
	break;
case '11':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = true;
	$zgiven            = false;
	$title             = I18N::translate('Month of birth');
	$xtitle            = I18N::translate('month');
	$ytitle            = I18N::translate('numbers');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	$xdata             = $monthdata;
	$xmax              = 12;
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Individuals');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = month_of_birth($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalIndividuals();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '12':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = true;
	$zgiven            = false;
	$title             = I18N::translate('Month of death');
	$xtitle            = I18N::translate('month');
	$ytitle            = I18N::translate('numbers');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	$xdata             = $monthdata;
	$xmax              = 12;
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Individuals');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = month_of_death($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalIndividuals();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '13':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}

	if ($z_axis === 301) {
		$z_axis = 300;
	}
	$xgiven            = true;
	$zgiven            = false;
	$title             = I18N::translate('Month of marriage');
	$xtitle            = I18N::translate('month');
	$ytitle            = I18N::translate('numbers');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	$xdata             = $monthdata;
	$xmax              = 12;
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Families');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = month_of_marriage($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalFamilies();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '14':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = true;
	$zgiven            = false;
	$title             = I18N::translate('Month of birth of first child in a relation');
	$xtitle            = I18N::translate('month');
	$ytitle            = I18N::translate('numbers');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	$xdata             = $monthdata;
	$xmax              = 12;
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Children');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = month_of_birth_of_first_child($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalFamilies();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '15':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}

	if ($z_axis === 301) {
		$z_axis = 300;
	}
	$xgiven            = true;
	$zgiven            = false;
	$title             = I18N::translate('Month of first marriage');
	$xtitle            = I18N::translate('month');
	$ytitle            = I18N::translate('numbers');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	$xdata             = $monthdata;
	$xmax              = 12;
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Families');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = month_of_first_marriage($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalFamilies();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '17':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = false;
	$zgiven            = false;
	$title             = I18N::translate('Age related to birth year');
	$xtitle            = I18N::translate('age');
	$ytitle            = I18N::translate('numbers');
	$boundaries_x_axis = Filter::get('x-axis-boundaries-ages');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	calculate_axis($boundaries_x_axis);
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Individuals');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = lifespan_by_birth_year($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalIndividuals();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '18':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = false;
	$zgiven            = false;
	$title             = I18N::translate('Age related to death year');
	$xtitle            = I18N::translate('age');
	$ytitle            = I18N::translate('numbers');
	$boundaries_x_axis = Filter::get('x-axis-boundaries-ages');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	calculate_axis($boundaries_x_axis);
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Individuals');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = lifespan_by_death_year($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalIndividuals();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '19':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = false;
	$zgiven            = false;
	$title             = I18N::translate('Age in year of marriage');
	$xtitle            = I18N::translate('age');
	$ytitle            = I18N::translate('numbers');
	$boundaries_x_axis = Filter::get('x-axis-boundaries-ages_m');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	calculate_axis($boundaries_x_axis);
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Individuals');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female     = false;
	$z_boundaries[0] = 100000;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = age_at_marriage($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalIndividuals();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '20':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = false;
	$zgiven            = false;
	$title             = I18N::translate('Age in year of first marriage');
	$xtitle            = I18N::translate('age');
	$ytitle            = I18N::translate('numbers');
	$boundaries_x_axis = Filter::get('x-axis-boundaries-ages_m');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	calculate_axis($boundaries_x_axis);
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Individuals');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = age_at_first_marriage($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalIndividuals();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
case '21':
	$monthdata = array();
	for ($i = 0; $i < 12; ++$i) {
		$monthdata[$i] = GregorianDate::monthNameNominativeCase($i + 1, false);
	}
	$xgiven            = false;
	$zgiven            = false;
	$title             = I18N::translate('Number of children');
	$xtitle            = I18N::translate('children');
	$ytitle            = I18N::translate('numbers');
	$boundaries_x_axis = Filter::get('x-axis-boundaries-numbers');
	$boundaries_z_axis = Filter::get('z-axis-boundaries-periods', null, '0');
	calculate_axis($boundaries_x_axis);
	if ($z_axis !== 300 && $z_axis !== 301) {
		calculate_legend($boundaries_z_axis);
	}
	$percentage = false;
	if ($y_axis === 201) {
		$percentage = false;
		$ytitle     = I18N::translate('Families');
	} elseif ($y_axis === 202) {
		$percentage = true;
		$ytitle     = I18N::translate('percentage');
	}
	$male_female = false;
	if ($z_axis === 300) {
		$zgiven          = false;
		$legend[0]       = 'all';
		$zmax            = 1;
		$z_boundaries[0] = 100000;
	} elseif ($z_axis === 301) {
		$male_female = true;
		$zgiven      = true;
		$legend[0]   = I18N::translate('Male');
		$legend[1]   = I18N::translate('Female');
		$zmax        = 2;
		$xtitle      = $xtitle . I18N::translate(' per gender');
	} elseif ($z_axis === 302) {
		$xtitle = $xtitle . I18N::translate(' per time period');
	}
	//-- reset the data array
	for ($i = 0; $i < $zmax; $i++) {
		for ($j = 0; $j < $xmax; $j++) {
			$ydata[$i][$j] = 0;
		}
	}
	$total = number_of_children($z_axis, $z_boundaries, $stats);
	$hstr  = $title . '|' . I18N::translate('Counts ') . ' ' . I18N::number($total) . ' ' . I18N::translate('of') . ' ' . $stats->totalChildren();
	my_plot($hstr, $xdata, $xtitle, $ydata, $ytitle, $legend);
	break;
default:
	echo '<i class="icon-loading-large"></i>';
	break;
}
echo '</div>';
