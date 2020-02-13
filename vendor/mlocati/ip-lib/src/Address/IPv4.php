<?php

namespace IPLib\Address;

use IPLib\Range\RangeInterface;
use IPLib\Range\Subnet;
use IPLib\Range\Type as RangeType;

/**
 * An IPv4 address.
 */
class IPv4 implements AddressInterface
{
    /**
     * The string representation of the address.
     *
     * @var string
     *
     * @example '127.0.0.1'
     */
    protected $address;

    /**
     * The byte list of the IP address.
     *
     * @var int[]|null
     */
    protected $bytes;

    /**
     * The type of the range of this IP address.
     *
     * @var int|null
     */
    protected $rangeType;

    /**
     * A string representation of this address than can be used when comparing addresses and ranges.
     *
     * @var string
     */
    protected $comparableString;

    /**
     * An array containing RFC designated address ranges.
     *
     * @var array|null
     */
    private static $reservedRanges = null;

    /**
     * Initializes the instance.
     *
     * @param string $address
     */
    protected function __construct($address)
    {
        $this->address = $address;
        $this->bytes = null;
        $this->rangeType = null;
        $this->comparableString = null;
    }

    /**
     * Parse a string and returns an IPv4 instance if the string is valid, or null otherwise.
     *
     * @param string|mixed $address the address to parse
     * @param bool $mayIncludePort set to false to avoid parsing addresses with ports
     *
     * @return static|null
     */
    public static function fromString($address, $mayIncludePort = true)
    {
        $result = null;
        if (is_string($address) && strpos($address, '.')) {
            $rx = '([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})';
            if ($mayIncludePort) {
                $rx .= '(?::\d+)?';
            }
            $matches = null;
            if (preg_match('/^'.$rx.'$/', $address, $matches)) {
                $ok = true;
                $nums = array();
                for ($i = 1; $ok && $i <= 4; ++$i) {
                    $ok = false;
                    $n = (int) $matches[$i];
                    if ($n >= 0 && $n <= 255) {
                        $ok = true;
                        $nums[] = (string) $n;
                    }
                }
                if ($ok) {
                    $result = new static(implode('.', $nums));
                }
            }
        }

        return $result;
    }

    /**
     * Parse an array of bytes and returns an IPv4 instance if the array is valid, or null otherwise.
     *
     * @param int[]|array $bytes
     *
     * @return static|null
     */
    public static function fromBytes(array $bytes)
    {
        $result = null;
        if (count($bytes) === 4) {
            $chunks = array_map(
                function ($byte) {
                    return (is_int($byte) && $byte >= 0 && $byte <= 255) ? (string) $byte : false;
                },
                $bytes
            );
            if (in_array(false, $chunks, true) === false) {
                $result = new static(implode('.', $chunks));
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::toString()
     */
    public function toString($long = false)
    {
        if ($long) {
            return $this->getComparableString();
        }

        return $this->address;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::__toString()
     */
    public function __toString()
    {
        return $this->address;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getBytes()
     */
    public function getBytes()
    {
        if ($this->bytes === null) {
            $this->bytes = array_map(
                function ($chunk) {
                    return (int) $chunk;
                },
                explode('.', $this->address)
            );
        }

        return $this->bytes;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getAddressType()
     */
    public function getAddressType()
    {
        return Type::T_IPv4;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getDefaultReservedRangeType()
     */
    public static function getDefaultReservedRangeType()
    {
        return RangeType::T_PUBLIC;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getReservedRanges()
     */
    public static function getReservedRanges()
    {
        if (self::$reservedRanges === null) {
            $reservedRanges = array();
            foreach (array(
                // RFC 5735
                '0.0.0.0/8' => array(RangeType::T_THISNETWORK, array('0.0.0.0/32' => RangeType::T_UNSPECIFIED)),
                // RFC 5735
                '10.0.0.0/8' => array(RangeType::T_PRIVATENETWORK),
                // RFC 5735
                '127.0.0.0/8' => array(RangeType::T_LOOPBACK),
                // RFC 5735
                '169.254.0.0/16' => array(RangeType::T_LINKLOCAL),
                // RFC 5735
                '172.16.0.0/12' => array(RangeType::T_PRIVATENETWORK),
                // RFC 5735
                '192.0.0.0/24' => array(RangeType::T_RESERVED),
                // RFC 5735
                '192.0.2.0/24' => array(RangeType::T_RESERVED),
                // RFC 5735
                '192.88.99.0/24' => array(RangeType::T_ANYCASTRELAY),
                // RFC 5735
                '192.168.0.0/16' => array(RangeType::T_PRIVATENETWORK),
                // RFC 5735
                '198.18.0.0/15' => array(RangeType::T_RESERVED),
                // RFC 5735
                '198.51.100.0/24' => array(RangeType::T_RESERVED),
                // RFC 5735
                '203.0.113.0/24' => array(RangeType::T_RESERVED),
                // RFC 5735
                '224.0.0.0/4' => array(RangeType::T_MULTICAST),
                // RFC 5735
                '240.0.0.0/4' => array(RangeType::T_RESERVED, array('255.255.255.255/32' => RangeType::T_LIMITEDBROADCAST)),
            ) as $range => $data) {
                $exceptions = array();
                if (isset($data[1])) {
                    foreach ($data[1] as $exceptionRange => $exceptionType) {
                        $exceptions[] = new AssignedRange(Subnet::fromString($exceptionRange), $exceptionType);
                    }
                }
                $reservedRanges[] = new AssignedRange(Subnet::fromString($range), $data[0], $exceptions);
            }
            self::$reservedRanges = $reservedRanges;
        }

        return self::$reservedRanges;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getRangeType()
     */
    public function getRangeType()
    {
        if ($this->rangeType === null) {
            $rangeType = null;
            foreach (static::getReservedRanges() as $reservedRange) {
                $rangeType = $reservedRange->getAddressType($this);
                if ($rangeType !== null) {
                    break;
                }
            }
            $this->rangeType = $rangeType === null ? static::getDefaultReservedRangeType() : $rangeType;
        }

        return $this->rangeType;
    }

    /**
     * Create an IPv6 representation of this address.
     *
     * @return \IPLib\Address\IPv6
     */
    public function toIPv6()
    {
        $myBytes = $this->getBytes();

        return IPv6::fromString('2002:'.sprintf('%02x', $myBytes[0]).sprintf('%02x', $myBytes[1]).':'.sprintf('%02x', $myBytes[2]).sprintf('%02x', $myBytes[3]).'::');
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getComparableString()
     */
    public function getComparableString()
    {
        if ($this->comparableString === null) {
            $chunks = array();
            foreach ($this->getBytes() as $byte) {
                $chunks[] = sprintf('%03d', $byte);
            }
            $this->comparableString = implode('.', $chunks);
        }

        return $this->comparableString;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::matches()
     */
    public function matches(RangeInterface $range)
    {
        return $range->contains($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getNextAddress()
     */
    public function getNextAddress()
    {
        $overflow = false;
        $bytes = $this->getBytes();
        for ($i = count($bytes) - 1; $i >= 0; --$i) {
            if ($bytes[$i] === 255) {
                if ($i === 0) {
                    $overflow = true;
                    break;
                }
                $bytes[$i] = 0;
            } else {
                ++$bytes[$i];
                break;
            }
        }

        return $overflow ? null : static::fromBytes($bytes);
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Address\AddressInterface::getPreviousAddress()
     */
    public function getPreviousAddress()
    {
        $overflow = false;
        $bytes = $this->getBytes();
        for ($i = count($bytes) - 1; $i >= 0; --$i) {
            if ($bytes[$i] === 0) {
                if ($i === 0) {
                    $overflow = true;
                    break;
                }
                $bytes[$i] = 255;
            } else {
                --$bytes[$i];
                break;
            }
        }

        return $overflow ? null : static::fromBytes($bytes);
    }
}
