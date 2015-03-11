<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrBj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrBj extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBj;
	}
}
