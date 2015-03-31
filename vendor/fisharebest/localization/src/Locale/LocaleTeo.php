<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTeo;

/**
 * Class LocaleTeo - Teso
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTeo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kiteso';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KITESO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTeo;
	}
}
