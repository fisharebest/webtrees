<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSvAx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSvAx extends LocaleSv {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAx;
	}
}
