<?php
// Functions for printing lists
//
// Various printing functions for printing lists
// used on the indilist, famlist, find, and search pages.
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

use Rhumsaa\Uuid\Uuid;

/**
 * Print a table of individuals
 *
 * @param WT_Individual[] $datalist
 * @param string          $option
 *
 * @return string
 */
function format_indi_table($datalist, $option = '') {
	global $GEDCOM, $SHOW_LAST_CHANGE, $SEARCH_SPIDER, $MAX_ALIVE_AGE, $controller, $WT_TREE;

	$table_id = 'table-indi-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
	$SHOW_EST_LIST_DATES = $WT_TREE->getPreference('SHOW_EST_LIST_DATES');

	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc"  ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc" ]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["num-html-asc" ]=function(a,b) {a=parseFloat(a.replace(/<[^<]*>/, "")); b=parseFloat(b.replace(/<[^<]*>/, "")); return (a<b) ? -1 : (a>b ? 1 : 0);};
			jQuery.fn.dataTableExt.oSort["num-html-desc"]=function(a,b) {a=parseFloat(a.replace(/<[^<]*>/, "")); b=parseFloat(b.replace(/<[^<]*>/, "")); return (a>b) ? -1 : (a<b ? 1 : 0);};
			jQuery("#' . $table_id . '").dataTable( {
				dom: \'<"H"<"filtersH_' . $table_id . '">T<"dt-clear">pf<"dt-clear">irl>t<"F"pl<"dt-clear"><"filtersF_' . $table_id . '">>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth: false,
				processing: true,
				retrieve: true,
				columns: [
					/*  0 givn      */ { dataSort: 2 },
					/*  1 surn      */ { dataSort: 3 },
					/*  2 GIVN,SURN */ { type: "unicode", visible: false },
					/*  3 SURN,GIVN */ { type: "unicode", visible: false },
					/*  4 sosa      */ { dataSort: 5, class: "center", visible: ' . ($option == 'sosa' ? 'true' : 'false') . ' },
					/*  5 SOSA      */ { type: "num", visible: false },
					/*  6 birt date */ { dataSort: 7 },
					/*  7 BIRT:DATE */ { visible: false },
					/*  8 anniv     */ { dataSort: 7, class: "center" },
					/*  9 birt plac */ { type: "unicode" },
					/* 10 children  */ { dataSort: 11, class: "center" },
					/* 11 children  */ { type: "num", visible: false },
					/* 12 deat date */ { dataSort: 13 },
					/* 13 DEAT:DATE */ { visible: false },
					/* 14 anniv     */ { dataSort: 13, class: "center" },
					/* 15 age       */ { dataSort: 16, class: "center" },
					/* 16 AGE       */ { type: "num", visible: false },
					/* 17 deat plac */ { type: "unicode" },
					/* 18 CHAN      */ { dataSort: 19, visible: ' . ($SHOW_LAST_CHANGE ? 'true' : 'false') . ' },
					/* 19 CHAN_sort */ { visible: false },
					/* 20 SEX       */ { visible: false },
					/* 21 BIRT      */ { visible: false },
					/* 22 DEAT      */ { visible: false },
					/* 23 TREE      */ { visible: false }
				],
				sorting: [[' . ($option == 'sosa' ? '4, "asc"' : '1, "asc"') . ']],
				displayLength: 20,
				pagingType: "full_numbers"
			});

			jQuery("#' . $table_id . '")
			/* Hide/show parents */
			.on("click", ".btn-toggle-parents", function() {
				jQuery(this).toggleClass("ui-state-active");
				jQuery(".parents", jQuery(this).closest("table").DataTable().rows().nodes()).slideToggle();
			})
			/* Hide/show statistics */
			.on("click", ".btn-toggle-statistics", function() {
				jQuery(this).toggleClass("ui-state-active");
				jQuery("#indi_list_table-charts_' . $table_id . '").slideToggle();
			})
			/* Filter buttons in table header */
			.on("click", "button[data-filter-column]", function() {
				var btn = jQuery(this);
				// De-activate the other buttons in this button group
				btn.siblings().removeClass("ui-state-active");
				// Apply (or clear) this filter
				var col = jQuery("#' . $table_id . '").DataTable().column(btn.data("filter-column"));
				if (btn.hasClass("ui-state-active")) {
					btn.removeClass("ui-state-active");
					col.search("").draw();
				} else {
					btn.addClass("ui-state-active");
					col.search(btn.data("filter-value")).draw();
				}
			});

			jQuery(".indi-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

	$stats = new WT_Stats($GEDCOM);

	// Bad data can cause "longest life" to be huge, blowing memory limits
	$max_age = min($MAX_ALIVE_AGE, $stats->LongestLifeAge()) + 1;

	// Inititialise chart data
	$deat_by_age = array();
	for ($age = 0; $age <= $max_age; $age++) {
		$deat_by_age[$age] = '';
	}
	$birt_by_decade = array();
	$deat_by_decade = array();
	for ($year = 1550; $year < 2030; $year += 10) {
		$birt_by_decade[$year] = '';
		$deat_by_decade[$year] = '';
	}

	$html = '
		<div class="loading-image">&nbsp;</div>
		<div class="indi-list">
			<table id="' . $table_id . '">
				<thead>
					<tr>
						<th colspan="24">
							<div class="btn-toolbar">
								<div class="btn-group">
									<button
										class="ui-state-default"
										data-filter-column="20"
										data-filter-value="M"
										title="' . WT_I18N::translate('Show only males.') . '"
										type="button"
									>
									 	' . WT_Individual::sexImage('M', 'large') . '
									</button>
									<button
										class="ui-state-default"
										data-filter-column="20"
										data-filter-value="F"
										title="' . WT_I18N::translate('Show only females.') . '"
										type="button"
									>
										' . WT_Individual::sexImage('F', 'large') . '
									</button>
									<button
										class="ui-state-default"
										data-filter-column="20"
										data-filter-value="U"
										title="' . WT_I18N::translate('Show only individuals for whom the gender is not known.') . '"
										type="button"
									>
										' . WT_Individual::sexImage('U', 'large') . '
									</button>
								</div>
								<div class="btn-group">
									<button
										class="ui-state-default"
										data-filter-column="22"
										data-filter-value="N"
										title="' . WT_I18N::translate('Show individuals who are alive or couples where both partners are alive.') . '"
										type="button"
									>
										' . WT_I18N::translate('Alive') . '
									</button>
									<button
										class="ui-state-default"
										data-filter-column="22"
										data-filter-value="Y"
										title="' . WT_I18N::translate('Show individuals who are dead or couples where both partners are deceased.') . '"
										type="button"
									>
										' . WT_I18N::translate('Dead') . '
									</button>
									<button
										class="ui-state-default"
										data-filter-column="22"
										data-filter-value="YES"
										title="' . WT_I18N::translate('Show individuals who died more than 100 years ago.') . '"
										type="button"
									>
										' . WT_Gedcom_Tag::getLabel('DEAT') . '&gt;100
									</button>
									<button
										class="ui-state-default"
										data-filter-column="22"
										data-filter-value="Y100"
										title="' . WT_I18N::translate('Show individuals who died within the last 100 years.') . '"
										type="button"
									>
										' . WT_Gedcom_Tag::getLabel('DEAT') . '&lt;=100
									</button>
								</div>
								<div class="btn-group">
									<button
										class="ui-state-default"
										data-filter-column="21"
										data-filter-value="YES"
										title="' . WT_I18N::translate('Show individuals born more than 100 years ago.') . '"
										type="button"
									>
										' . WT_Gedcom_Tag::getLabel('BIRT') . '&gt;100
									</button>
									<button
										class="ui-state-default"
										data-filter-column="21"
										data-filter-value="Y100"
										title="' . WT_I18N::translate('Show individuals born within the last 100 years.') . '"
										type="button"
									>
										' . WT_Gedcom_Tag::getLabel('BIRT') . '&lt;=100
									</button>
								</div>
								<div class="btn-group">
									<button
										class="ui-state-default"
										data-filter-column="23"
										data-filter-value="R"
										title="' . WT_I18N::translate('Show “roots” couples or individuals.  These individuals may also be called “patriarchs”.  They are individuals who have no parents recorded in the database.') . '"
										type="button"
									>
										' . WT_I18N::translate('Roots') . '
									</button>
									<button
										class="ui-state-default"
										data-filter-column="23"
										data-filter-value="L"
										title="' . WT_I18N::translate('Show “leaves” couples or individuals.  These are individuals who are alive but have no children recorded in the database.') . '"
										type="button"
									>
										' . WT_I18N::translate('Leaves') . '
									</button>
								</div>
							</div>
						</th>
					</tr>
					<tr>
						<th>' . WT_Gedcom_Tag::getLabel('GIVN') . '</th>
						<th>' . WT_Gedcom_Tag::getLabel('SURN') . '</th>
						<th>GIVN</th>
						<th>SURN</th>
						<th>' . /* I18N: Abbreviation for “Sosa-Stradonitz number”.  This is an individual’s surname, so may need transliterating into non-latin alphabets. */ WT_I18N::translate('Sosa') . '</th>
						<th>SOSA</th>
						<th>' . WT_Gedcom_Tag::getLabel('BIRT') . '</th>
						<th>SORT_BIRT</th>
						<th><i class="icon-reminder" title="' . WT_I18N::translate('Anniversary') . '"></i></th>
						<th>' . WT_Gedcom_Tag::getLabel('PLAC') . '</th>
						<th><i class="icon-children" title="' . WT_I18N::translate('Children') . '"></i></th>
						<th>NCHI</th>
						<th>' . WT_Gedcom_Tag::getLabel('DEAT') . '</th>
						<th>SORT_DEAT</th>
						<th><i class="icon-reminder" title="' . WT_I18N::translate('Anniversary') . '"></i></th>
						<th>' . WT_Gedcom_Tag::getLabel('AGE') . '</th>
						<th>AGE</th>
						<th>' . WT_Gedcom_Tag::getLabel('PLAC') . '</th>
						<th>' . WT_Gedcom_Tag::getLabel('CHAN') . '</th>
						<th>CHAN</th>
						<th>SEX</th>
						<th>BIRT</th>
						<th>DEAT</th>
						<th>TREE</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th colspan="24">
							<div class="btn-toolbar">
								<div class="btn-group">
									<button type="button" class="ui-state-default btn-toggle-parents">
										' . WT_I18N::translate('Show parents') . '
									</button>
									<button type="button" class="ui-state-default btn-toggle-statistics">
										' . WT_I18N::translate('Show statistics charts') . '
									</button>
								</div>
							</div>
						</th>
					</tr>
				</tfoot>
				<tbody>';

	$d100y = new WT_Date(date('Y') - 100);  // 100 years ago
	$unique_indis = array(); // Don't double-count indis with multiple names.
	foreach ($datalist as $key => $person) {
		if (!$person->canShowName()) {
			continue;
		}
		if ($person->isPendingAddtion()) {
			$class = ' class="new"';
		} elseif ($person->isPendingDeletion()) {
			$class = ' class="old"';
		} else {
			$class = '';
		}
		$html .= '<tr' . $class . '>';
		//-- Indi name(s)
		$html .= '<td colspan="2">';
		foreach ($person->getAllNames() as $num => $name) {
			if ($name['type'] == 'NAME') {
				$title = '';
			} else {
				$title = 'title="' . strip_tags(WT_Gedcom_Tag::getLabel($name['type'], $person)) . '"';
			}
			if ($num == $person->getPrimaryName()) {
				$class = ' class="name2"';
				$sex_image = $person->getSexImage();
				list($surn, $givn) = explode(',', $name['sort']);
			} else {
				$class = '';
				$sex_image = '';
			}
			$html .= '<a ' . $title . ' href="' . $person->getHtmlUrl() . '"' . $class . '>' . highlight_search_hits($name['full']) . '</a>' . $sex_image . '<br>';
		}
		// Indi parents
		$html .= $person->getPrimaryParentsNames('parents details1', 'none');
		$html .= '</td>';
		// Dummy column to match colspan in header
		$html .= '<td style="display:none;"></td>';
		//-- GIVN/SURN
		// Use "AAAA" as a separator (instead of ",") as Javascript.localeCompare() ignores
		// punctuation and "ANN,ROACH" would sort after "ANNE,ROACH", instead of before it.
		// Similarly, @N.N. would sort as NN.
		$html .= '<td>' . WT_Filter::escapeHtml(str_replace('@P.N.', 'AAAA', $givn)) . 'AAAA' . WT_Filter::escapeHtml(str_replace('@N.N.', 'AAAA', $surn)) . '</td>';
		$html .= '<td>' . WT_Filter::escapeHtml(str_replace('@N.N.', 'AAAA', $surn)) . 'AAAA' . WT_Filter::escapeHtml(str_replace('@P.N.', 'AAAA', $givn)) . '</td>';
		//-- SOSA
		if ($option == 'sosa') {
			$html .= '<td><a href="relationship.php?pid1=' . $datalist[1] . '&amp;pid2=' . $person->getXref() . '" title="' . WT_I18N::translate('Relationships') . '">' . WT_I18N::number($key) . '</a></td><td>' . $key . '</td>';
		} else {
			$html .= '<td>&nbsp;</td><td>0</td>';
		}
		//-- Birth date
		$html .= '<td>';
		if ($birth_dates = $person->getAllBirthDates()) {
			foreach ($birth_dates as $num => $birth_date) {
				if ($num) {
					$html .= '<br>';
				}
				$html .= $birth_date->Display(!$SEARCH_SPIDER);
			}
			if ($birth_dates[0]->gregorianYear() >= 1550 && $birth_dates[0]->gregorianYear() < 2030 && !isset($unique_indis[$person->getXref()])) {
				$birt_by_decade[(int)($birth_dates[0]->gregorianYear() / 10) * 10] .= $person->getSex();
			}
		} else {
			$birth_date = $person->getEstimatedBirthDate();
			if ($SHOW_EST_LIST_DATES) {
				$html .= $birth_date->display(!$SEARCH_SPIDER);
			} else {
				$html .= '&nbsp;';
			}
			$birth_dates[0] = new WT_Date('');
		}
		$html .= '</td>';
		//-- Event date (sortable)hidden by datatables code
		$html .= '<td>' . $birth_date->JD() . '</td>';
		//-- Birth anniversary
		$html .= '<td>' . WT_Date::getAge($birth_dates[0], null, 2) . '</td>';
		//-- Birth place
		$html .= '<td>';
		foreach ($person->getAllBirthPlaces() as $n => $birth_place) {
			$tmp = new WT_Place($birth_place, WT_GED_ID);
			if ($n) {
				$html .= '<br>';
			}
			if ($SEARCH_SPIDER) {
				$html .= $tmp->getShortName();
			} else {
				$html .= '<a href="' . $tmp->getURL() . '" title="' . strip_tags($tmp->getFullName()) . '">';
				$html .= highlight_search_hits($tmp->getShortName()) . '</a>';
			}
		}
		$html .= '</td>';
		//-- Number of children
		$nchi = $person->getNumberOfChildren();
		$html .= '<td>' . WT_I18N::number($nchi) . '</td><td>' . $nchi . '</td>';
		//-- Death date
		$html .= '<td>';
		if ($death_dates = $person->getAllDeathDates()) {
			foreach ($death_dates as $num => $death_date) {
				if ($num) {
					$html .= '<br>';
				}
				$html .= $death_date->Display(!$SEARCH_SPIDER);
			}
			if ($death_dates[0]->gregorianYear() >= 1550 && $death_dates[0]->gregorianYear() < 2030 && !isset($unique_indis[$person->getXref()])) {
				$deat_by_decade[(int)($death_dates[0]->gregorianYear() / 10) * 10] .= $person->getSex();
			}
		} else {
			$death_date = $person->getEstimatedDeathDate();
			// Estimated death dates are a fixed number of years after the birth date.
			// Don't show estimates in the future.
			if ($SHOW_EST_LIST_DATES && $death_date->MinJD() < WT_CLIENT_JD) {
				$html .= $death_date->display(!$SEARCH_SPIDER);
			} elseif ($person->isDead()) {
				$html .= WT_I18N::translate('yes');
			} else {
				$html .= '&nbsp;';
			}
			$death_dates[0] = new WT_Date('');
		}
		$html .= '</td>';
		//-- Event date (sortable)hidden by datatables code
		$html .= '<td>' . $death_date->JD() . '</td>';
		//-- Death anniversary
		$html .= '<td>' . WT_Date::getAge($death_dates[0], null, 2) . '</td>';
		//-- Age at death
		$age = WT_Date::getAge($birth_dates[0], $death_dates[0], 0);
		if (!isset($unique_indis[$person->getXref()]) && $age >= 0 && $age <= $max_age) {
			$deat_by_age[$age] .= $person->getSex();
		}
		// Need both display and sortable age
		$html .= '<td>' . WT_Date::getAge($birth_dates[0], $death_dates[0], 2) . '</td><td>' . WT_Date::getAge($birth_dates[0], $death_dates[0], 1) . '</td>';
		//-- Death place
		$html .= '<td>';
		foreach ($person->getAllDeathPlaces() as $n => $death_place) {
			$tmp = new WT_Place($death_place, WT_GED_ID);
			if ($n) {
				$html .= '<br>';
			}
			if ($SEARCH_SPIDER) {
				$html .= $tmp->getShortName();
			} else {
				$html .= '<a href="' . $tmp->getURL() . '" title="' . strip_tags($tmp->getFullName()) . '">';
				$html .= highlight_search_hits($tmp->getShortName()) . '</a>';
			}
		}
		$html .= '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $person->lastChangeTimestamp() . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Last change hidden sort column
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $person->lastChangeTimestamp(true) . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Sorting by gender
		$html .= '<td>';
		$html .= $person->getSex();
		$html .= '</td>';
		//-- Filtering by birth date
		$html .= '<td>';
		if (!$person->canShow() || WT_Date::Compare($birth_date, $d100y) > 0) {
			$html .= 'Y100';
		} else {
			$html .= 'YES';
		}
		$html .= '</td>';
		//-- Filtering by death date
		$html .= '<td>';
		// Died in last 100 years?  Died?  Not dead?
		if (WT_Date::Compare($death_date, $d100y) > 0) {
			$html .= 'Y100';
		} elseif ($death_date->minJD() || $person->isDead()) {
			$html .= 'YES';
		} else {
			$html .= 'N';
		}
		$html .= '</td>';
		//-- Roots or Leaves ?
		$html .= '<td>';
		if (!$person->getChildFamilies()) {
			$html .= 'R';
		}  // roots
		elseif (!$person->isDead() && $person->getNumberOfChildren() < 1) {
			$html .= 'L';
		} // leaves
		else {
			$html .= '&nbsp;';
		}
		$html .= '</td>';
		$html .= '</tr>';
		$unique_indis[$person->getXref()] = true;
	}
	$html .= '
				</tbody>
			</table>
			<div id="indi_list_table-charts_' . $table_id . '" style="display:none">
				<table class="list-charts">
					<tr>
						<td>
							' . print_chart_by_decade($birt_by_decade, WT_I18N::translate('Decade of birth')) . '
						</td>
						<td>
							' . print_chart_by_decade($deat_by_decade, WT_I18N::translate('Decade of death')) . '
						</td>
					</tr>
					<tr>
						<td colspan="2">
							' . print_chart_by_age($deat_by_age, WT_I18N::translate('Age related to death year')) . '
						</td>
					</tr>
				</table>
			</div>
		</div>';

	return $html;
}

/**
 * Print a table of families
 *
 * @param WT_Family[] $datalist
 *
 * @return string
 */
function format_fam_table($datalist) {
	global $GEDCOM, $SHOW_LAST_CHANGE, $SEARCH_SPIDER, $controller;

	$table_id = 'table-fam-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page

	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#' . $table_id . '").dataTable( {
				dom: \'<"H"<"filtersH_' . $table_id . '"><"dt-clear">pf<"dt-clear">irl>t<"F"pl<"dt-clear"><"filtersF_' . $table_id . '">>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth: false,
				processing: true,
				retrieve: true,
				columns: [
					/*  0 husb givn */ {dataSort: 2},
					/*  1 husb surn */ {dataSort: 3},
					/*  2 GIVN,SURN */ {type: "unicode", visible: false},
					/*  3 SURN,GIVN */ {type: "unicode", visible: false},
					/*  4 age       */ {dataSort: 5, class: "center"},
					/*  5 AGE       */ {type: "num", visible: false},
					/*  6 wife givn */ {dataSort: 8},
					/*  7 wife surn */ {dataSort: 9},
					/*  8 GIVN,SURN */ {type: "unicode", visible: false},
					/*  9 SURN,GIVN */ {type: "unicode", visible: false},
					/* 10 age       */ {dataSort: 11, class: "center"},
					/* 11 AGE       */ {type: "num", visible: false},
					/* 12 marr date */ {dataSort: 13},
					/* 13 MARR:DATE */ {visible: false},
					/* 14 anniv     */ {dataSort: 13, class: "center"},
					/* 15 marr plac */ {type: "unicode"},
					/* 16 children  */ {dataSort: 17, class: "center"},
					/* 17 NCHI      */ {type: "num", visible: false},
					/* 18 CHAN      */ {dataSort: 19, visible: ' . ($SHOW_LAST_CHANGE ? 'true' : 'false') . '},
					/* 19 CHAN_sort */ {visible: false},
					/* 20 MARR      */ {visible: false},
					/* 21 DEAT      */ {visible: false},
					/* 22 TREE      */ {visible: false}
				],
				sorting: [[1, "asc"]],
				displayLength: 20,
				pagingType: "full_numbers"
		   });

			jQuery("#' . $table_id . '")
			/* Hide/show parents */
			.on("click", ".btn-toggle-parents", function() {
				jQuery(this).toggleClass("ui-state-active");
				jQuery(".parents", jQuery(this).closest("table").DataTable().rows().nodes()).slideToggle();
			})
			/* Hide/show statistics */
			.on("click",  ".btn-toggle-statistics", function() {
				jQuery(this).toggleClass("ui-state-active");
				jQuery("#fam_list_table-charts_' . $table_id . '").slideToggle();
			})
			/* Filter buttons in table header */
			.on("click", "button[data-filter-column]", function() {
				var btn = $(this);
				// De-activate the other buttons in this button group
				btn.siblings().removeClass("ui-state-active");
				// Apply (or clear) this filter
				var col = jQuery("#' . $table_id . '").DataTable().column(btn.data("filter-column"));
				if (btn.hasClass("ui-state-active")) {
					btn.removeClass("ui-state-active");
					col.search("").draw();
				} else {
					btn.addClass("ui-state-active");
					col.search(btn.data("filter-value")).draw();
				}
			});

			jQuery(".fam-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
	');

	$stats = new WT_Stats($GEDCOM);
	$max_age = max($stats->oldestMarriageMaleAge(), $stats->oldestMarriageFemaleAge()) + 1;

	//-- init chart data
	$marr_by_age = array();
	for ($age = 0; $age <= $max_age; $age++) {
		$marr_by_age[$age] = '';
	}
	$birt_by_decade = array();
	$marr_by_decade = array();
	for ($year = 1550; $year < 2030; $year += 10) {
		$birt_by_decade[$year] = '';
		$marr_by_decade[$year] = '';
	}

	$html = '
		<div class="loading-image">&nbsp;</div>
		<div class="fam-list">
			<table id="' . $table_id . '">
				<thead>
					<tr>
						<th colspan="23">
							<div class="btn-toolbar">
								<div class="btn-group">
									<button
										type="button"
										data-filter-column="21"
										data-filter-value="N"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show individuals who are alive or couples where both partners are alive.') . '"
									>
										' . WT_I18N::translate('Both alive') . '
									</button>
									<button
										type="button"
										data-filter-column="21"
										data-filter-value="W"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show couples where only the female partner is deceased.') . '"
									>
										' . WT_I18N::translate('Widower') . '
									</button>
									<button
										type="button"
										data-filter-column="21"
										data-filter-value="H"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show couples where only the male partner is deceased.') . '"
									>
										' . WT_I18N::translate('Widow') . '
									</button>
									<button
										type="button"
										data-filter-column="21"
										data-filter-value="Y"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show individuals who are dead or couples where both partners are deceased.') . '"
									>
										' . WT_I18N::translate('Both dead') . '
									</button>
								</div>
								<div class="btn-group">
									<button
										type="button"
										data-filter-column="22"
										data-filter-value="R"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show “roots” couples or individuals.  These individuals may also be called “patriarchs”.  They are individuals who have no parents recorded in the database.') . '"
									>
										' . WT_I18N::translate('Roots') . '
									</button>
									<button
										type="button"
										data-filter-column="22"
										data-filter-value="L"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show “leaves” couples or individuals.  These are individuals who are alive but have no children recorded in the database.') . '"
									>
										' . WT_I18N::translate('Leaves') . '
									</button>
								</div>
								<div class="btn-group">
									<button
										type="button"
										data-filter-column="20"
										data-filter-value="U"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show couples with an unknown marriage date.') . '"
									>
										' . WT_Gedcom_Tag::getLabel('MARR') . '
									</button>
									<button
										type="button"
										data-filter-column="20"
										data-filter-value="YES"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show couples who married more than 100 years ago.') . '"
									>
										' . WT_Gedcom_Tag::getLabel('MARR') . '&gt;100
									</button>
									<button
										type="button"
										data-filter-column="20"
										data-filter-value="Y100"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show couples who married within the last 100 years.') . '"
									>
										' . WT_Gedcom_Tag::getLabel('MARR') . '&lt;=100
									</button>
									<button
										type="button"
										data-filter-column="20"
										data-filter-value="D"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show divorced couples.') . '"
									>
										' . WT_Gedcom_Tag::getLabel('DIV') . '
									</button>
									<button
										type="button"
										data-filter-column="20"
										data-filter-value="M"
										class="ui-state-default"
										title="' . WT_I18N::translate('Show couples where either partner married more than once.') . '"
									>
										' . WT_I18N::translate('Multiple marriages') . '
									</button>
								</div>
							</div>
						</th>
					</tr>
					<tr>
						<th>' . WT_Gedcom_Tag::getLabel('GIVN') . '</th>
						<th>' . WT_Gedcom_Tag::getLabel('SURN') . '</th>
						<th>HUSB:GIVN_SURN</th>
						<th>HUSB:SURN_GIVN</th>
						<th>' . WT_Gedcom_Tag::getLabel('AGE') . '</th>
						<th>AGE</th>
						<th>' . WT_Gedcom_Tag::getLabel('GIVN') . '</th>
						<th>' . WT_Gedcom_Tag::getLabel('SURN') . '</th>
						<th>WIFE:GIVN_SURN</th>
						<th>WIFE:SURN_GIVN</th>
						<th>' . WT_Gedcom_Tag::getLabel('AGE') . '</th>
						<th>AGE</th>
						<th>' . WT_Gedcom_Tag::getLabel('MARR') . '</th>
						<th>MARR:DATE</th>
						<th><i class="icon-reminder" title="' . WT_I18N::translate('Anniversary') . '"></i></th>
						<th>' . WT_Gedcom_Tag::getLabel('PLAC') . '</th>
						<th><i class="icon-children" title="' . WT_I18N::translate('Children') . '"></i></th>
					<th>NCHI</th>
					<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>' . WT_Gedcom_Tag::getLabel('CHAN') . '</th>
					<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>CHAN</th>
					<th>MARR</th>
					<th>DEAT</th>
					<th>TREE</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="23">
						<div class="btn-toolbar">
							<div class="btn-group">
								<button type="button" class="ui-state-default btn-toggle-parents">
									' . WT_I18N::translate('Show parents') . '
								</button>
								<button type="button" class="ui-state-default btn-toggle-statistics">
									' . WT_I18N::translate('Show statistics charts') . '
								</button>
							</div>
						</div>
					</th>
				</tr>
			</tfoot>
			<tbody>';

	$d100y = new WT_Date(date('Y') - 100);  // 100 years ago
	foreach ($datalist as $family) {
		//-- Retrieve husband and wife
		$husb = $family->getHusband();
		if (is_null($husb)) {
			$husb = new WT_Individual('H', '0 @H@ INDI', null, WT_GED_ID);
		}
		$wife = $family->getWife();
		if (is_null($wife)) {
			$wife = new WT_Individual('W', '0 @W@ INDI', null, WT_GED_ID);
		}
		if (!$family->canShow()) {
			continue;
		}
		if ($family->isPendingAddtion()) {
			$class = ' class="new"';
		} elseif ($family->isPendingDeletion()) {
			$class = ' class="old"';
		} else {
			$class = '';
		}
		$html .= '<tr' . $class . '>';
		//-- Husband name(s)
		$html .= '<td colspan="2">';
		foreach ($husb->getAllNames() as $num => $name) {
			if ($name['type'] == 'NAME') {
				$title = '';
			} else {
				$title = 'title="' . strip_tags(WT_Gedcom_Tag::getLabel($name['type'], $husb)) . '"';
			}
			if ($num == $husb->getPrimaryName()) {
				$class = ' class="name2"';
				$sex_image = $husb->getSexImage();
				list($surn, $givn) = explode(',', $name['sort']);
			} else {
				$class = '';
				$sex_image = '';
			}
			// Only show married names if they are the name we are filtering by.
			if ($name['type'] != '_MARNM' || $num == $husb->getPrimaryName()) {
				$html .= '<a ' . $title . ' href="' . $family->getHtmlUrl() . '"' . $class . '>' . highlight_search_hits($name['full']) . '</a>' . $sex_image . '<br>';
			}
		}
		// Husband parents
		$html .= $husb->getPrimaryParentsNames('parents details1', 'none');
		$html .= '</td>';
		// Dummy column to match colspan in header
		$html .= '<td style="display:none;"></td>';
		//-- Husb GIVN
		// Use "AAAA" as a separator (instead of ",") as Javascript.localeCompare() ignores
		// punctuation and "ANN,ROACH" would sort after "ANNE,ROACH", instead of before it.
		// Similarly, @N.N. would sort as NN.
		$html .= '<td>' . WT_Filter::escapeHtml(str_replace('@P.N.', 'AAAA', $givn)) . 'AAAA' . WT_Filter::escapeHtml(str_replace('@N.N.', 'AAAA', $surn)) . '</td>';
		$html .= '<td>' . WT_Filter::escapeHtml(str_replace('@N.N.', 'AAAA', $surn)) . 'AAAA' . WT_Filter::escapeHtml(str_replace('@P.N.', 'AAAA', $givn)) . '</td>';
		$mdate = $family->getMarriageDate();
		//-- Husband age
		$hdate = $husb->getBirthDate();
		if ($hdate->isOK() && $mdate->isOK()) {
			if ($hdate->gregorianYear() >= 1550 && $hdate->gregorianYear() < 2030) {
				$birt_by_decade[(int)($hdate->gregorianYear() / 10) * 10] .= $husb->getSex();
			}
			$hage = WT_Date::getAge($hdate, $mdate, 0);
			if ($hage >= 0 && $hage <= $max_age) {
				$marr_by_age[$hage] .= $husb->getSex();
			}
		}
		$html .= '<td>' . WT_Date::getAge($hdate, $mdate, 2) . '</td><td>' . WT_Date::getAge($hdate, $mdate, 1) . '</td>';
		//-- Wife name(s)
		$html .= '<td colspan="2">';
		foreach ($wife->getAllNames() as $num => $name) {
			if ($name['type'] == 'NAME') {
				$title = '';
			} else {
				$title = 'title="' . strip_tags(WT_Gedcom_Tag::getLabel($name['type'], $wife)) . '"';
			}
			if ($num == $wife->getPrimaryName()) {
				$class = ' class="name2"';
				$sex_image = $wife->getSexImage();
				list($surn, $givn) = explode(',', $name['sort']);
			} else {
				$class = '';
				$sex_image = '';
			}
			// Only show married names if they are the name we are filtering by.
			if ($name['type'] != '_MARNM' || $num == $wife->getPrimaryName()) {
				$html .= '<a ' . $title . ' href="' . $family->getHtmlUrl() . '"' . $class . '>' . highlight_search_hits($name['full']) . '</a>' . $sex_image . '<br>';
			}
		}
		// Wife parents
		$html .= $wife->getPrimaryParentsNames('parents details1', 'none');
		$html .= '</td>';
		// Dummy column to match colspan in header
		$html .= '<td style="display:none;"></td>';
		//-- Wife GIVN
		//-- Husb GIVN
		// Use "AAAA" as a separator (instead of ",") as Javascript.localeCompare() ignores
		// punctuation and "ANN,ROACH" would sort after "ANNE,ROACH", instead of before it.
		// Similarly, @N.N. would sort as NN.
		$html .= '<td>' . WT_Filter::escapeHtml(str_replace('@P.N.', 'AAAA', $givn)) . 'AAAA' . WT_Filter::escapeHtml(str_replace('@N.N.', 'AAAA', $surn)) . '</td>';
		$html .= '<td>' . WT_Filter::escapeHtml(str_replace('@N.N.', 'AAAA', $surn)) . 'AAAA' . WT_Filter::escapeHtml(str_replace('@P.N.', 'AAAA', $givn)) . '</td>';
		$mdate = $family->getMarriageDate();
		//-- Wife age
		$wdate = $wife->getBirthDate();
		if ($wdate->isOK() && $mdate->isOK()) {
			if ($wdate->gregorianYear() >= 1550 && $wdate->gregorianYear() < 2030) {
				$birt_by_decade[(int)($wdate->gregorianYear() / 10) * 10] .= $wife->getSex();
			}
			$wage = WT_Date::getAge($wdate, $mdate, 0);
			if ($wage >= 0 && $wage <= $max_age) {
				$marr_by_age[$wage] .= $wife->getSex();
			}
		}
		$html .= '<td>' . WT_Date::getAge($wdate, $mdate, 2) . '</td><td>' . WT_Date::getAge($wdate, $mdate, 1) . '</td>';
		//-- Marriage date
		$html .= '<td>';
		if ($marriage_dates = $family->getAllMarriageDates()) {
			foreach ($marriage_dates as $n => $marriage_date) {
				if ($n) {
					$html .= '<br>';
				}
				$html .= '<div>' . $marriage_date->Display(!$SEARCH_SPIDER) . '</div>';
			}
			if ($marriage_dates[0]->gregorianYear() >= 1550 && $marriage_dates[0]->gregorianYear() < 2030) {
				$marr_by_decade[(int)($marriage_dates[0]->gregorianYear() / 10) * 10] .= $husb->getSex() . $wife->getSex();
			}
		} elseif ($family->getFacts('_NMR')) {
			$html .= WT_I18N::translate('no');
		} elseif ($family->getFacts('MARR')) {
			$html .= WT_I18N::translate('yes');
		} else {
			$html .= '&nbsp;';
		}
		$html .= '</td>';
		//-- Event date (sortable)hidden by datatables code
		$html .= '<td>';
		if ($marriage_dates) {
			$html .= $marriage_date->JD();
		} else {
			$html .= 0;
		}
		$html .= '</td>';
		//-- Marriage anniversary
		$html .= '<td>' . WT_Date::getAge($mdate, null, 2) . '</td>';
		//-- Marriage place
		$html .= '<td>';
		foreach ($family->getAllMarriagePlaces() as $n => $marriage_place) {
			$tmp = new WT_Place($marriage_place, WT_GED_ID);
			if ($n) {
				$html .= '<br>';
			}
			if ($SEARCH_SPIDER) {
				$html .= $tmp->getShortName();
			} else {
				$html .= '<a href="' . $tmp->getURL() . '" title="' . strip_tags($tmp->getFullName()) . '">';
				$html .= highlight_search_hits($tmp->getShortName()) . '</a>';
			}
		}
		$html .= '</td>';
		//-- Number of children
		$nchi = $family->getNumberOfChildren();
		$html .= '<td>' . WT_I18N::number($nchi) . '</td><td>' . $nchi . '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $family->LastChangeTimestamp() . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Last change hidden sort column
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $family->LastChangeTimestamp(true) . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Sorting by marriage date
		$html .= '<td>';
		if (!$family->canShow() || !$mdate->isOK()) {
			$html .= 'U';
		} else {
			if (WT_Date::Compare($mdate, $d100y) > 0) {
				$html .= 'Y100';
			} else {
				$html .= 'YES';
			}
		}
		if ($family->getFacts(WT_EVENTS_DIV)) {
			$html .= 'D';
		}
		if (count($husb->getSpouseFamilies()) > 1 || count($wife->getSpouseFamilies()) > 1) {
			$html .= 'M';
		}
		$html .= '</td>';
		//-- Sorting alive/dead
		$html .= '<td>';
		if ($husb->isDead() && $wife->isDead()) {
			$html .= 'Y';
		}
		if ($husb->isDead() && !$wife->isDead()) {
			if ($wife->getSex() == 'F') {
				$html .= 'H';
			}
			if ($wife->getSex() == 'M') {
				$html .= 'W';
			} // male partners
		}
		if (!$husb->isDead() && $wife->isDead()) {
			if ($husb->getSex() == 'M') {
				$html .= 'W';
			}
			if ($husb->getSex() == 'F') {
				$html .= 'H';
			} // female partners
		}
		if (!$husb->isDead() && !$wife->isDead()) {
			$html .= 'N';
		}
		$html .= '</td>';
		//-- Roots or Leaves
		$html .= '<td>';
		if (!$husb->getChildFamilies() && !$wife->getChildFamilies()) {
			$html .= 'R';
		} elseif (!$husb->isDead() && !$wife->isDead() && $family->getNumberOfChildren() < 1) {
			$html .= 'L';
		}  else {
			$html .= '&nbsp;';
		}
		$html .= '</td>
		</tr>';
	}
	$html .= '
				</tbody>
			</table>
			<div id="fam_list_table-charts_' . $table_id . '" style="display:none">
				<table class="list-charts">
					<tr>
						<td>
							' . print_chart_by_decade($birt_by_decade, WT_I18N::translate('Decade of birth')) . '
						</td>
						<td>
							' . print_chart_by_decade($marr_by_decade, WT_I18N::translate('Decade of marriage')) . '
						</td>
					</tr>
					<tr>
						<td colspan="2">
							' . print_chart_by_age($marr_by_age, WT_I18N::translate('Age in year of marriage')) . '
						</td>
					</tr>
				</table>
			</div>
		</div>';

	return $html;
}

/**
 * Print a table of sources
 *
 * @param WT_Source[] $datalist
 *
 * @return string
 */
function format_sour_table($datalist) {
	global $SHOW_LAST_CHANGE, $controller;
	$html = '';
	$table_id = 'table-sour-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#' . $table_id . '").dataTable( {
				dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth: false,
				processing: true,
				columns: [
					/*  0 title     */ { dataSort: 1 },
					/*  1 TITL      */ { visible: false, type: "unicode" },
					/*  2 author    */ { type: "unicode" },
					/*  3 #indi     */ { dataSort: 4, class: "center" },
					/*  4 #INDI     */ { type: "num", visible: false },
					/*  5 #fam      */ { dataSort: 6, class: "center" },
					/*  6 #FAM      */ { type: "num", visible: false },
					/*  7 #obje     */ { dataSort: 8, class: "center" },
					/*  8 #OBJE     */ { type: "num", visible: false },
					/*  9 #note     */ { dataSort: 10, class: "center" },
					/* 10 #NOTE     */ { type: "num", visible: false },
					/* 11 CHAN      */ { dataSort: 12, visible: ' . ($SHOW_LAST_CHANGE ? 'true' : 'false') . ' },
					/* 12 CHAN_sort */ { visible: false },
					/* 13 DELETE    */ { visible: ' . (WT_USER_GEDCOM_ADMIN ? 'true' : 'false') . ', sortable: false }
				],
				displayLength: 20,
				pagingType: "full_numbers"
		   });
			jQuery(".source-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

	//--table wrapper
	$html .= '<div class="loading-image">&nbsp;</div>';
	$html .= '<div class="source-list">';
	//-- table header
	$html .= '<table id="' . $table_id . '"><thead><tr>';
	$html .= '<th>' . WT_Gedcom_Tag::getLabel('TITL') . '</th>';
	$html .= '<th>TITL</th>';
	$html .= '<th>' . WT_Gedcom_Tag::getLabel('AUTH') . '</th>';
	$html .= '<th>' . WT_I18N::translate('Individuals') . '</th>';
	$html .= '<th>#INDI</th>';
	$html .= '<th>' . WT_I18N::translate('Families') . '</th>';
	$html .= '<th>#FAM</th>';
	$html .= '<th>' . WT_I18N::translate('Media objects') . '</th>';
	$html .= '<th>#OBJE</th>';
	$html .= '<th>' . WT_I18N::translate('Shared notes') . '</th>';
	$html .= '<th>#NOTE</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>' . WT_Gedcom_Tag::getLabel('CHAN') . '</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>CHAN</th>';
	$html .= '<th>&nbsp;</th>';//delete
	$html .= '</tr></thead>';
	//-- table body
	$html .= '<tbody>';

	foreach ($datalist as $source) {
		if (!$source->canShow()) {
			continue;
		}
		if ($source->isPendingAddtion()) {
			$class = ' class="new"';
		} elseif ($source->isPendingDeletion()) {
			$class = ' class="old"';
		} else {
			$class = '';
		}
		$html .= '<tr' . $class . '>';
		//-- Source name(s)
		$html .= '<td>';
		foreach ($source->getAllNames() as $n => $name) {
			if ($n) {
				$html .= '<br>';
			}
			if ($n == $source->getPrimaryName()) {
				$html .= '<a class="name2" href="' . $source->getHtmlUrl() . '">' . highlight_search_hits($name['full']) . '</a>';
			} else {
				$html .= '<a href="' . $source->getHtmlUrl() . '">' . highlight_search_hits($name['full']) . '</a>';
			}
		}
		$html .= '</td>';
		// Sortable name
		$html .= '<td>' . strip_tags($source->getFullName()) . '</td>';
		//-- Author
		$auth = $source->getFirstFact('AUTH');
		if ($auth) {
			$author = $auth->getValue();
		} else {
			$author = '';
		}
		$html .= '<td>' . highlight_search_hits($author) . '</td>';
		//-- Linked INDIs
		$num = count($source->linkedIndividuals('SOUR'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Linked FAMs
		$num = count($source->linkedfamilies('SOUR'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Linked OBJEcts
		$num = count($source->linkedMedia('SOUR'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Linked NOTEs
		$num = count($source->linkedNotes('SOUR'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $source->LastChangeTimestamp() . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Last change hidden sort column
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $source->LastChangeTimestamp(true) . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Delete
		if (WT_USER_GEDCOM_ADMIN) {
			$html .= '<td><div title="' . WT_I18N::translate('Delete') . '" class="deleteicon" onclick="return delete_source(\'' . WT_I18N::translate('Are you sure you want to delete “%s”?', WT_Filter::escapeJs(WT_Filter::unescapeHtml($source->getFullName()))) . "', '" . $source->getXref() . '\');"><span class="link_text">' . WT_I18N::translate('Delete') . '</span></div></td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		$html .= '</tr>';
	}
	$html .= '</tbody></table></div>';

	return $html;
}

/**
 * Print a table of shared notes
 *
 * @param WT_Note[] $datalist
 *
 * @return string
 */
function format_note_table($datalist) {
	global $SHOW_LAST_CHANGE, $controller;
	$html = '';
	$table_id = 'table-note-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#' . $table_id . '").dataTable({
				dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth: false,
				processing: true,
				columns: [
					/*  0 title     */ { type: "unicode" },
					/*  1 #indi     */ { dataSort: 2, class: "center" },
					/*  2 #INDI     */ { type: "num", visible: false },
					/*  3 #fam      */ { dataSort: 4, class: "center" },
					/*  4 #FAM      */ { type: "num", visible: false },
					/*  5 #obje     */ { dataSort: 6, class: "center" },
					/*  6 #OBJE     */ { type: "num", visible: false },
					/*  7 #sour     */ { dataSort: 8, class: "center" },
					/*  8 #SOUR     */ { type: "num", visible: false },
					/*  9 CHAN      */ { dataSort: 10, visible: ' . ($SHOW_LAST_CHANGE ? 'true' : 'false') . ' },
					/* 10 CHAN_sort */ { visible: false },
					/* 11 DELETE    */ { visible: ' . (WT_USER_GEDCOM_ADMIN ? 'true' : 'false') . ', sortable: false }
				],
				displayLength: 20,
				pagingType: "full_numbers"
			});
			jQuery(".note-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

	//--table wrapper
	$html .= '<div class="loading-image">&nbsp;</div>';
	$html .= '<div class="note-list">';
	//-- table header
	$html .= '<table id="' . $table_id . '"><thead><tr>';
	$html .= '<th>' . WT_Gedcom_Tag::getLabel('TITL') . '</th>';
	$html .= '<th>' . WT_I18N::translate('Individuals') . '</th>';
	$html .= '<th>#INDI</th>';
	$html .= '<th>' . WT_I18N::translate('Families') . '</th>';
	$html .= '<th>#FAM</th>';
	$html .= '<th>' . WT_I18N::translate('Media objects') . '</th>';
	$html .= '<th>#OBJE</th>';
	$html .= '<th>' . WT_I18N::translate('Sources') . '</th>';
	$html .= '<th>#SOUR</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>' . WT_Gedcom_Tag::getLabel('CHAN') . '</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>CHAN</th>';
	$html .= '<th>&nbsp;</th>';//delete
	$html .= '</tr></thead>';
	//-- table body
	$html .= '<tbody>';
	foreach ($datalist as $note) {
		if (!$note->canShow()) {
			continue;
		}
		if ($note->isPendingAddtion()) {
			$class = ' class="new"';
		} elseif ($note->isPendingDeletion()) {
			$class = ' class="old"';
		} else {
			$class = '';
		}
		$html .= '<tr' . $class . '>';
		//-- Shared Note name
		$html .= '<td><a class="name2" href="' . $note->getHtmlUrl() . '">' . highlight_search_hits($note->getFullName()) . '</a></td>';
		//-- Linked INDIs
		$num = count($note->linkedIndividuals('NOTE'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Linked FAMs
		$num = count($note->linkedfamilies('NOTE'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Linked OBJEcts
		$num = count($note->linkedMedia('NOTE'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Linked SOURs
		$num = count($note->linkedSources('NOTE'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $note->LastChangeTimestamp() . '</td>';
		} else {
			$html .= '<td></td>';
		}
		//-- Last change hidden sort column
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $note->LastChangeTimestamp(true) . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Delete
		if (WT_USER_GEDCOM_ADMIN) {
			$html .= '<td><div title="' . WT_I18N::translate('Delete') . '" class="deleteicon" onclick="return delete_note(\'' . WT_I18N::translate('Are you sure you want to delete “%s”?', WT_Filter::escapeJs(WT_Filter::unescapeHtml($note->getFullName()))) . "', '" . $note->getXref() . '\');"><span class="link_text">' . WT_I18N::translate('Delete') . '</span></div></td>';
		} else {
			$html .= '<td></td>';
		}
		$html .= '</tr>';
	}
	$html .= '</tbody></table></div>';

	return $html;
}

/**
 * Print a table of repositories
 *
 * @param WT_Repository[] $repositories
 *
 * @return string
 */
function format_repo_table($repositories) {
	global $SHOW_LAST_CHANGE, $controller;

	$html = '';
	$table_id = 'table-repo-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#' . $table_id . '").dataTable({
				dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth: false,
				processing: true,
				columns: [
					/* 0 name      */ { type: "unicode" },
					/* 1 #sour     */ { dataSort: 2, class: "center" },
					/* 2 #SOUR     */ { type: "num", visible: false },
					/* 3 CHAN      */ { dataSort: 4, visible: ' . ($SHOW_LAST_CHANGE ? 'true' : 'false') . ' },
					/* 4 CHAN_sort */ { visible: false },
					/* 5 DELETE    */ { visible: ' . (WT_USER_GEDCOM_ADMIN ? 'true' : 'false') . ', sortable: false }
				],
				displayLength: 20,
				pagingType: "full_numbers"
			});
			jQuery(".repo-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

	//--table wrapper
	$html .= '<div class="loading-image">&nbsp;</div>';
	$html .= '<div class="repo-list">';
	//-- table header
	$html .= '<table id="' . $table_id . '"><thead><tr>';
	$html .= '<th>' . WT_I18N::translate('Repository name') . '</th>';
	$html .= '<th>' . WT_I18N::translate('Sources') . '</th>';
	$html .= '<th>#SOUR</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>' . WT_Gedcom_Tag::getLabel('CHAN') . '</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>CHAN</th>';
	$html .= '<th>&nbsp;</th>';//delete
	$html .= '</tr></thead>';
	//-- table body
	$html .= '<tbody>';

	foreach ($repositories as $repository) {
		if (!$repository->canShow()) {
			continue;
		}
		if ($repository->isPendingAddtion()) {
			$class = ' class="new"';
		} elseif ($repository->isPendingDeletion()) {
			$class = ' class="old"';
		} else {
			$class = '';
		}
		$html .= '<tr' . $class . '>';
		//-- Repository name(s)
		$html .= '<td>';
		foreach ($repository->getAllNames() as $n => $name) {
			if ($n) {
				$html .= '<br>';
			}
			if ($n == $repository->getPrimaryName()) {
				$html .= '<a class="name2" href="' . $repository->getHtmlUrl() . '">' . highlight_search_hits($name['full']) . '</a>';
			} else {
				$html .= '<a href="' . $repository->getHtmlUrl() . '">' . highlight_search_hits($name['full']) . '</a>';
			}
		}
		$html .= '</td>';
		//-- Linked SOURces
		$num = count($repository->linkedSources('REPO'));
		$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $repository->LastChangeTimestamp() . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Last change hidden sort column
		if ($SHOW_LAST_CHANGE) {
			$html .= '<td>' . $repository->LastChangeTimestamp(true) . '</td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		//-- Delete
		if (WT_USER_GEDCOM_ADMIN) {
			$html .= '<td><div title="' . WT_I18N::translate('Delete') . '" class="deleteicon" onclick="return delete_repository(\'' . WT_I18N::translate('Are you sure you want to delete “%s”?', WT_Filter::escapeJs(WT_Filter::unescapeHtml($repository->getFullName()))) . "', '" . $repository->getXref() . '\');"><span class="link_text">' . WT_I18N::translate('Delete') . '</span></div></td>';
		} else {
			$html .= '<td>&nbsp;</td>';
		}
		$html .= '</tr>';
	}
	$html .= '</tbody></table></div>';

	return $html;
}

/**
 * Print a table of media objects
 *
 * @param WT_Media[] $media_objects
 *
 * @return string
 */
function format_media_table($media_objects) {
	global $SHOW_LAST_CHANGE, $controller;

	$html = '';
	$table_id = 'table-obje-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#' . $table_id . '").dataTable({
				dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				autoWidth:false,
				processing: true,
				columns: [
					/* 0 media     */ { sortable: false },
					/* 1 title     */ { type: "unicode" },
					/* 2 #indi     */ { dataSort: 3, class: "center" },
					/* 3 #INDI     */ { type: "num", visible: false },
					/* 4 #fam      */ { dataSort: 5, class: "center" },
					/* 5 #FAM      */ { type: "num", visible: false },
					/* 6 #sour     */ { dataSort: 7, class: "center" },
					/* 7 #SOUR     */ { type: "num", visible: false },
					/* 8 CHAN      */ { dataSort: 9, visible: ' . ($SHOW_LAST_CHANGE ? 'true' : 'false') . ' },
					/* 9 CHAN_sort */ { visible: false },
				],
				displayLength: 20,
				pagingType: "full_numbers"
			});
			jQuery(".media-list").css("visibility", "visible");
			jQuery(".loading-image").css("display", "none");
		');

	//--table wrapper
	$html .= '<div class="loading-image">&nbsp;</div>';
	$html .= '<div class="media-list">';
	//-- table header
	$html .= '<table id="' . $table_id . '"><thead><tr>';
	$html .= '<th>' . WT_I18N::translate('Media') . '</th>';
	$html .= '<th>' . WT_Gedcom_Tag::getLabel('TITL') . '</th>';
	$html .= '<th>' . WT_I18N::translate('Individuals') . '</th>';
	$html .= '<th>#INDI</th>';
	$html .= '<th>' . WT_I18N::translate('Families') . '</th>';
	$html .= '<th>#FAM</th>';
	$html .= '<th>' . WT_I18N::translate('Sources') . '</th>';
	$html .= '<th>#SOUR</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>' . WT_Gedcom_Tag::getLabel('CHAN') . '</th>';
	$html .= '<th' . ($SHOW_LAST_CHANGE ? '' : '') . '>CHAN</th>';
	$html .= '</tr></thead>';
	//-- table body
	$html .= '<tbody>';

	foreach ($media_objects as $media_object) {
		if ($media_object->canShow()) {
			$name = $media_object->getFullName();
			if ($media_object->isPendingAddtion()) {
				$class = ' class="new"';
			} elseif ($media_object->isPendingDeletion()) {
				$class = ' class="old"';
			} else {
				$class = '';
			}
			$html .= '<tr' . $class . '>';
			//-- Object thumbnail
			$html .= '<td>' . $media_object->displayImage() . '</td>';
			//-- Object name(s)
			$html .= '<td>';
			$html .= '<a href="' . $media_object->getHtmlUrl() . '" class="list_item name2">';
			$html .= highlight_search_hits($name) . '</a>';
			if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
				$html .= '<br><a href="' . $media_object->getHtmlUrl() . '">' . basename($media_object->getFilename()) . '</a>';
			}
			$html .= '</td>';

			//-- Linked INDIs
			$num = count($media_object->linkedIndividuals('OBJE'));
			$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
			//-- Linked FAMs
			$num = count($media_object->linkedfamilies('OBJE'));
			$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
			//-- Linked SOURces
			$num = count($media_object->linkedSources('OBJE'));
			$html .= '<td>' . WT_I18N::number($num) . '</td><td>' . $num . '</td>';
			//-- Last change
			if ($SHOW_LAST_CHANGE) {
				$html .= '<td>' . $media_object->LastChangeTimestamp() . '</td>';
			} else {
				$html .= '<td>&nbsp;</td>';
			}
			//-- Last change hidden sort column
			if ($SHOW_LAST_CHANGE) {
				$html .= '<td>' . $media_object->LastChangeTimestamp(true) . '</td>';
			} else {
				$html .= '<td>&nbsp;</td>';
			}
			$html .= '</tr>';
		}
	}
	$html .= '</tbody></table></div>';

	return $html;
}

/**
 * Print a table of surnames, for the top surnames block, the indi/fam lists, etc.
 *
 * @param string[][] $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
 * @param string     $script   "indilist.php" (counts of individuals) or "famlist.php" (counts of spouses)
 *
 * @return string
 */
function format_surname_table($surnames, $script) {
	global $controller;
	$html = '';
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["num-asc" ]=function(a,b) {a=parseFloat(a); b=parseFloat(b); return (a<b) ? -1 : (a>b ? 1 : 0);};
			jQuery.fn.dataTableExt.oSort["num-desc"]=function(a,b) {a=parseFloat(a); b=parseFloat(b); return (a>b) ? -1 : (a<b ? 1 : 0);};
			jQuery(".surname-list").dataTable( {
				dom: \'t\',
				jQueryUI: true,
				autoWidth: false,
				paging: false,
				sorting: [],
				columns: [
					/*  0 name  */ { dataSort: 1 },
					/*  1 NAME  */ { visible: false },
					/*  2 count */ { dataSort: 3, class: "center" },
					/*  3 COUNT */ { visible: false }
				],
			});
		');

	if ($script == 'famlist.php') {
		$col_heading = WT_I18N::translate('Spouses');
	} else {
		$col_heading = WT_I18N::translate('Individuals');
	}

	$html .= '<table class="surname-list">' .
		'<thead><tr>' .
		'<th>' . WT_Gedcom_Tag::getLabel('SURN') . '</th>' .
		'<th>&nbsp;</th>' .
		'<th>' . $col_heading . '</th>' .
		'<th>&nbsp;</th>' .
		'</tr></thead>';

	$html .= '<tbody>';
	foreach ($surnames as $surn => $surns) {
		// Each surname links back to the indi/fam surname list
		if ($surn) {
			$url = $script . '?surname=' . rawurlencode($surn) . '&amp;ged=' . WT_GEDURL;
		} else {
			$url = $script . '?alpha=,&amp;ged=' . WT_GEDURL;
		}
		// Row counter
		$html .= '<tr>';
		// Surname
		$html .= '<td>';
		// Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc.
		foreach ($surns as $spfxsurn => $indis) {
			if ($spfxsurn) {
				$html .= '<a href="' . $url . '" dir="auto">' . WT_Filter::escapeHtml($spfxsurn) . '</a><br>';
			} else {
				// No surname, but a value from "2 SURN"?  A common workaround for toponyms, etc.
				$html .= '<a href="' . $url . '" dir="auto">' . WT_Filter::escapeHtml($surn) . '</a><br>';
			}
		}
		$html .= '</td>';
		// Sort column for name
		$html .= '<td>' . $surn . '</td>';
		// Surname count
		$html .= '<td>';
		$subtotal = 0;
		foreach ($surns as $indis) {
			$subtotal += count($indis);
			$html .= WT_I18N::number(count($indis)) . '<br>';
		}
		// More than one surname variant? Show a subtotal
		if (count($surns) > 1) {
			$html .= WT_I18N::number($subtotal);
		}
		$html .= '</td>';
		// add hidden numeric sort column
		$html .= '<td>' . $subtotal . '</td></tr>';
	}
	$html .= '</tbody></table>';

	return $html;
}

/**
 * Print a tagcloud of surnames.
 *
 * @param string[][] $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
 * @param string     $script   indilist or famlist
 * @param boolean    $totals   show totals after each name
 *
 * @return string
 */
function format_surname_tagcloud($surnames, $script, $totals) {
	$cloud = new Zend_Tag_Cloud(
		array(
			'tagDecorator'   => array(
				'decorator' => 'HtmlTag',
				'options'   => array(
					'htmlTags'     => array(),
					'fontSizeUnit' => '%',
					'minFontSize'  => 80,
					'maxFontSize'  => 250
				)
			),
			'cloudDecorator' => array(
				'decorator' => 'HtmlCloud',
				'options'   => array(
					'htmlTags' => array(
						'div' => array(
							'class' => 'tag_cloud'
						)
					)
				)
			)
		)
	);
	foreach ($surnames as $surn => $surns) {
		foreach ($surns as $spfxsurn => $indis) {
			$cloud->appendTag(array(
				'title'  => $totals ? WT_I18N::translate('%1$s (%2$d)', '<span dir="auto">' . $spfxsurn . '</span>', count($indis)) : $spfxsurn,
				'weight' => count($indis),
				'params' => array(
					'url' => $surn ?
						$script . '?surname=' . urlencode($surn) . '&amp;ged=' . WT_GEDURL :
						$script . '?alpha=,&amp;ged=' . WT_GEDURL
				)
			));
		}
	}

	return (string)$cloud;
}

/**
 * Print a list of surnames.
 *
 * @param string[][] $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
 * @param integer    $style    1=bullet list, 2=semicolon-separated list, 3=tabulated list with up to 4 columns
 * @param boolean    $totals   show totals after each name
 * @param string     $script   indilist or famlist
 *
 * @return string
 */
function format_surname_list($surnames, $style, $totals, $script) {
	global $GEDCOM;

	$html = array();
	foreach ($surnames as $surn => $surns) {
		// Each surname links back to the indilist
		if ($surn) {
			$url = $script . '?surname=' . urlencode($surn) . '&amp;ged=' . rawurlencode($GEDCOM);
		} else {
			$url = $script . '?alpha=,&amp;ged=' . rawurlencode($GEDCOM);
		}
		// If all the surnames are just case variants, then merge them into one
		// Comment out this block if you want SMITH listed separately from Smith
		$first_spfxsurn = null;
		foreach ($surns as $spfxsurn => $indis) {
			if ($first_spfxsurn) {
				if (WT_I18N::strtoupper($spfxsurn) == WT_I18N::strtoupper($first_spfxsurn)) {
					$surns[$first_spfxsurn] = array_merge($surns[$first_spfxsurn], $surns[$spfxsurn]);
					unset ($surns[$spfxsurn]);
				}
			} else {
				$first_spfxsurn = $spfxsurn;
			}
		}
		$subhtml = '<a href="' . $url . '" dir="auto">' . WT_Filter::escapeHtml(implode(WT_I18N::$list_separator, array_keys($surns))) . '</a>';

		if ($totals) {
			$subtotal = 0;
			foreach ($surns as $indis) {
				$subtotal += count($indis);
			}
			$subhtml .= '&nbsp;(' . WT_I18N::number($subtotal) . ')';
		}
		$html[] = $subhtml;

	}
	switch ($style) {
	case 1:
		return '<ul><li>' . implode('</li><li>', $html) . '</li></ul>';
	case 2:
		return implode(WT_I18N::$list_separator, $html);
	case 3:
		$i = 0;
		$count = count($html);
		if ($count > 36) {
			$col = 4;
		} elseif ($count > 18) {
			$col = 3;
		} elseif ($count > 6) {
			$col = 2;
		} else {
			$col = 1;
		}
		$newcol = ceil($count / $col);
		$html2 = '<table class="list_table"><tr>';
		$html2 .= '<td class="list_value" style="padding: 14px;">';

		foreach ($html as $surns) {
			$html2 .= $surns . '<br>';
			$i++;
			if ($i == $newcol && $i < $count) {
				$html2 .= '</td><td class="list_value" style="padding: 14px;">';
				$newcol = $i + ceil($count / $col);
			}
		}
		$html2 .= '</td></tr></table>';

		return $html2;
	}
}

/**
 * Print a table of events
 *
 * @param string[] $change_ids
 * @param string   $sort
 *
 * @return string
 */
function print_changes_list($change_ids, $sort) {
	$n = 0;
	$arr = array();
	foreach ($change_ids as $change_id) {
		$record = WT_GedcomRecord::getInstance($change_id);
		if (!$record || !$record->canShow()) {
			continue;
		}
		// setup sorting parameters
		$arr[$n]['record'] = $record;
		$arr[$n]['jd'] = ($sort == 'name') ? 1 : $n;
		$arr[$n]['anniv'] = $record->lastChangeTimestamp(true);
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
	$html = '';
	foreach ($arr as $value) {
		$html .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item name2">' . $value['record']->getFullName() . '</a>';
		$html .= '<div class="indent" style="margin-bottom: 5px;">';
		if ($value['record'] instanceof WT_Individual) {
			if ($value['record']->getAddName()) {
				$html .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item">' . $value['record']->getAddName() . '</a>';
			}
		}
		$html .= /* I18N: [a record was] Changed on <date/time> by <user> */ WT_I18N::translate('Changed on %1$s by %2$s', $value['record']->lastChangeTimestamp(), WT_Filter::escapeHtml($value['record']->lastChangeUser()));
		$html .= '</div>';
	}

	return $html;
}

/**
 * Print a table of events
 *
 * @param string[] $change_ids
 * @param string   $sort
 *
 * @return string
 */
function print_changes_table($change_ids, $sort) {
	global $controller;

	$n = 0;
	$table_id = 'table-chan-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
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
	$html = '';
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery.fn.dataTableExt.oSort["unicode-asc" ]=function(a,b) {return a.replace(/<[^<]*>/, "").localeCompare(b.replace(/<[^<]*>/, ""))};
			jQuery.fn.dataTableExt.oSort["unicode-desc"]=function(a,b) {return b.replace(/<[^<]*>/, "").localeCompare(a.replace(/<[^<]*>/, ""))};
			jQuery("#' . $table_id . '").dataTable({
				dom: \'t\',
				paging: false,
				autoWidth:false,
				lengthChange: false,
				filter: false,
				' . WT_I18N::datatablesI18N() . ',
				jQueryUI: true,
				sorting: [' . $aaSorting . '],
				columns: [
					/* 0-Type */    { sortable: false, class: "center" },
					/* 1-Record */  { dataSort: 5 },
					/* 2-Change */  { dataSort: 4 },
					/* 3-By */      null,
					/* 4-DATE */    { visible: false },
					/* 5-SORTNAME */{ type: "unicode", visible: false }
				]
			});
		');

	//-- table header
	$html .= '<table id="' . $table_id . '" class="width100">';
	$html .= '<thead><tr>';
	$html .= '<th>&nbsp;</th>';
	$html .= '<th>' . WT_I18N::translate('Record') . '</th>';
	$html .= '<th>' . WT_Gedcom_Tag::getLabel('CHAN') . '</th>';
	$html .= '<th>' . WT_Gedcom_Tag::getLabel('_WT_USER') . '</th>';
	$html .= '<th>DATE</th>';     //hidden by datatables code
	$html .= '<th>SORTNAME</th>'; //hidden by datatables code
	$html .= '</tr></thead><tbody>';

	//-- table body
	foreach ($change_ids as $change_id) {
		$record = WT_GedcomRecord::getInstance($change_id);
		if (!$record || !$record->canShow()) {
			continue;
		}
		$html .= '<tr><td>';
		switch ($record::RECORD_TYPE) {
		case 'INDI':
			$icon = $record->getSexImage('small');
			break;
		case 'FAM':
			$icon = '<i class="icon-button_family"></i>';
			break;
		case 'OBJE':
			$icon = '<i class="icon-button_media"></i>';
			break;
		case 'NOTE':
			$icon = '<i class="icon-button_note"></i>';
			break;
		case 'SOUR':
			$icon = '<i class="icon-button_source"></i>';
			break;
		case 'REPO':
			$icon = '<i class="icon-button_repository"></i>';
			break;
		default:
			$icon = '&nbsp;';
			break;
		}
		$html .= '<a href="' . $record->getHtmlUrl() . '">' . $icon . '</a>';
		$html .= '</td>';
		++$n;
		//-- Record name(s)
		$name = $record->getFullName();
		$html .= '<td class="wrap">';
		$html .= '<a href="' . $record->getHtmlUrl() . '">' . $name . '</a>';
		if ($record instanceof WT_Individual) {
			$addname = $record->getAddName();
			if ($addname) {
				$html .= '<div class="indent"><a href="' . $record->getHtmlUrl() . '">' . $addname . '</a></div>';
			}
		}
		$html .= "</td>";
		//-- Last change date/time
		$html .= '<td class="wrap">' . $record->lastChangeTimestamp() . '</td>';
		//-- Last change user
		$html .= '<td class="wrap">' . WT_Filter::escapeHtml($record->lastChangeUser()) . '</td>';
		//-- change date (sortable) hidden by datatables code
		$html .= '<td>' . $record->lastChangeTimestamp(true) . '</td>';
		//-- names (sortable) hidden by datatables code
		$html .= '<td>' . $record->getSortName() . '</td></tr>';
	}

	$html .= '</tbody></table>';

	return $html;
}

/**
 * Print a table of events
 *
 * @param integer $startjd
 * @param integer $endjd
 * @param string  $events
 * @param boolean $only_living
 * @param string  $sort_by
 *
 * @return string
 */
function print_events_table($startjd, $endjd, $events = 'BIRT MARR DEAT', $only_living = false, $sort_by = 'anniv') {
	global $controller;
	$html = '';
	$table_id = 'table-even-' . Uuid::uuid4(); // lists requires a unique ID in case there are multiple lists per page
	$controller
		->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
		->addInlineJavascript('
			jQuery("#' . $table_id . '").dataTable({
				dom: \'t\',
				' . WT_I18N::datatablesI18N() . ',
				autoWidth: false,
				paging: false,
				lengthChange: false,
				filter: false,
				info: true,
				jQueryUI: true,
				sorting: [[ ' . ($sort_by == 'alpha' ? 1 : 3) . ', "asc"]],
				columns: [
					/* 0-Record */ { dataSort: 1 },
					/* 1-NAME */   { visible: false },
					/* 2-Date */   { dataSort: 3 },
					/* 3-DATE */   { visible: false },
					/* 4-Anniv. */ { dataSort: 5, class: "center" },
					/* 5-ANNIV  */ { type: "num", visible: false },
					/* 6-Event */  { class: "center" }
				]
			});
		');

	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;
	$filtered_events = array();

	foreach (get_events_list($startjd, $endjd, $events) as $fact) {
		$record = $fact->getParent();
		//-- only living people ?
		if ($only_living) {
			if ($record instanceof WT_Individual && $record->isDead()) {
				$filter++;
				continue;
			}
			if ($record instanceof WT_Family) {
				$husb = $record->getHusband();
				if (is_null($husb) || $husb->isDead()) {
					$filter++;
					continue;
				}
				$wife = $record->getWife();
				if (is_null($wife) || $wife->isDead()) {
					$filter++;
					continue;
				}
			}
		}

		//-- Counter
		$output++;

		if ($output == 1) {
			//-- table body
			$html .= '<table id="' . $table_id . '" class="width100">';
			$html .= '<thead><tr>';
			$html .= '<th>' . WT_I18N::translate('Record') . '</th>';
			$html .= '<th>NAME</th>'; //hidden by datatables code
			$html .= '<th>' . WT_Gedcom_Tag::getLabel('DATE') . '</th>';
			$html .= '<th>DATE</th>'; //hidden by datatables code
			$html .= '<th><i class="icon-reminder" title="' . WT_I18N::translate('Anniversary') . '"></i></th>';
			$html .= '<th>ANNIV</th>';
			$html .= '<th>' . WT_Gedcom_Tag::getLabel('EVEN') . '</th>';
			$html .= '</tr></thead><tbody>';
		}

		$filtered_events[] = $fact;
	}

	foreach ($filtered_events as $n => $fact) {
		$record = $fact->getParent();
		$html .= '<tr>';
		$html .= '<td>';
		$html .= '<a href="' . $record->getHtmlUrl() . '">' . $record->getFullName() . '</a>';
		if ($record instanceof WT_Individual) {
			$html .= $record->getSexImage();
		}
		$html .= '</td>';
		$html .= '<td>' . $record->getSortName() . '</td>';
		$html .= '<td>' . $fact->getDate()->Display(empty($SEARCH_SPIDER)) . '</td>';
		$html .= '<td>' . $n . '</td>';
		$html .= '<td>' . WT_I18N::number($fact->anniv) . '</td>';
		$html .= '<td>' . $fact->anniv . '</td>';
		$html .= '<td>' . $fact->getLabel() . '</td>';
		$html .= '</tr>';
	}

	if ($output != 0) {
		$html .= '</tbody></table>';
	}

	// Print a final summary message about restricted/filtered facts
	$summary = '';
	if ($endjd == WT_CLIENT_JD) {
		// We're dealing with the Today’s Events block
		if ($output == 0) {
			if ($filter == 0) {
				$summary = WT_I18N::translate('No events exist for today.');
			} else {
				$summary = WT_I18N::translate('No events for living individuals exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($output == 0) {
			if ($filter == 0) {
				if ($endjd == $startjd) {
					$summary = WT_I18N::translate('No events exist for tomorrow.');
				} else {
					// I18N: translation for %s==1 is unused; it is translated separately as “tomorrow”
					$summary = WT_I18N::plural('No events exist for the next %s day.', 'No events exist for the next %s days.', $endjd - $startjd + 1, WT_I18N::number($endjd - $startjd + 1));
				}
			} else {
				if ($endjd == $startjd) {
					$summary = WT_I18N::translate('No events for living individuals exist for tomorrow.');
				} else {
					// I18N: translation for %s==1 is unused; it is translated separately as “tomorrow”
					$summary = WT_I18N::plural('No events for living people exist for the next %s day.', 'No events for living people exist for the next %s days.', $endjd - $startjd + 1, WT_I18N::number($endjd - $startjd + 1));
				}
			}
		}
	}
	if ($summary != "") {
		$html .= '<strong>' . $summary . '</strong>';
	}

	return $html;
}

/**
 * Print a list of events
 *
 * This performs the same function as print_events_table(), but formats the output differently.
 *
 * @param integer $startjd
 * @param integer $endjd
 * @param string  $events
 * @param boolean $only_living
 * @param string  $sort_by
 *
 * @return string
 */
function print_events_list($startjd, $endjd, $events = 'BIRT MARR DEAT', $only_living = false, $sort_by = 'anniv') {
	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;
	$filtered_events = array();
	$html = '';
	foreach (get_events_list($startjd, $endjd, $events) as $fact) {
		$record = $fact->getParent();
		//-- only living people ?
		if ($only_living) {
			if ($record instanceof WT_Individual && $record->isDead()) {
				$filter++;
				continue;
			}
			if ($record instanceof WT_Family) {
				$husb = $record->getHusband();
				if (is_null($husb) || $husb->isDead()) {
					$filter++;
					continue;
				}
				$wife = $record->getWife();
				if (is_null($wife) || $wife->isDead()) {
					$filter++;
					continue;
				}
			}
		}

		$output++;

		$filtered_events[] = $fact;
	}

	// Now we've filtered the list, we can sort by event, if required
	switch ($sort_by) {
	case 'anniv':
		// Data is already sorted by anniversary date
		break;
	case 'alpha':
		uasort($filtered_events, function (WT_Fact $x, WT_Fact $y) {
			return WT_GedcomRecord::compare($x->getParent(), $y->getParent());
		});
		break;
	}

	foreach ($filtered_events as $fact) {
		$record = $fact->getParent();
		$html .= '<a href="' . $record->getHtmlUrl() . '" class="list_item name2">' . $record->getFullName() . '</a>';
		if ($record instanceof WT_Individual) {
			$html .= $record->getSexImage();
		}
		$html .= '<br><div class="indent">';
		$html .= $fact->getLabel() . ' — ' . $fact->getDate()->Display(true);
		if ($fact->anniv) {
			$html .= ' (' . WT_I18N::translate('%s year anniversary', $fact->anniv) . ')';
		}
		if (!$fact->getPlace()->isEmpty()) {
			$html .= ' — <a href="' . $fact->getPlace()->getURL() . '">' . $fact->getPlace()->getFullName() . '</a>';
		}
		$html .= '</div>';
	}

	// Print a final summary message about restricted/filtered facts
	$summary = '';
	if ($endjd == WT_CLIENT_JD) {
		// We're dealing with the Today’s Events block
		if ($output == 0) {
			if ($filter == 0) {
				$summary = WT_I18N::translate('No events exist for today.');
			} else {
				$summary = WT_I18N::translate('No events for living individuals exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($output == 0) {
			if ($filter == 0) {
				if ($endjd == $startjd) {
					$summary = WT_I18N::translate('No events exist for tomorrow.');
				} else {
					// I18N: translation for %s==1 is unused; it is translated separately as “tomorrow”
					$summary = WT_I18N::plural('No events exist for the next %s day.', 'No events exist for the next %s days.', $endjd - $startjd + 1, WT_I18N::number($endjd - $startjd + 1));
				}
			} else {
				if ($endjd == $startjd) {
					$summary = WT_I18N::translate('No events for living individuals exist for tomorrow.');
				} else {
					// I18N: translation for %s==1 is unused; it is translated separately as “tomorrow”
					$summary = WT_I18N::plural('No events for living people exist for the next %s day.', 'No events for living people exist for the next %s days.', $endjd - $startjd + 1, WT_I18N::number($endjd - $startjd + 1));
				}
			}
		}
	}
	if ($summary) {
		$html .= "<b>" . $summary . "</b>";
	}

	return $html;
}

/**
 * Print a chart by age using Google chart API
 *
 * @param integer[] $data
 * @param string    $title
 *
 * @return string
 */
function print_chart_by_age($data, $title) {
	$count = 0;
	$agemax = 0;
	$vmax = 0;
	$avg = 0;
	foreach ($data as $age => $v) {
		$n = strlen($v);
		$vmax = max($vmax, $n);
		$agemax = max($agemax, $age);
		$count += $n;
		$avg += $age * $n;
	}
	if ($count < 1) {
		return;
	}
	$avg = round($avg / $count);
	$chart_url = "https://chart.googleapis.com/chart?cht=bvs"; // chart type
	$chart_url .= "&amp;chs=725x150"; // size
	$chart_url .= "&amp;chbh=3,2,2"; // bvg : 4,1,2
	$chart_url .= "&amp;chf=bg,s,FFFFFF99"; //background color
	$chart_url .= "&amp;chco=0000FF,FFA0CB,FF0000"; // bar color
	$chart_url .= "&amp;chdl=" . rawurlencode(WT_I18N::translate('Males')) . "|" . rawurlencode(WT_I18N::translate('Females')) . "|" . rawurlencode(WT_I18N::translate('Average age') . ": " . $avg); // legend & average age
	$chart_url .= "&amp;chtt=" . rawurlencode($title); // title
	$chart_url .= "&amp;chxt=x,y,r"; // axis labels specification
	$chart_url .= "&amp;chm=V,FF0000,0," . ($avg - 0.3) . ",1"; // average age line marker
	$chart_url .= "&amp;chxl=0:|"; // label
	for ($age = 0; $age <= $agemax; $age += 5) {
		$chart_url .= $age . "|||||"; // x axis
	}
	$chart_url .= "|1:||" . rawurlencode(WT_I18N::percentage($vmax / $count)); // y axis
	$chart_url .= "|2:||";
	$step = $vmax;
	for ($d = $vmax; $d > 0; $d--) {
		if ($vmax < ($d * 10 + 1) && ($vmax % $d) == 0) {
			$step = $d;
		}
	}
	if ($step == $vmax) {
		for ($d = $vmax - 1; $d > 0; $d--) {
			if (($vmax - 1) < ($d * 10 + 1) && (($vmax - 1) % $d) == 0) {
				$step = $d;
			}
		}
	}
	for ($n = $step; $n < $vmax; $n += $step) {
		$chart_url .= $n . "|";
	}
	$chart_url .= rawurlencode($vmax . " / " . $count); // r axis
	$chart_url .= "&amp;chg=100," . round(100 * $step / $vmax, 1) . ",1,5"; // grid
	$chart_url .= "&amp;chd=s:"; // data : simple encoding from A=0 to 9=61
	$CHART_ENCODING61 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for ($age = 0; $age <= $agemax; $age++) {
		$chart_url .= $CHART_ENCODING61[(int)(substr_count($data[$age], "M") * 61 / $vmax)];
	}
	$chart_url .= ",";
	for ($age = 0; $age <= $agemax; $age++) {
		$chart_url .= $CHART_ENCODING61[(int)(substr_count($data[$age], "F") * 61 / $vmax)];
	}
	$html = '<img src="' . $chart_url . '" alt="' . $title . '" title="' . $title . '" class="gchart">';

	return $html;
}

/**
 * Print a chart by decade using Google chart API
 *
 * @param integer[] $data
 * @param string    $title
 *
 * @return string
 */
function print_chart_by_decade($data, $title) {
	$count = 0;
	$vmax = 0;
	foreach ($data as $v) {
		$n = strlen($v);
		$vmax = max($vmax, $n);
		$count += $n;
	}
	if ($count < 1) {
		return;
	}
	$chart_url = "https://chart.googleapis.com/chart?cht=bvs"; // chart type
	$chart_url .= "&amp;chs=360x150"; // size
	$chart_url .= "&amp;chbh=3,3"; // bvg : 4,1,2
	$chart_url .= "&amp;chf=bg,s,FFFFFF99"; //background color
	$chart_url .= "&amp;chco=0000FF,FFA0CB"; // bar color
	$chart_url .= "&amp;chtt=" . rawurlencode($title); // title
	$chart_url .= "&amp;chxt=x,y,r"; // axis labels specification
	$chart_url .= "&amp;chxl=0:|&lt;|||"; // <1570
	for ($y = 1600; $y < 2030; $y += 50) {
		$chart_url .= $y . "|||||"; // x axis
	}
	$chart_url .= "|1:||" . rawurlencode(WT_I18N::percentage($vmax / $count)); // y axis
	$chart_url .= "|2:||";
	$step = $vmax;
	for ($d = $vmax; $d > 0; $d--) {
		if ($vmax < ($d * 10 + 1) && ($vmax % $d) == 0) {
			$step = $d;
		}
	}
	if ($step == $vmax) {
		for ($d = $vmax - 1; $d > 0; $d--) {
			if (($vmax - 1) < ($d * 10 + 1) && (($vmax - 1) % $d) == 0) {
				$step = $d;
			}
		}
	}
	for ($n = $step; $n < $vmax; $n += $step) {
		$chart_url .= $n . "|";
	}
	$chart_url .= rawurlencode($vmax . " / " . $count); // r axis
	$chart_url .= "&amp;chg=100," . round(100 * $step / $vmax, 1) . ",1,5"; // grid
	$chart_url .= "&amp;chd=s:"; // data : simple encoding from A=0 to 9=61
	$CHART_ENCODING61 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for ($y = 1570; $y < 2030; $y += 10) {
		$chart_url .= $CHART_ENCODING61[(int)(substr_count($data[$y], "M") * 61 / $vmax)];
	}
	$chart_url .= ",";
	for ($y = 1570; $y < 2030; $y += 10) {
		$chart_url .= $CHART_ENCODING61[(int)(substr_count($data[$y], "F") * 61 / $vmax)];
	}
	$html = '<img src="' . $chart_url . '" alt="' . $title . '" title="' . $title . '" class="gchart">';

	return $html;
}
