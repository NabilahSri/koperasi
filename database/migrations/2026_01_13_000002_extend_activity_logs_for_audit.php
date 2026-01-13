<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('action', 20)->nullable()->after('route_name');
            $table->string('model_type')->nullable()->after('action');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->json('changes')->nullable()->after('request_data');
            $table->text('message')->nullable()->after('changes');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['action', 'model_type', 'model_id', 'changes', 'message']);
        });
    }
};
