<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTt - Representation of the Tatar language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTt extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'tt';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryRu;
	}
}
