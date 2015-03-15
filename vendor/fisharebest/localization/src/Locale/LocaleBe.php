<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBe - Belarusian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBe extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'беларуская';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'БЕЛАРУСКАЯ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
