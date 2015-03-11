<?php namespace Fisharebest\Localization;

/**
 * Class LocaleGswFr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGswFr extends LocaleGsw {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryFr;
	}
}
