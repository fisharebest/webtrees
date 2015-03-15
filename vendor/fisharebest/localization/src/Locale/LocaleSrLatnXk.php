<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSrLatnXk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrLatnXk extends LocaleSrLatn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryXk;
	}
}
