<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMk;

/**
 * Class LocaleMk - Macedonian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMk extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'македонски';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'МАКЕДОНСКИ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMk;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
