<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrYt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrYt extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryYt;
	}
}
