<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // ==========================
            // 外部キー
            // ==========================
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // ==========================
            // 勤怠基本情報
            // ==========================
            $table->date('date')->comment('勤務日');

            $table->time('clock_in')
                ->nullable()
                ->comment('出勤時刻');

            $table->time('clock_out')
                ->nullable()
                ->comment('退勤時刻');

            // ==========================
            // 勤務時間（自動計算）
            // ==========================
            $table->time('total_time')
                ->nullable()
                ->comment('勤務合計時間（休憩控除後）');

            // ==========================
            // ステータス
            // ==========================
            $table->string('status')
                ->comment('勤務外 / 出勤中 / 休憩中 / 退勤済');

            // ==========================
            // 備考
            // ==========================
            $table->text('notes')
                ->nullable()
                ->comment('備考');

            $table->timestamps();

            // ==========================
            // 制約
            // ==========================
            $table->unique(['user_id', 'date'], 'attendances_user_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
