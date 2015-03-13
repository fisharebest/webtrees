<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRuBy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRuBy extends LocaleRu {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBy;
	}
}
