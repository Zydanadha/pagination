<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagination with Search and PDF Export</title>
    <?php
    include 'koneksi.php';
    require 'fpdf/fpdf.php'; // Pastikan path ini benar untuk file FPDF Anda

    // Set the number of data per page
    $jumlah_per_halaman = 10;

    // Get the current page from URL, default to page 1
    $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
    $mulai = ($halaman - 1) * $jumlah_per_halaman;

    // Get the search filters from URL
    $search = isset($_GET['search']) ? $_GET['search'] : "";
    $date = isset($_GET['date']) ? $_GET['date'] : "";
    $jumlah = isset($_GET['jumlah']) ? $_GET['jumlah'] : "";

    // Query to count the total data with search filters
    $query_count = "SELECT COUNT(*) AS total_transaksi FROM transaksi WHERE 1";
    
    // Apply search filters to the count query
    if ($search) {
        $query_count .= " AND nama_barang LIKE '$search%'";
    }
    if ($date) {
        $query_count .= " AND tanggal_transaksi = '$date'";
    }
    if ($jumlah) {
        $query_count .= " AND jumlah = '$jumlah'";
    }

    $result = $conn->query($query_count);
    $total_transaksi = $result->fetch_assoc()['total_transaksi'];

    // Calculate total pages
    $total_halaman = ceil($total_transaksi / $jumlah_per_halaman);

    // Query to get the data for the current page and filters
    $query = "SELECT * FROM transaksi WHERE 1";

    // Apply search filters to the main query
    if ($search) {
        $query .= " AND nama_barang LIKE '$search%'";
    }
    if ($date) {
        $query .= " AND tanggal_transaksi = '$date'";
    }
    if ($jumlah) {
        $query .= " AND jumlah = '$jumlah'";
    }

    $query .= " LIMIT $mulai, $jumlah_per_halaman"; // Apply pagination

    $data = $conn->query($query);
    ?>

    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #555;
        }
        .search-container, .pdf-container {
            margin-bottom: 20px;
        }
        .search-container form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .pdf-container {
            text-align: center;
            margin-top: 20px; /* Memberikan jarak lebih untuk tombol Cetak PDF */
        }
        input[type="text"], input[type="date"], input[type="number"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
        }
        button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        button[type="button"] {
            background-color: #dc3545;
        }
        button[type="button"]:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
        }
        .pagination a {
            padding: 10px 15px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        .pagination a.active {
            background-color: #0056b3;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Data Transaksi</h1>

    <!-- Search form -->
    <form method="GET" action="">
        <label for="search">Nama Barang:</label>
        <input type="text" name="search" id="search" value="<?= htmlspecialchars($search); ?>">

        <label for="date">Tanggal Transaksi:</label>
        <input type="date" name="date" id="date" value="<?= htmlspecialchars($date); ?>">

        <label for="jumlah">Jumlah:</label>
        <input type="number" name="jumlah" id="jumlah" value="<?= htmlspecialchars($jumlah); ?>">

        <button type="submit">Cari</button>
        <a href="index.php">Reset</a>
    </form>

    <!-- PDF Export -->
    <div class="pdf-container">
        <form method="POST" action="cetak_pdf.php">
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
            <input type="hidden" name="jumlah" value="<?= htmlspecialchars($jumlah) ?>">
            <button type="submit">Cetak PDF</button>
        </form>
    </div>

    <!-- Data Table -->
    <table>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Tanggal Transaksi</th>
            <th>Total Transaksi</th>
        </tr>
        <?php
        $no = $mulai + 1;
        while ($row = $data->fetch_assoc()):
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= $row['tanggal_transaksi'] ?></td>
            <td><?= $row['total_transaksi'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
            <a href="?halaman=<?= $i ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($date) ?>&jumlah=<?= urlencode($jumlah) ?>" class="<?= $halaman == $i ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>

</body>
</html>
