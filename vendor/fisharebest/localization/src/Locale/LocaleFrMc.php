<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrMc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMc extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMc;
	}
}
