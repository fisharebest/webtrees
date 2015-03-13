<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrBl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrBl extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBl;
	}
}
