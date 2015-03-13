<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrRw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrRw extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryRw;
	}
}
