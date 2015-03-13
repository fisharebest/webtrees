<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKn - Representation of the Kannada language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kn';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptKnda;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}
}
