<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBr;

/**
 * Class LocaleBr - Breton
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'brezhoneg';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'BREZHONEG';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBr;
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
