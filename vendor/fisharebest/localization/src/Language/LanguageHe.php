<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHe - Representation of the Hebrew language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHe extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'he';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptHebr;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIl;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
