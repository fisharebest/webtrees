<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCo;

/**
 * Class LocaleEsCo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsCo extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCo;
	}
}
