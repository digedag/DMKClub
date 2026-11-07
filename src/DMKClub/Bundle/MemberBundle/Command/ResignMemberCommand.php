<?php
namespace DMKClub\Bundle\MemberBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Oro\Bundle\CronBundle\Command\CronCommandActivationInterface;
use Oro\Bundle\CronBundle\Command\CronCommandScheduleDefinitionInterface;

use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;

/**
 * Update member status if resignation date is reached.
 */
#[AsCommand(
    name: self::NAME,
    description: 'Command to update member status if resignation date is reached.'
)]
class ResignMemberCommand extends Command implements
    CronCommandScheduleDefinitionInterface,
    CronCommandActivationInterface
{

    const NAME = 'oro:cron:dmkclub:member:resign';

    /**
     *
     * @var MemberManager
     */
    private $mbrMgr;

    public function __construct(MemberManager $mbrMgr)
    {
        parent::__construct();
        $this->mbrMgr = $mbrMgr;
    }

    public function isActive()
    {
        return true;
    }

    public function getDefaultDefinition()
    {
        return '10 0 * * *';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf('<info>Resigned %d members.</info>', $this->mbrMgr->checkResignedMembers()));

        return Command::SUCCESS;
    }
}
