<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSoDj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSoDj extends LocaleSo {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryDj;
	}
}
