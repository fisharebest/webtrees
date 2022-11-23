<?php

namespace Fisharebest\Localization\Variant;

/**
 * Class Posix - Representation of the Posix variant of en-US.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class VariantPosix implements VariantInterface
{
    public function code()
    {
        return 'posix';
    }
}
