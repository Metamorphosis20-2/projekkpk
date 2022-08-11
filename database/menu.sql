DELETE FROM `t_mas_usrmnu`
WHERE MNU_APPLIC = '19870301LO'
AND MNU_LEVELS in (1,2);

DELETE FROM `t_mas_appmnu`
WHERE MNU_APPLIC = '19870301LO';

INSERT INTO `t_mas_appmnu` 
(
`MNU_APPLIC`,
`MNU_NOMORS`,
`MNU_DESCRE`,
`MNU_DESCRB`,
`MNU_CHILDN`,
`MNU_HVCHLD`,
`MNU_ROUTES`,
`MNU_SORTBY`,
`MNU_EDTBLE`,
`MNU_ICONED`,
`MNU_PARENT`,
`MNU_AUTHRZ`,
`MNU_ACTIVE`,
`MNU_USRNAM`
)
VALUES 
('19870301LO','0100000000','Dashboard','Dashboard',0,0,'#',0/*MNU_SORTBY*/,'0','tachometer-alt','0','V',1,'9999'),
('19870301LO','1000000000','Beranda','Beranda',0,0,'proses/beranda',1/*MNU_SORTBY*/,'0','chart-pie','0','V',1,'9999'),
('19870301LO','0800000000','Penugasan','Penugasan',0,0,'proses/penugasan',3/*MNU_SORTBY*/,'0','user-tag','0','AEDV',1,'9999'),
('19870301LO','0900000000','Kuesioner','Kuesioner',0,0,'proses/kuesioner',4/*MNU_SORTBY*/,'0','question-circle','0','EV',1,'9999'),
('19870301LO','0200000000','Asesmen','Asesmen',1,1,'#',5/*MNU_SORTBY*/,'0','users','0','V',1,'9999'),
('19870301LO','0201000000','Jadwal Asesmen','Jadwal Asesmen',2,0,'asesmen/asesmen',1/*MNU_SORTBY*/,'0',NULL,'19870301LO0200000000','AEDV',1,'9999'),
('19870301LO','0202000000','Alokasi Unit Kerja','Alokasi Unit Kerja',2,0,'asesmen/unitkerja',2/*MNU_SORTBY*/,'0',NULL,'19870301LO0200000000','AEDV',1,'9999'),
('19870301LO','1101000000','Assignment Asesor','Assignment Asesor',2,0,'asesor/assignment',99/*MNU_SORTBY*/,'0',NULL,'19870301LO0200000000','AEDV',1,'9999'),
('19870301LO','0400000000','Master','Master',1,1,'#',6/*MNU_SORTBY*/,'0','database','0','V',1,'9999'),
('19870301LO','0401000000','Data Maturity','Data Maturity',2,1,'#',1/*MNU_SORTBY*/,'0',NULL,'19870301LO0400000000','V',1,'9999'),
('19870301LO','0401010000','Kategori','Kategori',3,0,'master/kategori',1/*MNU_SORTBY*/,'1',NULL,'19870301LO0401000000','AEDV',1,'9999'),
('19870301LO','0401020000','Pertanyaan','Pertanyaan',3,0,'master/pertanyaan',2/*MNU_SORTBY*/,'1',NULL,'19870301LO0401000000','AEDV',1,'9999'),
('19870301LO','0401030000','Tingkat','Tingkat',3,0,'master/tingkat',3/*MNU_SORTBY*/,'1',NULL,'19870301LO0401000000','AEDV',1,'9999'),
('19870301LO','0402000000','Referensi','Referensi',2,1,'#',2/*MNU_SORTBY*/,'0',NULL,'19870301LO0400000000','V',1,'9999'),
('19870301LO','0402010000','Unit Kerja','Unit Kerja',3,0,'master/unitkerja',1/*MNU_SORTBY*/,'1',NULL,'19870301LO0402000000','AEDV',1,'9999'),
('19870301LO','0500000000','Management','Manajemen',1,1,'#',99/*MNU_SORTBY*/,'0','cogs','0','V',1,'9999'),
('19870301LO','0501000000','Pengguna','Pengguna',1,0,'master/user',1/*MNU_SORTBY*/,'0',NULL,'19870301LO0500000000','AEDV',1,'9999'),
('19870301LO','0502000000','Menu Level','Menu Level',1,0,'master/menuuser',2/*MNU_SORTBY*/,'0',NULL,'19870301LO0500000000','EDV',1,'9999'),
('19870301LO','0503000000','Logs','Logs',2,1,'#',3/*MNU_SORTBY*/,'0',NULL,'19870301LO0500000000','V',1,'9999'),
('19870301LO','0503010000','Aktivitas Pengguna','Aktivitas Pengguna',3,0,'master/log/aktivitas',1/*MNU_SORTBY*/,'1',NULL,'19870301LO0503000000','V',1,'9999'),
('19870301LO','0503020000','Akses Pengguna','Akses Pengguna',3,0,'master/log/akses',2/*MNU_SORTBY*/,'1',NULL,'19870301LO0503000000','V',1,'9999'),
('19870301LO','0600000000','Analysis','Analisa',1,1,'#',7/*MNU_SORTBY*/,'0','chart-bar','0','V',1,'9999'),
('19870301LO','0601000000','Hasil Asesmen','Hasil Asesmen',2,0,'analysis/hasilasesmen',1/*MNU_SORTBY*/,'0',NULL,'19870301LO0600000000','V',1,'9999');

INSERT INTO `t_mas_usrmnu`
(`MNU_APPLIC`,`MNU_LEVELS`,`MNU_MENUCD`,`MNU_RIGHTS`,`MNU_USRNAM`)
SELECT MNU_APPLIC, 1, MNU_NOMORS, MNU_AUTHRZ, 'detanto'
FROM `t_mas_appmnu`
WHERE MNU_APPLIC = '19870301LO';

INSERT INTO `t_mas_usrmnu`
(`MNU_APPLIC`,`MNU_LEVELS`,`MNU_MENUCD`,`MNU_RIGHTS`,`MNU_USRNAM`)
SELECT MNU_APPLIC, 2, MNU_NOMORS, MNU_AUTHRZ, 'detanto'
FROM `t_mas_appmnu`
WHERE MNU_APPLIC = '19870301LO'
AND MNU_NOMORS <> '0502000000'