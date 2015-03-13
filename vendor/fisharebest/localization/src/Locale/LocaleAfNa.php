<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAfNa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAfNa extends LocaleAf {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryNa;
	}
}
