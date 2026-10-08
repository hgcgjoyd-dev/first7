DROP DATABASE IF EXISTS db_kesiswaan;

CREATE DATABASE db_kesiswaan
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE db_kesiswaan;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tabel user (Autentikasi & Akun Login)
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','siswa','guru_bk') NOT NULL DEFAULT 'siswa',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `user_username_unique` (`username`),
  UNIQUE KEY `user_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel guru (Nomor Guru tepat 6 digit angka saja & UNIQUE)
DROP TABLE IF EXISTS `guru`;
CREATE TABLE `guru` (
  `id_guru` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned DEFAULT NULL,
  `no_guru` varchar(6) NOT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_guru`),
  UNIQUE KEY `guru_no_guru_unique` (`no_guru`),
  KEY `guru_id_user_foreign` (`id_user`),
  CONSTRAINT `guru_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `chk_nomor_guru` CHECK (`no_guru` REGEXP '^[0-9]{6}$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel kelas
DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas` (
  `id_kelas` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) NOT NULL,
  `tingkat` varchar(10) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `wali_kelas_id` bigint(20) unsigned DEFAULT NULL,
  `tahun_ajaran` varchar(20) NOT NULL DEFAULT '2026/2027',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `kelas_wali_kelas_id_foreign` (`wali_kelas_id`),
  CONSTRAINT `kelas_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel guru_bk
DROP TABLE IF EXISTS `guru_bk`;
CREATE TABLE `guru_bk` (
  `id_guru_bk` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_guru` bigint(20) unsigned NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_guru_bk`),
  KEY `guru_bk_id_guru_foreign` (`id_guru`),
  CONSTRAINT `guru_bk_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel siswa (Nomor Siswa tepat 7 digit angka saja & UNIQUE)
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa` (
  `id_siswa` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned DEFAULT NULL,
  `no_siswa` varchar(7) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `id_kelas` bigint(20) unsigned NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `poin_pelanggaran` int(11) NOT NULL DEFAULT 0,
  `poin_penghargaan` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_siswa`),
  UNIQUE KEY `siswa_no_siswa_unique` (`no_siswa`),
  KEY `siswa_id_user_foreign` (`id_user`),
  KEY `siswa_id_kelas_foreign` (`id_kelas`),
  CONSTRAINT `siswa_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  CONSTRAINT `siswa_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `chk_nomor_siswa` CHECK (`no_siswa` REGEXP '^[0-9]{7}$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel mata_pelajaran
DROP TABLE IF EXISTS `mata_pelajaran`;
CREATE TABLE `mata_pelajaran` (
  `id_mapel` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(20) NOT NULL,
  `nama_mapel` varchar(100) NOT NULL,
  `tingkat` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_mapel`),
  UNIQUE KEY `mata_pelajaran_kode_mapel_unique` (`kode_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabel jadwal_pelajaran
DROP TABLE IF EXISTS `jadwal_pelajaran`;
CREATE TABLE `jadwal_pelajaran` (
  `id_jadwal` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_kelas` bigint(20) unsigned NOT NULL,
  `id_mapel` bigint(20) unsigned NOT NULL,
  `id_guru` bigint(20) unsigned NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruangan` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`),
  KEY `jadwal_pelajaran_id_kelas_foreign` (`id_kelas`),
  KEY `jadwal_pelajaran_id_mapel_foreign` (`id_mapel`),
  KEY `jadwal_pelajaran_id_guru_foreign` (`id_guru`),
  CONSTRAINT `jadwal_pelajaran_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_pelajaran_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_pelajaran_id_mapel_foreign` FOREIGN KEY (`id_mapel`) REFERENCES `mata_pelajaran` (`id_mapel`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel absensi
DROP TABLE IF EXISTS `absensi`;
CREATE TABLE `absensi` (
  `id_absensi` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `id_guru` bigint(20) unsigned DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status` enum('Hadir','Izin','Sakit','Terlambat','Alpa') NOT NULL DEFAULT 'Hadir',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `foto_biometrik` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_absensi`),
  KEY `absensi_id_siswa_foreign` (`id_siswa`),
  KEY `absensi_id_guru_foreign` (`id_guru`),
  CONSTRAINT `absensi_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL,
  CONSTRAINT `absensi_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabel konseling
DROP TABLE IF EXISTS `konseling`;
CREATE TABLE `konseling` (
  `id_konseling` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `id_guru_bk` bigint(20) unsigned NOT NULL,
  `tanggal_konseling` date NOT NULL,
  `jam_konseling` time DEFAULT NULL,
  `jenis_layanan` varchar(100) NOT NULL DEFAULT 'Konseling Individual',
  `topik_pembahasan` text NOT NULL,
  `hasil_konseling` text DEFAULT NULL,
  `status` enum('Dijadwalkan','Berlangsung','Selesai','Dibatalkan') NOT NULL DEFAULT 'Dijadwalkan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_konseling`),
  KEY `konseling_id_siswa_foreign` (`id_siswa`),
  KEY `konseling_id_guru_bk_foreign` (`id_guru_bk`),
  CONSTRAINT `konseling_id_guru_bk_foreign` FOREIGN KEY (`id_guru_bk`) REFERENCES `guru_bk` (`id_guru_bk`) ON DELETE CASCADE,
  CONSTRAINT `konseling_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabel jenis_pelanggaran
DROP TABLE IF EXISTS `jenis_pelanggaran`;
CREATE TABLE `jenis_pelanggaran` (
  `id_pelanggaran` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `kategori` enum('Ringan','Sedang','Berat') NOT NULL DEFAULT 'Ringan',
  `poin` int(11) NOT NULL DEFAULT 5,
  `sanksi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pelanggaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Tabel pelanggaran_siswa
DROP TABLE IF EXISTS `pelanggaran_siswa`;
CREATE TABLE `pelanggaran_siswa` (
  `id_pelanggaran_siswa` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `id_pelanggaran` bigint(20) unsigned NOT NULL,
  `id_guru_bk` bigint(20) unsigned DEFAULT NULL,
  `tanggal_kejadian` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tindak_lanjut` text DEFAULT NULL,
  `status_penanganan` enum('Diproses','Diberi SP-1','Diberi SP-2','Diberi SP-3','Selesai') NOT NULL DEFAULT 'Diproses',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pelanggaran_siswa`),
  KEY `pelanggaran_siswa_id_siswa_foreign` (`id_siswa`),
  KEY `pelanggaran_siswa_id_pelanggaran_foreign` (`id_pelanggaran`),
  KEY `pelanggaran_siswa_id_guru_bk_foreign` (`id_guru_bk`),
  CONSTRAINT `pelanggaran_siswa_id_guru_bk_foreign` FOREIGN KEY (`id_guru_bk`) REFERENCES `guru_bk` (`id_guru_bk`) ON DELETE SET NULL,
  CONSTRAINT `pelanggaran_siswa_id_pelanggaran_foreign` FOREIGN KEY (`id_pelanggaran`) REFERENCES `jenis_pelanggaran` (`id_pelanggaran`) ON DELETE CASCADE,
  CONSTRAINT `pelanggaran_siswa_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Tabel ekstrakurikuler
DROP TABLE IF EXISTS `ekstrakurikuler`;
CREATE TABLE `ekstrakurikuler` (
  `id_ekskul` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_ekskul` varchar(100) NOT NULL,
  `pembina` varchar(100) DEFAULT NULL,
  `hari_kegiatan` varchar(50) DEFAULT NULL,
  `jam_kegiatan` varchar(50) DEFAULT NULL,
  `tempat` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_ekskul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Tabel anggota_ekskul
DROP TABLE IF EXISTS `anggota_ekskul`;
CREATE TABLE `anggota_ekskul` (
  `id_anggota_ekskul` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `id_ekskul` bigint(20) unsigned NOT NULL,
  `jabatan` varchar(50) NOT NULL DEFAULT 'Anggota',
  `tanggal_bergabung` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_anggota_ekskul`),
  KEY `anggota_ekskul_id_siswa_foreign` (`id_siswa`),
  KEY `anggota_ekskul_id_ekskul_foreign` (`id_ekskul`),
  CONSTRAINT `anggota_ekskul_id_ekskul_foreign` FOREIGN KEY (`id_ekskul`) REFERENCES `ekstrakurikuler` (`id_ekskul`) ON DELETE CASCADE,
  CONSTRAINT `anggota_ekskul_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Tabel prestasi
DROP TABLE IF EXISTS `prestasi`;
CREATE TABLE `prestasi` (
  `id_prestasi` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_siswa` bigint(20) unsigned NOT NULL,
  `nama_prestasi` varchar(200) NOT NULL,
  `tingkat` enum('Sekolah','Kecamatan','Kabupaten/Kota','Provinsi','Nasional','Internasional') NOT NULL DEFAULT 'Sekolah',
  `poin` int(11) NOT NULL DEFAULT 10,
  `tanggal_perolehan` date NOT NULL,
  `sertifikat` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_prestasi`),
  KEY `prestasi_id_siswa_foreign` (`id_siswa`),
  CONSTRAINT `prestasi_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
