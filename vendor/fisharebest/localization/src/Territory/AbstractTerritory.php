<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of a geographic area.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
abstract class AbstractTerritory {
	public function firstDay() {
		return 1;
	}

	public function measurementSystem() {
		return 'metric';
	}

	public function paperSize() {
		return 'A4';
	}

	public function weekendStart() {
		return 6;
	}

	public function weekendEnd() {
		return 0;
	}
}
