<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrMu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMu extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMu;
	}
}
