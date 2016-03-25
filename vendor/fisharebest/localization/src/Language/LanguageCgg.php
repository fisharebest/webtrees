<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUg;

/**
 * Class LanguageCgg - Representation of the Chiga language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageCgg extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'cgg';
	}

	public function defaultTerritory() {
		return new TerritoryUg;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
