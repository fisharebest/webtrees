<?php

namespace IPLib\Address;

use IPLib\Range\RangeInterface;

/**
 * Interface of all the IP address types.
 */
interface AddressInterface
{
    /**
     * Get the string representation of this address.
     *
     * @param bool $long set to true to have a long/full representation, false otherwise
     *
     * @return string
     *
     * @example If $long is true, you'll get '0000:0000:0000:0000:0000:0000:0000:0001', '::1' otherwise.
     */
    public function toString($long = false);

    /**
     * Get the short string representation of this address.
     *
     * @return string
     */
    public function __toString();

    /**
     * Get the byte list of the IP address.
     *
     * @return int[]
     *
     * @example For IPv4 you'll get array(127, 0, 0, 1), for IPv6 array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1)
     */
    public function getBytes();

    /**
     * Get the type of the IP address.
     *
     * @return int One of the \IPLib\Address\Type::T_... constants
     */
    public function getAddressType();

    /**
     * Get the default RFC reserved range type.
     *
     * @return int One of the \IPLib\Range\Type::T_... constants
     */
    public static function getDefaultReservedRangeType();

    /**
     * Get the RFC reserved ranges (except the ones of type getDefaultReservedRangeType).
     *
     * @return \IPLib\Address\AssignedRange[] ranges are sorted
     */
    public static function getReservedRanges();

    /**
     * Get the type of range of the IP address.
     *
     * @return int One of the \IPLib\Range\Type::T_... constants
     */
    public function getRangeType();

    /**
     * Get a string representation of this address than can be used when comparing addresses and ranges.
     *
     * @return string
     */
    public function getComparableString();

    /**
     * Check if this address is contained in an range.
     *
     * @param \IPLib\Range\RangeInterface $range
     *
     * @return bool
     */
    public function matches(RangeInterface $range);

    /**
     * Get the address right after this IP address (if available).
     *
     * @return \IPLib\Address\AddressInterface|null
     */
    public function getNextAddress();

    /**
     * Get the address right before this IP address (if available).
     *
     * @return \IPLib\Address\AddressInterface|null
     */
    public function getPreviousAddress();
}
