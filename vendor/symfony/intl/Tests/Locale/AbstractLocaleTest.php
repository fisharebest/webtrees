<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\Tests\Locale;

/**
 * Test case for Locale implementations.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
abstract class AbstractLocaleTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDefault()
    {
        $this->call('setDefault', 'en_GB');

        $this->assertSame('en_GB', $this->call('getDefault'));
    }

    abstract protected function call($methodName);
}
