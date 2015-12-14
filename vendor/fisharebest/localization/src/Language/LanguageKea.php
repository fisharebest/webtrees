<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryCv;

/**
 * Class LanguageKea - Representation of the Kabuverdianu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKea extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kea';
	}

	public function defaultTerritory() {
		return new TerritoryCv;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
