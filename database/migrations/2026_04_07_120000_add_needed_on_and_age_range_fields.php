<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->date('needed_on')->nullable()->after('city');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('age_range')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropColumn('needed_on');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('age_range');
        });
    }
};
