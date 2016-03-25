<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageYo - Representation of the Yoruba language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageYo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'yo';
	}

	public function defaultTerritory() {
		return new TerritoryNg;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
