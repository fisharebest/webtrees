<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule10;
use Fisharebest\Localization\Territory\TerritorySi;

/**
 * Class LanguageSl - Representation of the Slovenian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSl extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'sl';
	}

	public function defaultTerritory() {
		return new TerritorySi;
	}

	public function pluralRule() {
		return new PluralRule10;
	}
}
