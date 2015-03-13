<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNlSx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlSx extends LocaleNl {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySx;
	}
}
