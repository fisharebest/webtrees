<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrMl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMl extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMl;
	}
}
