<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKhq - Koyra Chiini
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKhq extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Koyra ciini';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KOYRA CIINI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKhq;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
