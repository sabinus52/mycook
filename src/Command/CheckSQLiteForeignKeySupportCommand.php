<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('sqlite:check-foreign-key-support')]
class CheckSQLiteForeignKeySupportCommand extends Command
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('connection', 'c', InputOption::VALUE_REQUIRED, 'Name of the connection to check');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $connectionName = $input->getOption('connection');
        if ($connectionName) {
            $connections = $this->registry->getConnectionNames();
            if (!isset($connections[$connectionName])) {
                $error = sprintf('“%s” connection does not exist. ', $connectionName);
                $error .= match (count($connections)) {
                    0 => 'None has been configured.',
                    1 => 'You can remove the option to use the default one.',
                    default => sprintf('Try one of “%s”.', implode('”, “', array_keys($connections))),
                };
                $style->error($error);

                return Command::FAILURE;
            }
        }

        $connection = $this->registry->getConnection($connectionName);
        assert($connection instanceof Connection);

        $platformClass = $connection->getDatabasePlatform()::class;
        if (SqlitePlatform::class !== $platformClass) {
            $style->warning(sprintf(
                'Got %s instead of SqlitePlatform, aborting.',
                substr((string) strrchr((string) $platformClass, '\\'), 1)
            ));

            return Command::FAILURE;
        }

        /* @phpstan-ignore-next-line */
        match ($connection->executeQuery('PRAGMA foreign_keys')->fetchOne()) {
            false => $style->error('Foreign keys are not supported.'),
            0 => $style->warning('Foreign keys support is disabled.'),
            1 => $style->success('Foreign keys support is enabled.'),
        };

        return Command::SUCCESS;
    }
}
