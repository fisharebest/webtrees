<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrPm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrPm extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPm;
	}
}
