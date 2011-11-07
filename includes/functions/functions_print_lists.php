<?php
// Functions for printing lists
//
// Various printing functions for printing lists
// used on the indilist, famlist, find, and search pages.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

require_once WT_ROOT.'includes/functions/functions_places.php';

// print a table of individuals
function print_indi_table($datalist, $option='') {
	global $GEDCOM, $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER, $MAX_ALIVE_AGE, $controller;

	$table_id = 'ID'.floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	$SHOW_EST_LIST_DATES=get_gedcom_setting(WT_GED_ID, 'SHOW_EST_LIST_DATES');
	if ($option=='MARR_PLAC') return;
	if (count($datalist)<1) return;

	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			var oTable'.$table_id.' = jQuery("#'.$table_id.'").dataTable( {
				"sDom": \'<"H"<"filtersH_'.$table_id.'"><"dt-clear">pf<"dt-clear">irl>t<"F"pl<"dt-clear"><"filtersF_'.$table_id.'">>\',
				"oLanguage": {
					"sLengthMenu": "'./* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value=\"10\">10<option value=\"20\">20</option><option value=\"30\">30</option><option value=\"50\">50</option><option value=\"100\">100</option><option value=\"-1\">'.WT_I18N::translate('All').'</option></select>').'",
					"sZeroRecords": "'.WT_I18N::translate('No records to display').'",
					"sInfo": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",
					"sInfoEmpty": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0').'",
					"sInfoFiltered": "'./* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_').'",
					"sProcessing": "'.WT_I18N::translate('Loading...').'",
					"sSearch": "'.WT_I18N::translate('Filter').'",
					"oPaginate": {
						"sFirst":    "'./* I18N: button label, first page    */ WT_I18N::translate('first').'",
						"sLast":     "'./* I18N: button label, last page     */ WT_I18N::translate('last').'",
						"sNext":     "'./* I18N: button label, next page     */ WT_I18N::translate('next').'",
						"sPrevious": "'./* I18N: button label, previous page */ WT_I18N::translate('previous').'"
					}
				},
				"bJQueryUI": true,
				"bAutoWidth":false,
				"bProcessing": true,
				"bRetrieve": true,
				"aoColumns": [
					/*  0 name      */ {"iDataSort": 2},
					/*  1 GIVN,SURN */ {"sType": "unicode", "bVisible": false},
					/*  2 SURN,GIVN */ {"sType": "unicode", "bVisible": false},
					/*  3 sosa      */ {"bVisible": '.($option=='sosa'?'true':'false').'},
					/*  4 birt date */ {"iDataSort": 5},
					/*  5 BIRT:DATE */ {"bVisible": false},
					/*  6 anniv     */ {"bSortable": false, "sClass": "center"},
					/*  7 birt plac */ {"sType": "unicode"},
					/*  8 children  */ {"sClass": "center"},
					/*  9 deat date */ {"iDataSort": 11},
					/* 10 DEAT:DATE */ {"bVisible": false},
					/* 11 anniv     */ {"bSortable": false, "sClass": "center"},
					/* 12 age       */ {"sType": "numeric", "sClass": "center"},
					/* 13 deat plac */ {"sType": "unicode"},
					/* 14 CHAN      */ {"bVisible": '.($SHOW_LAST_CHANGE?'true':'false').'},
					/* 15 SEX       */ {"bVisible": false},
					/* 16 BIRT      */ {"bVisible": false},
					/* 17 DEAT      */ {"bVisible": false},
					/* 18 TREE      */ {"bVisible": false}
				],
				"iDisplayLength": 20,
				"sPaginationType": "full_numbers"
			});
	
			jQuery("div.filtersH_'.$table_id.'").html("'.addslashes(
				'<button type="button" id="SEX_M_'.    $table_id.'" class="ui-state-default SEX_M" title="'.    WT_I18N::translate('Show only males.').'">&nbsp;'.WT_Person::sexImage('M', 'small').'&nbsp;</button>'.
				'<button type="button" id="SEX_F_'.    $table_id.'" class="ui-state-default SEX_F" title="'.    WT_I18N::translate('Show only females.').'">&nbsp;'.WT_Person::sexImage('F', 'small').'&nbsp;</button>'.
				'<button type="button" id="SEX_U_'.    $table_id.'" class="ui-state-default SEX_U" title="'.    WT_I18N::translate('Show only persons of whom the gender is not known.').'">&nbsp;'.WT_Person::sexImage('U', 'small').'&nbsp;</button>'.
				'<button type="button" id="DEAT_N_'.   $table_id.'" class="ui-state-default DEAT_N" title="'.   WT_I18N::translate('Show people who are alive or couples where both partners are alive.').'">'.WT_I18N::translate('Alive').'</button>'.
				'<button type="button" id="DEAT_Y_'.   $table_id.'" class="ui-state-default DEAT_Y" title="'.   WT_I18N::translate('Show people who are dead or couples where both partners are deceased.').'">'.WT_I18N::translate('Dead').'</button>'.
				'<button type="button" id="DEAT_YES_'. $table_id.'" class="ui-state-default DEAT_YES" title="'. WT_I18N::translate('Show people who died more than 100 years ago.').'">'.WT_Gedcom_Tag::getLabel('DEAT').'&gt;100</button>'.
				'<button type="button" id="DEAT_Y100_'.$table_id.'" class="ui-state-default DEAT_Y100" title="'.WT_I18N::translate('Show people who died within the last 100 years.').'">'.WT_Gedcom_Tag::getLabel('DEAT').'&lt;=100</button>'.
				'<button type="button" id="BIRT_YES_'. $table_id.'" class="ui-state-default BIRT_YES" title="'. WT_I18N::translate('Show persons born more than 100 years ago.').'">'.WT_Gedcom_Tag::getLabel('BIRT').'&gt;100</button>'.
				'<button type="button" id="BIRT_Y100_'.$table_id.'" class="ui-state-default BIRT_Y100" title="'.WT_I18N::translate('Show persons born within the last 100 years.').'">'.WT_Gedcom_Tag::getLabel('BIRT').'&lt;=100</button>'.
				'<button type="button" id="TREE_R_'   .$table_id.'" class="ui-state-default TREE_R" title="'.   WT_I18N::translate('Show «roots» couples or individuals.  These people may also be called «patriarchs».  They are individuals who have no parents recorded in the database.').'">'.WT_I18N::translate('Roots').'</button>'.
				'<button type="button" id="TREE_L_'.   $table_id.'" class="ui-state-default TREE_L" title="'.   WT_I18N::translate('Show «leaves» couples or individuals.  These are individuals who are alive but have no children recorded in the database.').'">'.WT_I18N::translate('Leaves').'</button>'.
				'<button type="button" id="RESET_'.    $table_id.'" class="ui-state-default RESET" title="'.    WT_I18N::translate('Reset to the list defaults.').'">'.WT_I18N::translate('Reset').'</button>'
			).'");
	
			jQuery("div.filtersF_'.$table_id.'").html("'.addslashes(
				'<button type="button" class="ui-state-default" id="GIVEN_SORT_'.$table_id.'">'.WT_I18N::translate('Sort by given names').'</button>'.
				'<button type="button" class="ui-state-default" id="cb_parents_indi_list_table" onclick="jQuery(\'div.parents_indi_list_table_'.$table_id.'\').toggle();">'.WT_I18N::translate('Show parents').'</button>'.
				'<button type="button" class="ui-state-default" id="charts_indi_list_table" onclick="jQuery(\'div.indi_list_table-charts_'.$table_id.'\').toggle();">'.WT_I18N::translate('Show statistics charts').'</button>'
			).'");
	
			oTable'.$table_id.'.fnSortListener("#GIVEN_SORT_'.$table_id.'",1);
	
			/* Add event listeners for filtering inputs */
			jQuery("#SEX_M_'.    $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("M", 17 );});
			jQuery("#SEX_F_'.    $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("F", 17 );});
			jQuery("#SEX_U_'.    $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("U", 17 );});
			jQuery("#BIRT_YES_'. $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("YES", 18 );});
			jQuery("#BIRT_Y100_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("Y100", 18 );});
			jQuery("#DEAT_N_'.   $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("N", 19 );});
			jQuery("#DEAT_Y_'.   $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("^Y", 19, true, false );});
			jQuery("#DEAT_YES_'. $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("YES", 19 );});
			jQuery("#DEAT_Y100_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("Y100", 19 );});
			jQuery("#TREE_R_'.   $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("R", 20 );});
			jQuery("#TREE_L_'.   $table_id.'").click( function() { oTable'.$table_id.'.fnFilter("L", 20 );});	
			jQuery("#RESET_'.    $table_id.'").click( function() {
				for (i=0; i<21; i++){
					oTable'.$table_id.'.fnFilter("", i );
				};
			});

			/* This code is a temporary fix for Datatables bug http://www.datatables.net/forums/discussion/4730/datatables_sort_wrapper-being-added-to-columns-with-bsortable-false/p1*/
			jQuery("th span:eq(6)").css("display", "none");
			jQuery("th div:eq(6)").css("margin", "auto").css("text-align", "center");
			jQuery("th span:eq(12)").css("display", "none");
			jQuery("th div:eq(12)").css("margin", "auto").css("text-align", "center");
			
			jQuery(".indi-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

	$stats = new WT_Stats($GEDCOM);

	// Bad data can cause "longest life" to be huge, blowing memory limits
	$max_age = min($MAX_ALIVE_AGE, $stats->LongestLifeAge())+1;

	//-- init chart data
	for ($age=0; $age<=$max_age; $age++) $deat_by_age[$age]="";
	for ($year=1550; $year<2030; $year+=10) $birt_by_decade[$year]="";
	for ($year=1550; $year<2030; $year+=10) $deat_by_decade[$year]="";
	//--table wrapper
	echo '<div class="loading-image">&nbsp;</div>';
	echo '<div class="indi-list">';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_Gedcom_Tag::getLabel('NAME'), '</th>';
	echo '<th style="display:none">GIVN</th>';
	echo '<th style="display:none">SURN</th>';
	echo '<th ',($option=='sosa'?'':' style="display:none"'),'>', /* I18N: Abbreviation for "Sosa-Stradonitz number".  This is a person's surname, so may need transliterating into non-latin alphabets. */ WT_I18N::translate('Sosa'), '</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('BIRT'), '</th>';
	echo '<th style="display:none">SORT_BIRT</th>';
	echo '<th><img src="', $WT_IMAGES['reminder'], '" alt="', WT_I18N::translate('Anniversary'), '" title="', WT_I18N::translate('Anniversary'), '" border="0" /></th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('PLAC'), '</th>';
	echo '<th><img src="', $WT_IMAGES['children'], '" alt="', WT_I18N::translate('Children'), '" title="', WT_I18N::translate('Children'), '" border="0" /></th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('DEAT'), '</th>';
	echo '<th style="display:none">SORT_DEAT</th>';
	echo '<th><img src="', $WT_IMAGES['reminder'], '" alt="', WT_I18N::translate('Anniversary'), '" title="', WT_I18N::translate('Anniversary'), '" border="0" /></th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('AGE'), '</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('PLAC'), '</th>';
	echo '<th ',($SHOW_LAST_CHANGE?'':' style="display:none"'),'>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	echo '<th style="display:none">SEX</th>';
	echo '<th style="display:none">BIRT</th>';
	echo '<th style="display:none">DEAT</th>';
	echo '<th style="display:none">TREE</th>';
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$d100y=new WT_Date(date('Y')-100);  // 100 years ago
	$dateY = date('Y');
	$unique_indis=array(); // Don't double-count indis with multiple names.
	foreach ($datalist as $key=>$value) {
		if (is_object($value)) { // Array of objects
			$person=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$person = WT_Person::getInstance($value);
		} else { // Array of search results
			$gid = $key;
			if (isset($value['gid'])) $gid = $value['gid']; // from indilist
			if (isset($value[4])) $gid = $value[4]; // from indilist ALL
			$person = WT_Person::getInstance($gid);
		}
		if (is_null($person)) continue;
		if ($person->getType() !== 'INDI') continue;
		if (!$person->canDisplayName()) {
			continue;
		}
		//-- place filtering
		if ($option=='BIRT_PLAC' && strstr($person->getBirthPlace(), $filter)===false) continue;
		if ($option=='DEAT_PLAC' && strstr($person->getDeathPlace(), $filter)===false) continue;
		echo '<tr>';
		//-- Indi name(s)
		echo '<td align="', get_align($person->getFullName()), '">';
		list($surn, $givn)=explode(',', $person->getSortName());
		// If we're showing search results, then the highlighted name is not
		// necessarily the person's primary name.
		$primary=$person->getPrimaryName();
		$names=$person->getAllNames();
		foreach ($names as $num=>$name) {
			// Exclude duplicate names, which can occur when individuals have
			// multiple surnames, such as in Spain/Portugal
			$dupe_found=false;
			foreach ($names as $dupe_num=>$dupe_name) {
				if ($dupe_num>$num && $dupe_name['type']==$name['type'] && $dupe_name['full']==$name['full']) {
					// Take care not to skip the "primary" name
					if ($num==$primary) {
						$primary=$dupe_num;
					}
					$dupe_found=true;
					break;
				}
			}
			if ($dupe_found) {
				continue;
			}
			if ($name['type']!='NAME') {
				$title='title="'.WT_Gedcom_Tag::getLabel($name['type'], $person).'"';
			} else {
				$title='';
			}
			if ($num==$primary) {
				$class=' class="name2"';
				$sex_image=$person->getSexImage();
				list($surn, $givn)=explode(',', $name['sort']);
			} else {
				$class='';
				$sex_image='';
			}
			echo '<a ', $title, ' href="', $person->getHtmlUrl(), '"', $class. '>', highlight_search_hits($name['full']), '</a>', $sex_image, '<br/>';
		}
		// Indi parents
		echo $person->getPrimaryParentsNames("parents_indi_list_table_".$table_id." details1", 'none');
		echo '</td>';
		//-- GIVN/SURN
		echo '<td>', htmlspecialchars($givn), ',', htmlspecialchars($surn), '</td>';
		echo '<td>', htmlspecialchars($surn), ',', htmlspecialchars($givn), '</td>';
		//-- SOSA
		if ($option=='sosa') {
			echo '<td><a href="relationship.php?pid1=', $datalist[1], '&amp;pid2=', $person->getXref(), '" title="', WT_I18N::translate('Relationships'), '" class="name2">', $key, '</a></td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		//-- Birth date
		echo '<td>';
		if ($birth_dates=$person->getAllBirthDates()) {
			foreach ($birth_dates as $num=>$birth_date) {
				if ($num) {
					echo '<br/>';
				}
				echo $birth_date->Display(!$SEARCH_SPIDER);
			}
			if ($birth_dates[0]->gregorianYear()>=1550 && $birth_dates[0]->gregorianYear()<2030 && !isset($unique_indis[$person->getXref()])) {
				$birt_by_decade[floor($birth_dates[0]->gregorianYear()/10)*10] .= $person->getSex();
			}
		} else {
			$birth_date=$person->getEstimatedBirthDate();
			$birth_jd=$birth_date->JD();
			if ($SHOW_EST_LIST_DATES) {
				echo $birth_date->Display(!$SEARCH_SPIDER);
			} else {
				echo '&nbsp;';
			}
			$birth_dates[0]=new WT_Date('');
		}
		echo '</td>';
		//-- Event date (sortable)hidden by datatables code
		echo '<td>', $birth_date->JD(), '</td>';
		//-- Birth anniversary
		echo '<td>';
			$bage =WT_Date::GetAgeYears($birth_dates[0]);
			if (empty($bage)) { echo '&nbsp;'; } else { echo $bage; }
		echo '</td>';
		//-- Birth place
		echo '<td>';
		foreach ($person->getAllBirthPlaces() as $n=>$birth_place) {
			if ($n) {
				echo '<br>';
			}
			if ($SEARCH_SPIDER) {
				echo get_place_short($birth_place), ' ';
			} else {
				echo '<a href="', get_place_url($birth_place), '" title="', $birth_place, '">';
				echo highlight_search_hits(get_place_short($birth_place)), '</a>';
			}
		}
		echo '</td>';
		//-- Number of children
		echo '<td>', $person->getNumberOfChildren(), '</td>';
		//-- Death date
		echo '<td>';
		if ($death_dates=$person->getAllDeathDates()) {
			foreach ($death_dates as $num=>$death_date) {
				if ($num) {
					echo '<br/>';
				}
				echo $death_date->Display(!$SEARCH_SPIDER);
			}
			if ($death_dates[0]->gregorianYear()>=1550 && $death_dates[0]->gregorianYear()<2030 && !isset($unique_indis[$person->getXref()])) {
				$deat_by_decade[floor($death_dates[0]->gregorianYear()/10)*10] .= $person->getSex();
			}
		} else {
			$death_date=$person->getEstimatedDeathDate();
			$death_jd=$death_date->JD();
			if ($SHOW_EST_LIST_DATES) {
				echo $death_date->Display(!$SEARCH_SPIDER);
			} else if ($person->isDead()) {
				echo WT_I18N::translate('yes');
			} else {
				echo '&nbsp;';
			}
			$death_dates[0]=new WT_Date('');
		}
		echo '</td>';
		//-- Event date (sortable)hidden by datatables code
		echo '<td>', $death_date->JD(), '</td>';
		//-- Death anniversary
		echo '<td>';
			if ($death_dates[0]->isOK()) { echo WT_Date::GetAgeYears($death_dates[0]); } else { echo '&nbsp;'; }
		echo '</td>';
		//-- Age at death
		echo '<td>';
			if ($birth_dates[0]->isOK() && $death_dates[0]->isOK()) {
				$age = WT_Date::GetAgeYears($birth_dates[0], $death_dates[0]);
				echo $age;
				if (!isset($unique_indis[$person->getXref()])) {
					$deat_by_age[max(0, min($max_age, $age))] .= $person->getSex();
				}
			} else {
				// &nbsp; is required for validation (empty <td></td> not allowed), but
				// it breaks numeric sorting in datatables.
				//echo '&nbsp;';
			}
		echo '</td>';
		//-- Death place
		echo '<td>';
		foreach ($person->getAllDeathPlaces() as $n=>$death_place) {
			if ($n) {
				echo '<br>';
			}
			if ($SEARCH_SPIDER) {
				echo get_place_short($death_place), ' ';
			} else {
				echo '<a href="', get_place_url($death_place), '" title="', $death_place, '">';
				echo highlight_search_hits(get_place_short($death_place)), '</a>';
			}
		}
		echo '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>', $person->LastChangeTimestamp(empty($SEARCH_SPIDER)), '</td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		//-- Sorting by gender
		echo '<td>';
		echo $person->getSex();
		echo '</td>';
		//-- Filtering by birth date
		echo '<td>';
		if (!$person->canDisplayDetails() || WT_Date::Compare($birth_dates[0], $d100y)>0) {
			echo 'Y100';
		} else {
			echo 'YES';
		}
		echo '</td>';
		//-- Filtering by death date
		echo '<td>';
		if ($person->isDead()) {
			if (WT_Date::Compare($death_dates[0], $d100y)>0) {
				echo 'Y100';
			} else {
				echo 'YES';
			}
		} else {
			echo 'N';
		}
		echo '</td>';
		//-- Roots or Leaves ?
		echo '<td>';
		if (!$person->getChildFamilies()) { echo 'R'; }  // roots
		elseif (!$person->isDead() && $person->getNumberOfChildren()<1) { echo 'L'; } // leaves
		else { echo '&nbsp;'; }
		echo '</td>';
		echo '</tr>';
		$unique_indis[$person->getXref()]=true;
	}
	echo '</tbody>',
		'</table>';
	//-- charts
	echo '<div class="indi_list_table-charts_', $table_id, '" style="display:none">',
		'<table class="list_table center">',
		'<tr><td class="list_value_wrap">',
		print_chart_by_decade($birt_by_decade, WT_I18N::translate('Decade of birth')),
		'</td><td class="list_value_wrap">',
		print_chart_by_decade($deat_by_decade, WT_I18N::translate('Decade of death')),
		'</td></tr><tr><td colspan="2" class="list_value_wrap">',
		print_chart_by_age($deat_by_age, WT_I18N::translate('Age related to death year')),
		'</td></tr></table>',
		'</div>',
		'</div>'; // Close "indi-list"
}

// print a table of families
function print_fam_table($datalist, $option='') {
	global $GEDCOM, $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER, $controller;
	$table_id = 'ID'.floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	if ($option=='BIRT_PLAC' || $option=='DEAT_PLAC') return;
	if (count($datalist)<1) return;
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			var oTable'.$table_id.'=jQuery("#'.$table_id.'").dataTable( {
				"sDom": \'<"H"<"filtersH_'.$table_id.'"><"dt-clear">pf<"dt-clear">irl>t<"F"pl<"dt-clear"><"filtersF_'.$table_id.'">>\',
				"oLanguage": {
					"sLengthMenu": "'./* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value=\"10\">10<option value=\"20\">20</option><option value=\"30\">30</option><option value=\"50\">50</option><option value=\"100\">100</option><option value=\"-1\">'.WT_I18N::translate('All').'</option></select>').'",
					"sZeroRecords": "'.WT_I18N::translate('No records to display').'",
					"sInfo": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",
					"sInfoEmpty": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0').'",
					"sInfoFiltered": "'./* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_').'",
					"sProcessing": "'.WT_I18N::translate('Loading...').'",
					"sSearch": "'.WT_I18N::translate('Filter').'",
					"oPaginate": {
						"sFirst":    "'./* I18N: button label, first page    */ WT_I18N::translate('first').'",
						"sLast":     "'./* I18N: button label, last page     */ WT_I18N::translate('last').'",
						"sNext":     "'./* I18N: button label, next page     */ WT_I18N::translate('next').'",
						"sPrevious": "'./* I18N: button label, previous page */ WT_I18N::translate('previous').'"
					}
				},
				"bJQueryUI": true,
				"bAutoWidth":false,
				"bProcessing": true,
				"bRetrieve": true,
				"aoColumns": [
					/*  0 husb name */ {"iDataSort": 2},
					/*  1 GIVN,SURN */ {"sType": "unicode", "bVisible": false},
					/*  2 SURN,GIVN */ {"sType": "unicode", "bVisible": false},
					/*  3 age       */ {"sType": "numeric", "sClass": "center"},
					/*  4 wife name */ {"iDataSort": 6},
					/*  5 GIVN,SURN */ {"sType": "unicode", "bVisible": false},
					/*  6 SURN,GIVN */ {"sType": "unicode", "bVisible": false},
					/*  7 age       */ {"sType": "numeric", "sClass": "center"},
					/*  8 marr date */ {},
					/*  9 anniv     */ {"bSortable": false, "sClass": "center"},
					/* 10 marr plac */ {"sType": "unicode"},
					/* 12 children  */ {"sType": "numeric", "sClass": "center"},
					/* 13 CHAN      */ {"bVisible": '.($SHOW_LAST_CHANGE?'true':'false').'},
					/* 14 MARR      */ {"bVisible": false},
					/* 15 DEAT      */ {"bVisible": false},
					/* 16 TREE      */ {"bVisible": false}
				],
				"iDisplayLength": 20,
				"sPaginationType": "full_numbers"
		   });

			jQuery("div.filtersH_'.$table_id.'").html("'.addslashes(
				'<button type="button" id="DEAT_N_'.    $table_id.'" class="ui-state-default DEAT_N" title="'.    WT_I18N::translate('Show people who are alive or couples where both partners are alive.').'">'.WT_I18N::translate('Both alive').'</button>'.
				'<button type="button" id="DEAT_W_'.    $table_id.'" class="ui-state-default DEAT_W" title="'.    WT_I18N::translate('Show couples where only the female partner is deceased.').'">'.WT_I18N::translate('Widower').'</button>'.
				'<button type="button" id="DEAT_H_'.    $table_id.'" class="ui-state-default DEAT_H" title="'.    WT_I18N::translate('Show couples where only the male partner is deceased.').'">'.WT_I18N::translate('Widow').'</button>'.
				'<button type="button" id="DEAT_Y_'.    $table_id.'" class="ui-state-default DEAT_Y" title="'.    WT_I18N::translate('Show people who are dead or couples where both partners are deceased.').'">'.WT_I18N::translate('Both dead').'</button>'.
				'<button type="button" id="TREE_R_'.    $table_id.'" class="ui-state-default TREE_R" title="'.    WT_I18N::translate('Show «roots» couples or individuals.  These people may also be called «patriarchs».  They are individuals who have no parents recorded in the database.').'">'.WT_I18N::translate('Roots').'</button>'.
				'<button type="button" id="TREE_L_'.    $table_id.'" class="ui-state-default TREE_L" title="'.    WT_I18N::translate('Show «leaves» couples or individuals.  These are individuals who are alive but have no children recorded in the database.').'">'.WT_I18N::translate('Leaves').'</button>'.
				'<button type="button" id="MARR_U_'.    $table_id.'" class="ui-state-default MARR_U" title="'.    WT_I18N::translate('Show couples with an unknown marriage date.').'">'.WT_Gedcom_Tag::getLabel('MARR').'</button>'.
				'<button type="button" id="MARR_YES_'.  $table_id.'" class="ui-state-default MARR_YES" title="'.  WT_I18N::translate('Show couples who married more than 100 years ago.').'">'.WT_Gedcom_Tag::getLabel('MARR').'&gt;100</button>'.
				'<button type="button" id="MARR_Y100_'. $table_id.'" class="ui-state-default MARR_Y100" title="'. WT_I18N::translate('Show couples who married within the last 100 years.').'">'.WT_Gedcom_Tag::getLabel('MARR').'&lt;=100</button>'.
				'<button type="button" id="MARR_DIV_'.  $table_id.'" class="ui-state-default MARR_DIV" title="'.  WT_I18N::translate('Show divorced couples.').'">'.WT_Gedcom_Tag::getLabel('DIV').'</button>'.
				'<button type="button" id="MULTI_MARR_'.$table_id.'" class="ui-state-default MULTI_MARR" title="'.WT_I18N::translate('Show couples where one of partner is married more than once.').'">'.WT_I18N::translate('Multiple marriages').'</button>'.
				'<button type="button" id="RESET_'.$table_id.'" class="ui-state-default RESET" title="'.WT_I18N::translate('Reset to the list defaults.').'">'.WT_I18N::translate('Reset').'</button>'
			).'");

			jQuery("div.filtersF_'.$table_id.'").html("'.addslashes(
				'<button type="button" class="ui-state-default" id="GIVEN_SORT_M_'.$table_id.'">'.WT_I18N::translate('Sort by given names').'</button>'.
				'<button type="button" class="ui-state-default" id="GIVEN_SORT_F_'.$table_id.'">'.WT_I18N::translate('Sort by given names').'</button>'.
				'<button type="button" class="ui-state-default" id="cb_parents_'.$table_id.'" onclick="jQuery(\'div.parents_'.$table_id.'\').toggle();">'.WT_I18N::translate('Show parents').'</button>'.
				'<button type="button" class="ui-state-default" id="charts_fam_list_table" onclick="jQuery(\'div.fam_list_table-charts_'.$table_id.'\').toggle();">'. WT_I18N::translate('Show statistics charts').'</button>'
			).'");
			
			oTable'.$table_id.'.fnSortListener("#GIVEN_SORT_M_'.$table_id.'",1);
			oTable'.$table_id.'.fnSortListener("#GIVEN_SORT_F_'.$table_id.'",5);

			/* Add event listeners for filtering inputs */
			jQuery("#DEAT_N_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("N", 14);});
			jQuery("#DEAT_W_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("W", 14);});
			jQuery("#DEAT_H_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("H", 14);});
			jQuery("#DEAT_Y_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("Y", 14);});
			jQuery("#TREE_R_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("R", 15);});
			jQuery("#TREE_L_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("L", 15);});	
			jQuery("#MARR_U_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("U", 13);});
			jQuery("#MARR_YES_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("YES", 13);});
			jQuery("#MARR_Y100_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("Y100", 13);});
			jQuery("#MARR_DIV_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("DIV", 13);});
			jQuery("#MULTI_MARR_'.$table_id.'").click( function() { oTable'.$table_id.'.fnFilter("MULTI", 13);});
			jQuery("#RESET_'.$table_id.'").click( function() {
				for (i=0; i<17; i++) {
					oTable'.$table_id.'.fnFilter("", i );
				};
			});

			/* This code is a temporary fix for Datatables bug http://www.datatables.net/forums/discussion/4730/datatables_sort_wrapper-being-added-to-columns-with-bsortable-false/p1*/
			jQuery("th span:eq(9)").css("display", "none");
			jQuery("th div:eq(9)").css("margin", "auto").css("text-align", "center");
			
			jQuery(".fam-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
	');

	$stats = new WT_Stats($GEDCOM);
	$max_age = max($stats->oldestMarriageMaleAge(), $stats->oldestMarriageFemaleAge())+1;

	//-- init chart data
	for ($age=0; $age<=$max_age; $age++) $marr_by_age[$age]='';
	for ($year=1550; $year<2030; $year+=10) $birt_by_decade[$year]='';
	for ($year=1550; $year<2030; $year+=10) $marr_by_decade[$year]='';
	//--table wrapper
	echo '<div class="loading-image">&nbsp;</div>';
	echo '<div class="fam-list">';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_Gedcom_Tag::getLabel('NAME'), '</th>';
	echo '<th style="display:none;">HUSB:GIVN_SURN</th>';
	echo '<th style="display:none;">HUSB:SURN_GIVN</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('AGE'), '</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('NAME'), '</th>';
	echo '<th style="display:none;">WIFE:GIVN_SURN</th>';
	echo '<th style="display:none;">WIFE:SURN_GIVN</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('AGE'), '</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('MARR'), '</th>';
	echo '<th><img src="', $WT_IMAGES['reminder'], '" alt="', WT_I18N::translate('Anniversary'), '" title="', WT_I18N::translate('Anniversary'), '" border="0" /></th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('PLAC'), '</th>';
	echo '<th><img src="', $WT_IMAGES['children'], '" alt="', WT_I18N::translate('Children'), '" title="', WT_I18N::translate('Children'), '" border="0" /></th>';
	echo '<th ',($SHOW_LAST_CHANGE?'':' style="display:none"'),'>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	echo '<th style="display:none;">MARR</th>';
	echo '<th style="display:none;">DEAT</th>';
	echo '<th style="display:none;">TREE</th>';
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$num = 0;
	$d100y=new WT_Date(date('Y')-100);  // 100 years ago
	foreach ($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$family=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$family=WT_Family::getInstance($value);
		} else { // Array of search results
			$gid = "";
			if (isset($value['gid'])) $gid = $value['gid'];
			if (isset($value['gedcom'])) $family = new WT_Family($value['gedcom']);
			else $family = WT_Family::getInstance($gid);
		}
		if (is_null($family)) continue;
		if ($family->getType() !== 'FAM') continue;
		//-- Retrieve husband and wife
		$husb = $family->getHusband();
		if (is_null($husb)) $husb = new WT_Person('');
		$wife = $family->getWife();
		if (is_null($wife)) $wife = new WT_Person('');
		if (!$family->canDisplayDetails()) {
			continue;
		}
		//-- place filtering
		if ($option=='MARR_PLAC' && strstr($family->getMarriagePlace(), $filter)===false) continue;
		echo '<tr>';
		
		$sort_names=explode(' + ', $family->getSortName());
		if (count($sort_names)>1) {
			list($husb_name, $wife_name)=$sort_names;
		} else {
			$husb_name=$husb->getSortName();
			$wife_name=$wife->getSortName();
		}
		//-- Husband name(s)
		$names=$husb->getAllNames();
		// The husband's primary/secondary name might not be the family's primary name
		foreach ($names as $n=>$name) {
			if ($name['sort']==$husb_name) {
				$husb->setPrimaryName($n);
				break;
			}
		}
		$n1=$husb->getPrimaryName();
		$n2=$husb->getSecondaryName();
		echo '<td align="', get_align($names[$n1]['full']), '">';
		echo '<a href="', $family->getHtmlUrl(), '" class="name2" dir="', $TEXT_DIRECTION, '">', highlight_search_hits($names[$n1]['full']), '</a>';
		echo $husb->getSexImage();
		if ($n1!=$n2) {
			echo '<br /><a href="', $family->getHtmlUrl(), '">', highlight_search_hits($names[$n2]['full']), '</a>';
		}
		// Husband parents
		echo $husb->getPrimaryParentsNames('parents_'.$table_id.' details1', 'none');
		echo '</td>';
		//-- Husb GIVN
		list($surn, $givn)=explode(',', $husb->getSortName());
		echo '<td>', htmlspecialchars($givn), ',', htmlspecialchars($surn), '</td>';
		echo '<td>', htmlspecialchars($surn), ',', htmlspecialchars($givn), '</td>';
		$mdate=$family->getMarriageDate();
		//-- Husband age
		echo '<td>';
		$hdate=$husb->getBirthDate();
		if ($hdate->isOK()) {
			if ($hdate->gregorianYear()>=1550 && $hdate->gregorianYear()<2030) {
				$birt_by_decade[floor($hdate->gregorianYear()/10)*10] .= $husb->getSex();
			}
			if ($mdate->isOK()) {
				$hage=WT_Date::GetAgeYears($hdate, $mdate);
				$hage_jd = $mdate->MinJD()-$hdate->MinJD();
				echo $hage;
				$marr_by_age[max(0, min($max_age, $hage))] .= $husb->getSex();
			} else {
				echo '&nbsp;';
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Wife name(s)
		$names=$wife->getAllNames();
		// The husband's primary/secondary name might not be the family's primary name
		foreach ($names as $n=>$name) {
			if ($name['sort']==$wife_name) {
				$wife->setPrimaryName($n);
				break;
			}
		}
		$n1=$wife->getPrimaryName();
		$n2=$wife->getSecondaryName();
		echo '<td align="', get_align($names[$n1]['full']), '">';
		echo '<a href="', $family->getHtmlUrl(), '" class="name2" dir="', $TEXT_DIRECTION, '">', highlight_search_hits($names[$n1]['full']), '</a>';
		echo $wife->getSexImage();
		if ($n1!=$n2) {
			echo '<br /><a href="', $family->getHtmlUrl(), '">', highlight_search_hits($names[$n2]['full']), '</a>';
		}
		// Wife parents
		echo $wife->getPrimaryParentsNames('parents_'.$table_id.' details1', 'none');
		echo '</td>';
		//-- Wife GIVN
		list($surn, $givn)=explode(',', $wife->getSortName());
		echo '<td>', htmlspecialchars($givn), ',', htmlspecialchars($surn), '</td>';
		echo '<td>', htmlspecialchars($surn), ',', htmlspecialchars($givn), '</td>';
		$mdate=$family->getMarriageDate();
		//-- Wife age
		echo '<td>';
		$wdate=$wife->getBirthDate();
		if ($wdate->isOK()) {
			if ($wdate->gregorianYear()>=1550 && $wdate->gregorianYear()<2030) {
				$birt_by_decade[floor($wdate->gregorianYear()/10)*10] .= $wife->getSex();
			}
			if ($mdate->isOK()) {
				$wage=WT_Date::GetAgeYears($wdate, $mdate);
				$wage_jd = $mdate->MinJD()-$wdate->MinJD();
				echo $wage;
				$marr_by_age[max(0, min($max_age, $wage))] .= $wife->getSex();
			} else {
				echo '&nbsp;';
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Marriage date
		echo '<td>';
		if ($marriage_dates=$family->getAllMarriageDates()) {
			foreach ($marriage_dates as $n=>$marriage_date) {
				if ($n) {
					echo '<br/>';
				}
				echo '<div>', $marriage_date->Display(!$SEARCH_SPIDER), '</div>';
			}
			if ($marriage_dates[0]->gregorianYear()>=1550 && $marriage_dates[0]->gregorianYear()<2030) {
				$marr_by_decade[floor($marriage_dates[0]->gregorianYear()/10)*10] .= $husb->getSex().$wife->getSex();
			}
		} else if (get_sub_record(1, '1 _NMR', $family->getGedcomRecord())) {
			$hus = $family->getHusband();
			$wif = $family->getWife();
			if (empty($wif) && !empty($hus)) echo WT_Gedcom_Tag::getLabel('_NMR', $hus);
			else if (empty($hus) && !empty($wif)) echo WT_Gedcom_Tag::getLabel('_NMR', $wif);
			else echo WT_Gedcom_Tag::getLabel('_NMR');
		} else if (get_sub_record(1, '1 _NMAR', $family->getGedcomRecord())) {
			$hus = $family->getHusband();
			$wif = $family->getWife();
			if (empty($wif) && !empty($hus)) echo WT_Gedcom_Tag::getLabel('_NMAR', $hus);
			else if (empty($hus) && !empty($wif)) echo WT_Gedcom_Tag::getLabel('_NMAR', $wif);
			else echo WT_Gedcom_Tag::getLabel('_NMAR');
		} else {
			$factdetail = explode(' ', trim($family->getMarriageRecord()));
			if (isset($factdetail)) {
				if (count($factdetail) >= 3) {
					if (strtoupper($factdetail[2]) != "N") {
						echo WT_I18N::translate('yes');
					} else {
						echo WT_I18N::translate('no');
					}
				} else {
					echo '&nbsp;';
				}
			}
		}
		echo '</td>';
		//-- Marriage anniversary
		echo '<td>';
			$mage=WT_Date::GetAgeYears($mdate);
			if (empty($mage)) { echo '&nbsp;';} else { echo $mage; }
		echo '</td>';
		//-- Marriage place
		echo '<td>';
		foreach ($person->getAllMarriagePlaces() as $n=>$marriage_place) {
			if ($n) {
				echo '<br>';
			}
			if ($SEARCH_SPIDER) {
				echo get_place_short($marriage_place), ' ';
			} else {
				echo '<a href="', get_place_url($marriage_place), '" title="', $marriage_place, '">';
				echo highlight_search_hits(get_place_short($marriage_place)), '</a>';
			}
		}
		echo '</td>';
		//-- Number of children
		echo '<td>', $family->getNumberOfChildren(), '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>', $family->LastChangeTimestamp(empty($SEARCH_SPIDER)), '</td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		//-- Sorting by marriage date
		echo '<td>';
		if (!$family->canDisplayDetails() || !$mdate->isOK()) {
			echo 'U';
		} else {
			if (WT_Date::Compare($mdate, $d100y)>0) {
				echo 'Y100';
			} else {
				echo 'YES';
			}
		}
		if ($family->isDivorced()) {
			echo 'DIV';
		}
		if (count($husb->getSpouseFamilies())>1 || count($wife->getSpouseFamilies())>1) {
			echo 'MULTI';
		}
		echo '</td>';
		//-- Sorting alive/dead
		echo '<td>';
			if ($husb->isDead() && $wife->isDead()) echo 'Y';
			if ($husb->isDead() && !$wife->isDead()) {
				if ($wife->getSex()=='F') echo 'H';
				if ($wife->getSex()=='M') echo 'W'; // male partners
			}
			if (!$husb->isDead() && $wife->isDead()) {
				if ($husb->getSex()=='M') echo 'W';
				if ($husb->getSex()=='F') echo 'H'; // female partners
			}
			if (!$husb->isDead() && !$wife->isDead()) echo 'N';
		echo '</td>';
		//-- Roots or Leaves
		echo '<td>';
			if (!$husb->getChildFamilies() && !$wife->getChildFamilies()) { echo 'R'; } // roots
			elseif (!$husb->isDead() && !$wife->isDead() && $family->getNumberOfChildren()<1) { echo 'L'; } // leaves
			else { echo '&nbsp;'; }
		echo '</td>',
		'</tr>';
	}
	echo '</tbody>',
		'</table>';
	//-- charts
	echo '<div class="fam_list_table-charts_', $table_id, '" style="display:none">',
		'<table class="list_table center">',
		'<tr><td class="list_value_wrap">',
		print_chart_by_decade($birt_by_decade, WT_I18N::translate('Decade of birth')),
		'</td><td class="list_value_wrap">',
		print_chart_by_decade($marr_by_decade, WT_I18N::translate('Decade of marriage')),
		'</td></tr><tr><td colspan="2" class="list_value_wrap">',
		print_chart_by_age($marr_by_age, WT_I18N::translate('Age in year of marriage')),
		'</td></tr></table>',
		'</div>',
		'</div>'; // Close "fam-list"
}

// print a table of sources
function print_sour_table($datalist) {
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $controller;

	$table_id = "ID".floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#'.$table_id.'").dataTable( {
				"sDom": \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				"oLanguage": {
					"sLengthMenu": "'./* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value=\"10\">10<option value=\"20\">20</option><option value=\"30\">30</option><option value=\"50\">50</option><option value=\"100\">100</option><option value=\"-1\">'.WT_I18N::translate('All').'</option></select>').'",
					"sZeroRecords": "'.WT_I18N::translate('No records to display').'",
					"sInfo": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",
					"sInfoEmpty": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0').'",
					"sInfoFiltered": "'./* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_').'",
					"sProcessing": "'.WT_I18N::translate('Loading...').'",
					"sSearch": "'.WT_I18N::translate('Filter').'",
					"oPaginate": {
						"sFirst":    "'./* I18N: button label, first page    */ WT_I18N::translate('first').'",
						"sLast":     "'./* I18N: button label, last page     */ WT_I18N::translate('last').'",
						"sNext":     "'./* I18N: button label, next page     */ WT_I18N::translate('next').'",
						"sPrevious": "'./* I18N: button label, previous page */ WT_I18N::translate('previous').'"
					}
				},
				"bJQueryUI": true,
				"bAutoWidth":false,
				"bProcessing": true,
				"aoColumns": [
					/*  0 title  */ {"iDataSort": 1},
					/*  1 TITL   */ {"bVisible": false, "sType": "unicode"},
					/*  4 author */ {"sType": "unicode"},
					/*  5 #indi  */ {"sType": "numeric", "sClass": "center"},
					/*  6 #fam   */ {"sType": "numeric", "sClass": "center"},
					/*  7 #obje  */ {"sType": "numeric", "sClass": "center"},
					/*  8 #note  */ {"sType": "numeric", "sClass": "center"},
					/*  9 CHAN   */ {"bVisible": '.($SHOW_LAST_CHANGE?'true':'false').'},
					/* 10 DELETE */ {"bVisible": '.(WT_USER_GEDCOM_ADMIN?'true':'false').', "bSortable": false}
				],
				"iDisplayLength": 20,
				"sPaginationType": "full_numbers"
		   });
			jQuery(".source-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

	//--table wrapper
	echo '<div class="loading-image">&nbsp;</div>';
	echo '<div class="source-list">';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_Gedcom_Tag::getLabel('TITL'), '</th>';
	echo '<th style="display:none;">TITL</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('AUTH'), '</th>';
	echo '<th>', WT_I18N::translate('Individuals'), '</th>';
	echo '<th>', WT_I18N::translate('Families'), '</th>';
	echo '<th>', WT_I18N::translate('Media objects'), '</th>';
	echo '<th>', WT_I18N::translate('Shared notes'), '</th>';
	echo '<th ',($SHOW_LAST_CHANGE?'':' style="display:none"'),'>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	echo '<th style="display:none;">DEL</th>';
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$n=0;
	foreach ($datalist as $key=>$value) {
		if (is_object($value)) { // Array of objects
			$source=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$source=WT_Source::getInstance($key); // from placelist
			if (is_null($source)) {
				$source=WT_Source::getInstance($value);
			}
			unset($value);
		} else { // Array of search results
			$gid='';
			if (isset($value['gid'])) {
				$gid=$value['gid'];
			}
			if (isset($value['gedcom'])) {
				$source=new WT_Source($value['gedcom']);
			} else {
				$source=WT_Source::getInstance($gid);
			}
		}
		if (!$source || !$source->canDisplayDetails()) {
			continue;
		}
		$link_url=$source->getHtmlUrl();
		echo '<tr>';
		//-- Source name(s)
		$tmp=$source->getFullName();
		echo '<td><a href="', $link_url, '">';
		foreach ($source->getAllNames() as $n=>$name) {
			if ($n) {
				echo '<br/>';
			}
			echo highlight_search_hits($name['full']);
		}	
		echo '</a></td>';
		// Sortable name
		echo '<td>', strip_tags($source->getFullName()), '</td>';
		//-- Author
		echo '<td>', highlight_search_hits(htmlspecialchars($source->getAuth())), '</td>';
		//-- Linked INDIs
		echo '<td>', $source->countLinkedIndividuals(), '</td>';
		//-- Linked FAMs
		echo '<td>', $source->countLinkedfamilies(), '</td>';
		//-- Linked OBJEcts
		echo '<td>', $source->countLinkedMedia(), '</td>';
		//-- Linked NOTEs
		echo '<td>', $source->countLinkedNotes(), '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>'.$source->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		//-- Delete 
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<td><div title="', WT_I18N::translate('Delete'), '" class="deleteicon" onclick="if (confirm(\'', addslashes(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($source->getFullName()))), '\')) jQuery.post(\'action.php\',{action:\'delete-source\',xref:\'', $source->getXref(), '\'},function(){location.reload();})"><span class="link_text">', WT_I18N::translate('Delete'), '</span></div></td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		echo '</tr>';
	}
	echo
		'</tbody>',
		'</table>',
		'</div>';
}

// print a table of shared notes
function print_note_table($datalist) {
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $controller;

	if (count($datalist)<1) {
		return;
	}
	$table_id = 'ID'.floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#'.$table_id.'").dataTable({
			"sDom": \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
			"oLanguage": {
				"sLengthMenu": "'./* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value=\"10\">10<option value=\"20\">20</option><option value=\"30\">30</option><option value=\"50\">50</option><option value=\"100\">100</option><option value=\"-1\">'.WT_I18N::translate('All').'</option></select>').'",
				"sZeroRecords": "'.WT_I18N::translate('No records to display').'",
				"sInfo": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",
				"sInfoEmpty": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0').'",
				"sInfoFiltered": "'./* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_').'",
				"sProcessing": "'.WT_I18N::translate('Loading...').'",
				"sSearch": "'.WT_I18N::translate('Filter').'",
				"oPaginate": {
					"sFirst":    "'./* I18N: button label, first page    */ WT_I18N::translate('first').'",
					"sLast":     "'./* I18N: button label, last page     */ WT_I18N::translate('last').'",
					"sNext":     "'./* I18N: button label, next page     */ WT_I18N::translate('next').'",
					"sPrevious": "'./* I18N: button label, previous page */ WT_I18N::translate('previous').'"
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"bProcessing": true,
			"aoColumns": [
				/* 0 title  */ {"iDataSort": 1},
				/* 1 TITL   */ {"bVisible": false, "sType": "unicode"},
				/* 2 #indi  */ {"sType": "numeric", "sClass": "center"},
				/* 3 #fam   */ {"sType": "numeric", "sClass": "center"},
				/* 4 #obje  */ {"sType": "numeric", "sClass": "center"},
				/* 5 CHAN   */ {"bVisible": '.($SHOW_LAST_CHANGE?'true':'false').'},
				/* 6 DELETE */ {"bVisible": '.(WT_USER_GEDCOM_ADMIN?'true':'false').', "bSortable": false}
			],
			"iDisplayLength": 20,
			"sPaginationType": "full_numbers"
	   });
			jQuery(".note-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');
		
	//--table wrapper
	echo '<div class="loading-image">&nbsp;</div>';
	echo '<div class="note-list">';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_Gedcom_Tag::getLabel('TITL'), '</th>';
	echo '<th>', WT_I18N::translate('Individuals'), '</th>';
	echo '<th>', WT_I18N::translate('Families'), '</th>';
	echo '<th>', WT_I18N::translate('Media objects'), '</th>';
	echo '<th>', WT_I18N::translate('Sources'), '</th>';
	echo '<th ',($SHOW_LAST_CHANGE?'':' style="display:none"'),'>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	echo '<th style="display:none;">DEL</th>';
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	foreach ($datalist as $note) {
		if (!$note->canDisplayDetails()) {
			continue;
		}
		echo '<tr>';
		//-- Shared Note name
		$tmp=$note->getFullName();
		echo '<td><a href="', $note->getHtmlUrl(), '">', highlight_search_hits($tmp), '</a></td>';
		// Sortable name
		echo '<td>', strip_tags($note->getFullName()), '</td>';
		//-- Linked INDIs
		$tmp=$note->countLinkedIndividuals();
		echo '<td>', $tmp, '</td>';
		//-- Linked FAMs
		$tmp=$note->countLinkedfamilies();
		echo '<td>', $tmp, '</td>';
		//-- Linked OBJEcts
		$tmp=$note->countLinkedMedia();
		echo '<td>', $tmp, '</td>';
		//-- Linked SOURs
		$tmp=$note->countLinkedSources();
		echo '<td>', $tmp, '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>'.$note->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
		} else {
			echo '<td>CHAN</td>';
		}
		//-- Delete 
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<td><div title="', WT_I18N::translate('Delete'), '" class="deleteicon" onclick="if (confirm(\'', addslashes(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($note->getFullName()))), '\')) jQuery.post(\'action.php\',{action:\'delete-note\',xref:\'', $note->getXref(), '\'},function(){location.reload();})"><span class="link_text">', WT_I18N::translate('Delete'), '</span></div></td>';
		} else {
			echo '<td style="display:none;">DEL</td>';
		}
		echo '</tr>';
	}
	echo '</tbody>',
		'</table>',
		'</div>';
}

// print a table of repositories
function print_repo_table($repos) {
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER, $controller;

	if (!$repos) {
		return;
	}
	$table_id = 'ID'.floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery("#'.$table_id.'").dataTable( {
			"sDom": \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
			"oLanguage": {
				"sLengthMenu": "'./* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value=\"10\">10<option value=\"20\">20</option><option value=\"30\">30</option><option value=\"50\">50</option><option value=\"100\">100</option><option value=\"-1\">'.WT_I18N::translate('All').'</option></select>').'",
				"sZeroRecords": "'.WT_I18N::translate('No records to display').'",
				"sInfo": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",
				"sInfoEmpty": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0').'",
				"sInfoFiltered": "'./* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_').'",
				"sProcessing": "'.WT_I18N::translate('Loading...').'",
				"sSearch": "'.WT_I18N::translate('Filter').'",
				"oPaginate": {
					"sFirst":    "'./* I18N: button label, first page    */ WT_I18N::translate('first').'",
					"sLast":     "'./* I18N: button label, last page     */ WT_I18N::translate('last').'",
					"sNext":     "'./* I18N: button label, next page     */ WT_I18N::translate('next').'",
					"sPrevious": "'./* I18N: button label, previous page */ WT_I18N::translate('previous').'"
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"bProcessing": true,
			"aoColumnDefs": [
				{"bSortable": false, "aTargets": [ 3 ]},
				{"sType": "numeric", "aTargets": [ 1 ]}
			],
			"iDisplayLength": 20,
			"sPaginationType": "full_numbers"
	   });
		jQuery(".repo-list").css("visibility", "visible");
		jQuery(".loading-image").css("display", "none");
		');
		
	//--table wrapper
	echo '<div class="loading-image">&nbsp;</div>';
	echo '<div class="repo-list">';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_I18N::translate('Repository name'), '</th>';
	echo '<th>', WT_I18N::translate('Sources'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	} else {
		echo '<th style="display:none;">CHAN</th>';
	}
	if (WT_USER_GEDCOM_ADMIN) {
		echo '<th>&nbsp;</th>';//delete
	} else {
		echo '<th style="display:none;">DEL</th>';
	}
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$n=0;
	foreach ($repos as $repo) {
		if (!$repo->canDisplayDetails()) {
			continue;
		}
		echo '<tr>';
		//-- Repository name(s)
		$name = $repo->getFullName();
		echo '<td align="', get_align($name), '"><a href="', $repo->getHtmlUrl(), '" class="list_item name2">', highlight_search_hits(htmlspecialchars($name)), '</a>';
		$addname=$repo->getAddName();
		if ($addname) {
			echo '<br /><a href="', $repo->getHtmlUrl(), '" class="list_item">', highlight_search_hits($addname), '</a>';
		}
		echo '</td>';
		//-- Linked SOURces
		$tmp=$repo->countLinkedSources();
		echo '<td>', $tmp, '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>', $repo->LastChangeTimestamp(!$SEARCH_SPIDER), '</td>';
		} else {
			echo '<td style="display:none;">CHAN</td>';
		}
		//-- Delete 
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<td><div title="', WT_I18N::translate('Delete'), '" class="deleteicon" onclick="if (confirm(\'', addslashes(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($repo->getFullName()))), '\')) jQuery.post(\'action.php\',{action:\'delete-repository\',xref:\'', $repo->getXref(), '\'},function(){location.reload();})"><span class="link_text">', WT_I18N::translate('Delete'), '</span></div></td>';
		} else {
			echo '<td style="display:none;">DEL</td>';
		}
		echo '</tr>';
	}
	echo '</tbody>',
		'</table>',
		'</div>';
}

// print a table of media objects
function print_media_table($datalist) {
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $controller;

	if (count($datalist)<1) return;
	$table_id = 'ID'.floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery("#'.$table_id.'").dataTable( {
			"sDom": \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
			"oLanguage": {
				"sLengthMenu": "'./* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value=\"10\">10<option value=\"20\">20</option><option value=\"30\">30</option><option value=\"50\">50</option><option value=\"100\">100</option><option value=\"-1\">'.WT_I18N::translate('All').'</option></select>').'",
				"sZeroRecords": "'.WT_I18N::translate('No records to display').'",
				"sInfo": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_').'",
				"sInfoEmpty": "'./* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0').'",
				"sInfoFiltered": "'./* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_').'",
				"sProcessing": "'.WT_I18N::translate('Loading...').'",
				"sSearch": "'.WT_I18N::translate('Filter').'",
				"oPaginate": {
					"sFirst":    "'./* I18N: button label, first page    */ WT_I18N::translate('first').'",
					"sLast":     "'./* I18N: button label, last page     */ WT_I18N::translate('last').'",
					"sNext":     "'./* I18N: button label, next page     */ WT_I18N::translate('next').'",
					"sPrevious": "'./* I18N: button label, previous page */ WT_I18N::translate('previous').'"
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"bProcessing": true,
			"aoColumnDefs": [
				{"bSortable": false, "aTargets": [ 0 ]},
				{"sType": "numeric", "aTargets": [2, 3, 4]}
			],
			"iDisplayLength": 20,
			"sPaginationType": "full_numbers"
	   });
		jQuery(".media-list").css("visibility", "visible");
		jQuery(".loading-image").css("display", "none");
		');
		
	//--table wrapper
	echo '<div class="loading-image">&nbsp;</div>';
	echo '<div class="media-list">';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_I18N::translate('Media'), '</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('TITL'), '</th>';
	echo '<th>', WT_I18N::translate('Individuals'), '</th>';
	echo '<th>', WT_I18N::translate('Families'), '</th>';
	echo '<th>', WT_I18N::translate('Sources'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	} else {
		echo '<th style="display:none;">CHAN</th>';
	}
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$n = 0;
	foreach ($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$media=$value;
		} else {
			$media = new WT_Media($value["GEDCOM"]);
			if (is_null($media)) $media = WT_Media::getInstance($key);
			if (is_null($media)) continue;
		}
		if ($media->canDisplayDetails()) {
			$name = $media->getFullName();
			echo "<tr>";
			//-- Object thumbnail
			echo '<td><img src="', $media->getThumbnail(), '" alt="', $name, '" /></td>';
			//-- Object name(s)
			echo '<td align="', get_align($name), '">';
			echo '<a href="', $media->getHtmlUrl(), '" class="list_item name2">';
			echo highlight_search_hits($name), '</a>';
			if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT)
				echo '<br /><a href="', $media->getHtmlUrl(), '">', basename($media->getFilename()), '</a>';
			if ($media->getNote()) echo '<br />', print_fact_notes('1 NOTE '.$media->getNote(), 1);
			echo '</td>';

			//-- Linked INDIs
			$tmp=$media->countLinkedIndividuals();
			echo '<td>', $tmp, '</td>';
			//-- Linked FAMs
			$tmp=$media->countLinkedfamilies();
			echo '<td>', $tmp, '</td>';
			//-- Linked SOURces
			$tmp=$media->countLinkedSources();
			echo '<td>', $tmp, '</td>';
			//-- Last change
			if ($SHOW_LAST_CHANGE) {
				echo '<td>'.$media->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
			} else {
				echo '<td style="display:none;">CHAN</td>';
			}
			echo '</tr>';
		}
	}
	echo '</tbody>',
		'</table>',
		'</div>';
}

// Print a table of surnames, for the top surnames block, the indi/fam lists, etc.
// $surnames - array (of SURN, of array of SPFX_SURN, of array of PID)
// $type     - "indilist" (counts of individuals) or "famlist" (counts of spouses)
function format_surname_table($surnames, $type) {
	if ($type=='famlist') {
		$col_heading=WT_I18N::translate('Spouses');
	} else {
		$col_heading=WT_I18N::translate('Individuals');
	}

	$thead=
		'<tr>'.
		'<th class="list_label">'.WT_Gedcom_Tag::getLabel('SURN').'</th>'.
		'<th class="list_label">'.$col_heading.'</th>'.
		'</tr>';

	$tbody='';
	$unique_surn=array();
	$unique_indi=array();
	foreach ($surnames as $surn=>$surns) {
		// Each surname links back to the indi/fam surname list
		if ($surn) {
			$url=$type.'.php?surname='.rawurlencode($surn).'&amp;ged='.WT_GEDURL;
		} else {
			$url=$type.'.php?alpha=,&amp;ged='.WT_GEDURL;
		}
		// Row counter
		$tbody.='<tr>';
		// Surname
		$tbody.='<td class="list_value" align="'.get_align($surn).'">';
		if (count($surns)==1) {
			// Single surname variant
			foreach ($surns as $spfxsurn=>$indis) {
				$tbody.='<a href="'.$url.'" class="list_item name1">'.htmlspecialchars($spfxsurn).'</a>';
				$unique_surn[$spfxsurn]=true;
				foreach (array_keys($indis) as $pid) {
					$unique_indi[$pid]=true;
				}
			}
		} else {
			// Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc.
			foreach ($surns as $spfxsurn=>$indis) {
				$tbody.='<a href="'.$url.'" class="list_item name1">'.htmlspecialchars($spfxsurn).'</a><br />';
				$unique_surn[$spfxsurn]=true;
				foreach (array_keys($indis) as $pid) {
					$unique_indi[$pid]=true;
				}
			}
		}
		$tbody.='</td>';
		// Surname count
		$tbody.='<td class="list_value center">';
		if (count($surns)==1) {
			// Single surname variant
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal=count($indis);
				$tbody.=WT_I18N::number($subtotal);
			}
		} else {
			// Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc.
			$subtotal=0;
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal+=count($indis);
				$tbody.=WT_I18N::number(count($indis)).'<br />';
			}
			$tbody.=WT_I18N::number($subtotal);
		}
		$tbody.='</td></tr>';
	}

	$tfoot=
		'<tr>'.
		'<td class="list_item">&nbsp;</td>'.
		'<td class="list_label name2">'.
		/* I18N: A count of individuals */ WT_I18N::translate('Total individuals: %s', WT_I18N::number(count($unique_indi))).
		'<br/>'.
		/* I18N: A count of surnames */ WT_I18N::translate('Total surnames: %s', WT_I18N::number(count($unique_surn))).
		'</td>'.
		'</tr>';

	return
		'<table class="list_table surname-list">'.
		'<thead>'.$thead.'</thead>'.
		'<tfoot>'.$tfoot.'</tfoot>'.
		'<tbody>'.$tbody.'</tbody>'.
		'</table>';
}

// Print a tagcloud of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $type string, indilist or famlist
// @param $totals, boolean, show totals after each name
function format_surname_tagcloud($surnames, $type, $totals) {
	$cloud=new Zend_Tag_Cloud(
		array(
			'tagDecorator'=>array(
				'decorator'=>'HtmlTag',
				'options'=>array(
					'htmlTags'=>array(),
					'fontSizeUnit'=>'%',
					'minFontSize'=>80,
					'maxFontSize'=>250
				)
			),
			'cloudDecorator'=>array(
				'decorator'=>'HtmlCloud',
				'options'=>array(
					'htmlTags'=>array(
						'div'=>array(
							'class'=>'tag_cloud'
						)
					)
				)
			)
		)
	);
	foreach ($surnames as $surn=>$surns) {
		foreach ($surns as $spfxsurn=>$indis) {
			$cloud->appendTag(array(
				'title'=>$totals ? WT_I18N::translate('%1$s (%2$d)', $spfxsurn, count($indis)) : $spfxsurn,
				'weight'=>count($indis),
				'params'=>array(
					'url'=>$surn ?
						$type.'.php?surname='.urlencode($surn).'&amp;ged='.WT_GEDURL :
						$type.'.php?alpha=,&amp;ged='.WT_GEDURL
				)
			));
		}
	}
	return (string)$cloud;
}

// Print a list of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $style, 1=bullet list, 2=semicolon-separated list, 3=tabulated list with up to 4 columns
// @param $totals, boolean, show totals after each name
// @param $type string, indilist or famlist
function format_surname_list($surnames, $style, $totals, $type) {
	global $TEXT_DIRECTION, $GEDCOM;

	$html=array();
	foreach ($surnames as $surn=>$surns) {
		// Each surname links back to the indilist
		if ($surn) {
			$url=$type.'.php?surname='.urlencode($surn).'&amp;ged='.rawurlencode($GEDCOM);
		} else {
			$url=$type.'.php?alpha=,&amp;ged='.rawurlencode($GEDCOM);
		}
		// If all the surnames are just case variants, then merge them into one
		// Comment out this block if you want SMITH listed separately from Smith
		$first_spfxsurn=null;
		foreach ($surns as $spfxsurn=>$indis) {
			if ($first_spfxsurn) {
				if (utf8_strtoupper($spfxsurn)==utf8_strtoupper($first_spfxsurn)) {
					$surns[$first_spfxsurn]=array_merge($surns[$first_spfxsurn], $surns[$spfxsurn]);
					unset ($surns[$spfxsurn]);
				}
			} else {
				$first_spfxsurn=$spfxsurn;
			}
		}
		$subhtml='<a href="'.$url.'">'.htmlspecialchars(implode(', ', array_keys($surns))).'</a>';

		if ($totals) {
			$subtotal=0;
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal+=count($indis);
			}
			$subhtml.=' ['.$subtotal.']';
		}
		$html[]=$subhtml;

	}
	switch ($style) {
	case 1:
		return '<ul><li>'.implode('</li><li>', $html).'</li></ul>';
	case 2:
		return implode('; ', $html);
	case 3:
		$i = 0;
		$count = count($html);
		$count_indi = 0;
		$col = 1;
		if ($count>36) $col=4;
		else if ($count>18) $col=3;
		else if ($count>6) $col=2;
		$newcol=ceil($count/$col);
		$html2 ='<table class="list_table"><tr>';
		$html2.='<td class="list_value" style="padding: 14px;">';

		foreach ($html as $surn=>$surns) {
			$html2.= $surns.'<br />';
			$i++;
			if ($i==$newcol && $i<$count) {
				$html2.='</td><td class="list_value" style="padding: 14px;">';
				$newcol=$i+ceil($count/$col);
			}
		}
		$html2.='</td></tr></table>';

		return $html2;
	}
}


// print a list of recent changes
function print_changes_list($change_ids, $sort, $show_parents=false) {
	global $SHOW_MARRIED_NAMES;
	$n = 0;
	$arr=array();
	foreach ($change_ids as $change_id) {
		$record = WT_GedcomRecord::getInstance($change_id);
		if (!$record || !$record->canDisplayDetails()) {
			continue;
		}
		// setup sorting parameters
		$arr[$n]['record'] = $record;
		$arr[$n]['jd'] = ($sort == 'name') ? 1 : $n;
		$arr[$n]['anniv'] = $record->LastChangeTimestamp(false, true);
		$arr[$n++]['fact'] = $record->getSortName(); // in case two changes have same timestamp
	}

	switch ($sort) {
	case 'name':
		uasort($arr, 'event_sort_name');
		break;
	case 'date_asc':
		uasort($arr, 'event_sort');
		$arr = array_reverse($arr);
		break;
	case 'date_desc':
		uasort($arr, 'event_sort');
	}
	$return = '';
	foreach ($arr as $value) {
		$return .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item name2">' . $value['record']->getFullName() . '</a>';
		$return .= '<div class="indent" style="margin-bottom:5px">';
		if ($value['record']->getType() == 'INDI') {
			if ($value['record']->getAddName()) {
				$return .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item">' . $value['record']->getAddName() . '</a>';
			}
			if ($SHOW_MARRIED_NAMES) {
				foreach ($value['record']->getAllNames() as $name) {
					if ($name['type'] == '_MARNM') {
						$return .= '<div><a title="' . WT_Gedcom_Tag::getLabel('_MARNM') . '" href="' . $value['record']->getHtmlUrl() . '" class="list_item">' . $name['full'] . '</a></div>';
					}
				}
			}
			if ($show_parents) {
				$return .= $value['record']->getPrimaryParentsNames('details1');
			}
		}
		$return .= /* I18N: [a record was] Changed on <date/time> by <user> */ WT_I18N::translate('Changed on %1$s by %2$s', $value['record']->LastChangeTimestamp(false), $value['record']->LastChangeUser());
		$return .= '</div>';
	}
	return $return;
}

// print a table of recent changes
function print_changes_table($change_ids, $sort, $show_parents=false) {
	global $SHOW_MARRIED_NAMES, $TEXT_DIRECTION, $WT_IMAGES, $controller;

	$return = '';
	$n = 0;
	$table_id = "ID" . floor(microtime() * 1000000); // create a unique ID
	switch ($sort) {
	case 'name':        //name
		$aaSorting = "[5,'asc'], [4,'desc']";
		break;
	case 'date_asc':    //date ascending
		$aaSorting = "[4,'asc'], [5,'asc']";
		break;
	case 'date_desc':   //date descending
		$aaSorting = "[4,'desc'], [5,'asc']";
		break;
	}
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery("#'.$table_id.'").dataTable({
				"bAutoWidth":false,
				"bPaginate": false,
				"bLengthChange": false,
				"bFilter": false,
				"bInfo": false,
				"oLanguage": {
					"sZeroRecords": "'.WT_I18N::translate('No records to display').'",
				},
				"bJQueryUI": false,
				"aaSorting": ['.$aaSorting.'],
				"aoColumns": [
					/* 0-Sex */     { "bSortable" : false },
					/* 1-Record */  { "iDataSort" : 5 },
					/* 2-Change */  { "iDataSort" : 4 },
					/* 3=By */      null,
					/* 4-DATE */    { "bVisible" : false },
					/* 5-SORTNAME */{ "bVisible" : false }
				]
			});
		');

		//-- table header
		$return .= "<table id='" . $table_id . "' class='list_table center width100'>";
		$return .= "<thead><tr>";
		$return .= "<th class='list_label'></th>";
		$return .= "<th style='cursor:pointer;' class='list_label'>" . WT_I18N::translate('Record') . "</th>";
		$return .= "<th style='cursor:pointer;' class='list_label'>" . WT_Gedcom_Tag::getLabel('CHAN') . "</th>";
		$return .= "<th style='cursor:pointer;' class='list_label'>" . WT_Gedcom_Tag::getLabel('_WT_USER') . "</th>";
		$return .= "<th style='display:none;'>DATE</th>";     //hidden by datatables code
		$return .= "<th style='display:none;'>SORTNAME</th>"; //hidden by datatables code
		$return .= "</tr></thead><tbody>";
		//-- table body

		foreach ($change_ids as $change_id) {
		$record = WT_GedcomRecord::getInstance($change_id);
		if (!$record || !$record->canDisplayDetails()) {
			continue;
		}
		$return .= "<tr><td>";
		$indi = false;
		switch ($record->getType()) {
			case "INDI":
				$return .= $record->getSexImage('small', '', '', false);
				$indi = true;
				break;
			case "FAM":
				$return .= '<img src="' . $WT_IMAGES['cfamily'] . '" title="" alt="" height="12" />';
				break;
			case "OBJE":
				$return .= '<img src="' . $record->getMediaIcon() . '" title="" alt="" height="12" />';
				break;
			case "NOTE":
				$return .= '<img src="' . $WT_IMAGES['note'] . '" title="" alt="" height="12" />';
				break;
			case "SOUR":
				$return .= '<img src="' . $WT_IMAGES['source'] . '" title="" alt="" height="12" />';
				break;
			case "REPO":
				$return .= '<img src="' . $WT_IMAGES['repository'] . '" title="" alt="" height="12" />';
				break;
			default:
				break;
		}
		$return .= "</td>";
		++$n;
		//-- Record name(s)
		$name = $record->getFullName();
		$return .= "<td class='wrap' align='" . get_align($name) . "'>";
		$return .= "<a href='" . $record->getHtmlUrl() . "' class='name2' dir='" . $TEXT_DIRECTION . "'>" . $name . "</a>";
		if ($indi) {
			$return .= "<div class='indent'>";
			$addname = $record->getAddName();
			if ($addname) {
				$return .= "<a href='" . $record->getHtmlUrl() . "' class=''>" . $addname . "</a>";
			}
			if ($SHOW_MARRIED_NAMES) {
				foreach ($record->getAllNames() as $name) {
					if ($name['type'] == '_MARNM') {
						$return .= "<div><a title='" . WT_Gedcom_Tag::getLabel('_MARNM') . "' href='" . $record->getHtmlUrl() . "' class=''>" . $name['full'] . "</a></div>";
					}
				}
			}
			if ($show_parents) {
				$return .= $record->getPrimaryParentsNames("parents_$table_id details1");
			}
			$return .= "</div>"; //class='indent'
		}
		$return .= "</td>";
		//-- Last change date/time
		$return .= "<td class='wrap'>" . $record->LastChangeTimestamp(empty($SEARCH_SPIDER)) . "</td>";
		//-- Last change user
		$return .= "<td class='wrap'>" . $record->LastChangeUser() . "</td>";
		//-- change date (sortable) hidden by datatables code
		$return .= "<td  style='display:none;'>" . $record->LastChangeTimestamp(false, true) . "</td>";
		//-- names (sortable) hidden by datatables code
		$return .= "<td  style='display:none;'>" . $record->getSortName() . "</td></tr>";
	}

	//-- table footer
	$return .= "</tbody>";
	$return .= "</table>";
	if ($n>0) {
		$return .= WT_I18N::translate('Showing %1$s to %2$s of %3$s', 1, $n, $n);
	}
	return $return;
}


// print a table of events
function print_events_table($startjd, $endjd, $events='BIRT MARR DEAT', $only_living=false, $sort_by='anniv') {
	global $TEXT_DIRECTION, $WT_IMAGES, $controller;

	$table_id = "ID".floor(microtime()*1000000); // each table requires a unique ID
	$controller
		->addExternalJavaScript(WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js')
		->addInlineJavaScript('
			jQuery("#'.$table_id.'").dataTable({
				"sDom": \'<"F"li>\',
				"bAutoWidth":false,
				"bPaginate": false,
				"bLengthChange": false,
				"bFilter": false,
				"bInfo": false,
				"bJQueryUI": false,
				//"aaSorting": [[ '.($sort_by=='alpha' ? 0 : 3).', "asc"]],
				"aoColumns": [
					/* 0-Record */ { "iDataSort": 1 },
					/* 1-NAME */   { "bVisible": false },
					/* 2-Date */   { "iDataSort": 3 },
					/* 3-DATE */   { "bVisible": false },
					/* 4-Anniv. */  null,
					/* 5-Event */   null
				]
			});		
		');

	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;

	$return = '';

	$filtered_events = array();

	foreach (get_events_list($startjd, $endjd, $events) as $value) {
		$record=$value['record'];
		//-- only living people ?
		if ($only_living) {
			if ($record->getType()=="INDI" && $record->isDead()) {
				$filter ++;
				continue;
			}
			if ($record->getType()=="FAM") {
				$husb = $record->getHusband();
				if (is_null($husb) || $husb->isDead()) {
					$filter ++;
					continue;
				}
				$wife = $record->getWife();
				if (is_null($wife) || $wife->isDead()) {
					$filter ++;
					continue;
				}
			}
		}

		// Privacy
		if (!$record->canDisplayDetails() || !canDisplayFact($record->getXref(), $record->getGedId(), $value['factrec'])) {
			continue;
		}
		//-- Counter
		$output ++;

		if ($output==1) {
			//-- First table row:  start table headers, etc. first
			$return .= '<table id="'.$table_id.'" class="list_table center width100">';
			$return .= '<thead><tr>';
			$return .= '<th style="cursor:pointer;" class="list_label">'.WT_I18N::translate('Record').'</th>';
			$return .= '<th style="display:none;">NAME</th>'; //hidden by datables code
			$return .= '<th style="cursor:pointer;" class="list_label">'.WT_Gedcom_Tag::getLabel('DATE').'</th>';
			$return .= '<th style="display:none;">DATE</th>'; //hidden by datables code
			$return .= '<th style="cursor:pointer;" class="list_label"><img src="'.$WT_IMAGES["reminder"].'" alt="'.WT_I18N::translate('Anniversary').'" title="'.WT_I18N::translate('Anniversary').'" border="0" /></th>';
			$return .= '<th style="cursor:pointer;" class="list_label">'.WT_Gedcom_Tag::getLabel('EVEN').'</th>';
			$return .= '</tr></thead><tbody>'."\n";
		}

		$value['name'] = $record->getFullName();
		$value['url'] = $record->getHtmlUrl();
		if ($record->getType()=="INDI") {
			$value['sex'] = $record->getSexImage();
		} else {
			$value['sex'] = '';
		}
		$filtered_events[] = $value;
	}

	// Now we've filtered the list, we can sort by event, if required
	switch ($sort_by) {
	case 'anniv':
		uasort($filtered_events, 'event_sort');
		break;
	case 'alpha':
		uasort($filtered_events, 'event_sort_name');
		break;
	}

	foreach ($filtered_events as $n=>$value) {
		$return .= "<tr>";
		//-- Record name(s)
		$name = $value['name'];
		$return .= '<td class="list_value_wrap" align="'.get_align($name).'">';
		$return .= '<a href="'.$value['url'].'" class="list_item name2" dir="'.$TEXT_DIRECTION.'">'.$name.'</a>';
		if ($value['record']->getType()=="INDI") {
			$return .= $value['sex'];
			$return .= $value['record']->getPrimaryParentsNames("parents_$table_id details1", "none");
		}
		$return .= '</td>';
		//-- NAME
		$return .= '<td style="display:none;">'; //hidden by datables code
		$return .= $value['record']->getSortName();
		$return .= '</td>';
		//-- Event date
		$return .= '<td class="list_value_wrap">';
		$return .= $value['date']->Display(empty($SEARCH_SPIDER));
		$return .= '</td>';
		//-- Event date (sortable)
		$return .= '<td style="display:none;">'; //hidden by datables code
		$return .= $n;
		$return .= '</td>';
		//-- Anniversary
		$return .= '<td class="list_value_wrap">';
		$anniv = $value['anniv'];
		if ($anniv==0) $return .= '&nbsp;';
		else $return .= $anniv;
		$return .= '</td>';
		//-- Event name
		$return .= '<td class="list_value_wrap">';
		$return .= '<a href="'.$value['url'].'" class="list_item">'.WT_Gedcom_Tag::getLabel($value['fact']).'</a>';
		$return .= '&nbsp;</td>';

		$return .= '</tr>'."\n";
	}

	if ($output!=0) {
		//-- table footer
		$return .= '</tbody><tfoot><tr class="sortbottom">';
		$return .= '<td class="list_label">';
		$return .= "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"jQuery('div.parents_$table_id').toggle();\" /><label for=\"cb_parents_$table_id\">&nbsp;&nbsp;".WT_I18N::translate('Show parents').'</label><br />';
		$return .= '</td><td class="list_label">';
		$return .= /* I18N: A count of events */ WT_I18N::translate('Total events: %s', WT_I18N::number($output));
		$return .= '</td>';
		$return .= '<td class="list_label">&nbsp;</td><td class="list_label">&nbsp;</td><td class="list_label">&nbsp;</td><td class="list_label">&nbsp;</td>';//DataTables cannot work with colspan
		$return .= '</tr></tfoot>';
		$return .= '</table>';
	}

	// Print a final summary message about restricted/filtered facts
	$summary = "";
	if ($endjd==WT_CLIENT_JD) {
		// We're dealing with the Today's Events block
		if ($output==0) {
			if ($filter==0) {
				$summary = WT_I18N::translate('No events exist for today.');
			} else {
				$summary = WT_I18N::translate('No events for living people exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($output==0) {
			if ($filter==0) {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unsed; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events exist for the next %d day.', 'No events exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			} else {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events for living people exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unsed; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events for living people exist for the next %d day.', 'No events for living people exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			}
		}
	}
	if ($summary!="") {
		$return .= '<strong>'. $summary. '</strong>';
	}

	return $return;
}

/**
 * print a list of events
 *
 * This performs the same function as print_events_table(), but formats the output differently.
 */
function print_events_list($startjd, $endjd, $events='BIRT MARR DEAT', $only_living=false, $sort_by='anniv') {
	global $TEXT_DIRECTION;

	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;

	$return = '';

	$filtered_events = array();

	foreach (get_events_list($startjd, $endjd, $events) as $value) {
		$record = WT_GedcomRecord::getInstance($value['id']);
		//-- only living people ?
		if ($only_living) {
			if ($record->getType()=="INDI" && $record->isDead()) {
				$filter ++;
				continue;
			}
			if ($record->getType()=="FAM") {
				$husb = $record->getHusband();
				if (is_null($husb) || $husb->isDead()) {
					$filter ++;
					continue;
				}
				$wife = $record->getWife();
				if (is_null($wife) || $wife->isDead()) {
					$filter ++;
					continue;
				}
			}
		}

		// Privacy
		if (!$record->canDisplayDetails() || !canDisplayFact($record->getXref(), $record->getGedId(), $value['factrec'])) {
			continue;
		}
		$output ++;

		$value['name'] = $record->getFullName();
		$value['url'] = $record->getHtmlUrl();
		if ($record->getType()=="INDI") {
			$value['sex'] = $record->getSexImage();
		} else {
			$value['sex'] = '';
		}
		$filtered_events[] = $value;
	}

	// Now we've filtered the list, we can sort by event, if required
	switch ($sort_by) {
	case 'anniv':
		uasort($filtered_events, 'event_sort');
		break;
	case 'alpha':
		uasort($filtered_events, 'event_sort_name');
		break;
	}

	foreach ($filtered_events as $value) {
		$return .= "<a href=\"".$value['url']."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".$value['name']."</a>".$value['sex'];
		$return .= "<br /><div class=\"indent\">";
		$return .= WT_Gedcom_Tag::getLabel($value['fact']).' - '.$value['date']->Display(true);
		if ($value['anniv']!=0) $return .= " (" . WT_I18N::translate('%s year anniversary', $value['anniv']).")";
		if (!empty($value['plac'])) $return .= " - <a href=\"".get_place_url($value['plac'])."\">".$value['plac']."</a>";
		$return .= "</div>";
	}

	// Print a final summary message about restricted/filtered facts
	$summary = "";
	if ($endjd==WT_CLIENT_JD) {
		// We're dealing with the Today's Events block
		if ($output==0) {
			if ($filter==0) {
				$summary = WT_I18N::translate('No events exist for today.');
			} else {
				$summary = WT_I18N::translate('No events for living people exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($output==0) {
			if ($filter==0) {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unused; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events exist for the next %d day.', 'No events exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			} else {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events for living people exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unused; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events for living people exist for the next %d day.', 'No events for living people exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			}
		}
	}
	if ($summary) {
		$return .= "<b>". $summary. "</b>";
	}

	return $return;
}

// print a chart by age using Google chart API
function print_chart_by_age($data, $title) {
	$count = 0;
	$agemax = 0;
	$vmax = 0;
	$avg = 0;
	foreach ($data as $age=>$v) {
		$n = strlen($v);
		$vmax = max($vmax, $n);
		$agemax = max($agemax, $age);
		$count += $n;
		$avg += $age*$n;
	}
	if ($count<1) return;
	$avg = round($avg/$count);
	$chart_url = "http://chart.apis.google.com/chart?cht=bvs"; // chart type
	$chart_url .= "&amp;chs=725x150"; // size
	$chart_url .= "&amp;chbh=3,2,2"; // bvg : 4,1,2
	$chart_url .= "&amp;chf=bg,s,FFFFFF99"; //background color
	$chart_url .= "&amp;chco=0000FF,FFA0CB,FF0000"; // bar color
	$chart_url .= "&amp;chdl=".rawurlencode(WT_I18N::translate('Males'))."|".rawurlencode(WT_I18N::translate('Females'))."|".rawurlencode(WT_I18N::translate('Average age').": ".$avg); // legend & average age
	$chart_url .= "&amp;chtt=".rawurlencode($title); // title
	$chart_url .= "&amp;chxt=x,y,r"; // axis labels specification
	$chart_url .= "&amp;chm=V,FF0000,0,".($avg-0.3).",1"; // average age line marker
	$chart_url .= "&amp;chxl=0:|"; // label
	for ($age=0; $age<=$agemax; $age+=5) {
		$chart_url .= $age."|||||"; // x axis
	}
	$chart_url .= "|1:||".rawurlencode(WT_I18N::percentage($vmax/$count)); // y axis
	$chart_url .= "|2:||";
	$step = $vmax;
	for ($d=floor($vmax); $d>0; $d--) {
		if ($vmax<($d*10+1) && fmod($vmax, $d)==0) $step = $d;
	}
	if ($step==floor($vmax)) {
		for ($d=floor($vmax-1); $d>0; $d--) {
			if (($vmax-1)<($d*10+1) && fmod(($vmax-1), $d)==0) $step = $d;
		}
	}
	for ($n=$step; $n<$vmax; $n+=$step) {
		$chart_url .= $n."|";
	}
	$chart_url .= rawurlencode($vmax." / ".$count); // r axis
	$chart_url .= "&amp;chg=100,".round(100*$step/$vmax, 1).",1,5"; // grid
	$chart_url .= "&amp;chd=s:"; // data : simple encoding from A=0 to 9=61
	$CHART_ENCODING61 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for ($age=0; $age<=$agemax; $age++) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$age], "M")*61/$vmax)];
	}
	$chart_url .= ",";
	for ($age=0; $age<=$agemax; $age++) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$age], "F")*61/$vmax)];
	}
	echo "<img src=\"", $chart_url, "\" alt=\"", $title, "\" title=\"", $title, "\" class=\"gchart\" />";
}

// print a chart by decade using Google chart API
function print_chart_by_decade($data, $title) {
	$count = 0;
	$vmax = 0;
	foreach ($data as $age=>$v) {
		$n = strlen($v);
		$vmax = max($vmax, $n);
		$count += $n;
	}
	if ($count<1) return;
	$chart_url = "http://chart.apis.google.com/chart?cht=bvs"; // chart type
	$chart_url .= "&amp;chs=360x150"; // size
	$chart_url .= "&amp;chbh=3,3"; // bvg : 4,1,2
	$chart_url .= "&amp;chf=bg,s,FFFFFF99"; //background color
	$chart_url .= "&amp;chco=0000FF,FFA0CB"; // bar color
	$chart_url .= "&amp;chtt=".rawurlencode($title); // title
	$chart_url .= "&amp;chxt=x,y,r"; // axis labels specification
	$chart_url .= "&amp;chxl=0:|&lt;|||"; // <1570
	for ($y=1600; $y<2030; $y+=50) {
		$chart_url .= $y."|||||"; // x axis
	}
	$chart_url .= "|1:||".rawurlencode(WT_I18N::percentage($vmax/$count)); // y axis
	$chart_url .= "|2:||";
	$step = $vmax;
	for ($d=floor($vmax); $d>0; $d--) {
		if ($vmax<($d*10+1) && fmod($vmax, $d)==0) $step = $d;
	}
	if ($step==floor($vmax)) {
		for ($d=floor($vmax-1); $d>0; $d--) {
			if (($vmax-1)<($d*10+1) && fmod(($vmax-1), $d)==0) $step = $d;
		}
	}
	for ($n=$step; $n<$vmax; $n+=$step) {
		$chart_url .= $n."|";
	}
	$chart_url .= rawurlencode($vmax." / ".$count); // r axis
	$chart_url .= "&amp;chg=100,".round(100*$step/$vmax, 1).",1,5"; // grid
	$chart_url .= "&amp;chd=s:"; // data : simple encoding from A=0 to 9=61
	$CHART_ENCODING61 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for ($y=1570; $y<2030; $y+=10) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$y], "M")*61/$vmax)];
	}
	$chart_url .= ",";
	for ($y=1570; $y<2030; $y+=10) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$y], "F")*61/$vmax)];
	}
	echo "<img src=\"", $chart_url, "\" alt=\"", $title, "\" title=\"", $title, "\" class=\"gchart\" />";
}

/**
 * check string align direction depending on language and rtl config
 *
 * @param string $txt string argument
 * @return string left|right
 */
function get_align($txt) {
		global $TEXT_DIRECTION;

		if (!empty($txt)) {
			if ($TEXT_DIRECTION=="rtl" && !hasRTLText($txt) && hasLTRText($txt)) return "left";
			if ($TEXT_DIRECTION=="ltr" && hasRTLText($txt) && !hasLTRText($txt)) return "right";
		}
		if ($TEXT_DIRECTION=="rtl") return "right";
		return "left";
}
