<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritorySe;

/**
 * Class LanguageSv - Representation of the Swedish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSv extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'sv';
	}

	public function defaultTerritory() {
		return new TerritorySe;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
