<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnTk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnTk extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryTk;
	}
}
