<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryEs;

/**
 * Class LanguageEu - Representation of the Basque language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEu extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'eu';
	}

	public function defaultTerritory() {
		return new TerritoryEs;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
