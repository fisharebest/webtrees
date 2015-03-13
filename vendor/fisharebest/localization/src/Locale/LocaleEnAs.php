<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnAs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnAs extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAs;
	}
}
