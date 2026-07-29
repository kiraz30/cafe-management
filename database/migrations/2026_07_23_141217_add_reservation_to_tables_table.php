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
    Schema::table('tables', function (Blueprint $table) {
        $table->string('reserved_name')->nullable()->after('status');
        $table->string('reserved_time')->nullable()->after('reserved_name');
        $table->text('reserved_notes')->nullable()->after('reserved_time');
    });
}

public function down(): void
{
    Schema::table('tables', function (Blueprint $table) {
        $table->dropColumn(['reserved_name', 'reserved_time', 'reserved_notes']);
    });
}
};
