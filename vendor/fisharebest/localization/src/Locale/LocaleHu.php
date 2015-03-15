<?php namespace Fisharebest\Localization;

/**
 * Class LocaleHu - Hungarian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHu extends Locale {
	/** {@inheritdoc} */
	public function collation() {
		return 'hungarian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'magyar';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'MAGYAR';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHu;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
