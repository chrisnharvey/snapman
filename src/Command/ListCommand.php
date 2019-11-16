<?php

declare(strict_types=1);

namespace App\Command;

use App\SnapshotFinder;
use Encore\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
     * Constructor
     *
     * @param SnapshotFinder $snapshotFinder
     */
    public function __construct(SnapshotFinder $snapshotFinder)
    {
        $this->snapshotFinder = $snapshotFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('list')
            ->setDescription('Lists current snapshots');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $snapshots = $this->snapshotFinder->all();

        $table = new Table($output);

        $table
            ->setHeaders(['Name', 'Path', 'Created At', 'Age'])
            ->setRows($snapshots->map(function ($snapshot) {
                return [
                    $snapshot->getName(),
                    $snapshot->getPath(),
                    $snapshot->getTimestamp()->calendar(),
                    $snapshot->getTimestamp()->longAbsoluteDiffForHumans()
                ];
            })->values()->toArray());

        $table->render();
    }
}
