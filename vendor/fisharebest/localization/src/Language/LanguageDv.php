<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDv - Representation of the Divehi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDv extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'dv';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptThaa;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMv;
	}
}
