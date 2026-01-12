drop database kel1perpus_sekolah;
create database kel1perpus_sekolah;
use kel1perpus_sekolah;
create or REPLACE TABLE tbpetugas (
    nik varchar(10) PRIMARY KEY,
    sandi varchar(10),
    nama varchar(20),
    alamat varchar(20),
    telp varchar(12)
);
-- DIBUAT OLEH ZAKI
CREATE or REPLACE TABLE tbmhs (
nim VARCHAR(10) PRIMARY KEY,
nama VARCHAR(50)NOT NULL,
alamat VARCHAR(70),
telp VARCHAR(12)
);
create or REPLACE table tbpemasok (
idpemasok varchar(10) primary key,
namapemasok varchar(50),
alamat varchar(100),
telp varchar(12)
);
-- DIBUAT OLEH ZAKI
CREATE or REPLACE TABLE tbkategori (
idkategori VARCHAR(10) PRIMARY KEY,
namakategori VARCHAR(50)
);
create or REPLACE table tbbuku (
    idbuku varchar(10) primary key,
    idkategori varchar(10),
    judul varchar(50),
    author varchar(20),
    stok int,
    harga int,
    FOREIGN KEY (idkategori) REFERENCES tbkategori(idkategori)
);
create or REPLACE table tbpeminjaman (
    idpinjam varchar(10) primary key,
    nik varchar(10),
    nim varchar(10),
    tglpinjam date,
    tgljatuhtempo date,
    status enum('aktif','selesai'),
    FOREIGN KEY (nik) REFERENCES tbpetugas(nik),
    FOREIGN KEY (nim) REFERENCES tbmhs(nim)
);
create or REPLACE table tbdetailpinjam (
    idpinjam varchar(10),
    idbuku varchar(10),
    tglkembali date,
    status enum('dipinjam','kembali','hilang'),
    FOREIGN KEY (idpinjam) REFERENCES tbpeminjaman(idpinjam),
    FOREIGN KEY (idbuku) REFERENCES tbbuku(idbuku)
);
create or REPLACE table tbpembelian (
    notabeli varchar(10) PRIMARY KEY,
    tgl date,
    nik varchar(10),
    idpemasok varchar(10),
    totalbeli int,
    FOREIGN KEY (nik) REFERENCES tbpetugas(nik),
    FOREIGN KEY (idpemasok) REFERENCES tbpemasok(idpemasok)
);
create or REPLACE table tbdetailbeli (
    idbuku varchar(10),
    notabeli varchar(10),
    jml int,
    hargabeli int,
    subtotal int,
    FOREIGN KEY (idbuku) REFERENCES tbbuku(idbuku),
    FOREIGN KEY (notabeli) REFERENCES tbpembelian(notabeli)
);
create or REPLACE table tbdenda (
    iddenda varchar(10) PRIMARY KEY,
    idpinjam varchar(10),
    totaldenda int,
    status enum('belum lunas','lunas'),
    tglbuat date,
    tglbayar date,
    jmlbayar int,
    FOREIGN KEY (idpinjam) REFERENCES tbpeminjaman(idpinjam)
);
-- DIBUAT OLEH ZAKI
INSERT INTO tbmhs(nim, nama, alamat, telp) VALUES
('26HMS1', 'Bagus kusuma', 'Jatilawang', '081111111111'),
('26MHS2', 'Yusuf Dwi', 'Purwokerto', '082222222222'),
('26MHS3', 'Dias Catur', 'Purwokerto', '083333333333'),
('26MHS4', 'Raihan Zaki', 'Ajibarang', '084444444444');
insert into tbkategori (idkategori, namakategori) VALUES
('K01', 'Novel'),
('K02', 'Sejarah'),
('K03', 'Fiksi'),
('K04', 'Non-Fiksi'),
('K05', 'Biografi'),
('K06', 'Pendidikan'),
('K07', 'Sains'),
('K08', 'Hukum'),
('K09', 'Kesehatan'),
('K10', 'Agama');
-- DIBUAT OLEH ZAKI (DI MODIF DARI TBKATEGORI JADI TBBUKU)
INSERT INTO tbbuku (idbuku, idkategori, judul, author, stok, harga) VALUES
('B01', 'K01', 'Romeo Juilet', 'Alexandre Dumas', 0, 50000),
('B02', 'K02', 'Sejarah Dunia I', 'Yoseph Christian', 0, 60000),
('B03', 'K03', 'Kancil dimakan buaya', 'Benjamin', 0, 25000),
('B04', 'K04', 'Kasus Monas Aceh', 'Soekarno', 0, 75000),
('B05', 'K05', 'BJ Habibie', 'Mulyono', 0, 80000),  
('B06', 'K06', 'Kalkulus II', 'fufufafa', 0, 90000),
('B07', 'K07', 'Fisika III', 'Wowo', 0, 85000),
('B08', 'K08', 'Hukum Islam ', 'Ethanol', 0, 70000),
('B09', 'K09', 'Anatomi Tubuh', 'Deddy Kobuser', 0, 95000),
('B10', 'K10', "Iqro", "As'ad Human", 0, 15000);
-- DIBUAT OLEH BAGUS
INSERT INTO tbpemasok(idpemasok, namapemasok,alamat, telp) VALUES
('KP01', 'PT Cina','Jl. Jeruk No.8, Jakarta Timur', '083344556678'),
('KP02', 'PT Mie Sukses','Jl. Mangga muda No.10, Bogor', '081234567890'),
('KP03', 'PT Indomilk','Jl. Macan putih No.12, Banten', '082345678901');
insert into tbpetugas(nik, sandi, nama, alamat, telp) values
('P01', '12345', 'Muhammad Imron', 'Purbalingga', '081111111111'),
('P02', '54321', 'Yuli', 'Purwokerto', '082222222222');