<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAaDj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAaDj extends LocaleAa {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryDj;
	}
}
