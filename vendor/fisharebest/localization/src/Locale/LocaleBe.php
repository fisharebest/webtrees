<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBe;

/**
 * Class LocaleBe - Belarusian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'беларуская';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'БЕЛАРУСКАЯ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
