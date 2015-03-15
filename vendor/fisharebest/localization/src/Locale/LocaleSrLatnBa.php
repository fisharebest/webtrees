<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSrLatnBa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrLatnBa extends LocaleSrLatn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBa;
	}
}
