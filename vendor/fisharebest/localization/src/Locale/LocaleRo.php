<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRo;

/**
 * Class LocaleRo - Romanian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'romanian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'română';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ROMANA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRo;
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
