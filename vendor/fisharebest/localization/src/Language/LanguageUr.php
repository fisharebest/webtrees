<?php namespace Fisharebest\Localization;

/**
 * Class LanguageUr - Representation of the Urdu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageUr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ur';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArab;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPk;
	}
}
