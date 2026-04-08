<?php

namespace App\Console\Commands;

use App\Services\BloodRequestService;
use Illuminate\Console\Command;

class ArchiveBloodRequestsCommand extends Command
{
    protected $signature = 'blood-requests:archive';

    protected $description = 'Archive blood requests older than seven days';

    public function handle(BloodRequestService $bloodRequestService): int
    {
        $archived = $bloodRequestService->archiveExpired();

        $this->info("Archived {$archived} blood requests.");

        return self::SUCCESS;
    }
}
