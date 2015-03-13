<?php namespace Fisharebest\Localization;

/**
 * Class LocaleDaGl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDaGl extends LocaleDa {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGl;
	}
}
