<?php namespace Fisharebest\Localization;

/**
 * Class LanguageId - Representation of the Indonesian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageId extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'id';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryId;
	}
}
