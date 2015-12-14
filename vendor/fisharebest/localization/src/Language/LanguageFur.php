<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryIt;

/**
 * Class LanguageFur - Representation of the Friulian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFur extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'fur';
	}

	public function defaultTerritory() {
		return new TerritoryIt;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
