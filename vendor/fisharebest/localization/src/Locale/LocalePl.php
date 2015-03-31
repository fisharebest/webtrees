<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePl;

/**
 * Class LocalePl - Polish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePl extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'polish_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'polski';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'POLSKI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguagePl;
	}

	/** {@inheritdoc} */
	protected function minimumGroupingDigits() {
		return 2;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
