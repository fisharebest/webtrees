<?php
// Displays a place hierachy
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
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

define('WT_SCRIPT_NAME', 'placelist.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Base();

$use_googlemap = array_key_exists('googlemap', WT_Module::getActiveModules()) && get_module_setting('googlemap', 'GM_PLACE_HIERARCHY');

if ($use_googlemap) {
	require WT_ROOT.WT_MODULES_DIR.'googlemap/placehierarchy.php';
}

function case_in_array($value, $array) {
	foreach ($array as $key=>$val) {
		if (strcasecmp($value, $val)==0) {
			return true;
		}
	}
	return false;
}
$action =safe_GET('action',  array('find', 'show'), 'find');
$display=safe_GET('display', array('hierarchy', 'list'), 'hierarchy');
$parent =safe_GET('parent');
$level  =safe_GET('level');

if ($display=='hierarchy') {
	$controller->setPageTitle(WT_I18N::translate('Place hierarchy'));
} else {
	$controller->setPageTitle(WT_I18N::translate('Place List'));
}
$controller->pageHeader();

echo '<div id="place-heirarchy"><h2>';
if ($display=='hierarchy' && $level == 0)  {
	echo WT_I18N::translate('Place hierarchy');
} else if ($display=='hierarchy' && $level > 0) {
	echo WT_I18N::translate('Place hierarchy'), ' - ', $parent[$level-1];
} else {
	echo WT_I18N::translate('Place List');
}
echo '</h2>';

// Set original place name found (used later)
$base_parent = $parent[$level-1];

// Make sure the "parent" array has no holes
if (isset($parent) && is_array($parent)) {
	$parentKeys = array_keys($parent);
	$highKey = max($parentKeys);
	
	for ($j=0; $j<=$highKey; $j++) {
		if (!isset($parent[$j])) {
			$parent[$j] = '';
		}
	}
	ksort($parent, SORT_NUMERIC);
}


if (!isset($parent)) $parent=array();
else {
	if (!is_array($parent)) $parent = array();
	else $parent = array_values($parent);
}

if (!isset($level)) {
	$level=0;
}

if ($level>count($parent)) $level = count($parent);
if ($level<count($parent)) $level = 0;

//-- hierarchical display
if ($display=='hierarchy') {
	$placelist=get_place_list($parent, $level);
	$numfound=count($placelist);
	// -- sort the array
	$placelist = array_unique($placelist);
	uasort($placelist, 'utf8_strcasecmp');

	//-- create a query string for passing to search page
	$tempparent = array_reverse($parent);
	if (count($tempparent)>0) $squery = '&query='.urlencode($tempparent[0]);
	else $squery='';
	for ($i=1; $i<$level; $i++) {
		$squery.=', '.urlencode($tempparent[$i]);
	}

	//-- if the number of places found is 0 then automatically redirect to search page
	if ($numfound==0) {
		$action='show';
	}
	

	// -- echo the breadcrumb hierarchy
	echo '<h4>';
	$numls=0;
	if ($level>0) {
		//-- link to search results
		if ((($level>1)||($parent[0]!=''))&&($numfound>0)) {
			echo $numfound, ' ', WT_I18N::translate('Place connections found'), ': ';
		}
		//-- breadcrumb
		$numls = count($parent)-1;
		$num_place='';
		for ($i=$numls; $i>=0; $i--) {
			echo '<a href="?level=', ($i+1);
			for ($j=0; $j<=$i; $j++) {
				$levels = explode(', ', trim($parent[$j]));
				// Routine for replacing ampersands
				foreach ($levels as $pindex=>$ppart) {
					if ($j==$numls) {
						$ppart = rawurlencode($base_parent);
					} else {
						$ppart = rawurlencode($ppart);
					}
					$ppart = preg_replace('/amp\%3B/', '', trim($ppart));
					echo '&amp;parent%5B', $j, '%5D=', $ppart;
				}
			}
			echo '"><bdi dir="auto">';
			if (trim($parent[$i])=='') {
				echo WT_I18N::translate('unknown');
			} else if ($i == $numls) {
				echo $base_parent; 
			} else {
				echo htmlspecialchars($parent[$i]);
			}
			echo '</bdi></a>';
			echo ', ';
			if (empty($num_place)) {
				$num_place=$parent[$i];
			}
		}
	}
	echo '<a href="?level=0">';
	echo WT_I18N::translate('Top Level');
	echo '</a>',
		'</h4>';


	//-- create a string to hold the variable links and place names
	$linklevels='';
	if ($use_googlemap) {
		$placelevels='';
		$place_names=array();
	}
	for ($j=0; $j<$level; $j++) {
		$linklevels .= '&amp;parent['.$j.']='.urlencode($parent[$j]);
		if ($use_googlemap) {
			if (trim($parent[$j])=='') {
				$placelevels = ', '.WT_I18N::translate('unknown').$placelevels;
			} else {
				$placelevels = ', '.$parent[$j].$placelevels;
			}
		}
	}

	if ($use_googlemap) {
		create_map($placelevels);
	} else {
		echo '<br><br>';
		if (array_key_exists('places_assistant', WT_Module::getActiveModules())) {
			// show clickable map if found
			places_assistant_WT_Module::display_map($level, $parent);
		}
	}

	$i=0;
	$ct1=count($placelist);

	// -- echo the array
	foreach ($placelist as $key => $value) {
		if ($i==0) {
			echo '<table id="place_hierarchy" class="list_table" ';
			echo '><tr><td class="list_label" ';
			if ($ct1 > 20) {
				echo 'colspan="3"';
			} elseif ($ct1 > 4) {
				echo 'colspan="2"';
			}
			echo '><i class="icon-place"></i> ';
			if ($level>0) {
				echo /* I18N: %s is a country or region */WT_I18N::translate('Places in %s', $num_place);
			} else {
				echo WT_I18N::translate('Place hierarchy');
			}
			echo '</td></tr><tr><td class="list_value"><ul>';
		}

		echo '<li><a href="?action=', $action, '&amp;level=', $level+1, $linklevels;
		echo '&amp;parent%5B', $level, '%5D=', urlencode($value), '" class="list_item">';

		if (trim($value)=='') echo WT_I18N::translate('unknown');
		else echo htmlspecialchars($value);
		if ($use_googlemap) $place_names[$i]=trim($value);
		echo '</a></li>';
		$i++;
		if ($ct1 > 20) {
			if ($i == floor($ct1 / 3)) {
				echo '</ul></td><td class="list_value"><ul>';
			}
			if ($i == floor(($ct1 / 3) * 2)) {
				echo '</ul></td><td class="list_value"><ul>';
			}
		} elseif ($ct1 > 4 && $i == floor($ct1 / 2)) {
			echo '</ul></td><td class="list_value"><ul>';
		}
	}
	if ($i>0) {
		echo '</ul></td></tr>';
		if (($action!='show')&&($level>0)) {
			echo '<tr><td class="list_label" ';
			if ($ct1 > 20) {
				echo 'colspan="3"';
			} elseif ($ct1 > 4) {
				echo 'colspan="2"';
			}
			echo '>';
			echo WT_I18N::translate('View all records found in this place');
			echo help_link('ppp_view_records');
			echo '</td></tr><tr><td class="list_value" ';
			if ($ct1 > 20) {
				echo 'colspan="3"';
			} elseif ($ct1 > 4) {
				echo 'colspan="2"';
			}
			echo ' style="text-align: center;">';
			echo '<a href="?action=show&amp;level=', $level;
			foreach ($parent as $key=>$value) {
				echo '&amp;parent%5B', $key, '%5D=', urlencode(trim($value));
			}
			echo '"><span class="formField">';
			if (trim($value)=='') {
				echo WT_I18N::translate('unknown');
			} else {
				echo htmlspecialchars($value);
			}
			echo '</span></a>';
			echo '</td></tr>';
		}
		echo '</table>';
	}
	echo '</td></tr></table>';
}

$positions = get_place_positions($parent, $level);
if ($level > 0) {
	if ($action=='show') {
		// -- array of names
		$myindilist = array();
		$mysourcelist = array();
		$myfamlist = array();
		foreach ($positions as $position) {
			$record=WT_GedcomRecord::getInstance($position);
			if ($record->canDisplayDetails()) {
				switch ($record->getType()) {
				case 'INDI':
					$myindilist[]=$record;
					break;
				case 'SOUR':
					$mysourcelist[]=$record;
					break;
				case 'FAM':
					$myfamlist[]=$record;
					break;
				}
			}
		}
		echo '<br>';

		//-- display results
		echo WT_JS_START;
		?>	jQuery(document).ready(function() {
				jQuery("#places-tabs").tabs();
				jQuery("#places-tabs").css("visibility", "visible");
				jQuery(".loading-image").css("display", "none");
			});
		<?php
		echo WT_JS_END;
		echo '<div class="loading-image">&nbsp;</div>';
		echo '<div id="places-tabs"><ul>';
		if ($myindilist) {
			echo '<li><a href="#places-indi"><span id="indisource">', WT_I18N::translate('Individuals'), '</span></a></li>';
		}
		if ($myfamlist) {
			echo '<li><a href="#places-fam"><span id="famsource">', WT_I18N::translate('Families'), '</span></a></li>';
		}
		if ($mysourcelist) {
			echo '<li><a href="#places-source"><span id="mediasource">', WT_I18N::translate('Sources'), '</span></a></li>';
		}
		echo '</ul>';
		if ($myindilist) {
			echo '<div id="places-indi">', format_indi_table($myindilist), '</div>';
		}
		if ($myfamlist) {
			echo '<div id="places-fam">', format_fam_table($myfamlist), '</div>';
		}
		if ($mysourcelist) {
			echo '<div id="places-source">', format_sour_table($mysourcelist), '</div>';
		}
		if (!$myindilist && !$myfamlist && !$mysourcelist) {
			echo '<div id="places-indi">', format_indi_table(array()), '</div>';
		}
		echo '</div>';//close #places-tabs
	}
}

//-- list type display
if ($display=='list') {
	$placelist = array();
	$placelist=find_place_list('');
	$placelist = array_unique($placelist);
	uasort($placelist, 'utf8_strcasecmp');
	if (count($placelist)==0) {
		echo '<b>', WT_I18N::translate('No results found.'), '</b><br>';
	} else {
		echo '<table class="list_table">';
		echo '<tr><td class="list_label" ';
		$ct = count($placelist);
		echo ' colspan="', $ct>20 ? 3 : 2, '"><i class="icon-place"></i> ';
		echo WT_I18N::translate('Place List');
		echo '</td></tr><tr><td class="list_value_wrap"><ul>';
		$i=0;
		foreach ($placelist as $indexval => $revplace) {
			$linklevels = '';
			$levels = explode(',', $revplace); // -- split the place into comma seperated values
			$level=0;
			$revplace = '';
			foreach ($levels as $indexval => $place) {
				$place = trim($place);
				$linklevels .= '&amp;parent%5B'.$level.'%5D='.urlencode($place);
				$level++;
				if ($level>1) $revplace .= ', ';
				if ($place=='') $revplace .= WT_I18N::translate('unknown');
				else $revplace .= $place;
			}
			echo '<li><a href="?action=show&amp;display=hierarchy&amp;level=', $level, $linklevels, '">';
			echo htmlspecialchars($revplace), '</a></li>';
			$i++;
			if ($ct > 20) {
				if ($i == floor($ct / 3)) {
					echo '</ul></td><td class="list_value_wrap"><ul>';
				}
				if ($i == floor(($ct / 3) * 2)) {
					echo '</ul></td><td class="list_value_wrap"><ul>';
				}
			} elseif ($i == floor($ct/2)) {
				echo '</ul></td><td class="list_value_wrap"><ul>';
			}
		}
		echo '</ul></td></tr>';
		if ($i>1) {
			echo '<tr><td>';
			if ($i>0) {
				echo WT_I18N::translate('Total unique places'), ' ', $i;
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}
}

echo '<h4><a href="?display=';
if ($display=='list') {
	echo 'hierarchy">', WT_I18N::translate('Show Places in Hierarchy');
} else {
	echo 'list">', WT_I18N::translate('Show All Places in a List');
}
echo '</a></h4></div>';

if ($use_googlemap && $display=='hierarchy') {
	echo '<link type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">';
	map_scripts($numfound, $level, $parent, $linklevels, $placelevels, $place_names);
}
