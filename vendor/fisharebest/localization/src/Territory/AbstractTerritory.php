<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of a geographic area.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
abstract class AbstractTerritory {
	/** {@inheritdoc} */
	public function firstDay() {
		return 1;
	}

	/** {@inheritdoc} */
	public function measurementSystem() {
		return 'metric';
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'A4';
	}

	/** {@inheritdoc} */
	public function weekendStart() {
		return 6;
	}

	/** {@inheritdoc} */
	public function weekendEnd() {
		return 0;
	}
}
