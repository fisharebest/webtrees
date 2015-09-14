<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePrg;

/**
 * Class LocalePrg - Old Prussian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePrg extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'latvian_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'Old Prussian';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'OLD PRUSSIAN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguagePrg;
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
