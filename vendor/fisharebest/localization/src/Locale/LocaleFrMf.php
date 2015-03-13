<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrMf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMf extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMf;
	}
}
