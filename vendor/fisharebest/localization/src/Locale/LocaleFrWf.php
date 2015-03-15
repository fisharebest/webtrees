<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFrWf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrWf extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryWf;
	}
}
