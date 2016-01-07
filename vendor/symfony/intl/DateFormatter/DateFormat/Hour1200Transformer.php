<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\DateFormatter\DateFormat;

/**
 * Parser and formatter for 12 hour format (0-11).
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 *
 * @internal
 */
class Hour1200Transformer extends HourTransformer
{
    /**
     * {@inheritdoc}
     */
    public function format(\DateTime $dateTime, $length)
    {
        $hourOfDay = $dateTime->format('g');
        $hourOfDay = '12' == $hourOfDay ? '0' : $hourOfDay;

        return $this->padLeft($hourOfDay, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeHour($hour, $marker = null)
    {
        if ('PM' === $marker) {
            $hour += 12;
        }

        return $hour;
    }

    /**
     * {@inheritdoc}
     */
    public function getReverseMatchingRegExp($length)
    {
        return '\d{1,2}';
    }

    /**
     * {@inheritdoc}
     */
    public function extractDateOptions($matched, $length)
    {
        return array(
            'hour' => (int) $matched,
            'hourInstance' => $this,
        );
    }
}
