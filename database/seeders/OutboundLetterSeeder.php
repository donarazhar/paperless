<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Letter;
use App\Models\LetterHistory;
use App\Models\User;
use App\Models\Unit;
use Faker\Factory as Faker;
use Carbon\Carbon;

class OutboundLetterSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $users = User::all();
        $units = Unit::all();

        // 100 Surat Keluar Internal
        for ($i = 1; $i <= 100; $i++) {
            $sender = $users->random();
            $recipientUnit = $units->where('id', '!=', $sender->unit_id)->random() ?? $units->random();
            
            $status = $faker->randomElement(['pending_agenda', 'in_consideration', 'completed']);
            $createdAt = Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 24));

            $letter = new Letter();
            $letter->type = 'internal';
            $letter->subject = 'Internal Outbound ' . $faker->sentence(rand(3, 6));
            $letter->body = $faker->paragraphs(rand(1, 3), true);
            $letter->status = $status;
            
            $letter->letter_number = 'SRT-INT-OUT-' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/' . date('Y');

            if ($status !== 'pending_agenda') {
                $letter->agenda_number = 'AGD-' . str_pad($i + 1000, 4, '0', STR_PAD_LEFT) . '/' . date('Y');
            } else {
                $letter->agenda_number = null;
            }

            $letter->from_user_id = $sender->id;
            $letter->created_by_user_id = $sender->id;
            $letter->to_unit_id = $recipientUnit->id;
            
            $letter->created_at = $createdAt;
            $letter->updated_at = $createdAt;
            $letter->save();

            LetterHistory::create([
                'letter_id' => $letter->id,
                'user_id' => $sender->id,
                'action' => 'Surat internal keluar dibuat',
                'note' => 'Surat dikirimkan ke ' . $recipientUnit->name,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // 100 Surat Keluar Eksternal
        for ($i = 1; $i <= 100; $i++) {
            $sender = $users->random();
            $createdAt = Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 24));

            $letter = new Letter();
            $letter->type = 'outbound_external';
            $letter->subject = 'External Outbound ' . $faker->sentence(rand(3, 6));
            $letter->body = $faker->paragraphs(rand(1, 3), true);
            $letter->status = 'completed'; // Outbound external is always completed automatically based on current controller logic
            $letter->letter_number = 'SRT-EXT-OUT-' . str_pad($i, 4, '0', STR_PAD_LEFT) . '/' . date('Y');
            
            $letter->external_recipient_name = $faker->company . ' ' . $faker->companySuffix;
            $letter->external_notes = $faker->sentence(rand(4, 10));

            $letter->from_user_id = $sender->id;
            $letter->created_by_user_id = $sender->id;
            
            $letter->created_at = $createdAt;
            $letter->updated_at = $createdAt;
            $letter->save();

            LetterHistory::create([
                'letter_id' => $letter->id,
                'user_id' => $sender->id,
                'action' => 'Surat eksternal keluar dibuat',
                'note' => 'Surat keluar dicatat.',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
