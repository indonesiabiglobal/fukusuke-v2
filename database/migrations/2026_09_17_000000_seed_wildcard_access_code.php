<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('access')->insertOrIgnore([
            'access_name' => 'Full Access (Super Admin)',
            'code' => '*',
            'description' => 'Bypasses every access: middleware check on every gated route.',
            'status' => 1,
        ]);
    }

    public function down(): void
    {
        DB::table('access')->where('code', '*')->delete();
    }
};
