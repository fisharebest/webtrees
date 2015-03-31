<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptArmn;
use Fisharebest\Localization\Territory\TerritoryAm;

/**
 * Class LanguageHy - Representation of the Armenian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHy extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'hy';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArmn;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryAm;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
