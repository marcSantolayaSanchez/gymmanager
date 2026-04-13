<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Notifications\MembershipExpiringSoon;
use Illuminate\Console\Command;

class NotifyExpiringMemberships extends Command
{
    protected $signature   = 'gym:notify-expiring {--days=7 : Days before expiry to notify}';
    protected $description = 'Envía email a clientes cuya membresía vence pronto';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $clients = Client::expiringSoon($days)
            ->with(['user', 'membership'])
            ->get();

        if ($clients->isEmpty()) {
            $this->info('No hay membresías por vencer en los próximos ' . $days . ' días.');
            return self::SUCCESS;
        }

        foreach ($clients as $client) {
            $client->user->notify(new MembershipExpiringSoon($client));
            $this->line("  📧 Notificación enviada a: {$client->user->name} ({$client->user->email})");
        }

        $this->info("✅ {$clients->count()} notificaciones enviadas.");

        return self::SUCCESS;
    }
}
