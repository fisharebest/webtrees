<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAf - Afrikaans
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAf extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Afrikaans';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'AFRIKAANS';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAf;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
