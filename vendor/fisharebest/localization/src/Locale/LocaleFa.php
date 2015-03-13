<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFa - Persian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFa extends Locale {
	/** {@inheritdoc} */
	public function collation() {
		return 'persian_ci';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '۰',
			'1' => '۱',
			'2' => '۲',
			'3' => '۳',
			'4' => '۴',
			'5' => '۵',
			'6' => '۶',
			'7' => '۷',
			'8' => '۸',
			'9' => '۹',
		);
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'فارسی';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageFa;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::ARAB_GROUP,
			self::DECIMAL  => self::ARAB_DECIMAL,
			self::NEGATIVE => self::LTR_MARK . self::MINUS_SIGN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::ARAB_PERCENT;
	}
}
