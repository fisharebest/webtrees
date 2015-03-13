<?php namespace Fisharebest\Localization;

/**
 * Class LocaleCaIt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCaIt extends LocaleCa {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryIt;
	}
}
