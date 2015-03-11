<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRn - Rundi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRn extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ikirundi';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'IKIRUNDI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRn;
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
