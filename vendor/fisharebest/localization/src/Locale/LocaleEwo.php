<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEwo - Ewondo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEwo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ewondo';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'EWONDO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEwo;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
