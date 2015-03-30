<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDe;

/**
 * Class LocaleDe - German
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'german2_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'Deutsch';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'DEUTSCH';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageDe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
