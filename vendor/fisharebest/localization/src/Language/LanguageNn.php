<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNo;

/**
 * Class LanguageNn - Representation of the Norwegian Nynorsk language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'nn';
	}

	public function defaultTerritory() {
		return new TerritoryNo;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
