<?php
declare(strict_types=1);

namespace UCRM\Plugins\Data;

use MVQN\XML\XmlElementClass;

/**
 * Class CustomerDetails
 *
 * @package UCRM\Plugins\Data
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method string getApiKey()
 * @method string getUsername()
 * @method string getPassword()
 * @method string getFirstName()
 * @method string getLastName()
 * @method string getStreetAddress()
 * @method string getCustomerLat()
 * @method string getCustomerLong()
 * @method string getCity()
 * @method string getState()
 * @method string getCountry()
 * @method string getZip()
 * @method string getPhoneNumber()
 * @method string getEmailAddress()
 * @method string getHearAbout()
 * @method string getContactMethod()
 * @method string getContactTime()
 * @method string getComments()
 * @method string getFiberIncludes()
 * @method CustomerLinkInfo getCustomerLinkInfo()
 *
 */
final class CustomerDetails extends XmlElementClass
{
    /**
     * @var string
     * @XmlElement apikey
     */
    protected $apiKey;

    /**
     * @var string
     * @XmlElement username
     */
    protected $username;

    /**
     * @var string
     * @XmlElement password
     */
    protected $password;

    /**
     * @var string
     * @XmlElement FirstName
     */
    protected $firstName;

    /**
     * @var string
     * @XmlElement LastName
     */
    protected $lastName;

    /**
     * @var string
     * @XmlElement StreetAddress
     */
    protected $streetAddress;

    /**
     * @var string
     * @XmlElement CustomerLat
     */
    protected $customerLat;

    /**
     * @var string
     * @XmlElement CustomerLong
     */
    protected $customerLong;

    /**
     * @var string
     * @XmlElement City
     */
    protected $city;

    /**
     * @var string
     * @XmlElement State
     */
    protected $state;

    /**
     * @var string
     * @XmlElement Country
     */
    protected $country;

    /**
     * @var string
     * @XmlElement ZIP
     */
    protected $zip;

    /**
     * @var string
     * @XmlElement PhoneNumber
     */
    protected $phoneNumber;

    /**
     * @var string
     * @XmlElement EmailAddress
     */
    protected $emailAddress;

    /**
     * @var string
     * @XmlElement HearAbout
     */
    protected $hearAbout;

    /**
     * @var string
     * @XmlElement ContactMethod
     */
    protected $contactMethod;

    /**
     * @var string
     * @XmlElement ContactTime
     */
    protected $contactTime;

    /**
     * @var array
     * @XmlElement Comment
     */
    protected $comments;

    /**
     * @var array
     * @XmlElement fiberincludes
     */
    protected $fiberIncludes;

    /**
     * @var CustomerLinkInfo
     * @XmlElement CustomerLinkInfo
     */
    protected $customerLinkInfo;




}