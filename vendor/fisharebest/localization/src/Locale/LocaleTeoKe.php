<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTeoKe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTeoKe extends LocaleTeo {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryKe;
	}
}
