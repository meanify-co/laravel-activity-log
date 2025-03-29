<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->nullable();
            $table->string('user_id')->nullable();
            $table->uuid('request_uuid')->nullable();
            $table->uuid('telescope_uuid')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('method');
            $table->string('uri');
            $table->integer('status_code');
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->uuid('request_uuid')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('model');
            $table->unsignedBigInteger('model_id');
            $table->string('action');
            $table->json('original')->nullable();
            $table->json('changes')->nullable();
            $table->json('final')->nullable();
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('request_logs');
        Schema::dropIfExists('activity_logs');
    }
};
