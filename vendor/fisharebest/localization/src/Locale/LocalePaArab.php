<?php namespace Fisharebest\Localization;

/**
 * Class LocalePaArab
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePaArab extends LocalePa {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::ARAB_GROUP,
			self::DECIMAL  => self::ARAB_DECIMAL,
			self::NEGATIVE => self::LTR_MARK . self::HYPHEN . self::LTR_MARK,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::ARAB_PERCENT;
	}

	/** {@inheritdoc} */
	public function script() {
		return new ScriptArab;
	}
}
