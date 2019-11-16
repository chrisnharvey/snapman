<?php

namespace App;

use App\Command\ListCommand;
use App\Command\PurgeCommand;
use App\Command\SnapshotCommand;
use Encore\Application as EncoreApplication;

class Application extends EncoreApplication
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'Snapman';

    /**
     * {@inheritdoc}
     */
    protected $version = 'dev';

    /**
     * {@inheritdoc}
     */
    public function commands(): array
    {
        return [
            'list' => ListCommand::class,
            'snapshot' => SnapshotCommand::class,
            'purge' => PurgeCommand::class
        ];
    }
}