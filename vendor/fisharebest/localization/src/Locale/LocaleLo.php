<?php namespace Fisharebest\Localization;

/**
 * Class LocaleLo - Lao
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ລາວ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageLo;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
