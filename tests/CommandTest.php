<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Entity\Table;
use App\Domain\Repository\OptimizesTable;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DatabaseOptimizeTableCommandTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel();

        // trying to replace the optimizesTable interface with my mock
        self::getContainer()->set(OptimizesTable::class, new class implements OptimizesTable {
            public function optimize(Table $table): array
            {
                return [
                    [
                        'Table' => 'test.v_u',
                        'Op' => 'optimize',
                        'Msg_type' => 'note',
                        'Msg_text' => 'Table does not support optimize, doing recreate + analyze instead',
                    ],
                    [
                        'Table' => 'test.v_u',
                        'Op' => 'optimize',
                        'Msg_type' => 'status',
                        'Msg_text' => 'OK',
                    ],
                ];
            }
        });
    }

    public function test(): void
    {
        $application = new Application(self::$kernel);
        $commandTester = new CommandTester($application->find('database:optimize'));

        $commandTester->execute([
            'table' => 'foo',
        ]);

        static::assertSame(
            <<<OUTPUT
                Optimizing Table v_u
                +----------+----------+--------------+-------------------------------------------------------------------+
                | table    | query    | message_type | message                                                           |
                +----------+----------+--------------+-------------------------------------------------------------------+
                | test.foo | optimize | note         | Table does not support optimize, doing recreate + analyze instead |
                | test.foo | optimize | status       | OK                                                                |
                +----------+----------+--------------+-------------------------------------------------------------------+

                OUTPUT,
            $commandTester->getDisplay()
        );
    }
}
