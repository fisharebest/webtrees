<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKu;

/**
 * Class LocaleKu - Kurdish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKu extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kurdî';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'KURDI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKu;
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
		return self::PERCENT . '%s';
	}
}
