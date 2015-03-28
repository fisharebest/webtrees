<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AF - Afghanistan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAf extends AbstractTerritory implements TerritoryInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'AF';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 6;
	}

	/** {@inheritdoc} */
	public function weekendStart() {
		return 4;
	}

	/** {@inheritdoc} */
	public function weekendEnd() {
		return 5;
	}
}
