<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrMg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMg extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMg;
	}
}
