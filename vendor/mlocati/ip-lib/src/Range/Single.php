<?php

namespace IPLib\Range;

use IPLib\Address\AddressInterface;
use IPLib\Address\IPv4;
use IPLib\Address\Type as AddressType;
use IPLib\Factory;

/**
 * Represents a single address (eg a range that contains just one address).
 *
 * @example 127.0.0.1
 * @example ::1
 */
class Single implements RangeInterface
{
    /**
     * @var \IPLib\Address\AddressInterface
     */
    protected $address;

    /**
     * Initializes the instance.
     *
     * @param \IPLib\Address\AddressInterface $address
     */
    protected function __construct(AddressInterface $address)
    {
        $this->address = $address;
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
        $address = Factory::addressFromString($range);
        if ($address !== null) {
            $result = new static($address);
        }

        return $result;
    }

    /**
     * Create the range instance starting from an address instance.
     *
     * @param \IPLib\Address\AddressInterface $address
     *
     * @return static
     */
    public static function fromAddress(AddressInterface $address)
    {
        return new static($address);
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::toString()
     */
    public function toString($long = false)
    {
        return $this->address->toString($long);
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::__toString()
     */
    public function __toString()
    {
        return $this->address->__toString();
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getAddressType()
     */
    public function getAddressType()
    {
        return $this->address->getAddressType();
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getRangeType()
     */
    public function getRangeType()
    {
        return $this->address->getRangeType();
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
            if ($address->toString(false) === $this->address->toString(false)) {
                $result = true;
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
            if ($range->toString(false) === $this->toString(false)) {
                $result = true;
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
        return $this->address;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getEndAddress()
     */
    public function getEndAddress()
    {
        return $this->address;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getComparableStartString()
     */
    public function getComparableStartString()
    {
        return $this->address->getComparableString();
    }

    /**
     * {@inheritdoc}
     *
     * @see \IPLib\Range\RangeInterface::getComparableEndString()
     */
    public function getComparableEndString()
    {
        return $this->address->getComparableString();
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

        return IPv4::fromBytes(array(255, 255, 255, 255));
    }
}
