<?php
/**
 * Creates some statistics out of the GEDCOM information.
 * We will start with the following possibilities
 * number of persons -> periodes of 50 years from 1700-2000
 * age -> periodes of 10 years (different for 0-1,1-5,5-10,10-20 etc)
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
 * @version $Id$
 * @package webtrees
 * @subpackage Lists
 */

define('WT_SCRIPT_NAME', 'statisticsplot.php');
require './includes/session.php';
require_once WT_ROOT.'includes/classes/class_stats.php';
/*
 * Initiate the stats object.
 */
$stats = new stats($GEDCOM);

// Month of birth
function bimo() {
	global $z_as, $months, $zgrenzen, $stats, $n1;

	if ($z_as == 300){
		$num = $stats->statsBirth(false);
		foreach ($num as $values) {
			foreach ($months as $key=>$month) {
				if($month==$values['d_month']) {
					fill_ydata(0, $key, $values['total']);
					$n1+=$values['total'];
				}
			}
		}
	}
	else if ($z_as == 301) {
		$num = $stats->statsBirth(false, true);
		foreach ($num as $values) {
			foreach ($months as $key=>$month) {
				if($month==$values['d_month']) {
					if ($values['i_sex']=='M') {
						fill_ydata(0, $key, $values['total']);
						$n1+=$values['total'];
					}
					else if ($values['i_sex']=='F') {
						fill_ydata(1, $key, $values['total']);
						$n1+=$values['total'];
					}
				}
			}
		}
	}
	else {
		$zstart = 0;
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsBirth(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($months as $key=>$month) {
					if($month==$values['d_month']) {
						fill_ydata($boundary, $key, $values['total']);
						$n1+=$values['total'];
					}
				}
			}
			$zstart=$boundary+1;
		}
	}
}

//Month of birth of first child in a relation
function bimo1() {
	global $z_as, $famgeg, $n1;
//TODO

echo "not work yet";
}

//Month of death
function demo() {
	global $z_as, $months, $zgrenzen, $stats, $n1;

	if ($z_as == 300){
		$num = $stats->statsDeath(false);
		foreach ($num as $values) {
			foreach ($months as $key=>$month) {
				if($month==$values['d_month']) {
					fill_ydata(0, $key, $values['total']);
					$n1+=$values['total'];
				}
			}
		}
	}
	else if ($z_as == 301) {
		$num = $stats->statsDeath(false, true);
		foreach ($num as $values) {
			foreach ($months as $key=>$month) {
				if($month==$values['d_month']) {
					if ($values['i_sex']=='M') {
						fill_ydata(0, $key, $values['total']);
						$n1+=$values['total'];
					}
					else if ($values['i_sex']=='F') {
						fill_ydata(1, $key, $values['total']);
						$n1+=$values['total'];
					}
				}
			}
		}
	}
	else {
		$zstart = 0;
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsDeath(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($months as $key=>$month) {
					if($month==$values['d_month']) {
						fill_ydata($boundary, $key, $values['total']);
						$n1+=$values['total'];
					}
				}
			}
			$zstart=$boundary+1;
		}
	}
}

//Month of marriage
function mamo() {
	global $z_as, $months, $zgrenzen, $stats, $n1;

	if ($z_as == 300) {
		$num = $stats->statsMarr(false, false);
		foreach ($num as $values) {
			foreach ($months as $key=>$month) {
				if($month==$values['d_month']) {
					fill_ydata(0, $key, $values['total']);
					$n1+=$values['total'];
				}
			}
		}
	}
	else {
		$zstart = 0;
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsMarr(false, false, $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($months as $key=>$month) {
					if($month==$values['d_month']) {
						fill_ydata($boundary, $key, $values['total']);
						$n1+=$values['total'];
					}
				}
			}
			$zstart=$boundary+1;
		}
	}
}

//Month of first marriage
function mamo1() {
	global $z_as, $months, $zgrenzen, $stats, $n1;

	if ($z_as == 300) {
		$num = $stats->statsMarr(false, true);
		$indi=array();
		$fam=array();
		foreach ($num as $values) {
			if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
				foreach ($months as $key=>$month) {
					if($month==$values['month']) {
						fill_ydata(0, $key, 1);
						$n1++;
					}
				}
				$indi[]=$values['indi'];
				$fam[]=$values['fams'];
			}
		}
	}
	else {
		$zstart = 0;
		$indi=array();
		$fam=array();
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsMarr(false, true, $zstart, $boundary);
			foreach ($num as $values) {
				if (!in_array($values['indi'], $indi) && !in_array($values['fams'], $fam)) {
					foreach ($months as $key=>$month) {
						if($month==$values['month']) {
							fill_ydata($boundary, $key, 1);
							$n1++;
						}
					}
					$indi[]=$values['indi'];
					$fam[]=$values['fams'];
				}
			}
			$zstart=$boundary+1;
		}
	}
	unset($indi, $fam);
}

//Months between marriage and first child
function mamam() {
	global $z_as, $n1;
//TODO

echo "not work yet";
}

//Age related to birth year
function agbi() {
	global $z_as, $zgrenzen, $stats, $n1;

	if ($z_as == 300){
		$num = $stats->statsAge(false, 'BIRT');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_ydata(0, floor($age_value/365.25), 1);
				$n1++;
			}
		}
	}
	else if ($z_as == 301) {
		$num = $stats->statsAge(false, 'BIRT', 'M');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_ydata(0, floor($age_value/365.25), 1);
				$n1++;
			}
		}
		$num = $stats->statsAge(false, 'BIRT', 'F');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_ydata(1, floor($age_value/365.25), 1);
				$n1++;
			}
		}
	}
	else {
		$zstart = 0;
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsAge(false, 'BIRT', 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					fill_ydata($boundary, floor($age_value/365.25), 1);
					$n1++;
				}
			}
			$zstart=$boundary+1;
		}
	}
}

//Age related to death year
function agde() {
	global $z_as, $zgrenzen, $stats, $n1;

	if ($z_as == 300){
		$num = $stats->statsAge(false, 'DEAT');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_ydata(0, floor($age_value/365.25), 1);
				$n1++;
			}
		}
	}
	else if ($z_as == 301) {
		$num = $stats->statsAge(false, 'DEAT', 'M');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_ydata(0, floor($age_value/365.25), 1);
				$n1++;
			}
		}
		$num = $stats->statsAge(false, 'DEAT', 'F');
		foreach ($num as $values) {
			foreach ($values as $age_value) {
				fill_ydata(1, floor($age_value/365.25), 1);
				$n1++;
			}
		}
	}
	else {
		$zstart = 0;
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsAge(false, 'DEAT', 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				foreach ($values as $age_value) {
					fill_ydata($boundary, floor($age_value/365.25), 1);
					$n1++;
				}
			}
			$zstart=$boundary+1;
		}
	}
}

//Age in year of marriage
function agma() {
	global $z_as, $zgrenzen, $stats, $n1;

	if ($z_as == 300){
		$num = $stats->statsMarrAge(false, 'M');
		foreach ($num as $values) {
			fill_ydata(0, floor($values['age']/365.25), 1);
			$n1++;
		}
		$num = $stats->statsMarrAge(false, 'F');
		foreach ($num as $values) {
			fill_ydata(0, floor($values['age']/365.25), 1);
			$n1++;
		}
	}
	else if ($z_as == 301) {
		$num = $stats->statsMarrAge(false, 'M');
		foreach ($num as $values) {
			fill_ydata(0, floor($values['age']/365.25), 1);
			$n1++;
		}
		$num = $stats->statsMarrAge(false, 'F');
		foreach ($num as $values) {
			fill_ydata(1, floor($values['age']/365.25), 1);
			$n1++;
		}
	}
	else {
		$zstart = 0;
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsMarrAge(false, 'M', $zstart, $boundary);
			foreach ($num as $values) {
				fill_ydata($boundary, floor($values['age']/365.25), 1);
				$n1++;
			}
			$num = $stats->statsMarrAge(false, 'F', $zstart, $boundary);
			foreach ($num as $values) {
				fill_ydata($boundary, floor($values['age']/365.25), 1);
				$n1++;
			}
			$zstart=$boundary+1;
		}
	}
}

//Age in year of first marriage
function agma1() {
	global $z_as, $zgrenzen, $stats, $n1;

	if ($z_as == 300) {
		$num = $stats->statsMarrAge(false, 'M');
		$indi=array();
		foreach ($num as $values) {
			if (!in_array($values['indi'], $indi)) {
				fill_ydata(0, floor($values['age']/365.25), 1);
				$n1++;
				$indi[]=$values['indi'];
			}
		}
		$num = $stats->statsMarrAge(false, 'F');
		$indi=array();
		foreach ($num as $values) {
			if (!in_array($values['indi'], $indi)) {
				fill_ydata(0, floor($values['age']/365.25), 1);
				$n1++;
				$indi[]=$values['indi'];
			}
		}
	}
	else if ($z_as == 301) {
		$num = $stats->statsMarrAge(false, 'M');
		$indi=array();
		foreach ($num as $values) {
			if (!in_array($values['indi'], $indi)) {
				fill_ydata(0, floor($values['age']/365.25), 1);
				$n1++;
				$indi[]=$values['indi'];
			}
		}
		$num = $stats->statsMarrAge(false, 'F');
		$indi=array();
		foreach ($num as $values) {
			if (!in_array($values['indi'], $indi)) {
				fill_ydata(1, floor($values['age']/365.25), 1);
				$n1++;
				$indi[]=$values['indi'];
			}
		}
	}
	else {
		$zstart = 0;
		$indi=array();
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsMarrAge(false, 'M', $zstart, $boundary);
			foreach ($num as $values) {
				if (!in_array($values['indi'], $indi)) {
					fill_ydata($boundary, floor($values['age']/365.25), 1);
					$n1++;
					$indi[]=$values['indi'];
				}
			}
			$num = $stats->statsMarrAge(false, 'F', $zstart, $boundary);
			foreach ($num as $values) {
				if (!in_array($values['indi'], $indi)) {
					fill_ydata($boundary, floor($values['age']/365.25), 1);
					$n1++;
					$indi[]=$values['indi'];
				}
			}
			$zstart=$boundary+1;
		}
	}
	unset($indi);
}

//Number of children
function nuch() {
	global $z_as, $zgrenzen, $stats, $n1;

	if ($z_as == 300) {
		$num = $stats->statsChildren(false);
		foreach ($num as $values) {
			fill_ydata(0, $values['f_numchil'], $values['total']);
			$n1+=$values['f_numchil']*$values['total'];
		}
	}
	else if ($z_as == 301) {
		$num = $stats->statsChildren(false, 'M');
		foreach ($num as $values) {
			fill_ydata(0, $values['num'], $values['total']);
			$n1+=$values['num']*$values['total'];
		}
		$num = $stats->statsChildren(false, 'F');
		foreach ($num as $values) {
			fill_ydata(1, $values['num'], $values['total']);
			$n1+=$values['num']*$values['total'];
		}
	}
	else {
		$zstart = 0;
		foreach ($zgrenzen as $boundary) {
			$num = $stats->statsChildren(false, 'BOTH', $zstart, $boundary);
			foreach ($num as $values) {
				fill_ydata($boundary, $values['f_numchil'], $values['total']);
				$n1+=$values['f_numchil']*$values['total'];
			}
			$zstart=$boundary+1;
		}
		
	}
}

function fill_ydata($z, $x, $val) {
	global $ydata, $xmax, $xgrenzen, $zmax, $zgrenzen, $xgiven, $zgiven;
	//--	calculate index $i out of given z value
	//--	calculate index $j out of given x value
	if ($xgiven) $j = $x;
	else {
		$j=0;
		while (($x > $xgrenzen[$j]) && ($j < $xmax)) {
			$j++;
		}
	}
	if ($zgiven) $i = $z;
	else {
		$i=0;
		while (($z > $zgrenzen[$i]) && ($i < $zmax)) {
			$i++;
		}
	}
	if (isset($ydata[$i][$j])) $ydata[$i][$j] += $val;
	else $ydata[$i][$j] = $val;
}

function myplot($mytitle, $n, $xdata, $xtitle, $ydata, $ytitle, $legend) {
	global $percentage, $male_female;
	global $ymax, $scalefactor, $datastring, $imgurl;
	//Google Chart API only allows text encoding for numbers less than 100
	//and it does not allow adjusting the y-axis range, so we must find the maximum y-value
	//in order to adjust beforehand by changing the numbers

	if ($male_female==1) $stop = 2;
	else $stop = count($ydata);
	$yprocentmax = 0;
	if ($percentage) {
		for($i=0; $i<$stop; $i++) {
			$ytotal = 0;
			$ymax = 0;
			$yprocent = 0;
			if (isset($ydata[$i])) {
				for($j=0; $j<count($ydata[$i]); $j++) {
					if ($ydata[$i][$j] > $ymax) {
						$ymax = $ydata[$i][$j];
					}
					$ytotal += $ydata[$i][$j];
				}
				$yt[$i] = $ytotal;
				if ($ytotal>0)
					$yprocent = round($ymax/$ytotal*100, 1);
				if ($yprocentmax < $yprocent) $yprocentmax = $yprocent;
			}
		}
		$ymax = $yprocentmax;
		if ($ymax>0) $scalefactor = 100.0/$ymax;
		else $scalefactor = 0;
		$datastring = "chd=t:";
		for($i=0; $i<$stop; $i++) {
			if (isset($ydata[$i])) {
				for($j=0; $j<count($ydata[$i]); $j++){
					if ($yt[$i] > 0) {
						$datastring .= round($ydata[$i][$j]/$yt[$i]*100*$scalefactor, 1);
					}
					else {
						$datastring .= "0";
					}
					if (!($j == (count($ydata[$i])-1))){
						$datastring .= ",";
					}
				}
				if (!($i == ($stop-1))) {
					$datastring .= "|";
				}
			}
		}
	}
	else {
		for($i=0; $i<$stop; $i++) {
			for($j=0; $j<count($ydata[$i]); $j++) {
				if ($ydata[$i][$j]>$ymax) {
					$ymax = $ydata[$i][$j];
				}
			}
		}
		if ($ymax>0) $scalefactor = 100.0/$ymax;
		else $scalefactor = 0;
		$datastring = "chd=t:";
		for($i=0; $i<$stop; $i++) {
			for($j=0; $j<count($ydata[$i]); $j++){
				$datastring .= round($ydata[$i][$j]*$scalefactor, 1);
				if (!($j == (count($ydata[$i])-1))){
					$datastring .= ",";
				}
			}
			if (!($i == ($stop-1))) {
				$datastring .= "|";
			}
		}
	}
	$colors = array("0000FF", "FFA0CB", "9F00FF", "FF7000", "905030", "FF0000", "00FF00", "F0F000");
	$colorstring = "chco=";
	for($i=0; $i<$stop; $i++) {
		if (isset($colors[$i])) {
			$colorstring .= $colors[$i];
			if ($i != ($stop-1)){
				$colorstring .= ",";
			}
		}
	}

	$titleLength = strpos($mytitle."\n", "\n");
	$title = substr($mytitle, 0, $titleLength);

	$imgurl = "http://chart.apis.google.com/chart?cht=bvg&chs=950x300&chf=bg,s,ffffff00|c,s,ffffff00&chtt=".$title."&".$datastring."&".$colorstring."&chbh=";
	if (count($ydata) > 3) {
		$imgurl .= "5,1";
	} elseif (count($ydata) < 2) {
		$imgurl .= "45,1";
	} else {
		$imgurl .= "20,3";
	}
	$imgurl .= "&chxt=x,x,y,y&chxl=0:|";
	for($i=0; $i<count($xdata); $i++) {
		$imgurl .= $xdata[$i]."|";
	}

	$imgurl .= "1:||||".$xtitle."|2:|";
	$imgurl .= "0|";
	if ($percentage){
		for($i=1; $i<11; $i++) {
			if ($ymax < 11)
				$imgurl .= round($ymax*$i/10, 1)."|";
			else
				$imgurl .= round($ymax*$i/10, 0)."|";
		}
		$imgurl .= "3:||%|";
	}
	else {
		if ($ymax < 11) {
			for($i=1; $i<$ymax+1; $i++) {
				$imgurl .= round($ymax*$i/($ymax), 0)."|";
			}
		}
		else {
			for($i=1; $i<11; $i++) {
				$imgurl .= round($ymax*$i/10, 0)."|";
			}
		}
		$imgurl .= "3:||".$ytitle."|";
	}
	//only show legend if y-data is non-2-dimensional
	if (count($ydata) > 1) {
		$imgurl .= "&chdl=";
		for($i=0; $i<count($legend); $i++){
			$imgurl .= $legend[$i];
			if (!($i == (count($legend)-1))){
				$imgurl .= "|";
			}
		}
	}
	// in PHP 5.3.0 we can use
	//$title = strstr($mytitle, '|', true);
	$title = substr($mytitle, 0, strpos($mytitle, '|'));
	echo "<center><div class=\"statistics_chart\">";
	echo "<img src=\"", encode_url($imgurl), "\" width=\"950\" height=\"300\" border=\"0\" alt=\"", $title, "\" title=\"", $title, "\"/>";
	echo "</div></center><br /><br />";
}

function calc_axis($xas_grenzen) {
	global $x_as, $xdata, $xmax, $xgrenzen;

	//calculate xdata and zdata elements out of given POST values
	$hulpar = explode(",", $xas_grenzen);
	$i=1;
	if ($x_as==21 && $hulpar[0]==1) {
		$xdata[0] = 0;
	} elseif ($x_as==16 && $hulpar[0]==0) {
		$xdata[0] = i18n::translate('before');
	} elseif ($x_as==16 && $hulpar[0]<0) {
		$xdata[0] = i18n::translate('over')." ".$hulpar[0];
	} else {
		$xdata[0] = i18n::translate('less than')." ".$hulpar[0];
	}
	$xgrenzen[0] = $hulpar[0]-1;
	while (isset($hulpar[$i])) {
		$i1 = $i-1;
		if (($hulpar[$i] - $hulpar[$i1]) == 1) {
			$xdata[$i] = $hulpar[$i1];
			$xgrenzen[$i] = $hulpar[$i1];
		}
		else if ($hulpar[$i1]==$hulpar[0]){
			$xdata[$i]= $hulpar[$i1]."-".$hulpar[$i];
			$xgrenzen[$i] = $hulpar[$i];
		}
		else {
			$xdata[$i]= ($hulpar[$i1]+1)."-".$hulpar[$i];
			$xgrenzen[$i] = $hulpar[$i];
		}
		$i++;
	}
	$xdata[$i] = $hulpar[$i-1];
	$xgrenzen[$i] = $hulpar[$i-1];
	if ($hulpar[$i-1]==$i)
		$xmax = $i+1;
	else
		$xmax = $i;
	$xdata[$xmax] = i18n::translate('over')." ".$hulpar[$i-1];
	$xgrenzen[$xmax] = 10000;
	$xmax = $xmax+1;
	if ($xmax > 20) $xmax = 20;
}

function calc_legend($grenzen_zas) {
	global $legend, $zmax, $zgrenzen;

	// calculate the legend values
	$hulpar = array();
	//-- get numbers out of $grenzen_zas
	$hulpar = explode(",", $grenzen_zas);
	$i=1;
	// Allow special processing for different languages
	$func="date_localisation_".WT_LOCALE;
	if (!function_exists($func))
		$func="DefaultDateLocalisation";
	// Localise the date
	$q1='bef';
	$d1=$hulpar[0];
	$q2=''; $d2=''; $q3='';
	$func($q1, $d1, $q2, $d2, $q3);
	$legend[0] = trim("{$q1} {$d1} {$q2} {$d2} {$q3}");
	$zgrenzen[0] = $hulpar[0]-1;
	while (isset($hulpar[$i])) {
		$i1 = $i-1;
		$legend[$i] = $hulpar[$i1]."-".($hulpar[$i]-1);
		$zgrenzen[$i] = $hulpar[$i]-1;
		$i++;
	}
	$zmax = $i;
	$zmax1 = $zmax-1;
	// Localise the date
	$q1='from';
	$d1 = $hulpar[$zmax1];
	$func($q1, $d1, $q2, $d2, $q3);
	$legend[$zmax] = trim("{$q1} {$d1} {$q2} {$d2} {$q3}");
	$zgrenzen[$zmax] = 10000;
	$zmax = $zmax+1;
	if ($zmax > 8) $zmax=8;
}

//--------------------nr,-----bron ,xgiven,zgiven,title, xtitle,ytitle,grenzen_xas, grenzen-zas,functie,
function set_params($current, $indfam, $xg, $zg, $titstr, $xt, $yt, $gx, $gz, $myfunc) {
	global $x_as, $y_as, $z_as, $nrfam, $nrpers, $n1, $months;
	global $legend, $xdata, $ydata, $xmax, $zmax, $zgrenzen, $xgiven, $zgiven, $percentage, $male_female;
	global $stats;

	if (!function_exists($myfunc)) {
		echo $myfunc, " ", i18n::translate(' not implemented:');
		exit;
	}

	$monthdata= array();
	$monthdata[] = i18n::translate('Jan');
	$monthdata[] = i18n::translate('Feb');
	$monthdata[] = i18n::translate('March');
	$monthdata[] = i18n::translate('April');
	$monthdata[] = i18n::translate('May');
	$monthdata[] = i18n::translate('June');
	$monthdata[] = i18n::translate('July');
	$monthdata[] = i18n::translate('Aug');
	$monthdata[] = i18n::translate('Sep');
	$monthdata[] = i18n::translate('Oct');
	$monthdata[] = i18n::translate('Nov');
	$monthdata[] = i18n::translate('Dec');
	foreach ($monthdata as $key=>$month) {
		$monthdata[$key] = $month;
	}
	$months= array();
	$months[] = 'JAN';
	$months[] = 'FEB';
	$months[] = 'MAR';
	$months[] = 'APR';
	$months[] = 'MAY';
	$months[] = 'JUN';
	$months[] = 'JUL';
	$months[] = 'AUG';
	$months[] = 'SEP';
	$months[] = 'OCT';
	$months[] = 'NOV';
	$months[] = 'DEC';
	foreach ($months as $key=>$month) {
		$months[$key] = $month;
	}

	if ($x_as == $current) {
		if (($x_as==13 || $x_as==15) && $z_as == 301) {
			$z_as = 300;
		}
		$xgiven = $xg;
		$zgiven = $zg;
		$title = $titstr;
		$xtitle = $xt;
		$ytitle = i18n::translate('numbers');
		$grenzen_xas = $gx;
		$grenzen_zas = $gz;
		if ($xg == true) {
			$xdata = $monthdata;
			$xmax = 12;
		} else
			calc_axis($grenzen_xas);
		if ($z_as != 300 && $z_as != 301)
			calc_legend($grenzen_zas);

		$percentage = false;
		if ($y_as == 201) {
			$percentage = false;
			if ($current == 13 || $current == 15 || $current == 16 || $current == 21) {
				$ytitle = i18n::translate('Total families');
			} elseif ($current == 14) {
				$ytitle = i18n::translate('Number of children');
			} else {
				$ytitle = i18n::translate('Total individuals');
			}
		} elseif ($y_as == 202) {
			$percentage = true;
			$ytitle = i18n::translate('percentage');
		}
		$male_female = false;
		if ($z_as == 300) {
			$zgiven = false;
			$legend[0] = "all";
			$zmax = 1;
			$zgrenzen[0] = 100000;
		} elseif ($z_as == 301) {
			$male_female = true;
			$zgiven = true;
			$legend[0] = i18n::translate('Male');
			$legend[1] = i18n::translate('Female');
			$zmax = 2;
			$xtitle = $xtitle.i18n::translate(' per gender');
		} elseif ($z_as == 302) {
			$xtitle= $xtitle.i18n::translate(' per time period');
		}
		//-- reset the data array
		for($i=0; $i<$zmax; $i++) {
			for($j=0; $j<$xmax; $j++) {
				$ydata[$i][$j] = 0;
			}
		}
		$myfunc();
		if ($indfam == "IND") {
			$hstr = $title."|" .i18n::translate('Counts ')." ".$n1." ".i18n::translate('of')." ".$nrpers;
		} elseif ($x_as==21) {
			$hstr = $title."|" .i18n::translate('Counts ')." ".$n1." ".i18n::translate('of')." ".$stats->totalChildren();
		}
		else {
			$hstr = $title."|" .i18n::translate('Counts ')." ".$n1." ".i18n::translate('of')." ".$nrfam;
		}
		myplot($hstr, $zmax, $xdata, $xtitle, $ydata, $ytitle, $legend);
	}
}

function print_sources_stats_chart($type){
	global $stats;

	$params[0] = "700x200";
	$params[1] = "ffffff";
	$params[2] = "84beff";
	switch ($type) {
	case '9':
		echo '<div id="google_charts" class="center">';
		echo '<b>', i18n::translate('Individuals with sources'), '</b><br /><br />';
		echo $stats->chartIndisWithSources($params);
		echo '</div><br />';
		break;
	case '8':
		echo '<div id="google_charts" class="center">';
		echo '<b>', i18n::translate('Families with sources'), '</b><br /><br />';
		echo $stats->chartFamsWithSources($params);
		echo '</div><br />';
		break;
	}
}

//--	========= start of main program =========
$action	= safe_REQUEST($_REQUEST, 'action', WT_REGEX_XREF);

if ($action=="update") {
	$x_as = $_POST["x-as"];
	$y_as = $_POST["y-as"];
	if (isset($_POST["z-as"])) $z_as = $_POST["z-as"];
	else $z_as = 300;
	$xgl = $_POST["xas-grenzen-leeftijden"];
	$xglm = $_POST["xas-grenzen-leeftijden_m"];
	$xgm = $_POST["xas-grenzen-maanden"];
	$xga = $_POST["xas-grenzen-aantallen"];
	if (isset($_POST["zas-grenzen-periode"])) $zgp = $_POST["zas-grenzen-periode"];
	else $zgp = 0;
	$chart_shows = $_POST["chart_shows"];
	$chart_type  = $_POST["chart_type"];
	$surname	 = $_POST["SURN"];

	$_SESSION[$GEDCOM."statTicks"]["xasGrLeeftijden"] = $xgl;
	$_SESSION[$GEDCOM."statTicks"]["xasGrLeeftijden_m"] = $xglm;
	$_SESSION[$GEDCOM."statTicks"]["xasGrMaanden"] = $xgm;
	$_SESSION[$GEDCOM."statTicks"]["xasGrAantallen"] = $xga;
	$_SESSION[$GEDCOM."statTicks"]["zasGrPeriode"] = $zgp;
	$_SESSION[$GEDCOM."statTicks"]["chart_shows"] = $chart_shows;
	$_SESSION[$GEDCOM."statTicks"]["chart_type"] = $chart_type;
	$_SESSION[$GEDCOM."statTicks"]["SURN"] = $surname;

	// Save the input variables
	$savedInput = array();
	$savedInput["x_as"] = $x_as;
	$savedInput["y_as"] = $y_as;
	$savedInput["z_as"] = $z_as;
	$savedInput["xgl"] = $xgl;
	$savedInput["xglm"] = $xglm;
	$savedInput["xgm"] = $xgm;
	$savedInput["xga"] = $xga;
	$savedInput["zgp"] = $zgp;
	$savedInput["chart_shows"] = $chart_shows;
	$savedInput["chart_type"] = $chart_type;
	$savedInput["SURN"] = $surname;
	$_SESSION[$GEDCOM."statisticsplot"] = $savedInput;
	unset($savedInput);
}
else {
	if (!isset($_SESSION[$GEDCOM."statisticsplot"])) {
		header("Location: statistics.php");
		exit;
	}
	// Recover the saved input variables
	$savedInput = $_SESSION[$GEDCOM."statisticsplot"];
	$x_as = $savedInput["x_as"];
	$y_as = $savedInput["y_as"];
	$z_as = $savedInput["z_as"];
	$xgl = $savedInput["xgl"];
	$xglm = $savedInput["xglm"];
	$xgm = $savedInput["xgm"];
	$xga = $savedInput["xga"];
	$zgp = $savedInput["zgp"];
	$chart_shows = $savedInput["chart_shows"];
	$chart_type = $savedInput["chart_type"];
	$surname = $savedInput["SURN"];
	unset($savedInput);
}

print_simple_header(i18n::translate('Statistics Plot'));
echo "<center><h2>", i18n::translate('Statistics Plot'), "</h2>";
echo "</center><br />";

$nrpers = $_SESSION[$GEDCOM."nrpers"];
$nrfam = $_SESSION[$GEDCOM."nrfam"];
$nrmale = $_SESSION[$GEDCOM."nrmale"];
$nrfemale = $_SESSION[$GEDCOM."nrfemale"];

//-- out of range values
if (($y_as < 201) || ($y_as > 202)) {
	echo i18n::translate('%s not implemented', $y_as), "<br/>";
	exit;
}
if (($z_as < 300) || ($z_as > 302)) {
	echo i18n::translate('%s not implemented', $z_as), "<br/>";
	exit;
}

//-- Set params for request out of the information for plot
$g_xas = "1,2,3,4,5,6,7,8,9,10,11,12"; //should not be needed. but just for month

switch ($x_as) {
case '11':
	//---------		nr,  type,	  xgiven,	zgiven,	title,				xtitle,		ytitle,	boundaries_x, boundaries-z, function
	set_params(11, "IND", true, 	false, i18n::translate('Month of birth'),  i18n::translate('month'), 	$y_as, 	$g_xas, 	$zgp, "bimo");
	break;
case '12':
	set_params(12, "IND", true, 	false, i18n::translate('Month of death'),  i18n::translate('month'), 	$y_as, 	$g_xas, 	$zgp, "demo");
	break;
case '13':
	set_params(13, "FAM", true, 	false, i18n::translate('Month of marriage'),  i18n::translate('month'), 	$y_as, 	$g_xas, 	$zgp, "mamo");
	break;
case '14':
	set_params(14, "FAM", true, 	false, i18n::translate('Month of birth of first child in a relation'), i18n::translate('month'), 	$y_as, 	$g_xas, 	$zgp, "bimo1");
	break;
case '15':
	set_params(15, "FAM", true, 	false, i18n::translate('Months between marriage and first child'), i18n::translate('month'), 	$y_as, 	$g_xas, 	$zgp, "mamo1");
	break;
case '16':
	set_params(16, "FAM", false, 	false, i18n::translate('Months between marriage and first child'), i18n::translate('Months between marriage and birth of first child'), $y_as, $xgm, 	$zgp, "mamam");
	break;
case '17':
	set_params(17, "IND", false, 	false, i18n::translate('Age related to birth year'), i18n::translate('age'), 	$y_as, 	$xgl, 	$zgp, "agbi");
	break;
case '18':
	set_params(18, "IND", false, 	false, i18n::translate('Age related to death year'), i18n::translate('age'), 	$y_as, 	$xgl, 	$zgp, "agde");
	break;
case '19':
	set_params(19, "IND", false, 	false, i18n::translate('Age in year of marriage'), i18n::translate('age'), 	$y_as, 	$xglm, 	$zgp, "agma");
	break;
case '20':
	set_params(20, "IND", false, 	false, i18n::translate('Age in year of first marriage'), i18n::translate('age'), 	$y_as, 	$xglm, 	$zgp, "agma1");
	break;
case '21':
	set_params(21, "FAM", false, 	false, i18n::translate('Number of children'), i18n::translate('children'), 	$y_as, 	$xga, 	$zgp, "nuch");  //plot Number of children
	break;
case '1':
	echo $stats->chartDistribution($chart_shows, $chart_type, $surname);
	break;
case '2':
	echo $stats->chartDistribution($chart_shows, 'birth_distribution_chart');
	break;
case '3':
	echo $stats->chartDistribution($chart_shows, 'death_distribution_chart');
	break;
case '4':
	echo $stats->chartDistribution($chart_shows, 'marriage_distribution_chart');
	break;
case '8':
case '9':
	print_sources_stats_chart($x_as);
	break;
default:
	echo i18n::translate('%s not implemented', $x_as), "<br/>";
	exit;
}
echo "<br /><div class =\"center noprint\">";
echo "<input type=\"button\" value=\"", i18n::translate('Close Window'), "\" onclick=\"window.close()\" /><br /><br />";
echo "</div>\n";
print_simple_footer();
?>
