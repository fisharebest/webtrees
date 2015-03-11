<?php namespace Fisharebest\Localization;

/**
 * Class LocaleZhHansMo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHansMo extends LocaleZhHans {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMo;
	}
}
