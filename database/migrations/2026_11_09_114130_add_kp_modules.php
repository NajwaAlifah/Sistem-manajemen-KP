<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->text('address')->nullable();
            $t->string('bidang')->nullable();
            $t->string('phone', 30)->nullable();
            $t->timestamps();
        });

        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete(); // perusahaan pembimbing lapangan
            $t->foreignId('lapangan_id')->nullable()->constrained('users')->nullOnDelete();    // pembimbing lapangan mahasiswa
        });

        Schema::table('kp_data', function (Blueprint $t) {
            $t->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $t->string('status', 30)->default('menunggu_persetujuan');
            $t->text('catatan_admin')->nullable();
            $t->timestamp('dikonfirmasi_at')->nullable();   // konfirmasi pelaksanaan oleh pembimbing lapangan
            $t->boolean('nilai_published')->default(false);
        });

        Schema::table('categories', function (Blueprint $t) {
            $t->date('deadline')->nullable();
            $t->boolean('wajib')->default(true);
        });

        Schema::create('logbooks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('tanggal');
            $t->text('kegiatan');
            $t->string('file_path')->nullable();
            $t->string('file_name')->nullable();
            $t->string('status', 20)->default('menunggu'); // menunggu | disetujui | revisi
            $t->text('catatan')->nullable();
            $t->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('validated_at')->nullable();
            $t->timestamps();
        });

        Schema::create('guidances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $t->dateTime('tanggal_usul');
            $t->dateTime('tanggal_final')->nullable();
            $t->string('metode', 10)->default('daring');
            $t->string('topik');
            $t->string('status', 20)->default('diajukan'); // diajukan | disetujui | jadwal_diubah | selesai
            $t->text('catatan_dosen')->nullable();
            $t->string('lampiran_path')->nullable();
            $t->string('lampiran_name')->nullable();
            $t->timestamps();
        });

        Schema::create('grades', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('komponen', 20); // dosen | lapangan | seminar
            $t->decimal('nilai', 5, 2);
            $t->text('catatan')->nullable();
            $t->foreignId('graded_by')->constrained('users');
            $t->timestamps();
            $t->unique(['user_id', 'komponen']);
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
        });

        Schema::create('notifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('body')->nullable();
            $t->string('url')->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'read_at']);
        });

        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->text('body');
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 60);
            $t->text('detail')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index('action');
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'announcements', 'notifications', 'settings', 'grades', 'guidances', 'logbooks'] as $x) Schema::dropIfExists($x);
        Schema::table('categories', fn (Blueprint $t) => $t->dropColumn(['deadline', 'wajib']));
        Schema::table('kp_data', function (Blueprint $t) { $t->dropConstrainedForeignId('company_id'); $t->dropColumn(['status', 'catatan_admin', 'dikonfirmasi_at', 'nilai_published']); });
        Schema::table('users', function (Blueprint $t) { $t->dropConstrainedForeignId('company_id'); $t->dropConstrainedForeignId('lapangan_id'); });
        Schema::dropIfExists('companies');
    }
};
