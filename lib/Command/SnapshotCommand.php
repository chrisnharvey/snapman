<?php

namespace App\Command;

use App\Snapshot;
use App\SnapshotFinder;
use Encore\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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
            ->addOption(
                'skip-grub-config',
                null,
                InputOption::VALUE_NONE,
                "Don't generate grub config after creating snapshot",
                null
            )
            ->addArgument(
                'subvolume-path',
                InputArgument::OPTIONAL,
                "Btrfs mount point to create the snapshot on",
                '/'
            )
            ->addArgument(
                'snapshot-subvolume-path',
                InputArgument::OPTIONAL,
                "Btrfs snapshot subvolume path",
                '/snapshots'
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
                'snapshot-subvolume-path' => $input->getArgument('snapshot-subvolume-path'),
                '--older-than' => $purgeOlderThan
            ]), $output);
        }

        $snapshot = Snapshot::createTimestampedSnapshot(
            $input->getArgument('subvolume-path'),
            $input->getArgument('snapshot-subvolume-path')
        );

        $output->writeln("<fg=green>Snapshot created at {$snapshot->getPath()}</>");

        if (! $input->getOption('skip-grub-config')) {
            $output->writeln('Generating grub config...');

            $process = new Process(['grub-mkconfig', '-o', '/boot/grub/grub.cfg']);

            $process->mustRun();

            $output->writeln('<fg=green>Grub config generated successfully</>');
        }
    }
}