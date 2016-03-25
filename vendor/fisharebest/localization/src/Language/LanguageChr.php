<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUs;

/**
 * Class LanguageChr - Representation of the Cherokee language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageChr extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'chr';
	}

	public function defaultTerritory() {
		return new TerritoryUs;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
