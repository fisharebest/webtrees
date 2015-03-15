<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRu - Russian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRu extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'русский';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'РУССКИЙ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRu;
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
