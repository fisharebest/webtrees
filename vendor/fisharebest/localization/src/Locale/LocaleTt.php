<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTt - Tatar
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTt extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'татар';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ТАТАР';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTt;
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
