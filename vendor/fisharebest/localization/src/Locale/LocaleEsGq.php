<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsGq
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsGq extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGq;
	}
}
