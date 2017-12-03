<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\IndividualListController;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

require 'includes/session.php';

$controller = new IndividualListController;

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
		Auth::user()->setPreference('indilist.php_show_marnm', $show_marnm);
		break;
	default:
		$show_marnm = Auth::user()->getPreference('indilist.php_show_marnm');
}

// Make sure selections are consistent.
// i.e. can’t specify show_all and surname at the same time.
if ($show_all === 'yes') {
	if ($show_all_firstnames === 'yes') {
		$alpha   = '';
		$surname = '';
		$legend  = I18N::translate('All');
		$url     = '?show_all=yes&amp;ged=' . $controller->tree()->getNameUrl();
		$show    = 'indi';
	} elseif ($falpha) {
		$alpha   = '';
		$surname = '';
		$legend  = I18N::translate('All') . ', ' . Html::escape($falpha) . '…';
		$url     = '?show_all=yes&amp;ged=' . $controller->tree()->getNameUrl();
		$show    = 'indi';
	} else {
		$alpha   = '';
		$surname = '';
		$legend  = I18N::translate('All');
		$url     = '?show_all=yes' . '&amp;ged=' . $controller->tree()->getNameUrl();
		$show    = Filter::get('show', 'surn|indi', 'surn');
	}
} elseif ($surname) {
	$alpha    = $controller->initialLetter($surname); // so we can highlight the initial letter
	$show_all = 'no';
	if ($surname === '@N.N.') {
		$legend = I18N::translateContext('Unknown surname', '…');
	} else {
		// The surname parameter is a root/canonical form.
		// Display it as the actual surname
		$legend = implode('/', array_keys($controller->surnames($surname, $alpha, $show_marnm === 'yes', false)));
	}
	$url = '?surname=' . rawurlencode($surname) . '&amp;ged=' . $controller->tree()->getNameUrl();
	switch ($falpha) {
		case '':
			break;
		case '@':
			$legend .= ', ' . I18N::translateContext('Unknown given name', '…');
			$url .= '&amp;falpha=' . rawurlencode($falpha) . '&amp;ged=' . $controller->tree()->getNameUrl();
			break;
		default:
			$legend .= ', ' . Html::escape($falpha) . '…';
			$url .= '&amp;falpha=' . rawurlencode($falpha) . '&amp;ged=' . $controller->tree()->getNameUrl();
			break;
	}
	$show = 'indi'; // SURN list makes no sense here
} elseif ($alpha === '@') {
	$show_all = 'no';
	$legend   = I18N::translateContext('Unknown surname', '…');
	$url      = '?alpha=' . rawurlencode($alpha) . '&amp;ged=' . $controller->tree()->getNameUrl();
	$show     = 'indi'; // SURN list makes no sense here
} elseif ($alpha === ',') {
	$show_all = 'no';
	$legend   = I18N::translate('None');
	$url      = '?alpha=' . rawurlencode($alpha) . '&amp;ged=' . $controller->tree()->getNameUrl();
	$show     = 'indi'; // SURN list makes no sense here
} elseif ($alpha) {
	$show_all = 'no';
	$legend   = Html::escape($alpha) . '…';
	$url      = '?alpha=' . rawurlencode($alpha) . '&amp;ged=' . $controller->tree()->getNameUrl();
	$show     = Filter::get('show', 'surn|indi', 'surn');
} else {
	$show_all = 'no';
	$legend   = '…';
	$url      = '?ged=' . $controller->tree()->getNameUrl();
	$show     = 'none'; // Don't show lists until something is chosen
}
$legend = '<span dir="auto">' . $legend . '</span>';

$controller
	->setPageTitle(I18N::translate('Individuals') . ' : ' . $legend)
	->pageHeader();

?>
<h2 class="wt-page-title"><?= I18N::translate('Individuals') ?></h2>

<div class="d-flex flex-column wt-page-options wt-page-options-individual-list d-print-none">
	<ul class="d-flex flex-wrap wt-initials-list">

	<?php
foreach ($controller->surnameAlpha($show_marnm === 'yes', false) as $letter => $count) {
	echo '<li class="wt-initials-list-item">';
	if ($count > 0) {
		echo '<a href="?alpha=' . rawurlencode($letter) . '&amp;ged=' . $controller->tree()->getNameUrl() . '" class="wt-initial' . ($letter === $alpha ? ' active' : '') . '" title="' . I18N::number($count) . '">' . $controller->surnameInitial($letter) . '</a>';
	} else {
		echo '<span class="wt-initial text-muted">' . $controller->surnameInitial($letter) . '</span>';
	}
	echo '</li>';
}

// Search spiders don't get the "show all" option as the other links give them everything.
if (Session::has('initiated')) {
	echo '<li class="wt-initials-list-item">';
	echo '<a class="wt-initial' . ($show_all === 'yes' ? ' active' : '') . '" href="?show_all=yes' . '&amp;ged=' . $controller->tree()->getNameUrl() . '">';
	echo I18N::translate('All');
	echo '</a>';
	echo '</li>';
}
echo '</ul>';

// Search spiders don't get an option to show/hide the surname sublists,
// nor does it make sense on the all/unknown/surname views
if (Session::has('initiated') && $show !== 'none') {
	if ($show_marnm === 'yes') {
		echo '<p><a href="', $url, '&amp;show=' . $show . '&amp;show_marnm=no">', I18N::translate('Exclude individuals with “%s” as a married name', $legend), '</a></p>';
	} else {
		echo '<p><a href="', $url, '&amp;show=' . $show . '&amp;show_marnm=yes">', I18N::translate('Include individuals with “%s” as a married name', $legend), '</a></p>';
	}

	if ($alpha !== '@' && $alpha !== ',' && !$surname) {
		if ($show === 'surn') {
			echo '<p><a href="', $url, '&amp;show=indi">', I18N::translate('Show the list of individuals'), '</a></p>';
		} else {
			echo '<p><a href="', $url, '&amp;show=surn">', I18N::translate('Show the list of surnames'), '</a></p>';
		}
	}
}

?>
</div>
<div class="wt-page-content">
	<?php

if ($show === 'indi' || $show === 'surn') {
	$surns = $controller->surnames($surname, $alpha, $show_marnm === 'yes', false);
	if ($show === 'surn') {
		// Show the surname list
		switch ($controller->tree()->getPreference('SURNAME_LIST_STYLE')) {
			case 'style1':
				echo FunctionsPrintLists::surnameList($surns, 3, true, 'indilist.php', $controller->tree());
				break;
			case 'style3':
				echo FunctionsPrintLists::surnameTagCloud($surns, 'indilist.php', true, $controller->tree());
				break;
			case 'style2':
			default:
				echo FunctionsPrintLists::surnameTable($surns, 'indilist.php', $controller->tree());
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
		if ($count < $controller->tree()->getPreference('SUBLIST_TRIGGER_I')) {
			$falpha              = '';
			$show_all_firstnames = 'no';
		} else {
			$givn_initials = $controller->givenAlpha($surname, $alpha, $show_marnm === 'yes', false);
			// Break long lists by initial letter of given name
			if ($surname || $show_all === 'yes') {
				if ($show_all === 'no') {
					echo '<h2 class="wt-page-title">', I18N::translate('Individuals with surname %s', $legend), '</h2>';
				}
				// Don't show the list until we have some filter criteria
				$show = ($falpha || $show_all_firstnames === 'yes') ? 'indi' : 'none';
				$list = [];
				echo '<ul class="d-flex flex-wrap justify-content-center wt-initials-list">';
				foreach ($givn_initials as $givn_initial => $count) {
					echo '<li class="wt-initials-list-item">';
					if ($count > 0) {
						if ($show === 'indi' && $givn_initial === $falpha && $show_all_firstnames === 'no') {
							echo '<a class="wt-initial active" href="' . $url . '&amp;falpha=' . rawurlencode($givn_initial) . '" title="' . I18N::number($count) . '">' . $controller->givenNameInitial($givn_initial) . '</a>';
						} else {
							echo '<a class="wt-initial" href="' . $url . '&amp;falpha=' . rawurlencode($givn_initial) . '" title="' . I18N::number($count) . '">' . $controller->givenNameInitial($givn_initial) . '</a>';
						}
					} else {
						echo '<span class="wt-initial text-muted">' . $controller->givenNameInitial($givn_initial) . '</span>';
					}
					echo '</li>';
				}
				// Search spiders don't get the "show all" option as the other links give them everything.
				if (Session::has('initiated')) {
					echo '<li class="wt-initials-list-item">';
					if ($show_all_firstnames === 'yes') {
						echo '<span class="wt-initial warning">' . I18N::translate('All') . '</span>';
					} else {
						echo '<a class="wt-initial" href="' . $url . '&amp;show_all_firstnames=yes">' . I18N::translate('All') . '</a>';
					}
					echo '</li>';
				}
				echo '</ul>';
				echo '<p class="center alpha_index">', implode(' | ', $list), '</p>';
			}
		}
		if ($show === 'indi') {
			echo FunctionsPrintLists::individualTable($controller->individuals($surname, $alpha, $falpha, $show_marnm === 'yes', false));
		}
	}
}
?>
</div>
