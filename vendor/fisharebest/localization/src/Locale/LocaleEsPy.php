<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsPy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsPy extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPy;
	}
}
