<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrMq
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMq extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMq;
	}
}
