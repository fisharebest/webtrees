<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritoryNa;

/**
 * Class LanguageNaq - Representation of the Nama (Namibia) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNaq extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'naq';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNa;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRuleOneTwoOther;
	}
}
