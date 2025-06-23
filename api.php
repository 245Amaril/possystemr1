<?php
// File: api.php
header('Content-Type: application/json'); // Pastikan ini ada di paling atas untuk output JSON

// Memasukkan file koneksi database
require 'db_connect.php'; // Pastikan file ini ada dan berisi koneksi $conn

// Mendapatkan aksi yang diminta dari frontend
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Menggunakan switch untuk menangani berbagai aksi
switch ($action) {
    // Mengambil semua produk dari database
    case 'get_products':
        $sql = "SELECT * FROM products ORDER BY name ASC";
        $result = $conn->query($sql);
        $products = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        echo json_encode($products);
        break;

    // Menambahkan produk baru
    case 'add_product':
        $name = $_POST['name'] ?? null;
        $category = $_POST['category'] ?? null;
        $price = $_POST['price'] ?? null;
        $stock = $_POST['stock'] ?? null;

        // Validasi dasar
        if (!$name || !$category || !isset($price) || !isset($stock)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
            exit;
        }

        // Menggunakan prepared statement untuk keamanan (mencegah SQL Injection)
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock) VALUES (?, ?, ?, ?)");
        // "ssdi" -> string, string, double, integer
        $stmt->bind_param("ssdi", $name, $category, $price, $stock);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
        break;

    // --- AKSI BARU UNTUK EDIT PRODUK ---
    case 'update_product':
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? null;
        $category = $_POST['category'] ?? null;
        $price = $_POST['price'] ?? null;
        $stock = $_POST['stock'] ?? null;

        // Validasi input
        if (!$id || !$name || !$category || !isset($price) || !isset($stock)) {
            echo json_encode(['success' => false, 'error' => 'All fields are required.']);
            exit;
        }

        // Konversi tipe data
        $id = (int)$id;
        $price = (float)$price;
        $stock = (int)$stock;

        // Gunakan prepared statement untuk UPDATE
        $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ? WHERE id = ?");
        // "ssdii" -> string, string, double, integer, integer
        $stmt->bind_param("ssdii", $name, $category, $price, $stock, $id);

        if ($stmt->execute()) {
            // Check if any rows were affected (product found and updated)
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'No changes made or product not found.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
        break;
    // --- AKHIR AKSI BARU UNTUK EDIT PRODUK ---

    // Menghapus produk
    case 'delete_product':
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Product ID is required.']);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
        break;

    // Memproses transaksi pembayaran
    case 'process_transaction':
        $cart = json_decode($_POST['cart'], true);
        $total = floatval($_POST['total']);

        // Memulai transaksi database untuk memastikan semua query berhasil atau tidak sama sekali
        $conn->begin_transaction();

        try {
            // 1. Simpan data utama transaksi ke tabel 'transactions'
            $stmt = $conn->prepare("INSERT INTO transactions (total_amount) VALUES (?)");
            $stmt->bind_param("d", $total);
            $stmt->execute();
            $transaction_id = $conn->insert_id; // Dapatkan ID dari transaksi yang baru saja dibuat
            $stmt->close();

            // 2. Siapkan statement untuk menyimpan detail item dan mengurangi stok
            $stmt_details = $conn->prepare("INSERT INTO transaction_details (transaction_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
            $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            // Loop setiap item di keranjang
            foreach ($cart as $item) {
                // Simpan detail transaksi
                $stmt_details->bind_param("iiid", $transaction_id, $item['id'], $item['quantity'], $item['price']);
                $stmt_details->execute();

                // Kurangi stok produk
                $stmt_stock->bind_param("ii", $item['quantity'], $item['id']);
                $stmt_stock->execute();
            }
            $stmt_details->close();
            $stmt_stock->close();

            // Jika semua query berhasil, commit (simpan permanen) transaksi
            $conn->commit();
            echo json_encode(['success' => true, 'transaction_id' => $transaction_id]);

        } catch (Exception $e) {
            // Jika terjadi kesalahan, batalkan semua perubahan (rollback)
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    // Mengambil riwayat transaksi
    case 'get_transactions':
        $sql = "SELECT id, transaction_date, total_amount FROM transactions ORDER BY transaction_date DESC";
        $result = $conn->query($sql);
        $transactions = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $transactions[] = $row;
            }
        }
        echo json_encode($transactions);
        break;
        
    // Mengambil detail transaksi spesifik
    case 'get_transaction_details':
        $id = $_GET['id'] ?? null;
        $response = [];
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Transaction ID is required.']);
            exit;
        }

        // Ambil info transaksi utama
        $stmt = $conn->prepare("SELECT id, transaction_date, total_amount FROM transactions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $response['transaction'] = $result->fetch_assoc();
        $stmt->close();
        
        // Ambil item-item dalam transaksi
        $stmt = $conn->prepare("
            SELECT td.quantity, td.price_per_item, p.name 
            FROM transaction_details td
            JOIN products p ON td.product_id = p.id
            WHERE td.transaction_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $response['items'] = $items;
        $stmt->close();
        
        echo json_encode($response);
        break;

    // Mengambil data untuk laporan
    case 'get_reports':
        $reports = [];

        // Penjualan Hari Ini
        $result = $conn->query("SELECT SUM(total_amount) as total FROM transactions WHERE DATE(transaction_date) = CURDATE()");
        $reports['daily_sales'] = $result->fetch_assoc()['total'] ?? 0;

        // Transaksi Hari Ini
        $result = $conn->query("SELECT COUNT(id) as count FROM transactions WHERE DATE(transaction_date) = CURDATE()");
        $reports['daily_transactions'] = $result->fetch_assoc()['count'] ?? 0;

        // Total Produk
        $result = $conn->query("SELECT COUNT(id) as count FROM products");
        $reports['total_products'] = $result->fetch_assoc()['count'] ?? 0;
        
        echo json_encode($reports);
        break;

    // Aksi default jika tidak ada yang cocok
    default:
        echo json_encode(['success' => false, 'error' => 'Aksi tidak valid']);
        break;
}

// Menutup koneksi database
$conn->close();
?>