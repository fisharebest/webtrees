<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBoIn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBoIn extends LocaleBo {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryIn;
	}
}
