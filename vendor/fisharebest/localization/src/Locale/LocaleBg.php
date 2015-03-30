<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBg;

/**
 * Class LocaleBg - Bulgarian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBg extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'български';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'БЪЛГАРСКИ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBg;
	}

	/** {@inheritdoc} */
	protected function minimumGroupingDigits() {
		return 2;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
