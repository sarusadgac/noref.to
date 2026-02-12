<?php

namespace Database\Seeders;

use App\Enums\ReportStatus;
use App\Models\Link;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemUser = User::where('email', config('anonto.system_user_email'))->first();
        $admin = User::where('role', 'admin')->first();

        $suspiciousUrls = [
            'https://phishing-site.example.com/login',
            'https://malware-download.example.net/free-stuff',
            'https://scam-offers.example.org/win-prize',
            'https://fake-bank.example.com/verify-account',
            'https://phishing-site.example.com/reset-password',
            'https://spam-links.example.net/cheap-deals',
            'https://malware-download.example.net/update',
            'https://crypto-scam.example.org/invest-now',
        ];

        $comments = [
            'This URL leads to a phishing page that mimics a bank login.',
            'Distributing malware disguised as a software update.',
            'Advance-fee scam promising fake lottery winnings.',
            'Fake banking portal stealing credentials.',
            'Another phishing page from the same domain.',
            'Spam site pushing unsolicited advertisements.',
            'Drive-by download of malicious software.',
            'Crypto investment scam with guaranteed returns.',
        ];

        $creatorId = $systemUser?->id ?? User::factory()->system()->create()->id;

        foreach ($suspiciousUrls as $i => $url) {
            $link = Link::findOrCreateByUrl($url, $creatorId);

            // Skip if a report already exists for this link
            if (Report::where('link_id', $link->id)->exists()) {
                continue;
            }

            $status = ReportStatus::Pending;
            $resolvedBy = null;
            $resolvedAt = null;

            // Make the last 2 resolved so there's a mix
            if ($i >= 6 && $admin) {
                $status = $i === 6 ? ReportStatus::Resolved : ReportStatus::Dismissed;
                $resolvedBy = $admin->id;
                $resolvedAt = now()->subHours(rand(1, 48));
            }

            Report::create([
                'link_id' => $link->id,
                'email' => fake()->safeEmail(),
                'comment' => $comments[$i],
                'status' => $status,
                'resolved_by' => $resolvedBy,
                'resolved_at' => $resolvedAt,
            ]);
        }
    }
}
