<?php

namespace IPLib;

use IPLib\Address\AddressInterface;

/**
 * Factory methods to build class instances.
 */
class Factory
{
    /**
     * Parse an IP address string.
     *
     * @param string $address the address to parse
     * @param bool $mayIncludePort set to false to avoid parsing addresses with ports
     * @param bool $mayIncludeZoneID set to false to avoid parsing IPv6 addresses with zone IDs (see RFC 4007)
     *
     * @return \IPLib\Address\AddressInterface|null
     */
    public static function addressFromString($address, $mayIncludePort = true, $mayIncludeZoneID = true)
    {
        $result = null;
        if ($result === null) {
            $result = Address\IPv4::fromString($address, $mayIncludePort);
        }
        if ($result === null) {
            $result = Address\IPv6::fromString($address, $mayIncludePort, $mayIncludeZoneID);
        }

        return $result;
    }

    /**
     * Convert a byte array to an address instance.
     *
     * @param int[]|array $bytes
     *
     * @return \IPLib\Address\AddressInterface|null
     */
    public static function addressFromBytes(array $bytes)
    {
        $result = null;
        if ($result === null) {
            $result = Address\IPv4::fromBytes($bytes);
        }
        if ($result === null) {
            $result = Address\IPv6::fromBytes($bytes);
        }

        return $result;
    }

    /**
     * Parse an IP range string.
     *
     * @param string $range
     *
     * @return \IPLib\Range\RangeInterface|null
     */
    public static function rangeFromString($range)
    {
        $result = null;
        if ($result === null) {
            $result = Range\Subnet::fromString($range);
        }
        if ($result === null) {
            $result = Range\Pattern::fromString($range);
        }
        if ($result === null) {
            $result = Range\Single::fromString($range);
        }

        return $result;
    }

    /**
     * Create a Range instance starting from its boundaries.
     *
     * @param string|\IPLib\Address\AddressInterface $from
     * @param string|\IPLib\Address\AddressInterface $to
     *
     * @return \IPLib\Range\RangeInterface|null
     */
    public static function rangeFromBoundaries($from, $to)
    {
        $result = null;
        $invalid = false;
        foreach (array('from', 'to') as $param) {
            if (!($$param instanceof AddressInterface)) {
                $$param = (string) $$param;
                if ($$param === '') {
                    $$param = null;
                } else {
                    $$param = static::addressFromString($$param);
                    if ($$param === null) {
                        $invalid = true;
                    }
                }
            }
        }
        if ($invalid === false) {
            $result = static::rangeFromBoundaryAddresses($from, $to);
        }

        return $result;
    }

    /**
     * @param \IPLib\Address\AddressInterface $from
     * @param \IPLib\Address\AddressInterface $to
     *
     * @return \IPLib\Range\RangeInterface|null
     */
    protected static function rangeFromBoundaryAddresses(AddressInterface $from = null, AddressInterface $to = null)
    {
        if ($from === null && $to === null) {
            $result = null;
        } elseif ($to === null) {
            $result = Range\Single::fromAddress($from);
        } elseif ($from === null) {
            $result = Range\Single::fromAddress($to);
        } else {
            $result = null;
            $addressType = $from->getAddressType();
            if ($addressType === $to->getAddressType()) {
                $cmp = strcmp($from->getComparableString(), $to->getComparableString());
                if ($cmp === 0) {
                    $result = Range\Single::fromAddress($from);
                } else {
                    if ($cmp > 0) {
                        list($from, $to) = array($to, $from);
                    }
                    $fromBytes = $from->getBytes();
                    $toBytes = $to->getBytes();
                    $numBytes = count($fromBytes);
                    $sameBits = 0;
                    for ($byteIndex = 0; $byteIndex < $numBytes; ++$byteIndex) {
                        $fromByte = $fromBytes[$byteIndex];
                        $toByte = $toBytes[$byteIndex];
                        if ($fromByte === $toByte) {
                            $sameBits += 8;
                        } else {
                            $differentBitsInByte = decbin($fromByte ^ $toByte);
                            $sameBits += 8 - strlen($differentBitsInByte);
                            break;
                        }
                    }
                    $result = static::rangeFromString($from->toString(true).'/'.(string) $sameBits);
                }
            }
        }

        return $result;
    }
}
