<?php

namespace DMKClub\Bundle\PublicRelationBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;

use DMKClub\Bundle\BasicsBundle\Model\LifecycleTrait;
use DMKClub\Bundle\PublicRelationBundle\Model\ExtendPRContact;
use Oro\Bundle\ChannelBundle\Model\ChannelAwareInterface;
use Oro\Bundle\ChannelBundle\Model\ChannelEntityTrait;
use Oro\Bundle\AccountBundle\Entity\Account;

/**
 * Class P/R Contact
 *
 * @package DMKClub\Bundle\DMKClubPublicRelationBundle\Entity
 *
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\PublicRelationBundle\Entity\Repository\PRContactRepository")
 * @ORM\Table(name="dmkclub_prcontact")
 * @ORM\HasLifecycleCallbacks()
 * Die Angaben in "form" dienen dem create_select_form_inline
 */
#[Config(
    routeName: 'dmkclub_prcontact_index',
    routeView: 'dmkclub_prcontact_view',
    defaultValues: [
        'entity' => [
            'icon' => 'fa-newspaper-o',
        ],
        'ownership' => [
            'owner_type' => 'USER',
            'owner_field_name' => 'owner',
            'owner_column_name' => 'user_owner_id',
            'organization_field_name' => 'organization',
            'organization_column_name' => 'organization_id',
        ],
        'security' => [
            'type' => 'ACL',
            'group_name' => '',
            'category' => 'dmkclub_data',
        ],
        'form' => [
            'form_type' => 'dmkclub_prcontact_select',
            'grid_name' => 'dmkclub-prcontact-select-grid',
        ],
        'dataaudit' => [
            'auditable' => true,
        ],
    ],
)]
class PRContact extends ExtendPRContact implements ChannelAwareInterface
{
    use ChannelEntityTrait, LifecycleTrait;

    /*
	 * Fields have to be duplicated here to enable dataaudit and soap transformation only for contact
	*/
	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	#[ConfigField(
	    defaultValues: [
	        'importexport' => [
	            'order' => 10,
	        ],
	    ],
	)]
	protected $id;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	#[ConfigField(
	    defaultValues: [
	        'dataaudit' => [
	            'auditable' => true,
	        ],
	        'importexport' => [
	            'identity' => true,
	            'order' => 30,
	        ],
	    ],
	)]
	protected $name;

	/**
	 * @var Collection
	 *
	 * @ORM\ManyToOne(targetEntity="DMKClub\Bundle\PublicRelationBundle\Entity\PRCategory")
	 * @ORM\JoinColumn(name="category", referencedColumnName="id", onDelete="SET NULL")
	 */
	#[ConfigField(
	    defaultValues: [
	        'dataaudit' => [
	            'auditable' => true,
	        ],
	        'importexport' => [
	            'order' => 230,
	            'short' => true,
	        ],
	    ],
	)]
	protected $category;

	/**
	 * @var Account
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\AccountBundle\Entity\Account", cascade="PERSIST")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $account;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $owner;

	/**
	 * @var Organization
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
	 * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $organization;


	/**
	 * {@inheritdoc}
	 */
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * Set id
	 *
	 * @param int $id
	 * @return PRContact
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set endDate
	 *
	 * @param \DateTime $endDate
	 * @return PRContact
	 */
	public function setName($id)
	{
		$this->name = $id;

		return $this;
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Gets the Category related to contact
	 *
	 * @return PRContact
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Add specified Category
	 *
	 * @param PRCategory $category
	 *
	 * @return PRContact
	 */
	public function setCategory(PRCategory $category) {
		$this->category = $category;
		return $this;
	}
	/**
	 * @param Account $account
	 *
	 * @return PRContact
	 */
	public function setAccount($account) {
		$this->account = $account;

		return $this;
	}

	/**
	 * @return Account
	 */
	public function getAccount() {
		return $this->account;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->getName();
	}

	/**
	 * @return User
	 */
	public function getOwner()
	{
		return $this->owner;
	}

	/**
	 * @param User $user
	 */
	public function setOwner(User $user)
	{
		$this->owner = $user;
	}

	/**
	 * Set organization
	 *
	 * @param Organization $organization
	 * @return PRContact
	 */
	public function setOrganization(Organization $organization = null)
	{
		$this->organization = $organization;

		return $this;
	}

	/**
	 * Get organization
	 *
	 * @return Organization
	 */
	public function getOrganization()
	{
		return $this->organization;
	}
}
