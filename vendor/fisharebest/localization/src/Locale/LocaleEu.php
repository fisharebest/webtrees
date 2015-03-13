<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEu - Basque
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEu extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'euskara';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'EUSKARA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEu;
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
		return self::PERCENT . self::NBSP . '%s';
	}
}
