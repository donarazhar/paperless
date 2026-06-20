<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Letter;
use App\Models\LetterHistory;
use App\Models\Disposition;
use App\Models\User;
use App\Models\Unit;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $users = User::all();
        $units = Unit::all();

        $stafTu = $users->where('role', 'staf_tu')->first();
        $kasubagTu = $users->where('role', 'kasubag_tu')->first();
        $kepalaSekretariat = $users->where('role', 'kepala_sekretariat')->first();
        
        $stafUnits = $users->where('role', 'staf_unit');

        $statuses = ['pending_agenda', 'in_consideration', 'completed'];

        DB::beginTransaction();

        try {
            $letterCount = 1;
            
            foreach ($units as $unit) {
                $unitStaf = $unit->users->first() ?? $stafUnits->random();

                // 1. 10 Surat Masuk Internal
                for ($i = 0; $i < 10; $i++) {
                    $sender = $stafUnits->where('unit_id', '!=', $unit->id)->random() ?? $stafUnits->random();
                    $this->createLetter(
                        $faker, $letterCount++, 'internal', $sender->id, $unit->id, $sender->id, $statuses, $stafTu, $kasubagTu, $kepalaSekretariat
                    );
                }

                // 2. 10 Surat Masuk Eksternal
                for ($i = 0; $i < 10; $i++) {
                    $this->createLetter(
                        $faker, $letterCount++, 'external', null, $unit->id, $stafTu->id, $statuses, $stafTu, $kasubagTu, $kepalaSekretariat
                    );
                }

                // 3. 10 Surat Keluar Internal
                for ($i = 0; $i < 10; $i++) {
                    $recipientUnit = $units->where('id', '!=', $unit->id)->random() ?? $units->random();
                    $this->createLetter(
                        $faker, $letterCount++, 'internal', $unitStaf->id, $recipientUnit->id, $unitStaf->id, $statuses, $stafTu, $kasubagTu, $kepalaSekretariat
                    );
                }

                // 4. 10 Surat Keluar Eksternal
                for ($i = 0; $i < 10; $i++) {
                    $this->createLetter(
                        $faker, $letterCount++, 'outbound_external', $unitStaf->id, null, $unitStaf->id, ['completed'], $stafTu, $kasubagTu, $kepalaSekretariat
                    );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createLetter($faker, $index, $type, $fromUserId, $toUnitId, $createdBy, $statuses, $stafTu, $kasubagTu, $kepalaSekretariat)
    {
        $status = $faker->randomElement($statuses);
        $createdAt = Carbon::now()->subDays(rand(1, 60))->subHours(rand(1, 24));

        $letter = new Letter();
        $letter->type = $type;
        $letter->subject = $faker->sentence(rand(3, 6));
        $letter->body = $faker->paragraphs(rand(1, 3), true);
        $letter->status = $status;
        
        $prefix = match($type) {
            'internal' => 'SRT-INT',
            'external' => 'SRT-EXT',
            'outbound_external' => 'SRT-OUT-EXT',
            default => 'SRT'
        };

        $letter->letter_number = $prefix . '-' . str_pad($index, 5, '0', STR_PAD_LEFT) . '/' . date('Y');

        if ($status !== 'pending_agenda') {
            $letter->agenda_number = 'AGD-' . str_pad($index, 5, '0', STR_PAD_LEFT) . '/' . date('Y');
        } else {
            $letter->agenda_number = null;
        }

        if ($type === 'external') {
            $letter->external_sender_name = $faker->company;
        }
        if ($type === 'outbound_external') {
            $letter->external_recipient_name = $faker->company;
            $letter->external_notes = $faker->sentence();
        }

        $letter->from_user_id = $fromUserId;
        $letter->created_by_user_id = $createdBy;
        $letter->to_unit_id = $toUnitId;
        
        $letter->created_at = $createdAt;
        $letter->updated_at = $createdAt;
        $letter->save();

        LetterHistory::insert([
            'letter_id' => $letter->id,
            'user_id' => $createdBy,
            'action' => 'created',
            'note' => 'Surat baru ditambahkan ke sistem.',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        if ($type !== 'outbound_external' && ($status === 'in_consideration' || $status === 'completed')) {
            $dispDate1 = clone $createdAt;
            $dispDate1->addHours(rand(1, 4));
            
            LetterHistory::insert([
                'letter_id' => $letter->id,
                'user_id' => $stafTu->id,
                'action' => 'agenda_set',
                'note' => 'Nomor agenda dan nomor surat telah diberikan.',
                'created_at' => $dispDate1,
                'updated_at' => $dispDate1,
            ]);

            $dispDate2 = clone $dispDate1;
            $dispDate2->addHours(rand(1, 24));
            
            $dispNote = $faker->sentence;

            Disposition::insert([
                'letter_id' => $letter->id,
                'from_user_id' => $kasubagTu->id,
                'to_user_id' => rand(0, 1) === 1 ? $kepalaSekretariat->id : null,
                'to_unit_id' => $toUnitId,
                'note' => $dispNote,
                'created_at' => $dispDate2,
                'updated_at' => $dispDate2,
            ]);

            LetterHistory::insert([
                'letter_id' => $letter->id,
                'user_id' => $kasubagTu->id,
                'action' => 'disposed',
                'note' => $dispNote,
                'created_at' => $dispDate2,
                'updated_at' => $dispDate2,
            ]);
            
            if ($status === 'completed') {
                $dispDate3 = clone $dispDate2;
                $dispDate3->addHours(rand(1, 24));
                LetterHistory::insert([
                    'letter_id' => $letter->id,
                    'user_id' => $stafTu->id,
                    'action' => 'completed',
                    'note' => 'Surat telah selesai diproses dan masuk arsip.',
                    'created_at' => $dispDate3,
                    'updated_at' => $dispDate3,
                ]);
            }
        }
    }
}
