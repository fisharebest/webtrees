<?php namespace Fisharebest\Localization;

/**
 * Class LanguageUk - Representation of the Ukrainian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageUk extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'uk';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUa;
	}
}
