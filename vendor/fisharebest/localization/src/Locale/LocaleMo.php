<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMo;

/**
 * Class LocaleIt - Italian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMo extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'limba moldovenească';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'LIMBA MOLDOVENEASCĂ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMo;
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
		return '%s' . self::NBSP . self::PERCENT;
	}
}
