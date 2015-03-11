<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTaMy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTaMy extends LocaleTa {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 3;
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMy;
	}
}
