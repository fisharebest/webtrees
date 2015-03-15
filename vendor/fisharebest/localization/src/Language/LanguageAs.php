<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAs - Representation of the Assamese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'as';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptBeng;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}
}
