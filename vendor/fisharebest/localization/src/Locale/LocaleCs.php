<?php namespace Fisharebest\Localization;

/**
 * Class LocaleCs - Czech
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCs extends Locale {
	/** {@inheritdoc} */
	public function collation() {
		return 'croatian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'čeština';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'CESTINA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCs;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
