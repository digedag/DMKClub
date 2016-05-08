<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubMemberBundleInstaller implements Installation, ActivityExtensionAwareInterface, CommentExtensionAwareInterface
{
	/** @var CommentExtension */
	protected $comment;

	/** @var ActivityExtension */
	protected $activityExtension;

	/**
	 * @param CommentExtension $commentExtension
	 */
	public function setCommentExtension(CommentExtension $commentExtension)
	{
	    $this->comment = $commentExtension;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMigrationVersion()
	{
	    return 'v1_5';
	}

	/**
	 * {@inheritdoc}
	 */
	public function setActivityExtension(ActivityExtension $activityExtension)
	{
	    $this->activityExtension = $activityExtension;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
	    /** Tables generation **/
		  $this->createDmkclubMemberTable($schema);
		  $this->createDmkclubMemberBillingTable($schema);
		  $this->createDmkclubMemberFeeTable($schema);
		  $this->createDmkclubMemberFeepositionTable($schema);

	    /** Foreign keys generation **/
			$this->addDmkclubMemberForeignKeys($schema);
			$this->addDmkclubMemberBillingForeignKeys($schema);
			$this->addDmkclubMemberFeeForeignKeys($schema);
			$this->addDmkclubMemberFeepositionForeignKeys($schema);

	    $this->comment->addCommentAssociation($schema, 'dmkclub_member');
	}


	/**
	 * Create dmkclub_member table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_member');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('bank_account', 'integer', ['notnull' => false]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('postal_address', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
		$table->addColumn('account_id', 'integer', ['notnull' => false]);
		$table->addColumn('contact_id', 'integer', ['notnull' => false]);
		$table->addColumn('member_code', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('start_date', 'date', ['notnull' => false]);
		$table->addColumn('end_date', 'date', ['notnull' => false]);
		$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('created_at', 'datetime', []);
		$table->addColumn('updated_at', 'datetime', []);
		$table->addColumn('is_active', 'boolean', ['default' => '0']);
		$table->addColumn('status', 'string', ['default' => 'active', 'notnull' => false, 'length' => 20]);
		$table->addColumn('payment_option', 'string', ['default' => 'none', 'notnull' => false, 'length' => 20]);
		$table->addColumn('is_honorary', 'boolean', ['default' => '0']);
		$table->addColumn('is_free_of_charge', 'boolean', ['default' => '0']);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['contact_id'], 'IDX_6A79FCCDE7A1254A', []);
		$table->addIndex(['postal_address'], 'IDX_6A79FCCD972EFBF7', []);
		$table->addIndex(['user_owner_id'], 'IDX_6A79FCCD9EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_6A79FCCD32C8A3DE', []);
		$table->addIndex(['account_id'], 'IDX_6A79FCCD9B6B5FBA', []);
		$table->addIndex(['data_channel_id'], 'IDX_6A79FCCDBDC09B73', []);
		$table->addIndex(['bank_account'], 'IDX_6A79FCCD53A23E0A', []);
	}

	/**
	 * Create dmkclub_member_billing table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberBillingTable(Schema $schema) {
		$table = $schema->createTable('dmkclub_member_billing');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('segment_id', 'integer', ['notnull' => false]);
		$table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('start_date', 'date', ['notnull' => false]);
		$table->addColumn('end_date', 'date', ['notnull' => false]);
		$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('fee_total', 'integer', ['notnull' => false]);
		$table->addColumn('created_at', 'datetime', []);
		$table->addColumn('updated_at', 'datetime', []);
		$table->addColumn('processor', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('processor_config', 'text', ['notnull' => false]);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['user_owner_id'], 'IDX_25B89C799EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_25B89C7932C8A3DE', []);
		$table->addIndex(['data_channel_id'], 'IDX_25B89C79BDC09B73', []);
		$table->addIndex(['segment_id'], 'IDX_25B89C79DB296AAD', []);
	}

	/**
	 * Create dmkclub_member_fee table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberFeeTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_member_fee');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('member', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
		$table->addColumn('billing', 'integer', ['notnull' => false]);
		$table->addColumn('start_date', 'date', ['notnull' => false]);
		$table->addColumn('end_date', 'date', ['notnull' => false]);
		$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('created_at', 'datetime', []);
		$table->addColumn('updated_at', 'datetime', []);
		$table->addColumn('price_total', 'money', ['notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['member'], 'IDX_B0418BD970E4FA78', []);
		$table->addIndex(['user_owner_id'], 'IDX_B0418BD99EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_B0418BD932C8A3DE', []);
		$table->addIndex(['data_channel_id'], 'IDX_B0418BD9BDC09B73', []);
		$table->addIndex(['billing'], 'IDX_B0418BD9EC224CAA', []);
	}
	/**
	 * Create dmkclub_member_feeposition table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberFeepositionTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_member_feeposition');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
		$table->addColumn('member_fee', 'integer', ['notnull' => false]);
		$table->addColumn('quantity', 'float', ['notnull' => false]);
		$table->addColumn('unit', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('description', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('price_single', 'money', ['notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
		$table->addColumn('price_total', 'money', ['notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
		$table->addColumn('tax_amount', 'money', ['precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
		$table->addColumn('sort_order', 'integer', []);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['member_fee'], 'IDX_1ACE617A7ED44EE', []);
		$table->addIndex(['data_channel_id'], 'IDX_1ACE617BDC09B73', []);
	}

	/**
	 * Add dmkclub_member foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_member');
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_bankaccount'),
				['bank_account'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_organization'),
				['organization_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_address'),
				['postal_address'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_user'),
				['user_owner_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('orocrm_channel'),
				['data_channel_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('orocrm_account'),
				['account_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('orocrm_contact'),
				['contact_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}

	/**
	 * Add dmkclub_member_billing foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberBillingForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_member_billing');
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_segment'),
				['segment_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('orocrm_channel'),
				['data_channel_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_organization'),
				['organization_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_user'),
				['user_owner_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}

	/**
	 * Add dmkclub_member_fee foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberFeeForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_member_fee');
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_member_billing'),
				['billing'],
				['id'],
				['onDelete' => 'CASCADE', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_organization'),
				['organization_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_member'),
				['member'],
				['id'],
				['onDelete' => 'CASCADE', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_user'),
				['user_owner_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('orocrm_channel'),
				['data_channel_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}
	/**
	 * Add dmkclub_member_feeposition foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberFeepositionForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_member_feeposition');
		$table->addForeignKeyConstraint(
				$schema->getTable('orocrm_channel'),
				['data_channel_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_member_fee'),
				['member_fee'],
				['id'],
				['onDelete' => 'CASCADE', 'onUpdate' => null]
		);
	}
}
