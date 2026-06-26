<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = App\Models\User::where('role', 'kepala_unit')->first();
$q = App\Models\Letter::whereHas('dispositions', function($sq) use ($u) {
    $sq->where('to_user_id', $u->id)->where('status', 'pending');
});
echo "Count before: " . $q->count() . "\n";

$l = App\Models\Letter::create([
    'type' => 'external',
    'external_sender_name' => 'External Guy',
    'created_by_user_id' => 1,
    'status' => 'in_consideration',
    'subject' => 'Test External',
    'body' => 'Body',
    'letter_number' => '123'
]);
App\Models\Disposition::create([
    'letter_id' => $l->id,
    'from_user_id' => 1,
    'to_user_id' => $u->id,
    'status' => 'pending',
    'note' => 'test'
]);
echo "Count after: " . $q->count() . "\n";
$l->delete();
