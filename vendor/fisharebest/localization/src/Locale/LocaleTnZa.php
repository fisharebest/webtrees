<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTnZa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTnZa extends LocaleTn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryZa;
	}
}
