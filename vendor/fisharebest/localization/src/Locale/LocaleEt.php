<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEt;

/**
 * Class LocaleEt - Estonian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEt extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'estonian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'eesti';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'EESTI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEt;
	}

	/** {@inheritdoc} */
	protected function minimumGroupingDigits() {
		return 3;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
