<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeName;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the individual page.
 */
class IndividualController extends AbstractBaseController {
	// Do not show these facts in the expanded chart boxes.
	const EXCLUDE_CHART_FACTS = [
		'ADDR',
		'ALIA',
		'ASSO',
		'CHAN',
		'CHIL',
		'EMAIL',
		'FAMC',
		'FAMS',
		'HUSB',
		'NAME',
		'NOTE',
		'OBJE',
		'PHON',
		'RESI',
		'RESN',
		'SEX',
		'SOUR',
		'SSN',
		'SUBM',
		'TITL',
		'URL',
		'WIFE',
		'WWW',
		'_EMAIL',
		'_TODO',
		'_UID',
		'_WT_OBJE_SORT',
	];

	/**
	 * Show a individual's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function show(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, false);

		// What is (was) the age of the individual
		$bdate = $individual->getBirthDate();
		$ddate = $individual->getDeathDate();
		if ($bdate->isOK() && !$individual->isDead()) {
			// If living display age
			$age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, new Date(strtoupper(date('d M Y'))))) . ')';
		} elseif ($bdate->isOK() && $ddate->isOK()) {
			// If dead, show age at death
			$age = ' (' . I18N::translate('age') . ' ' . FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($bdate, $ddate)) . ')';
		} else {
			$age = '';
		}

		// What images are linked to this individual
		$individual_media = [];
		foreach ($individual->getFacts() as $fact) {
			$media_object = $fact->getTarget();
			if ($media_object instanceof Media) {
				$individual_media[] = $media_object->firstImageFile();
			}
		}
		$individual_media = array_filter($individual_media);

		$name_records = [];
		foreach ($individual->getFacts('NAME') as $n => $name_fact) {
			$name_records[] = $this->formatNameRecord($n, $name_fact);
		}

		$sex_records = [];
		foreach ($individual->getFacts('SEX') as $n => $sex_fact) {
			$sex_records[] = $this->formatSexRecord($sex_fact);
		}

		// If this individual is linked to a user account, show the link
		$user_link = '';
		if (Auth::isAdmin()) {
			$user = User::findByIndividual($individual);
			if ($user) {
				$user_link = ' —  <a href="' . e(route('admin-users', ['filter' => $user->getEmail()])) . '">' . e($user->getUserName()) . '</a>';
			};
		}

		return $this->viewResponse('individual-page', [
			'age'              => $age,
			'count_media'      => $this->countFacts($individual, 'OBJE'),
			'count_names'      => $this->countFacts($individual, 'NAME'),
			'count_sex'        => $this->countFacts($individual, 'SEX'),
			'individual'       => $individual,
			'individual_media' => $individual_media,
			'meta_robots'      => 'index,follow',
			'name_records'     => $name_records,
			'sex_records'      => $sex_records,
			'sidebars'         => $this->getSidebars($individual),
			'tabs'             => $this->getTabs($individual),
			'significant'      => $this->significant($individual),
			'title'            => $individual->getFullName() . ' ' . $individual->getLifeSpan(),
			'user_link'        => $user_link,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function tab(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = Individual::getInstance($xref, $tree);
		$tab    = $request->get('module');
		$tabs   = Module::getActiveTabs($tree);

		if ($record === null || !array_key_exists($tab, $tabs)) {
			return new Response('', Response::HTTP_NOT_FOUND);
		} elseif (!$record->canShow()) {
			return new Response('', Response::HTTP_FORBIDDEN);
		} else {
			$tab = $tabs[$tab];

			$layout = view('layouts/ajax', [
				'content' => $tab->getTabContent($record),
			]);

			return new Response($layout);
		}
	}

	/**
	 * Show additional details for a chart box.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function expandChartBox(Request $request): Response {
		/** @var Tree $tree */
		$tree       = $request->attributes->get('tree');
		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual, false);

		$facts = $individual->getFacts();
		foreach ($individual->getSpouseFamilies() as $family) {
			foreach ($family->getFacts() as $fact) {
				$facts[] = $fact;
			}
		}
		Functions::sortFacts($facts);

		$facts = array_filter($facts, function (Fact $fact) {
			return !in_array($fact->getTag(), self::EXCLUDE_CHART_FACTS);
		});

		$html = view('expand-chart-box', [
			'facts' => $facts,
		]);

		return new Response($html);
	}

	/**
	 * Count the (non-pending-delete) name records for an individual.
	 *
	 * @param Individual $individual
	 * @param string     $fact_name
	 *
	 * @return int
	 */
	private function countFacts(Individual $individual, $fact_name): int {
		$count = 0;

		foreach ($individual->getFacts($fact_name) as $fact) {
			if (!$fact->isPendingDeletion()) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Format a name record
	 *
	 * @param int  $n
	 * @param Fact $fact
	 *
	 * @return string
	 */
	private function formatNameRecord($n, Fact $fact) {
		$individual = $fact->getParent();

		// Create a dummy record, so we can extract the formatted NAME value from it.
		$dummy = new Individual(
			'xref',
			"0 @xref@ INDI\n1 DEAT Y\n" . $fact->getGedcom(),
			null,
			$individual->getTree()
		);
		$dummy->setPrimaryName(0); // Make sure we use the name from "1 NAME"

		$container_class = 'card';
		$content_class   = 'collapse';
		$aria            = 'false';

		if ($n === 0) {
			$content_class = 'collapse show';
			$aria          = 'true';
		}
		if ($fact->isPendingDeletion()) {
			$container_class .= ' old';
		} elseif ($fact->isPendingAddition()) {
			$container_class .= ' new';
		}

		ob_start();
		echo '<dl><dt class="label">', I18N::translate('Name'), '</dt>';
		echo '<dd class="field">', $dummy->getFullName(), '</dd>';
		$ct = preg_match_all('/\n2 (\w+) (.*)/', $fact->getGedcom(), $nmatch, PREG_SET_ORDER);
		for ($i = 0; $i < $ct; $i++) {
			$tag = $nmatch[$i][1];
			if ($tag != 'SOUR' && $tag != 'NOTE' && $tag != 'SPFX') {
				echo '<dt class="label">', GedcomTag::getLabel($tag, $individual), '</dt>';
				echo '<dd class="field">'; // Before using dir="auto" on this field, note that Gecko treats this as an inline element but WebKit treats it as a block element
				if (isset($nmatch[$i][2])) {
					$name = e($nmatch[$i][2]);
					$name = str_replace('/', '', $name);
					$name = preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $name);
					switch ($tag) {
						case 'TYPE':
							echo GedcomCodeName::getValue($name, $individual);
							break;
						case 'SURN':
							// The SURN field is not necessarily the surname.
							// Where it is not a substring of the real surname, show it after the real surname.
							$surname = e($dummy->getAllNames()[0]['surname']);
							$surns   = preg_replace('/, */', ' ', $nmatch[$i][2]);
							if (strpos($dummy->getAllNames()[0]['surname'], $surns) !== false) {
								echo '<span dir="auto">' . $surname . '</span>';
							} else {
								echo I18N::translate('%1$s (%2$s)', '<span dir="auto">' . $surname . '</span>', '<span dir="auto">' . $name . '</span>');
							}
							break;
						default:
							echo '<span dir="auto">' . $name . '</span>';
							break;
					}
				}
				echo '</dd>';
				echo '</dl>';
			}
		}
		if (preg_match("/\n2 SOUR/", $fact->getGedcom())) {
			echo '<div id="indi_sour" class="clearfloat">', FunctionsPrintFacts::printFactSources($fact->getGedcom(), 2), '</div>';
		}
		if (preg_match("/\n2 NOTE/", $fact->getGedcom())) {
			echo '<div id="indi_note" class="clearfloat">', FunctionsPrint::printFactNotes($fact->getGedcom(), 2), '</div>';
		}
		$content = ob_get_clean();

		if ($individual->canEdit() && !$fact->isPendingDeletion()) {
			$edit_links =
				FontAwesome::linkIcon('delete', I18N::translate('Delete this name'), [
					'class'   => 'btn btn-link',
					'href'    => '#',
					'onclick' => 'return delete_fact("' . I18N::translate('Are you sure you want to delete this fact?') . '", "' . e($individual->getTree()->getName()) . '", "' . e($individual->getXref()) . '", "' . $fact->getFactId() . '");',
				]) .
				FontAwesome::linkIcon('edit', I18N::translate('Edit the name'), [
					'class' => 'btn btn-link',
					'href'  => 'edit_interface.php?action=editname&xref=' . $individual->getXref() . '&fact_id=' . $fact->getFactId() . '&ged=' . e($individual->getTree()->getName()),
				]);
		} else {
			$edit_links = '';
		}

		return '
			<div class="' . $container_class . '">
        <div class="card-header" role="tab" id="name-header-' . $n . '">
		        <a data-toggle="collapse" data-parent="#individual-names" href="#name-content-' . $n . '" aria-expanded="' . $aria . '" aria-controls="name-content-' . $n . '">' . $dummy->getFullName() . '</a>
		      ' . $edit_links . '
        </div>
		    <div id="name-content-' . $n . '" class="' . $content_class . '" role="tabpanel" aria-labelledby="name-header-' . $n . '">
		      <div class="card-body">' . $content . '</div>
        </div>
      </div>';
	}

	/**
	 * print information for a sex record
	 *
	 * @param Fact $fact
	 *
	 * @return string
	 */
	private function formatSexRecord(Fact $fact) {
		$individual = $fact->getParent();

		switch ($fact->getValue()) {
			case 'M':
				$sex = I18N::translate('Male');
				break;
			case 'F':
				$sex = I18N::translate('Female');
				break;
			default:
				$sex = I18N::translateContext('unknown gender', 'Unknown');
				break;
		}

		$container_class = 'card';
		if ($fact->isPendingDeletion()) {
			$container_class .= ' old';
		} elseif ($fact->isPendingAddition()) {
			$container_class .= ' new';
		}

		if ($individual->canEdit() && !$fact->isPendingDeletion()) {
			$edit_links = FontAwesome::linkIcon('edit', I18N::translate('Edit the gender'), [
				'class' => 'btn btn-link',
				'href'  => 'edit_interface.php?action=edit&xref=' . $individual->getXref() . '&fact_id=' . $fact->getFactId() . '&ged=' . e($individual->getTree()->getName()),
			]);
		} else {
			$edit_links = '';
		}

		return '
		<div class="' . $container_class . '">
			<div class="card-header" role="tab" id="name-header-add">
				<div class="card-title mb-0">
					<b>' . I18N::translate('Gender') . '</b> ' . $sex . $edit_links . '
				</div>
			</div>
		</div>';
	}

	/**
	 * Which tabs should we show on this individual's page.
	 * We don't show empty tabs.
	 *
	 * @param Individual $individual
	 *
	 * @return ModuleTabInterface[]
	 */
	public function getSidebars(Individual $individual) {
		$sidebars = Module::getActiveSidebars($individual->getTree());

		return array_filter($sidebars, function (ModuleSidebarInterface $sidebar) use ($individual) {
			return $sidebar->hasSidebarContent($individual);
		});
	}

	/**
	 * Which tabs should we show on this individual's page.
	 * We don't show empty tabs.
	 *
	 * @param Individual $individual
	 *
	 * @return ModuleTabInterface[]
	 */
	public function getTabs(Individual $individual) {
		$tabs = Module::getActiveTabs($individual->getTree());

		return array_filter($tabs, function (ModuleTabInterface $tab) use ($individual) {
			return $tab->hasTabContent($individual);
		});
	}

	/**
	 * What are the significant elements of this page?
	 * The layout will need them to generate URLs for charts and reports.
	 *
	 * @param Individual $individual
	 *
	 * @return stdClass
	 */
	private function significant(Individual $individual) {
		$significant = (object) [
			'family'     => null,
			'individual' => $individual,
			'surname'    => '',
		];

		list($significant->surname) = explode(',', $individual->getSortName());

		foreach ($individual->getChildFamilies() + $individual->getSpouseFamilies() as $family) {
			$significant->family = $family;
			break;
		}

		return $significant;
	}
}
