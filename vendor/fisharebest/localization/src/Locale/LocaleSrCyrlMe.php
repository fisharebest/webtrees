<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSrCyrlMe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrCyrlMe extends LocaleSrCyrl {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMe;
	}
}
