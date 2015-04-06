<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule9;
use Fisharebest\Localization\Territory\TerritoryPl;

/**
 * Class LanguagePl - Representation of the Polish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguagePl extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'pl';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPl;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule9;
	}
}
