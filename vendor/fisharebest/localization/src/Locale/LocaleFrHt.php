<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrHt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrHt extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryHt;
	}
}
