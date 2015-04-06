<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTr;

/**
 * Class LocaleTr - Turkish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'turkish_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'Türkçe';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'TURKCE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTr;
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
		return self::PERCENT . '%s';
	}
}
