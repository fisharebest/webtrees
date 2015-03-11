<?php namespace Fisharebest\Localization;

/**
 * Class LanguageGv - Representation of the Manx language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGv extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'gv';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIm;
	}
}
