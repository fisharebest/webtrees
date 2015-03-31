<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBs;

/**
 * Class LocaleBs - Bosnian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBs extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'bosanski';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'BOSANSKI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageBs;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
