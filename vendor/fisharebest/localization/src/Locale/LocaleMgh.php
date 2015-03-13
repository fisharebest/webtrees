<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMgh - Makhuwa-Meetto
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMgh extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Makua';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'MAKUA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMgh;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
