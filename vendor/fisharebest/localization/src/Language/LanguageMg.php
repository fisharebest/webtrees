<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryMg;

/**
 * Class LanguageMg - Representation of the Malagasy language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMg extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'mg';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMg;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
