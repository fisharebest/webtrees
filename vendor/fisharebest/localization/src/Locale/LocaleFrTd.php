<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrTd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrTd extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryTd;
	}
}
