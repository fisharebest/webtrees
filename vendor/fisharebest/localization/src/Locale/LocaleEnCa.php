<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCa;

/**
 * Class LocaleEnCa - Canadian English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCa extends LocaleEn {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Canadian English';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ENGLISH, CANADIAN';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCa;
	}
}
