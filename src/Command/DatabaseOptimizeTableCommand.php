<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Entity\Table;
use App\Domain\Repository\FindsTable;
use App\Domain\Repository\OptimizesTable;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table as IOTable;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('database:optimize-table')]
final class DatabaseOptimizeTableCommand extends Command
{
    private Table $table;

    public function __construct(
        private readonly FindsTable $findTableRepository,
        private readonly OptimizesTable $optimizeTableRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('table', InputArgument::REQUIRED, 'The name of the table you want to optimize');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        \assert(\is_string($tableName = $input->getArgument('table')), 'tablename must be a string');
        $this->table = $this->findTableRepository->findByName($tableName) ?? throw new RuntimeException('table not found');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Optimizing Table {$this->table->tableName}");
        $messages = $this->optimizeTableRepository->optimize($this->table);

        $table = new IOTable($output);
        $table->setHeaders([
            'table',
            'query',
            'message_type',
            'message',
        ]);
        $table->addRows($messages);
        $table->render();

        return Command::SUCCESS;
    }
}
