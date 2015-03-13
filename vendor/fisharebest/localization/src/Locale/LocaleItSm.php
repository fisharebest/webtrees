<?php namespace Fisharebest\Localization;

/**
 * Class LocaleItSm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleItSm extends LocaleIt {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySm;
	}
}
