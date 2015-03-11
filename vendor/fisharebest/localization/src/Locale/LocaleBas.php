<?php namespace Fisharebest\Localization;

/**
 * Class LocaleBas - Basaa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBas extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ɓàsàa';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'BASAA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBas;
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
