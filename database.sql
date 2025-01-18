CREATE TABLE konsultasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_pasien INT,
    id_dokter INT,
    pesan TEXT,
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
    pengirim ENUM('dokter', 'pasien'),
    status ENUM('aktif', 'selesai') DEFAULT 'aktif',
    FOREIGN KEY (id_pasien) REFERENCES pasien(id),
    FOREIGN KEY (id_dokter) REFERENCES dokter(id)
); 