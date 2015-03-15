<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFfMr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFfMr extends LocaleFf {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMr;
	}
}
