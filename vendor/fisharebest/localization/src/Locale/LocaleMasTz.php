<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMasTz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMasTz extends LocaleMas {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryTz;
	}
}
