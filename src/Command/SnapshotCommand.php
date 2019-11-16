<?php

namespace App\Command;

use App\Snapshot;
use App\SnapshotFinder;
use Encore\Command;
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
            ->setDescription('Take a snapshot of the current system state');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $snapshot = Snapshot::create();

        $output->writeln("<fg=green>Snapshot created at {$snapshot->getPath()}</>");
    }
}