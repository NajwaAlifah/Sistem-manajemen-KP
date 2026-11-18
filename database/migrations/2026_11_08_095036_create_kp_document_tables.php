<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Data pengguna: role + relasi mahasiswa -> dosen pembimbing (REQ-MASTER-07)
        Schema::table('users', function (Blueprint $t) {
            $t->enum('role', ['mahasiswa', 'dosen', 'admin', 'lapangan'])->default('mahasiswa');
            $t->string('nim', 20)->nullable()->unique();
            $t->string('nip', 30)->nullable()->unique();
            $t->string('phone', 20)->nullable();
            $t->string('profile_photo')->nullable();
            $t->boolean('is_active')->default(true);
            $t->foreignId('dosen_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->unsignedSmallInteger('max_file_size')->default(10); // MB, diatur admin (REQ-MASTER-06)
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        // Data KP mahasiswa (REQ-MASTER-01)
        Schema::create('kp_data', function (Blueprint $t) {
            $t->id();
            $t->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $t->string('perusahaan');
            $t->string('judul')->nullable();
            $t->date('tanggal_mulai')->nullable();
            $t->date('tanggal_selesai')->nullable();
            $t->timestamps();
        });

        Schema::create('documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('category_id')->constrained('categories');
            $t->string('name');
            $t->text('description')->nullable();
            $t->enum('status', ['draft', 'submitted', 'reviewed', 'revision', 'approved', 'archived'])->default('draft');
            $t->unsignedInteger('current_version')->default(1);
            $t->timestamp('submitted_at')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['status', 'category_id']);
        });

        // Riwayat versi (REQ-DOC-06)
        Schema::create('document_versions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('document_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('version');
            $t->string('path');
            $t->string('original_name');
            $t->string('mime', 100);
            $t->unsignedBigInteger('size');
            $t->string('status_at_upload', 20)->default('draft');
            $t->foreignId('uploaded_by')->constrained('users');
            $t->timestamps();
        });

        // Catatan review + riwayat status (REQ-REVIEW-08)
        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('document_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users');
            $t->unsignedInteger('version');
            $t->enum('type', ['revision', 'approved', 'rejected', 'comment']);
            $t->text('message')->nullable();
            $t->string('status_after', 20);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('kp_data');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('dosen_id');
            $t->dropColumn(['role', 'nim', 'nip', 'phone', 'profile_photo', 'is_active']);
        });
    }
};
