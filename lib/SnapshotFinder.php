<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Finder\Finder;
use Tightenco\Collect\Support\Collection;

class SnapshotFinder
{
    /**
     * Constructor
     *
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Get all snapshots
     *
     * @return Collection
     */
    public function findIn(string $snapshotPath): Collection
    {
        $snapshots = $this->finder
            ->directories()
            ->depth(0)
            ->in($snapshotPath);

        return collect($snapshots)->mapInto(Snapshot::class);
    }
}