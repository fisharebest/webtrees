<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryEt;

/**
 * Class LanguageAa - Representation of the Afar language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAa extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'aa';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEt;
	}
}
