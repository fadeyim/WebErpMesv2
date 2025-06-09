<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('methods_locations_id')->nullable()->after('user_id');
            $table->foreign('methods_locations_id')->references('id')->on('methods_locations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['methods_locations_id']);
            $table->dropColumn('methods_locations_id');
        });
    }
};
