<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule7;
use Fisharebest\Localization\Territory\TerritoryBa;

/**
 * Class LanguageBs - Representation of the Bosnian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBs extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'bs';
	}

	public function defaultTerritory() {
		return new TerritoryBa;
	}

	public function pluralRule() {
		return new PluralRule7;
	}
}
