DROP TABLE IF EXISTS t_asm_asesmen;
CREATE TABLE `t_asm_asesmen` (
  `asm_idents` int NOT NULL AUTO_INCREMENT,
  `asm_tahun` varchar(4) DEFAULT NULL,
  `asm_periode` varchar(3) DEFAULT NULL,
  `asm_periode_start` date DEFAULT NULL,
  `asm_periode_end` date DEFAULT NULL,
  `asm_keterangan` varchar(400) DEFAULT NULL,
  `asm_file` date DEFAULT NULL,
  `asm_alasan` varchar(400) DEFAULT NULL,
  `asm_is_deleted` tinyint DEFAULT NULL,
  `asm_usrnam` varchar(100) DEFAULT NULL,
  `asm_usrdat` datetime DEFAULT CURRENT_TIMESTAMP,
  `asm_updnam` varchar(100) DEFAULT NULL,
  `asm_upddat` datetime DEFAULT NULL,
  PRIMARY KEY (`asm_idents`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS t_asm_lokasi;
CREATE TABLE `t_asm_lokasi` (
  `lok_idents` int NOT NULL AUTO_INCREMENT,
  `lok_asmidents` int NOT NULL,
  `lok_provinsi` varchar(2) NOT NULL,
  `lok_kabptn` varchar(5) NOT NULL,
  `lok_periode_start` date DEFAULT NULL,
  `lok_periode_end` date DEFAULT NULL,
  `lok_supervisor` int DEFAULT NULL,
  `lok_alasan` varchar(500) NOT NULL,
  `lok_is_deleted` tinyint NOT NULL,
  `lok_usrnam` varchar(100) DEFAULT NULL,
  `lok_usrdat` datetime DEFAULT CURRENT_TIMESTAMP,
  `lok_updnam` varchar(100) DEFAULT NULL,
  `lok_upddat` datetime DEFAULT NULL,
  `lok_spvnam` varchar(100) DEFAULT NULL,
  `lok_spvdat` datetime DEFAULT NULL,
  PRIMARY KEY (`lok_idents`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS t_mas_kategori;
CREATE TABLE `t_mas_kategori` (
  `idk_idents` int NOT NULL AUTO_INCREMENT,
  `idk_tahun` varchar(4) DEFAULT NULL,
  `idk_nama` varchar(2000) DEFAULT NULL,
  `idk_parent` int NOT NULL,
  `idk_petunjuk` varchar(5000) DEFAULT NULL,
  `idk_icon` varchar(100) DEFAULT NULL,
  `idk_active` tinyint DEFAULT '1',
  `idk_is_deleted` tinyint DEFAULT '0',
  `idk_usrnam` varchar(30) DEFAULT NULL,
  `idk_usrdat` datetime DEFAULT CURRENT_TIMESTAMP,
  `idk_updnam` varchar(30) DEFAULT NULL,
  `idk_upddat` datetime DEFAULT NULL,
  PRIMARY KEY (`idk_idents`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '5. Ekonomi',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '6. Pendidikan',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7. Energi',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '8. Lingkungan dan perubahan iklim',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '9. Keuangan',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '10. Pemerintahan',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '11. Kesehatan',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '12. Perumahan',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '13. Populasi dan kondisi sosial',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '14. Rekreasi',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '15. Keamanan',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '16. Limbah Padat',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '17. Olahraga dan budaya',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '18. Telekomunikasi',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19. Transportasi',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '20. Pertanian perkotaan / lokal dan ketangguhan pangan',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '21. Perencanaan kota',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '22. Air Limbah',0,'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '23. Air',0,'admin_aplikasi');

INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '5.1. Persentase kontrak layanan yang menyediakan layanan kota yang memuat kebijakan data terbuka',1, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '5.2. Tingkat kelangsungan bisnis baru per 100.000 penduduk',1, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '5.3. Persentase tenaga kerja yang bekerja di Sektor Teknologi Informasi dan Komunikasi (TIK)',1, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '5.4. Persentase angkatan kerja yang bekerja di sektor pendidikan, penelitian dan pengembangan',1, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '6.1. Persentase populasi kota dengan kecakapan profesional di lebih dari satu bahasa',2, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '6.2. Jumlah komputer, laptop, tablet atau perangkat pembelajaran digital lainnya yang tersedia per 1.000 siswa',2, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '6.3. Jumlah tingkat pendidikan tinggi sains, teknologi, teknik dan matematika (STEM) per 100.000 penduduk',2, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.1. Persentase energi listrik dan energi termal yang dihasilkan dari pengolahan air limbah, limbah padat dan pengolahan limbah cair lainnya serta sumber daya limbah panas lainnya, sebagai bagian dari total bauran energi kota untuk tahun tertentu',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.2. Energi listrik dan termal (GJ) yang dihasilkan dari pengolahan air limbah per kapita per tahun',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.3. Energi listrik dan panas (GJ) dihasilkan dari limbah padat atau pengolahan limbah cair per kapita per tahun',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.4. Persentase listrik kota yang diproduksi menggunakan sistem produksi listrik desentralisasi',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.5. Kapasitas penyimpanan jaringan energi kota per total konsumsi energi kota',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.6. Persentase penerangan jalan yang dikelola oleh sistem manajemen kinerja cahaya/lampu',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.7. Persentase penerangan jalan yang telah dipugar dan yang baru dipasang',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.8. Persentase bangunan umum yang membutuhkan renovasi/perbaikan',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.9. Persentase bangunan di kota dengan pengukur energi cerdas',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '7.10. Jumlah stasiun pengisian kendaraan listrik per kendaraan listrik terdaftar',3, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '8.1. Persentase bangunan yang dibangun atau diperbaharui dalam 5 tahun terakhir sesuai dengan prinsip-prinsip bangunan hijau', 4, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '8.2. Jumlah stasiun pemantauan kualitas udara jarak jauh secara langsung (real-time) per kilometer persegi (km2)', 4, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '8.3. Persentase bangunan umum yang dilengkapi untuk memantau kualitas udara dalam ruangan', 4, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '9.1. Jumlah pendapatan tahunan yang dikumpulkan dari ekonomi berbagi sebagai persentase dari pendapatan sumber sendiri', 5, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '9.2. Persentase pembayaran ke kota yang dibayar secara elektronik berdasarkan faktur elektronik', 5, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '10.1. Jumlah kunjungan daring tahunan ke portal data terbuka kota per 100.000 penduduk', 6, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '10.2. Persentase layanan kota yang dapat diakses dan yang dapat diminta secara daring', 6, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '10.3. Rata-rata waktu respons terhadap pertanyaan yang dilakukan melalui sistem penyelidikan non-darurat kota (hari)', 6, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '10.4. Rata-rata waktu henti (downtime) infrastruktur teknologi informasi (TI) kota', 6, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '11.1. Persentase populasi kota yang masuk dalam file kesehatan terpadu daring yang dapat diakses oleh penyedia layanan kesehatan', 7, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '11.2. Jumlah janji temu medis tahunan yang dilakukan melalui jarak jauh per 100.000 penduduk', 7, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '11.3. Persentase populasi kota yang memiliki akses ke sistem peringatan publik langsung (real-time) untuk saran kualitas udara dan air', 7, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '12.1. Persentase rumah tangga dengan pengukur energi pintar', 8, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '12.2 Persentase rumah tangga dengan pengukur air pintar', 8, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '13.1. Persentase bangunan publik yang dapat diakses oleh orang-orang dengan kebutuhan khusus', 9, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '13.2. Persentase anggaran kota yang dialokasikan untuk penyediaan alat bantu mobilitas, perangkat, dan teknologi pendampingan bagi warga negara dengan kebutuhan khusus', 9, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '13.3. Persentase penyeberangan pejalan kaki yang ditandai dilengkapi dengan sinyal pejalan kaki yang dapat diakses', 9, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '13.4. Persentase anggaran kota yang dialokasikan untuk penyediaan program yang ditujukan untuk menjembatani kesenjangan digital', 9, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '14.1. Persentase layanan rekreasi publik yang dapat dipesan secara daring', 10, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '15.1. Persentase area kota yang dicakup oleh kamera pengintai digital', 11, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '16.1. Persentase pusat pembuangan limbah (kontainer) yang dilengkapi dengan telemetering', 12, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '16.2. Persentase populasi kota yang memiliki pengumpulan sampah dari pintu ke pintu dengan pemantauan individu terhadap jumlah sampah rumah tangga', 12, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '16.3. Persentase jumlah total sampah di kota yang digunakan untuk menghasilkan energi', 12, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '16.4. Persentase dari jumlah total sampah plastik yang didaur ulang di kota', 12, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '16.5. Persentase tempat sampah umum yang merupakan tempat sampah umum yang difungsikan dengan sensor', 12, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '16.6. Persentase limbah listrik dan elektronik kota yang didaur ulang', 12, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '17.1. Jumlah pemesanan daring untuk fasilitas budaya per 100.000 penduduk', 13, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '17.2. Persentase catatan budaya kota yang telah didigitalkan', 13, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '17.3. Jumlah buku perpustakaan umum dan judul e-book per 100.000 penduduk', 13, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '17.4. Persentase populasi kota yang merupakan pengguna perpustakaan umum yang aktif', 13, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '18.1. Persentase populasi kota dengan akses ke pita lebar (broadband) berkecepatan memadai', 14, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '18.2. Persentase area kota di bawah zona putih / titik mati / tidak tercakup oleh konektivitas telekomunikasi', 14, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '18.3. Persentase area kota yang dicakup oleh konektivitas Internet yang disediakan oleh kota', 14, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.1. Persentase jalan umum dan jalan berbayar yang dicakup oleh peringatan dan informasi lalu lintas daring langsung (real-time) ', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.2. Jumlah pengguna transportasi ekonomi berbagi per 100.000 penduduk', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.3. Persentase kendaraan yang terdaftar di kota yang merupakan kendaraan rendah emisi', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.4. Jumlah sepeda yang tersedia melalui layanan berbagi sepeda yang disediakan oleh kota per 100.000 penduduk', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.5. Persentase jalur transportasi umum yang dilengkapi dengan sistem langsung (real-time) yang dapat diakses publik', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.6. Persentase layanan transportasi umum kota yang dicakup oleh sistem pembayaran terpadu', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.7. Persentase ruang parkir umum yang dilengkapi dengan sistem pembayaran elektronik', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.8. Persentase ruang parkir umum yang dilengkapi dengan sistem ketersediaan parkir langsung (real-time)', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.9. Persentase lampu lalu lintas yang cerdas', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.10. Area kota dipetakan oleh peta jalan interaktif langsung (real-time) sebagai persentase dari total luas kota', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.11. Persentase kendaraan yang terdaftar di kota yang merupakan kendaraan otonom (autonomous)', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.12. Persentase rute angkutan umum dengan konektivitas internet yang disediakan kota dan/atau dikelola untuk komuter', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.13. Persentase jalan yang sesuai dengan sistem mengemudi otonom', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '19.14. Persentase armada bus kota yang digerakkan dengan motor', 15, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '20.1. Persentase tahunan dari anggaran kota yang dihabiskan untuk inisiatif pertanian perkotaan', 16, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '20.2. Total limbah makanan kota yang dikumpulkan tahunan dikirim ke fasilitas pemrosesan untuk pengomposan per kapita (dalam ton)', 16, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '20.3. Persentase luas tanah kota yang dicakup oleh sistem pemetaan pemasok makanan daring', 16, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '21.1. Jumlah warga kota per 100.000 penduduk per tahun yang terlibat dalam proses perencanaan',  17, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '21.2. Persentase izin bangunan yang diajukan melalui sistem pengiriman elektronik', 17, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '21.3. Waktu rata-rata untuk persetujuan izin bangunan (hari)', 17, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '21.4. Persentase populasi kota yang hidup dalam kepadatan populasi sedang hingga tinggi', 17, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '22.1. Persentase air limbah olahan yang digunakan kembali', 18, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '22.2. Persentase biosolids yang digunakan kembali (massa bahan kering)', 18, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '22.3. Energi yang berasal dari air limbah sebagai persentase dari total konsumsi energi kota', 18, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '22.4. Persentase jumlah total air limbah di perkotaan yang digunakan untuk menghasilkan energi', 18, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '22.5. Persentase jaringan pipa air limbah yang dimonitor oleh system sensor data tracking real-time', 18, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '23.1. Persentase air minum yang dilacak oleh stasiun pemantauan kualitas air langsung (real-time) ', 19, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '23.2. Jumlah stasiun pemantauan kualitas air lingkungan langsung (real-time) per 100.000 populasi', 19, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '23.3. Persentase jaringan distribusi air kota yang dipantau oleh air cerdas sistem', 19, 'admin_aplikasi');
INSERT INTO t_mas_kategori (idk_tahun, idk_nama, idk_parent, idk_usrnam ) VALUES ('2021', '23.4. Persentase bangunan di perkotaan dengan meteran air cerdas', 19, 'admin_aplikasi');

DROP TABLE IF EXISTS t_mas_pertanyaan;
CREATE TABLE `t_mas_pertanyaan` (
  `tny_idents` int NOT NULL AUTO_INCREMENT,
  `tny_kelompok` int DEFAULT NULL,
  `tny_indikator` int DEFAULT NULL,
  `tny_pertanyaan` varchar(5000) DEFAULT NULL,
  `tny_petunjuk` varchar(2000) DEFAULT NULL,
  `tny_active` tinyint DEFAULT '1',
  `tny_alasan` varchar(1000) DEFAULT NULL,
  `tny_is_deleted` tinyint DEFAULT '0',
  `tny_usrnam` varchar(30) DEFAULT NULL,
  `tny_usrdat` datetime DEFAULT CURRENT_TIMESTAMP,
  `tny_updnam` varchar(30) DEFAULT NULL,
  `tny_upddat` datetime DEFAULT NULL,
  PRIMARY KEY (`tny_idents`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS t_asm_asesmen_operator;
CREATE TABLE `t_asm_asesmen_operator` (
  `aso_idents` int NOT NULL AUTO_INCREMENT,
  `aso_asmidents` int DEFAULT NULL,
  `aso_operator` int DEFAULT NULL,
  `aso_kelompok_Kategori` int DEFAULT NULL,
  `aso_alasan` varchar(400) DEFAULT NULL,
  `aso_is_deleted` tinyint DEFAULT NULL,
  `aso_usrnam` varchar(100) DEFAULT NULL,
  `aso_usrdat` datetime DEFAULT CURRENT_TIMESTAMP,
  `aso_updnam` varchar(100) DEFAULT NULL,
  `aso_upddat` datetime DEFAULT NULL,
  PRIMARY KEY (`aso_idents`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

