<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNeIn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNeIn extends LocaleNe {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryIn;
	}
}
