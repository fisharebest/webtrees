<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLv;

/**
 * Class LocaleLv - Latvian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLv extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'latvian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'latviešu';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LATVIESU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLv;
	}

	/** {@inheritdoc} */
	protected function minimumGroupingDigits() {
		return 3;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
