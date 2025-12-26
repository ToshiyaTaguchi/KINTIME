<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();

            // 申請者
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 対象勤怠
            $table->foreignId('attendance_id')
                ->constrained()
                ->cascadeOnDelete();

            // 修正内容
            $table->time('corrected_clock_in')->nullable();
            $table->time('corrected_clock_out')->nullable();

            // 休憩（複数対応）
            $table->json('corrected_breaks')->nullable();

            // 申請理由
            $table->text('reason');

            // 承認状態
            $table->foreignId('approval_status_id')
                ->constrained()
                ->comment('1:承認待ち 2:承認済み 3:却下');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
