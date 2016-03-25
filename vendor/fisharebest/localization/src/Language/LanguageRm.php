<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LanguageRm - Representation of the Romansh language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRm extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'rm';
	}

	public function defaultTerritory() {
		return new TerritoryCh;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
