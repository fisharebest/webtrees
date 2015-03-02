<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global string $UNKNOWN_NN
 * @global string $UNKNOWN_PN
 * @global Tree   $WT_TREE
 */
global $UNKNOWN_NN, $UNKNOWN_PN, $WT_TREE;

define('WT_SCRIPT_NAME', 'indilist.php');
require './includes/session.php';

$controller = new PageController;

// We show three different lists: initials, surnames and individuals
// Note that the data may contain special chars, such as surname="<unknown>",
$alpha    = Filter::get('alpha'); // All surnames beginning with this letter where "@"=unknown and ","=none
$surname  = Filter::get('surname'); // All indis with this surname
$show_all = Filter::get('show_all', 'no|yes', 'no'); // All indis
// Long lists can be broken down by given name
$show_all_firstnames = Filter::get('show_all_firstnames', 'no|yes', 'no');
if ($show_all_firstnames === 'yes') {
	$falpha = '';
} else {
	$falpha = Filter::get('falpha'); // All first names beginning with this letter
}

$show_marnm = Filter::get('show_marnm', 'no|yes');
switch ($show_marnm) {
case 'no':
case 'yes':
	Auth::user()->setPreference(WT_SCRIPT_NAME . '_show_marnm', $show_marnm);
	break;
default:
	$show_marnm = Auth::user()->getPreference(WT_SCRIPT_NAME . '_show_marnm');
}

// Make sure selections are consistent.
// i.e. can’t specify show_all and surname at the same time.
if ($show_all === 'yes') {
	if ($show_all_firstnames === 'yes') {
		$alpha   = '';
		$surname = '';
		$legend  = I18N::translate('All');
		$url     = WT_SCRIPT_NAME . '?show_all=yes&amp;ged=' . WT_GEDURL;
		$show    = 'indi';
	} elseif ($falpha) {
		$alpha   = '';
		$surname = '';
		$legend  = I18N::translate('All') . ', ' . Filter::escapeHtml($falpha) . '…';
		$url     = WT_SCRIPT_NAME . '?show_all=yes&amp;ged=' . WT_GEDURL;
		$show    = 'indi';
	} else {
		$alpha   = '';
		$surname = '';
		$legend  = I18N::translate('All');
		$url     = WT_SCRIPT_NAME . '?show_all=yes' . '&amp;ged=' . WT_GEDURL;
		$show    = Filter::get('show', 'surn|indi', 'surn');
	}
} elseif ($surname) {
	$alpha    = QueryName::initialLetter($surname); // so we can highlight the initial letter
	$show_all = 'no';
	if ($surname === '@N.N.') {
		$legend = $UNKNOWN_NN;
	} else {
		$legend = Filter::escapeHtml($surname);
	}
	$url = WT_SCRIPT_NAME . '?surname=' . rawurlencode($surname) . '&amp;ged=' . WT_GEDURL;
	switch ($falpha) {
	case '':
		break;
	case '@':
		$legend .= ', ' . $UNKNOWN_PN;
		$url .= '&amp;falpha=' . rawurlencode($falpha) . '&amp;ged=' . WT_GEDURL;
		break;
	default:
		$legend .= ', ' . Filter::escapeHtml($falpha) . '…';
		$url .= '&amp;falpha=' . rawurlencode($falpha) . '&amp;ged=' . WT_GEDURL;
		break;
	}
	$show = 'indi'; // SURN list makes no sense here
} elseif ($alpha === '@') {
	$show_all = 'no';
	$legend   = $UNKNOWN_NN;
	$url      = WT_SCRIPT_NAME . '?alpha=' . rawurlencode($alpha) . '&amp;ged=' . WT_GEDURL;
	$show     = 'indi'; // SURN list makes no sense here
} elseif ($alpha === ',') {
	$show_all = 'no';
	$legend   = I18N::translate('None');
	$url      = WT_SCRIPT_NAME . '?alpha=' . rawurlencode($alpha) . '&amp;ged=' . WT_GEDURL;
	$show     = 'indi'; // SURN list makes no sense here
} elseif ($alpha) {
	$show_all = 'no';
	$legend   = Filter::escapeHtml($alpha) . '…';
	$url      = WT_SCRIPT_NAME . '?alpha=' . rawurlencode($alpha) . '&amp;ged=' . WT_GEDURL;
	$show     = Filter::get('show', 'surn|indi', 'surn');
} else {
	$show_all = 'no';
	$legend   = '…';
	$url      = WT_SCRIPT_NAME . '?ged=' . WT_GEDURL;
	$show     = 'none'; // Don't show lists until something is chosen
}
$legend = '<span dir="auto">' . $legend . '</span>';

$controller
	->setPageTitle(I18N::translate('Individuals') . ' : ' . $legend)
	->pageHeader();

echo '<h2 class="center">', I18N::translate('Individuals'), '</h2>';

// Print a selection list of initial letters
$list = array();
foreach (QueryName::surnameAlpha($show_marnm === 'yes', false, WT_GED_ID) as $letter => $count) {
	switch ($letter) {
	case '@':
		$html = $UNKNOWN_NN;
		break;
	case ',':
		$html = I18N::translate('None');
		break;
	default:
		$html = Filter::escapeHtml($letter);
		break;
	}
	if ($count) {
		if ($letter == $alpha) {
			$list[] = '<a href="' . WT_SCRIPT_NAME . '?alpha=' . rawurlencode($letter) . '&amp;ged=' . WT_GEDURL . '" class="warning" title="' . I18N::number($count) . '">' . $html . '</a>';
		} else {
			$list[] = '<a href="' . WT_SCRIPT_NAME . '?alpha=' . rawurlencode($letter) . '&amp;ged=' . WT_GEDURL . '" title="' . I18N::number($count) . '">' . $html . '</a>';
		}
	} else {
		$list[] = $html;
	}
}

// Search spiders don't get the "show all" option as the other links give them everything.
if (!Auth::isSearchEngine()) {
	if ($show_all === 'yes') {
		$list[] = '<span class="warning">' . I18N::translate('All') . '</span>';
	} else {
		$list[] = '<a href="' . WT_SCRIPT_NAME . '?show_all=yes' . '&amp;ged=' . WT_GEDURL . '">' . I18N::translate('All') . '</a>';
	}
}
echo '<p class="center alpha_index">', join(' | ', $list), '</p>';

// Search spiders don't get an option to show/hide the surname sublists,
// nor does it make sense on the all/unknown/surname views
if (!Auth::isSearchEngine()) {
	echo '<p class="center">';
	if ($show !== 'none') {
		if ($show_marnm === 'yes') {
			echo '<a href="', $url, '&amp;show=' . $show . '&amp;show_marnm=no">', I18N::translate('Exclude individuals with “%s” as a married name', $legend), '</a>';
		} else {
			echo '<a href="', $url, '&amp;show=' . $show . '&amp;show_marnm=yes">', I18N::translate('Include individuals with “%s” as a married name', $legend), '</a>';
		}

		if ($alpha !== '@' && $alpha !== ',' && !$surname) {
			if ($show === 'surn') {
				echo '<br><a href="', $url, '&amp;show=indi">', I18N::translate('Show the list of individuals'), '</a>';
			} else {
				echo '<br><a href="', $url, '&amp;show=surn">', I18N::translate('Show the list of surnames'), '</a>';
			}
		}
	}
	echo '</p>';
}

if ($show === 'indi' || $show === 'surn') {
	$surns = QueryName::surnames($surname, $alpha, $show_marnm === 'yes', false, WT_GED_ID);
	if ($show === 'surn') {
		// Show the surname list
		switch ($WT_TREE->getPreference('SURNAME_LIST_STYLE')) {
		case 'style1';
			echo format_surname_list($surns, 3, true, WT_SCRIPT_NAME, $WT_TREE);
			break;
		case 'style3':
			echo format_surname_tagcloud($surns, WT_SCRIPT_NAME, true);
			break;
		case 'style2':
		default:
			echo format_surname_table($surns, WT_SCRIPT_NAME);
			break;
		}
	} else {
		// Show the list
		$count = 0;
		foreach ($surns as $surnames) {
			foreach ($surnames as $list) {
				$count += count($list);
			}
		}
		// Don't sublists short lists.
		if ($count < $WT_TREE->getPreference('SUBLIST_TRIGGER_I')) {
			$falpha              = '';
			$show_all_firstnames = 'no';
		} else {
			$givn_initials = QueryName::givenAlpha($surname, $alpha, $show_marnm === 'yes', false, WT_GED_ID);
			// Break long lists by initial letter of given name
			if ($surname || $show_all === 'yes') {
				// Don't show the list until we have some filter criteria
				$show = ($falpha || $show_all_firstnames === 'yes') ? 'indi' : 'none';
				$list = array();
				foreach ($givn_initials as $givn_initial => $count) {
					switch ($givn_initial) {
					case '@':
						$html = $UNKNOWN_PN;
						break;
					default:
						$html = Filter::escapeHtml($givn_initial);
						break;
					}
					if ($count) {
						if ($show === 'indi' && $givn_initial === $falpha && $show_all_firstnames === 'no') {
							$list[] = '<a class="warning" href="' . $url . '&amp;falpha=' . rawurlencode($givn_initial) . '" title="' . I18N::number($count) . '">' . $html . '</a>';
						} else {
							$list[] = '<a href="' . $url . '&amp;falpha=' . rawurlencode($givn_initial) . '" title="' . I18N::number($count) . '">' . $html . '</a>';
						}
					} else {
						$list[] = $html;
					}
				}
				// Search spiders don't get the "show all" option as the other links give them everything.
				if (!Auth::isSearchEngine()) {
					if ($show_all_firstnames === 'yes') {
						$list[] = '<span class="warning">' . I18N::translate('All') . '</span>';
					} else {
						$list[] = '<a href="' . $url . '&amp;show_all_firstnames=yes">' . I18N::translate('All') . '</a>';
					}
				}
				if ($show_all === 'no') {
					echo '<h2 class="center">', I18N::translate('Individuals with surname %s', $legend), '</h2>';
				}
				echo '<p class="center alpha_index">', join(' | ', $list), '</p>';
			}
		}
		if ($show === 'indi') {
			echo format_indi_table(QueryName::individuals($surname, $alpha, $falpha, $show_marnm === 'yes', false, WT_GED_ID));
		}
	}
}
