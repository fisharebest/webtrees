<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAaEr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAaEr extends LocaleAa {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryEr;
	}
}
