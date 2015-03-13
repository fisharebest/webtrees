<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBnIn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBnIn extends LocaleBn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryIn;
	}
}
