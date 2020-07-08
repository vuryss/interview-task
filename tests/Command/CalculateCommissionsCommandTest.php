<?php

namespace Command;

use App\Command\CalculateCommissionsCommand;
use App\Entity\Transaction;
use App\Parser\FileParser;
use App\Payment\CommissionCalculator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CalculateCommissionsCommandTest extends TestCase
{
    public function testCanExecuteCalculateCommissionsCommand()
    {

        $fileParser = $this->createMock(FileParser::class);
        $fileParser
            ->expects($this->once())
            ->method('parseFile')
            ->with('/path/to/some/some-file.txt')
            ->willReturn(
                (function() { yield (object) ['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR']; })()
            );
        $commissionCalculator = $this->createMock(CommissionCalculator::class);
        $commissionCalculator
            ->expects($this->once())
            ->method('calculateForTransaction')
            ->with($this->isInstanceOf(Transaction::class))
            ->willReturn(
                (function() { yield '1.23'; })()
            );

        $logger = $this->createMock(LoggerInterface::class);

        $application = new Application();
        $application->add(
            new CalculateCommissionsCommand(
                $fileParser,
                $commissionCalculator,
                $logger
            )
        );
        $command = $application->find('app:calculate-commission');

        $tester = new CommandTester($command);
        $tester->execute(['file' => '/path/to/some/some-file.txt']);

        $this->assertTrue($command instanceof Command);

        $this->assertStringContainsString('1.23', $tester->getDisplay());
    }
}
