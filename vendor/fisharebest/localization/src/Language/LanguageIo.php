<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageIo - Representation of the Ido language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageIo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'io';
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
