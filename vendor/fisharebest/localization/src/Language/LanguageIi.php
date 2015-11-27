<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryCn;

/**
 * Class LanguageIi - Representation of the Sichuan Yi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIi extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ii';
	}

	public function defaultTerritory() {
		return new TerritoryCn;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
