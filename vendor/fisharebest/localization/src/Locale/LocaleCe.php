<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCe;

/**
 * Class LocaleCe - Chechen
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Нохчийн мотт';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'НОХЧИЙН МОТТ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageCe;
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
