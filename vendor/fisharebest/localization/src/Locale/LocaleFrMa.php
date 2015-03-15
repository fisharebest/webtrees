<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrMa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMa extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMa;
	}
}
