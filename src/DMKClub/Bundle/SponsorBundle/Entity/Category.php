<?php

namespace DMKClub\Bundle\SponsorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;

use DMKClub\Bundle\SponsorBundle\Model\ExtendCategory;
use DMKClub\Bundle\BasicsBundle\Model\LifecycleTrait;

/**
 * Class Sponsor Category
 *
 * @package DMKClub\Bundle\DMKClubSponsorBundle\Entity
 *
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\SponsorBundle\Entity\Repository\CategoryRepository")
 * @ORM\Table(name="dmkclub_sponsorcategory")
 * @ORM\HasLifecycleCallbacks()
 * Die Angaben in "form" dienen dem create_select_form_inline
 */
#[Config(
    routeName: 'dmkclub_sponsorcategory_index',
    routeView: 'dmkclub_sponsorcategory_view',
    defaultValues: [
        'entity' => [
            'icon' => 'fa-gear',
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
            'form_type' => 'dmkclub_sponsorcategory_select',
            'grid_name' => 'dmkclub-sponsorcategories-select-grid',
        ],
        'dataaudit' => [
            'auditable' => true,
        ],
    ],
)]
class Category extends ExtendCategory
{
    use LifecycleTrait;

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
	 * @return Category
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
	 * @return Category
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
	 * @return Category
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
