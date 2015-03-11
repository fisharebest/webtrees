<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAa - Representation of the Afar language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'aa';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEt;
	}
}
