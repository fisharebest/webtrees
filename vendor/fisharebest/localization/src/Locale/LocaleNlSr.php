<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNlSr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlSr extends LocaleNl {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySr;
	}
}
