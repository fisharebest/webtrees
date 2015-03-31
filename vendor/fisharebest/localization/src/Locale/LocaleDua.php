<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDua;

/**
 * Class LocaleDua - Duala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDua extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'duálá';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'DUALA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageDua;
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
