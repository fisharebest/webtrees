<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritorySo;

/**
 * Class LanguageSo - Representation of the Somali language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSo extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'so';
	}

	public function defaultTerritory() {
		return new TerritorySo;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
