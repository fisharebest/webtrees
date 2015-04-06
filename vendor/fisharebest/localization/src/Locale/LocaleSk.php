<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSk;

/**
 * Class LocaleSk - Slovak
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSk extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'slovak_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'slovenčina';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'SLOVENCINA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSk;
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
