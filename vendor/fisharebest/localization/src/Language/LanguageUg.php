<?php namespace Fisharebest\Localization;

/**
 * Class LanguageUg - Representation of the Uighur language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageUg extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ug';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArab;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCn;
	}
}
