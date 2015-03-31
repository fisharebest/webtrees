<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLt;

/**
 * Class LocaleLt - Lithuanian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLt extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'lithuanian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'lietuvių';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LIETUVIU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLt;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::NBSP,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
