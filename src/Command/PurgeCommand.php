<?php

namespace App\Command;

use App\SnapshotFinder;
use Encore\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class PurgeCommand extends Command
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
            ->setName('purge')
            ->setDescription('Purge old snapshots')
            ->addOption(
                'older-than',
                null,
                InputOption::VALUE_REQUIRED,
                'How old should a snapshot be (in days) to be purged?',
                3
            );;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $snapshots = $this->snapshots->all()->filter(function ($snapshot) use ($input) {
            return $snapshot->isOlderThanDays(
                $input->getOption('older-than')
            );
        });

        if ($snapshots->isEmpty()) {
            $output->writeln('<fg=red>No snapshots to delete</>');
            return;
        }

        $output->writeln('<fg=red>The following snapshots will be deleted</>');

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


        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to delete these snapshots?', true);

        if (! $helper->ask($input, $output, $question)) {
            return;
        }

        foreach ($snapshots as $snapshot) {
            $snapshot->delete();
        }

        $output->writeln('Snapshots deleted');
    }
}