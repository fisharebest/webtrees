<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEs;
use Fisharebest\Localization\Territory\TerritoryEs;

/**
 * Class LocaleEs - Spanish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEs extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function collation() {
		return 'spanish_ci';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'español';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'ESPANOL';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEs;
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryEs;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
