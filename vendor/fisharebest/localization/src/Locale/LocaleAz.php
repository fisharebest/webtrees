<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAz - Azerbaijani
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAz extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'azərbaycan';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'AZERBAYCAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAz;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
