<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnIo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnIo extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryIo;
	}
}
