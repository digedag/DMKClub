<?php
namespace DMKClub\Bundle\MemberBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\LocaleBundle\Model\LocaleSettings;

use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Mailer\Processor;
use DMKClub\Bundle\BasicsBundle\Model\TemplateNotFoundException;

/**
 * Send membership fees to member by email.
 */
#[AsCommand(
    name: 'dmkclub:send:fee',
    description: 'Command to send a number of fees by email to member'
)]
class SendFeeMailsCommand extends Command
{

    const NAME = 'dmkclub:send:fee';

    const FLUSH_BATCH_SIZE = 100;

    protected $output;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /** @var LocaleSettings */
    private $localeSettings;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Processor */
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        Processor $mailer,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        LocaleSettings $localeSettings
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->localeSettings = $localeSettings;
    }

    protected function configure()
    {
        $this->addArgument('ids', InputArgument::REQUIRED, 'commaseperated id of fees to send');
    }

    /**
     *
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     *
     * @return Processor
     */
    protected function getMailer()
    {
        return $this->mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initLanguage();

        $this->output = $output;
        $ids = $input->getArgument('ids');
        try {
            $result = $this->processEmails(explode(',', $ids));
            $output->writeln(sprintf('<info>Job finished</info> with %d processed fees and %d errors', $result['cnt'], $result['errors']));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Job failed</error>'));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function processEmails($feeIds)
    {
        $iteration = 0;
        $errorCnt = 0;
        foreach ($feeIds as $feeId) {
            $iteration ++;
            $entity = $this->getEntityManager()
                ->getRepository(MemberFee::class)
                ->find($feeId);
            try {
                // SEND EMAIL
                $this->getMailer()->sendBillToMemberEmail($entity);
            } catch (TemplateNotFoundException $tnfe) {
                $this->output->writeln($tnfe->getMessage());
                $this->log($tnfe->getMessage());
                // Ohne Template wird keine Mail verschickt werden.
                throw $tnfe;
            } catch (\Exception $e) {
                $msg = sprintf('Fee mailing failed for member %s (%s), fee-id: %d, Error: %s', $entity->getMember()
                    ->getName(), $entity->getMember()
                    ->getId(), $entity->getId(), $e->getMessage());
                $this->log($msg);
                $this->output->writeln($msg);
                // Diese Exception wird nicht weitergegeben, damit der Rest versendet wird
                $errorCnt += 1;
            }

            if (($iteration % self::FLUSH_BATCH_SIZE) === 0) {
                $this->getEntityManager()->flush();
                $this->getEntityManager()->clear();
            }
        }
        return [
            'cnt' => $iteration,
            'errors' => $errorCnt
        ];
    }

    protected function log($msg)
    {
        $this->logger->critical($msg);
    }

    protected function initLanguage()
    {
        $this->translator->setLocale($this->localeSettings->getLanguage());
    }
}
