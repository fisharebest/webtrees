<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageEo - Representation of the Esperanto language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageEo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'eo';
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
