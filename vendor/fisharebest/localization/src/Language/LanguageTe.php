<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTe - Representation of the Telugu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTe extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'te';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptTelu;
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
