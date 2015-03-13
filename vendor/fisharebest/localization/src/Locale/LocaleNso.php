<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNso - Northern Sotho
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNso extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Sesotho sa Leboa';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SESOTHO SA LEBOA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNso;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
