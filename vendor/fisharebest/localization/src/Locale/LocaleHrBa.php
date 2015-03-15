<?php namespace Fisharebest\Localization;

/**
 * Class LocaleHrBa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHrBa extends LocaleHr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBa;
	}
}
