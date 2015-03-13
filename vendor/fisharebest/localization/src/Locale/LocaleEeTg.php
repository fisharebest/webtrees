<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEeTg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEeTg extends LocaleEe {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryTg;
	}
}
