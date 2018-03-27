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
declare(strict_types = 1);

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Functions\FunctionsCharts;

class FactLocation extends Location {
	private $indi;
	private $fact;

	/**
	 * FactLocation constructor.
	 * @param Fact $fact
	 * @param Individual $indi
	 * @throws \Exception
	 */
	public function __construct(Fact $fact, Individual $indi) {
		$this->indi = $indi;
		$this->fact = $fact;
		parent::__construct($fact->getPlace()->getGedcomName());
		$coords = $this->getCoordsFromGedcom();
		if ($coords !== null) {
			// give priority to co-ordinates stored in gedcom
			$this->record->pl_lati = $coords->latitude;
			$this->record->pl_long = $coords->longitude;
		}
	}

	/**
	 * @param $datatype
	 * @param $sosa
	 * @return array
	 */
	public function shortSummary($datatype, $sosa) {
		$self        = $this->indi->getXref();
		$parent      = $this->fact->getParent();
		$name        = '';
		$url         = '';
		$tag         = $this->fact->getLabel();
		$addbirthtag = false;

		if ($parent instanceof Family) {
			//marriage
			$spouse = $parent->getSpouse($this->indi);
			if ($spouse) {
				$url  = $spouse->url();
				$name = $spouse->getFullName();
				$tag  = GedcomTag::getLabel('MARR');
			}
		} elseif ($parent->getXref() !== $self) {
			//birth of a child
			$url  = $parent->url();
			$name = $parent->getFullName();
			$tag  = GedcomTag::getLabel('_BIRT_CHIL', $parent);
		}
		if ($datatype == 'pedigree' && $sosa > 1) {
			$addbirthtag = true;
			$tag         = ucfirst(FunctionsCharts::getSosaName($sosa));
		}

		return [
			'tag'    => $tag,
			'url'    => $url,
			'name'   => $name,
			'value'  => $this->fact->getValue(),
			'date'   => $this->fact->getDate()->display(true),
			'place'  => $this->fact->getPlace(),
			'addtag' => $addbirthtag,
		];
	}

	/**
	 * @return array
	 */
	public function getIconDetails() {
		$tag = $this->fact->getTag();
		if (false !== stripos(WT_EVENTS_BIRT, $tag)) {
			$icon = [
				'color' => 'Crimson',
				'name'  => 'birthday-cake',
			];
		} elseif (false !== stripos(WT_EVENTS_MARR, $tag)) {
			$icon = [
				'color' => 'Green',
				'name'  => 'venus-mars',
			];
		} elseif (false !== stripos(WT_EVENTS_DEAT, $tag)) {
			$icon = [
				'color' => 'Black',
				'name'  => 'plus',
			];
		} elseif (false !== stripos('CENS', $tag)) {
			$icon = [
				'color' => 'MediumBlue',
				'name'  => 'users',
			];
		} elseif (false !== stripos('RESI', $tag)) {
			$icon = [
				'color' => 'MediumBlue',
				'name'  => 'home',
			];
		} elseif (false !== stripos('OCCU', $tag)) {
			$icon = [
				'color' => 'MediumBlue',
				'name'  => 'briefcase',
			];
		} elseif (false !== stripos('GRAD', $tag)) {
			$icon = [
				'color' => 'MediumBlue',
				'name'  => 'graduation-cap',
			];
		} elseif (false !== stripos('EDUC', $tag)) {
			$icon = [
				'color' => 'MediumBlue',
				'name'  => 'university',
			];
		} else {
			$icon = [
				'color' => 'Gold',
				'name'  => 'bullseye ',
			];
		}

		return $icon;
	}

	/**
	 * @return mixed
	 */
	public function toolTip() {
		return $this->fact->getPlace()->getGedcomName();
	}

	/**
	 * Populate this objects lat/lon values, if possible
	 */
	private function getCoordsFromGedcom() {
		$coords = null;
		if (!$this->fact->getPlace()->isEmpty()) {
			$gedcom = $this->fact->getGedcom();
			$f1     = preg_match("/\d LATI (.*)/", $gedcom, $match1);
			$f2     = preg_match("/\d LONG (.*)/", $gedcom, $match2);
			if ($f1 && $f2) {
				$coords = (object) [
					'latitude'  => $match1[1],
					'longitude' => $match2[1],
				];
			}
		}

		return $coords;
	}
}
