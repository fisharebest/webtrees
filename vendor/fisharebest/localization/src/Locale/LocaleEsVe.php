<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsVe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsVe extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryVe;
	}
}
