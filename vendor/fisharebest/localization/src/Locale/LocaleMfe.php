<?php namespace Fisharebest\Localization;

/**
 * Class LocaleMfe - Morisyen
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMfe extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kreol morisien';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KREOL MORISIEN';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageMfe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
