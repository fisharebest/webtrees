<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrNc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrNc extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryNc;
	}
}
