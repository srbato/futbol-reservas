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
        // Clear existing plaintext tokens so the new encrypted cast doesn't fail
        // Venue admins will need to reconnect their Mercado Pago accounts once
        \DB::table('venues')->update([
            'mp_access_token'  => null,
            'mp_refresh_token' => null,
        ]);
    }

    public function down(): void
    {
        // Nothing to reverse — tokens were already plaintext before
    }
};
