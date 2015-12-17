<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryCf;

/**
 * Class LanguageSg - Representation of the Sango language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSg extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'sg';
	}

	public function defaultTerritory() {
		return new TerritoryCf;
	}

	public function pluralRule() {
		return new PluralRule0;
	}
}
