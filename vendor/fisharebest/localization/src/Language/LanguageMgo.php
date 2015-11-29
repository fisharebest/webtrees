<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryCm;

/**
 * Class LanguageMgo - Representation of the Meta' language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMgo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'mgo';
	}

	public function defaultTerritory() {
		return new TerritoryCm;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
