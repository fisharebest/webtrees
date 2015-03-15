<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSoEt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSoEt extends LocaleSo {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryEt;
	}
}
