<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fitur review dihapus (model, relasi, dan UI tidak pernah ada/dipakai).
     * Migrasi ini membuang tabel 'reviews' pada database yang sudah ada.
     */
    public function up(): void
    {
        Schema::dropIfExists('reviews');
    }

    /**
     * Reverse the migrations.
     *
     * Fitur sudah dihapus permanen — tidak ada rollback yang membangun ulang tabel.
     */
    public function down(): void
    {
        // no-op
    }
};
