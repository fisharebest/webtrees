<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUs;

/**
 * Class LanguageHaw - Representation of the Hawaiian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHaw extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'haw';
	}

	public function defaultTerritory() {
		return new TerritoryUs;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
