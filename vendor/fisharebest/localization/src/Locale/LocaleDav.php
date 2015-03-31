<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDav;

/**
 * Class LocaleDav - Taita
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDav extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kitaita';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KITAITA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageDav;
	}
}
