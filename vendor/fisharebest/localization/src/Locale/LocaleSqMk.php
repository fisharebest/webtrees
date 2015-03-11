<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSqMk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSqMk extends LocaleSq {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMk;
	}
}
