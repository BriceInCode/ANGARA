<?php

namespace App\Console\Commands;

use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredSessions extends Command
{
    protected $signature = 'sessions:check-expired';
    protected $description = 'Vérifie les sessions expirées et les met à jour à EXPIRE';

    public function handle()
    {
        Session::where('expires_at', '<', Carbon::now())
            ->whereNotIn('status', [
                \App\Enums\SessionStatus::REVOQUE,
                \App\Enums\SessionStatus::EN_ATTENTE,
                \App\Enums\SessionStatus::EXPIRE
            ])
            ->update(['status' => \App\Enums\SessionStatus::EXPIRE]);

        $this->info('Sessions expirées mises à jour.');
    }
}
