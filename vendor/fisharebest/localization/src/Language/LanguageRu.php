<?php namespace Fisharebest\Localization;

/**
 * Class LanguageRu - Representation of the Russian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ru';
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
