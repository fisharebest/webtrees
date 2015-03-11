<?php namespace Fisharebest\Localization;

/**
 * Class LocaleDeBe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDeBe extends LocaleDe {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBe;
	}
}
