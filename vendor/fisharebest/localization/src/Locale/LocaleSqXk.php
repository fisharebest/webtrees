<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSqXk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSqXk extends LocaleSq {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryXk;
	}
}
