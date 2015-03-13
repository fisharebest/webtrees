<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSoKe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSoKe extends LocaleSo {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryKe;
	}
}
