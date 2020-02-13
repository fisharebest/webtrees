<?php

namespace IPLib\Range;

use IPLib\Address\AddressInterface;
use IPLib\Address\IPv4;
use IPLib\Address\IPv6;
use IPLib\Address\Type as AddressType;
use IPLib\Factory;

/**
 * Represents an address range in subnet format (eg CIDR).
 *
 * @example 127.0.0.1/32
 * @example ::/8
 */
class Subnet implements RangeInterface
{
    /**
     * Starting address of the range.
     *
     * @var \IPLib\Address\AddressInterface
     */
    protected $fromAddress;

    /**
     * Final address of the range.
     *
     * @var \IPLib\Address\AddressInterface
     */
    protected $toAddress;

    /**
     * Number of the same bits of the range.
     *
     * @var int
     */
    protected $networkPrefix;

    /**
     * The type of the range of this IP range.
     *
     * @var int|null
     */
    protected $rangeType;

    /**
     * The 6to4 address IPv6 address range.
     *
     * @var self|null
     */
    private static $sixToFour;

    /**
     * Initializes the instance.
     *
     * @param \IPLib\Address\AddressInterface $fromAddress
     * @param \IPLib\Address\AddressInterface $toAddress
     * @param int $networkPrefix
     *
     * @internal
     */
    public function __construct(AddressInterface $fromAddress, AddressInterface $toAddress, $networkPrefix)
    {
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
        $this->networkPrefix = $networkPrefix;
    }

    /**
     * Try get the range instance starting from its string representation.
     *
     * @param string|mixed $range
     *
     * @return static|null
     */
    public static function fromString($range)
    {
        $result = null;
        if (is_string($range)) {
            $parts = explode('/', $range);
            if (count($parts) === 2) {
                $address = Factory::addressFromString($parts[0]);
                if ($address !== null) {
                    if (preg_match('/^[0-9]{1,9}$/', $parts[1])) {
                        $networkPrefix = (int) $parts[1];
                        if ($networkPrefix >= 0) {
                            $addressBytes = $address->getBytes();
                            $totalBytes = count($addressBytes);
                            $numDifferentBits = $totalBytes * 8 - $networkPrefix;
                            if ($numDifferentBits >= 0) {
                                $numSameBytes = $networkPrefix >> 3;
                                $sameBytes = array_slice($addressBytes, 0, $numSameBytes);
                                $differentBytesStart = ($totalBytes === $numSameBytes) ? array() : array_fill(0, $totalBytes - $numSameBytes, 0);
                                $differentBytesEnd = ($totalBytes === $numSameBytes) ? array() : array_fill(0, $totalBytes - $numSameBytes, 255);
                                $startSameBits = $networkPrefix % 8;
                                if ($startSameBits !== 0) {
                                    $varyingByte = $addressBytes[$numSameBytes];
                                    $differentBytesStart[0] = $varyingByte & bindec(str_pad(str_repeat('1', $startSameBits), 8, '0', STR_PAD_RIGHT));
                                    $differentBytesEnd[0] = $differentBytesStart[0] + bindec(str_repeat('1', 8 - $startSameBits));
                                }
                                $result = new static(
                                    Factory::addressFromBytes(array_merge($sameBytes, $differentBytesStart)),
                                    Factory::addressFromBytes(array_merge($sameBytes, $differentBytesEnd)),
                                    $networkPrefix
                                );
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::toString()
     */
    public function toString($long = false)
    {
        return $this->fromAddress->toString($long).'/'.$this->networkPrefix;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::__toString()
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getAddressType()
     */
    public function getAddressType()
    {
        return $this->fromAddress->getAddressType();
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getRangeType()
     */
    public function getRangeType()
    {
        if ($this->rangeType === null) {
            $addressType = $this->getAddressType();
            if ($addressType === AddressType::T_IPv6 && static::get6to4()->containsRange($this)) {
                $this->rangeType = Factory::rangeFromBoundaries($this->fromAddress->toIPv4(), $this->toAddress->toIPv4())->getRangeType();
            } else {
                switch ($addressType) {
                    case AddressType::T_IPv4:
                        $defaultType = IPv4::getDefaultReservedRangeType();
                        $reservedRanges = IPv4::getReservedRanges();
                        break;
                    case AddressType::T_IPv6:
                        $defaultType = IPv6::getDefaultReservedRangeType();
                        $reservedRanges = IPv6::getReservedRanges();
                        break;
                    default:
                        throw new \Exception('@todo'); // @codeCoverageIgnore
                }
                $rangeType = null;
                foreach ($reservedRanges as $reservedRange) {
                    $rangeType = $reservedRange->getRangeType($this);
                    if ($rangeType !== null) {
                        break;
                    }
                }
                $this->rangeType = $rangeType === null ? $defaultType : $rangeType;
            }
        }

        return $this->rangeType === false ? null : $this->rangeType;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::contains()
     */
    public function contains(AddressInterface $address)
    {
        $result = false;
        if ($address->getAddressType() === $this->getAddressType()) {
            $cmp = $address->getComparableString();
            $from = $this->getComparableStartString();
            if ($cmp >= $from) {
                $to = $this->getComparableEndString();
                if ($cmp <= $to) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::containsRange()
     */
    public function containsRange(RangeInterface $range)
    {
        $result = false;
        if ($range->getAddressType() === $this->getAddressType()) {
            $myStart = $this->getComparableStartString();
            $itsStart = $range->getComparableStartString();
            if ($itsStart >= $myStart) {
                $myEnd = $this->getComparableEndString();
                $itsEnd = $range->getComparableEndString();
                if ($itsEnd <= $myEnd) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getStartAddress()
     */
    public function getStartAddress()
    {
        return $this->fromAddress;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getEndAddress()
     */
    public function getEndAddress()
    {
        return $this->toAddress;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getComparableStartString()
     */
    public function getComparableStartString()
    {
        return $this->fromAddress->getComparableString();
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getComparableEndString()
     */
    public function getComparableEndString()
    {
        return $this->toAddress->getComparableString();
    }

    /**
     * Get the 6to4 address IPv6 address range.
     *
     * @return self
     */
    public static function get6to4()
    {
        if (self::$sixToFour === null) {
            self::$sixToFour = self::fromString('2002::/16');
        }

        return self::$sixToFour;
    }

    /**
     * Get subnet prefix.
     *
     * @return int
     */
    public function getNetworkPrefix()
    {
        return $this->networkPrefix;
    }

    /**
     * Get the pattern representation (if applicable) of this range.
     *
     * @return \IPLib\Range\Pattern|null return NULL if this range can't be represented by a pattern notation
     */
    public function asPattern()
    {
        $address = $this->getStartAddress();
        $networkPrefix = $this->getNetworkPrefix();
        switch ($address->getAddressType()) {
            case AddressType::T_IPv4:
                return $networkPrefix % 8 === 0 ? new Pattern($address, $address, 4 - $networkPrefix / 8) : null;
            case AddressType::T_IPv6:
                return $networkPrefix % 16 === 0 ? new Pattern($address, $address, 8 - $networkPrefix / 16) : null;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getSubnetMask()
     */
    public function getSubnetMask()
    {
        if ($this->getAddressType() !== AddressType::T_IPv4) {
            return null;
        }
        $bytes = array();
        $prefix = $this->getNetworkPrefix();
        while ($prefix >= 8) {
            $bytes[] = 255;
            $prefix -= 8;
        }
        if ($prefix !== 0) {
            $bytes[] = bindec(str_pad(str_repeat('1', $prefix), 8, '0'));
        }
        $bytes = array_pad($bytes, 4, 0);

        return IPv4::fromBytes($bytes);
    }
}
