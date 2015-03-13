<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrGa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrGa extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGa;
	}
}
