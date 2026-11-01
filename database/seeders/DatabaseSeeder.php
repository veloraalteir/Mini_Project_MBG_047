<?php

namespace Database\Seeders;

use App\Models\Distribution;
use App\Models\Report;
use App\Models\ReportForwarding;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (password semua: password)
        $pelapor = User::create(['name' => 'Bu Rina', 'email' => 'pelapor@mbg.test', 'password' => 'password', 'role' => User::PELAPOR]);
        $pelapor2 = User::create(['name' => 'Pak Budi', 'email' => 'pelapor2@mbg.test', 'password' => 'password', 'role' => User::PELAPOR]);
        $pemantau = User::create(['name' => 'Pak Dedi', 'email' => 'pemantau@mbg.test', 'password' => 'password', 'role' => User::PEMANTAU]);
        $koordinator = User::create(['name' => 'Bu Sari', 'email' => 'koordinator@mbg.test', 'password' => 'password', 'role' => User::KOORDINATOR]);

        // Sekolah dummy
        $s1 = School::create(['nama' => 'SMP Harapan', 'alamat' => 'Jl. Melati No. 1']);
        $s2 = School::create(['nama' => 'SD Mentari', 'alamat' => 'Jl. Kenanga No. 5']);
        $s3 = School::create(['nama' => 'SMA Nusantara', 'alamat' => 'Jl. Mawar No. 9']);

        // Distribusi dummy: 3 per sekolah (1, 3, dan 7 hari lalu)
        $menus = ['Nasi, ayam goreng, sayur bening', 'Nasi, ikan, tumis kangkung', 'Nasi, telur, sayur sop'];
        foreach ([$s1, $s2, $s3] as $school) {
            foreach ([1, 3, 7] as $i => $daysAgo) {
                Distribution::create([
                    'school_id' => $school->id,
                    'tanggal' => now()->subDays($daysAgo)->toDateString(),
                    'menu' => $menus[$i],
                ]);
            }
        }

        $dist = fn (School $s) => Distribution::where('school_id', $s->id)->orderByDesc('tanggal')->get();
        $d1 = $dist($s1);
        $d2 = $dist($s2);
        $d3 = $dist($s3);

        $make = function (User $user, School $school, Distribution $d, string $judul, string $status, ?string $catatan = null) {
            return Report::create([
                'user_id' => $user->id,
                'school_id' => $school->id,
                'distribution_id' => $d->id,
                'judul' => $judul,
                'deskripsi' => 'Data dummy: beberapa siswa mengeluh mual dan sakit perut beberapa jam setelah makan.',
                'waktu_kejadian' => $d->tanggal->copy()->setTime(14, 0),
                'jumlah_terdampak' => rand(2, 12),
                'status' => $status,
                'catatan_pemantau' => $catatan,
            ]);
        };

        // Dua laporan pada distribusi yang sama (untuk menguji keterkaitan)
        $make($pelapor, $s1, $d1[1], 'Mual setelah makan siang', Report::DIAJUKAN);
        $make($pelapor2, $s1, $d1[1], 'Anak sakit perut sepulang sekolah', Report::DITINJAU);
        $make($pelapor, $s1, $d1[2], 'Keluhan pusing dan mual', Report::PERLU_DILENGKAPI, 'Mohon lengkapi jumlah siswa yang terdampak.');
        $make($pelapor2, $s2, $d2[0], 'Beberapa siswa mual', Report::PERLU_DITERUSKAN, 'Ada beberapa laporan serupa, perlu diteruskan ke sekolah.');
        $r5 = $make($pelapor, $s3, $d3[1], 'Siswa mengeluh sakit perut', Report::DITERUSKAN, 'Sudah ditinjau.');
        $make($pelapor2, $s3, $d3[2], 'Keluhan ringan', Report::SELESAI, 'Tidak perlu tindak lanjut.');

        ReportForwarding::create([
            'report_id' => $r5->id,
            'user_id' => $koordinator->id,
            'pihak' => 'sekolah',
            'keterangan' => 'Informasi diteruskan ke pihak sekolah.',
            'forwarded_at' => now()->subDay(),
        ]);
    }
}