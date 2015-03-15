<?php namespace Fisharebest\Localization;

/**
 * Class Territory - Representation of the territory PK - Pakistan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryPk extends Territory {
	/** {@inheritdoc} */
	public function code() {
		return 'PK';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}
}
