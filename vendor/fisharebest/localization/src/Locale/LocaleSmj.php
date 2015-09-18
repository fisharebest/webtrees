<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSmj;

/**
 * Class LocaleSmj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSmj extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'julevsámegiella';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'JULEVSAMEGIELLA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSmj;
	}
}
