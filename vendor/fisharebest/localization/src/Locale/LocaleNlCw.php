<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNlCw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlCw extends LocaleNl {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCw;
	}
}
