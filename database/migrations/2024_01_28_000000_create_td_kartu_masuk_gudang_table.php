<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('td_kartu_masuk_gudang', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_palet');
            $table->integer('revisi');
            $table->string('nomer_dok');
            $table->unsignedBigInteger('printed_by')->nullable();
            $table->timestamp('printed_on')->nullable();
            $table->timestamps();

            $table->index(['nomor_palet', 'revisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('td_kartu_masuk_gudang');
    }
};
