<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptMlym;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageMl - Representation of the Malayalam language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMl extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'ml';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptMlym;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
