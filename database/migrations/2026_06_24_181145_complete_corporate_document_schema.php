<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            if (! Schema::hasColumn('areas', 'name')) {
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
            }
        });

        Schema::table('departments', function (Blueprint $table) {
            if (! Schema::hasColumn('departments', 'area_id')) {
                $table->foreignId('area_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->string('name');
                $table->string('slug')->unique();
                $table->string('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->string('position')->nullable();
                $table->string('phone')->nullable();
                $table->string('extension')->nullable();
                $table->string('avatar_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_login_at')->nullable();
            }
        });

        Schema::table('document_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('document_categories', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->string('name');
                $table->string('slug')->unique();
                $table->string('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (! Schema::hasColumn('documents', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->foreignId('document_category_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->string('title');
                $table->string('slug')->unique();
                $table->text('summary')->nullable();
                $table->longText('content')->nullable();
                $table->string('source_type')->default('upload');
                $table->string('document_type')->default('other');
                $table->string('file_path')->nullable();
                $table->string('original_filename')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size_bytes')->nullable();
                $table->string('external_url')->nullable();
                $table->string('status')->default('draft');
                $table->string('visibility')->default('department');
                $table->string('version', 20)->default('1.0');
                $table->boolean('requires_read_confirmation')->default(false);
                $table->boolean('is_featured')->default(false);

                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('updated_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp('published_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('archived_at')->nullable();
                $table->jsonb('metadata')->nullable();

                $table->index(['department_id', 'status']);
                $table->index(['document_category_id', 'status']);
                $table->index(['status', 'published_at']);
            }
        });

        Schema::table('document_versions', function (Blueprint $table) {
            if (! Schema::hasColumn('document_versions', 'document_id')) {
                $table->foreignId('document_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('version', 20);
                $table->string('title')->nullable();
                $table->string('file_path')->nullable();
                $table->string('original_filename')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size_bytes')->nullable();
                $table->string('external_url')->nullable();
                $table->longText('content')->nullable();
                $table->text('change_summary')->nullable();

                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->unique(['document_id', 'version']);
            }
        });

        Schema::table('department_document', function (Blueprint $table) {
            if (! Schema::hasColumn('department_document', 'department_id')) {
                $table->foreignId('department_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('document_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('permission')->default('view');
                $table->unique(['department_id', 'document_id']);
            }
        });

        Schema::table('document_reads', function (Blueprint $table) {
            if (! Schema::hasColumn('document_reads', 'document_id')) {
                $table->foreignId('document_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->timestamp('read_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->unique(['document_id', 'user_id']);
            }
        });
    }

    public function down(): void
    {
        //
    }
};
