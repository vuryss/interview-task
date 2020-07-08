<?php
/** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace App\Command;

use App\Entity\Transaction;
use App\Parser\FileParser;
use App\Payment\CommissionCalculator;
use Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class CalculateCommissionsCommand extends Command
{
    protected static $defaultName = 'app:calculate-commission';

    private FileParser           $fileParser;
    private CommissionCalculator $commissionCalculator;
    private LoggerInterface      $logger;

    public function __construct(
        FileParser $fileParser,
        CommissionCalculator $commissionCalculator,
        LoggerInterface $logger
    ) {
        $this->fileParser           = $fileParser;
        $this->commissionCalculator = $commissionCalculator;
        $this->logger               = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Calculates commission based on input data')
            ->addArgument('file', InputArgument::REQUIRED, 'Input json data file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $commissions = $this->getCommissionsForTransactionInFile($input->getArgument('file'));

            foreach ($commissions as $commission) {
                $output->writeln($commission);
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->logger->error(
                'Could not calculate transaction commissions'
                . ' Exception: ' . get_class($e)
                . ' Message: ' . $e->getMessage()
                . ' Stack trace: ' . $e->getTraceAsString()
            );

            $output->writeln('Error occurred while trying to calculate commissions for the transactions.');

            return Command::FAILURE;
        }
    }

    /**
     * @throws Throwable
     *
     * @param string $filePath
     *
     * @return Generator
     */
    private function getCommissionsForTransactionInFile(string $filePath): Generator
    {
        $inputGenerator = $this->fileParser->parseFile($filePath);

        foreach ($inputGenerator as $object) {
            $transaction = Transaction::createFromObject($object);
            yield $this->commissionCalculator->calculateForTransaction($transaction);
        }
    }
}
