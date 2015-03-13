<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSeh - Sena
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSeh extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'sena';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SENA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSeh;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
