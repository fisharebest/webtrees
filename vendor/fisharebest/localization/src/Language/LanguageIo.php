<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\Territory001;

/**
 * Class LanguageIo - Representation of the Ido language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LanguageIo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'io';
    }

    public function defaultTerritory()
    {
        return new Territory001();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
