<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJbo;

/**
 * Class LocalePap - Lojban
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleJbo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Lojban';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LOJBAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageJbo;
	}
}
