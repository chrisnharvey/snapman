<?php

namespace App\Command;

use App\Snapshot;
use App\SnapshotFinder;
use Encore\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SnapshotCommand extends Command
{
    /**
     * Constructor
     *
     * @param SnapshotFinder $snapshots
     */
    public function __construct(SnapshotFinder $snapshots)
    {
        $this->snapshots = $snapshots;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('snapshot')
            ->addOption(
                'purge-older-than',
                null,
                InputOption::VALUE_OPTIONAL,
                'How old should a snapshot be (in days) to be purged?',
                null
            )
            ->setDescription('Take a snapshot of the current system state');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($purgeOlderThan = $input->getOption('purge-older-than')) {
            $command = $this->getApplication()->find('purge');

            $command->run(new ArrayInput([
                '--older-than' => $purgeOlderThan
            ]), $output);
        }

        $snapshot = Snapshot::create();

        $output->writeln("<fg=green>Snapshot created at {$snapshot->getPath()}</>");
    }
}