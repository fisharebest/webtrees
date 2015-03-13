<?php namespace Fisharebest\Localization;

/**
 * Class LocaleZhHansSg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHansSg extends LocaleZhHans {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySg;
	}
}
