<?php

declare(strict_types=1);

namespace App;

use Carbon\Carbon;
use SplFileInfo;
use Symfony\Component\Process\Process;

class Snapshot
{
    /**
     * Constructor
     *
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
        $this->timestamp = Carbon::createFromFormat('Y-m-d_H:i:s', $file->getBasename());
    }

    /**
     * Create snapshot
     *
     * @return self
     */
    public static function create(): self
    {
        $date = date('Y-m-d_H:i:s');
        $snapshotPath = "/snapshots/{$date}";

        $process = new Process(['btrfs', 'subvolume', 'snapshot', '/', $snapshotPath]);

        $process->mustRun();

        return new self(new SplFileInfo($snapshotPath));
    }

    /**
     * Get the name of the snapshot
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->file->getBasename();
    }

    /**
     * Get the full path to the snapshot
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->file->getRealPath();
    }

    /**
     * Check if snapshot is older than the specified
     * days
     *
     * @param integer $days
     * @return boolean
     */
    public function isOlderThanDays(int $days): bool
    {
        return $this->timestamp->diffInDays() >= $days;
    }

    /**
     * Get the timestamp for the snapshot
     *
     * @return Carbon
     */
    public function getTimestamp(): Carbon
    {
        return $this->timestamp;
    }

    /**
     * Delete the snapshot
     *
     * @return void
     */
    public function delete()
    {
        $process = new Process(['btrfs', 'subvolume', 'delete', $this->getPath()]);

        $process->mustRun();
    }
}