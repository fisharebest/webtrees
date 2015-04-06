<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFr;

/**
 * Class LocaleFr - French
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'français';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'FRANCAIS';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageFr;
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
