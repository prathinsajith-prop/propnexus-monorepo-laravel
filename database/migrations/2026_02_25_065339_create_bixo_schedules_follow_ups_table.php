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
        Schema::create('bixo_schedules_follow_ups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('division_id')->nullable();
            $table->uuid('uuid')->nullable();
            $table->string('title', 100)->nullable();
            $table->string('status', 30)->nullable();
            $table->string('priority', 30)->nullable();
            $table->integer('assigned_to')->nullable();
            $table->integer('contact_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->datetime('start_date')->nullable();
            $table->time('estimate')->nullable();
            $table->text('description')->nullable();
            $table->text('documents')->nullable();
            $table->text('comments')->nullable();
            $table->string('sub_type', 150)->nullable();
            $table->text('details')->nullable();
            $table->integer('property_id')->nullable();
            $table->integer('sort_order')->nullable();
            $table->integer('subject_id')->nullable();
            $table->string('subject_type', 100)->nullable();
            $table->string('user_type', 50)->nullable();
            $table->string('group_by', 150)->nullable();
            $table->string('call_direction', 100)->nullable();
            $table->text('attachments')->nullable();
            $table->string('type', 50)->nullable();
            $table->integer('opportunity_id')->nullable();
            $table->string('meeting_type', 100)->nullable();
            $table->text('meeting_data')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bixo_schedules_follow_ups');
    }
};
