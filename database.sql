-- Dumping data for table kasir.products: ~6 rows (approximately)
INSERT INTO `products` (`id`, `name`, `category`, `price`, `stock`) VALUES
	(1, 'Nasi Goreng', 'makanan', 15000.00, 50),
	(2, 'Mie Ayam', 'makanan', 12000.00, 30),
	(3, 'Es Teh', 'minuman', 5000.00, 94),
	(4, 'Kopi', 'minuman', 8000.00, 80),
	(5, 'Keripik', 'snack', 10000.00, 23),
	(6, 'Coklat', 'snack', 7000.00, 40);

-- Dumping data for table kasir.transactions: ~4 rows (approximately)
INSERT INTO `transactions` (`id`, `transaction_date`, `total_amount`) VALUES
	(1, '2025-06-23 22:08:08', 7700.00),
	(2, '2025-06-23 22:14:51', 11000.00),
	(3, '2025-06-23 22:18:37', 5500.00),
	(4, '2025-06-23 22:25:04', 16500.00),
	(5, '2025-06-23 22:52:40', 5500.00);

-- Dumping data for table kasir.transaction_details: ~6 rows (approximately)
INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `quantity`, `price_per_item`) VALUES
	(1, 1, 6, 1, 7000.00),
	(2, 2, 5, 1, 10000.00),
	(3, 3, 3, 1, 5000.00),
	(4, 4, 5, 1, 10000.00),
	(5, 4, 3, 1, 5000.00),
	(6, 5, 3, 1, 5000.00);