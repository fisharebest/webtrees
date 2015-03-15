<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNmg - Representation of the Kwasio language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNmg extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nmg';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
