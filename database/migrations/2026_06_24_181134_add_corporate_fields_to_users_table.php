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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->string('position')->nullable()->after('profile');
            $table->string('phone')->nullable()->after('position');
            $table->string('extension')->nullable()->after('phone');
            $table->string('avatar_path')->nullable()->after('extension');

            $table->boolean('is_active')->default(true)->after('avatar_path');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');

            $table->dropColumn([
                'position',
                'phone',
                'extension',
                'avatar_path',
                'is_active',
                'last_login_at',
            ]);
        });
    }
};
