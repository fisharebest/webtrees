<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSl - Slovenian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSl extends Locale {
	/** {@inheritdoc} */
	public function collation() {
		return 'slovenian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'slovenščina';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SLOVENSCINA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSl;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
