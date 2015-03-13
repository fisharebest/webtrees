<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrGn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrGn extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGn;
	}
}
