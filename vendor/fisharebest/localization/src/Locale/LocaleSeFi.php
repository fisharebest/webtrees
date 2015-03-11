<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSeFi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSeFi extends LocaleSe {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryFi;
	}
}
