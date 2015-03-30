<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleManx;
use Fisharebest\Localization\Territory\TerritoryIm;

/**
 * Class LanguageGv - Representation of the Manx language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGv extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'gv';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIm;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRuleManx;
	}
}
