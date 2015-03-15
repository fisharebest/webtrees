<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrRe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrRe extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryRe;
	}
}
