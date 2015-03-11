<?php namespace Fisharebest\Localization;

/**
 * Class LocaleOs - Ossetic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleOs extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ирон';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ИРОН';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageOs;
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryRu;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
