<?php namespace Fisharebest\Localization;

/**
 * Class LanguageEl - Representation of the Modern Greek (1453-) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEl extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'el';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptGrek;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGr;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
