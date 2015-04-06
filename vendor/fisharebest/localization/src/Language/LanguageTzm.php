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
	/** {@inheritdoc} */
	public function code() {
		return 'tzm';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMa;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRuleCentralAtlasTamazight();
	}
}
