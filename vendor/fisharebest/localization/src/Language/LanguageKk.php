<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKk - Representation of the Kazakh language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKk extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kk';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryKz;
	}
}
