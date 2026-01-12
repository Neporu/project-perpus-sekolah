<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="./assets/Universitas Amikom Purwokerto.ico" type="image/x-icon">
	<title>Perpustakaan Sekolah Kel-1</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
		.box {
			margin: 10px 0 10px 0;
			background-color: rgba(255, 255, 255, 0.25);
			padding: 20px;
			border-radius: 15px;
			box-shadow: 0 0 10px rgba(255, 255, 255, 0.25);
		} body {
			font-family: "Poppins", sans-serif;
			font-weight: 600;
			font-style: normal;
			background-image: url('./assets/perpus-amikom-landscape.jpg');
			background-size: cover;
			background-position: top;
		} .table {
			background-color: #ffffff;
			border-radius: 15px;
		} .container {background: rgba(255, 255, 255, 0.25);}
	</style>
</head>
<body>
	<div class="container">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="active"><a href="index.php">Home</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">Data Master
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="petugas.php">Petugas</a></li>
								<li><a href="mhs.php">Mahasiswa</a></li>
								<li><a href="pemasok.php">Pemasok</a></li>
								<li><a href="kategori.php">Kategori</a></li>
								<li><a href="buku.php">Buku</a></li>
								<li><a href="peminjaman.php">Peminjaman</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">Data Transaksi
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="pembelian.php">Pembelian</a></li>
								<li><a href="denda.php">Denda</a></li>
							</ul>
						</li>
						<li><a href="tentang.php">Tentang Kelompok Kami</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
