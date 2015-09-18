<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKcg;

/**
 * Class LocaleKcg - Tyap
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKcg extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Tyap';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'TYAP';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKcg;
	}
}
