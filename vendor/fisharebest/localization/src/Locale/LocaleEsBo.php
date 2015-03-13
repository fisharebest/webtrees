<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsBo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsBo extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBo;
	}
}
