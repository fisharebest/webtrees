<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBas - Representation of the Basa (Cameroon) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBas extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'bas';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
