<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsCl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsCl extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCl;
	}
}
