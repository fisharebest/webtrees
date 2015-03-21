<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMl - Representation of the Malayalam language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMl extends Language {
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
