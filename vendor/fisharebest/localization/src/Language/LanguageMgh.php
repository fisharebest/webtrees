<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryMz;

/**
 * Class LanguageMgh - Representation of the Makhuwa-Meetto language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMgh extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'mgh';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMz;
	}
}
