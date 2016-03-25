<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleCentralAtlasTamazight;
use Fisharebest\Localization\Territory\TerritoryMa;

/**
 * Class LanguageTzm - Representation of the Central Atlas Tamazight language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTzm extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'tzm';
	}

	public function defaultTerritory() {
		return new TerritoryMa;
	}

	public function pluralRule() {
		return new PluralRuleCentralAtlasTamazight();
	}
}
