<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNe - Representation of the Nepali language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNe extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ne';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptDeva;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNp;
	}
}
