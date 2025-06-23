<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem POS - Point of Sale</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .product-card {
            cursor: pointer;
            transition: all 0.3s;
        }
        .product-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
        }
        /* Style untuk cetak */
        @media print {
            body * {
                visibility: hidden;
            }
            #printableArea, #printableArea * {
                visibility: visible;
            }
            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="text-white"><i class="fas fa-cash-register"></i> Kedai Kopi</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="#" onclick="showSection(event, 'pos')">
                        <i class="fas fa-shopping-cart me-2"></i> Kasir
                    </a>
                    <a class="nav-link" href="#" onclick="showSection(event, 'products')">
                        <i class="fas fa-box me-2"></i> Produk
                    </a>
                    <a class="nav-link" href="#" onclick="showSection(event, 'transactions')">
                        <i class="fas fa-receipt me-2"></i> Transaksi
                    </a>
                    <a class="nav-link" href="#" onclick="showSection(event, 'reports')">
                        <i class="fas fa-chart-bar me-2"></i> Laporan
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- POS Section -->
                <div id="pos-section">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5><i class="fas fa-shopping-bag me-2"></i>Pilih Produk</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" placeholder="Cari produk..." onkeyup="searchProducts(this.value)">
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select" onchange="filterByCategory(this.value)">
                                                <option value="">Semua Kategori</option>
                                                <option value="makanan">Makanan</option>
                                                <option value="minuman">Minuman</option>
                                                <option value="snack">Snack</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" id="products-grid">
                                        <!-- Products will be loaded here by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5><i class="fas fa-shopping-cart me-2"></i>Keranjang</h5>
                                </div>
                                <div class="card-body">
                                    <div id="cart-items">
                                        <p class="text-muted text-center">Keranjang kosong</p>
                                    </div>
                                </div>
                            </div>
                            <div class="total-section mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pajak (10%):</span>
                                    <span id="tax">Rp 0</span>
                                </div>
                                <hr class="text-white">
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong id="total">Rp 0</strong>
                                </div>
                                <button class="btn btn-light w-100 fw-bold" onclick="processPayment()">
                                    <i class="fas fa-credit-card me-2"></i>Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div id="products-section" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-box me-2"></i>Manajemen Produk</h5>
                            <button class="btn btn-light" onclick="showAddProduct()">
                                <i class="fas fa-plus me-2"></i>Tambah Produk
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Stok</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-table">
                                        <!-- Products table will be populated here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Section -->
                <div id="transactions-section" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-receipt me-2"></i>Riwayat Transaksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID Transaksi</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactions-table">
                                        <!-- Transactions will be populated here by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Section -->
                <div id="reports-section" style="display: none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                    <h5>Penjualan Hari Ini</h5>
                                    <h3 class="text-success" id="daily-sales">Rp 0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                    <h5>Transaksi Hari Ini</h5>
                                    <h3 class="text-primary" id="daily-transactions">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-box fa-2x text-warning mb-2"></i>
                                    <h5>Total Produk</h5>
                                    <h3 class="text-warning" id="total-products">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                    <h5>Rata-rata per Transaksi</h5>
                                    <h3 class="text-info" id="avg-transaction">Rp 0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="productId">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="productName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" id="productCategory" required>
                                <option value="">Pilih Kategori</option>
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" id="productPrice" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" id="productStock" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveProductButton" onclick="saveProduct()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId" name="id">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="editProductName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductCategory" class="form-label">Kategori</label>
                            <select class="form-select" id="editProductCategory" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                                </select>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Harga (Rp)</label>
                            <input type="number" class="form-control" id="editProductPrice" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductStock" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="editProductStock" name="stock" min="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Total Pembayaran</label>
                        <input type="text" class="form-control" id="paymentTotal" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="number" class="form-control" id="paymentAmount" placeholder="Masukkan jumlah bayar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kembalian</label>
                        <input type="text" class="form-control" id="paymentChange" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="completePayment()">Selesai</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaction Detail/Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi (Struk)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="receiptModalBody">
                    <!-- Receipt content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printReceipt()"><i class="fas fa-print me-2"></i>Cetak</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- [BARU] Payment Success Modal -->
    <div class="modal fade" id="paymentSuccessModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                    <h4 class="mb-3">Pembayaran Berhasil!</h4>
                    <p>Transaksi telah selesai. Apakah Anda ingin mencetak struk?</p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <button type="button" class="btn btn-primary btn-lg px-4 gap-3" id="printReceiptFromSuccessButton">Cetak Struk</button>
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data storage sekarang hanya untuk keranjang (cart) dan cache produk
        let cart = [];
        let productsCache = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadAllData();
        });
        
        function loadAllData() {
            loadProducts();
            updateCartDisplay();
        }

        // Navigation
        function showSection(event, section) {
            event.preventDefault();
            document.querySelectorAll('[id$="-section"]').forEach(el => el.style.display = 'none');
            document.getElementById(section + '-section').style.display = 'block';
            
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            event.currentTarget.classList.add('active');
            
            if(section === 'products') loadProductsTable();
            if(section === 'transactions') loadTransactionsTable();
            if(section === 'reports') updateReports();
        }

        // --- FUNGSI-FUNGSI API ---

        async function loadProducts() {
            try {
                const response = await fetch('api.php?action=get_products');
                productsCache = await response.json();
                displayFilteredProducts(productsCache);
                loadProductsTable();
                updateReports();
            } catch (error) {
                console.error('Error loading products:', error);
                alert('Gagal memuat produk dari server.');
            }
        }
        
        function displayFilteredProducts(products, filter = '', category = '') {
            const grid = document.getElementById('products-grid');
            grid.innerHTML = '';
            
            let filteredProducts = products.filter(p => p.stock > 0);

            if (filter) {
                filteredProducts = filteredProducts.filter(p => p.name.toLowerCase().includes(filter.toLowerCase()));
            }
            if (category) {
                filteredProducts = filteredProducts.filter(p => p.category === category);
            }

            if(filteredProducts.length === 0) {
                grid.innerHTML = '<p class="text-center text-muted col-12">Produk tidak ditemukan.</p>';
                return;
            }

            filteredProducts.forEach(product => {
                const productCard = `
                    <div class="col-md-4 mb-3">
                        <div class="card product-card" onclick="addToCart(${product.id})">
                            <div class="card-body text-center">
                                <i class="fas ${product.category === 'makanan' ? 'fa-utensils' : product.category === 'minuman' ? 'fa-coffee' : 'fa-cookie-bite'} fa-3x text-primary mb-2"></i>
                                <h6 class="card-title">${product.name}</h6>
                                <p class="card-text">
                                    <small class="text-muted">${product.category}</small><br>
                                    <strong class="text-success">Rp ${parseInt(product.price).toLocaleString()}</strong><br>
                                    <small>Stok: <span class="badge bg-success">${product.stock}</span></small>
                                </p>
                            </div>
                        </div>
                    </div>
                `;
                grid.innerHTML += productCard;
            });
        }
        
        function searchProducts(query) {
            const category = document.querySelector('select[onchange="filterByCategory(this.value)"]').value;
            displayFilteredProducts(productsCache, query, category);
        }

        function filterByCategory(category) {
            const query = document.querySelector('input[placeholder="Cari produk..."]').value;
            displayFilteredProducts(productsCache, query, category);
        }

        // Cart functions
        function addToCart(productId) {
            const product = productsCache.find(p => p.id == productId);
            if (!product || product.stock <= 0) {
                alert('Produk tidak tersedia atau stok habis');
                return;
            }

            const existingItem = cart.find(item => item.id == productId);
            if (existingItem) {
                if (existingItem.quantity < product.stock) {
                    existingItem.quantity++;
                } else {
                    alert('Stok tidak mencukupi');
                }
            } else {
                cart.push({ id: parseInt(product.id), name: product.name, price: parseFloat(product.price), quantity: 1 });
            }
            updateCartDisplay();
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.id != productId);
            updateCartDisplay();
        }

        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id == productId);
            const product = productsCache.find(p => p.id == productId);
            
            if (item) {
                const newQuantity = item.quantity + change;
                if (newQuantity <= 0) {
                    removeFromCart(productId);
                } else if (newQuantity > product.stock) {
                    item.quantity = product.stock;
                    alert('Stok tidak mencukupi');
                } else {
                    item.quantity = newQuantity;
                }
                updateCartDisplay();
            }
        }
        
        function updateCartDisplay() {
            const cartItems = document.getElementById('cart-items');
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-muted text-center">Keranjang kosong</p>';
            } else {
                cartItems.innerHTML = '';
                cart.forEach(item => {
                    const cartItemHTML = `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">${item.name}</h6>
                                    <small class="text-muted">Rp ${parseInt(item.price).toLocaleString()}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, -1)">-</button>
                                    <span class="mx-2">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, 1)">+</button>
                                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromCart(${item.id})"> <i class="fas fa-trash"></i> </button>
                                </div>
                            </div>
                            <div class="text-end mt-1"> <strong>Rp ${(item.price * item.quantity).toLocaleString()}</strong> </div>
                        </div>
                    `;
                    cartItems.innerHTML += cartItemHTML;
                });
            }
            
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            const tax = subtotal * 0.1;
            const total = subtotal + tax;
            
            document.getElementById('subtotal').textContent = `Rp ${subtotal.toLocaleString()}`;
            document.getElementById('tax').textContent = `Rp ${tax.toLocaleString()}`;
            document.getElementById('total').textContent = `Rp ${total.toLocaleString()}`;
        }
        
        // Payment functions
        function processPayment() {
            if (cart.length === 0) {
                alert('Keranjang kosong, tidak ada yang bisa dibayar.');
                return;
            }
            
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            const totalWithTax = subtotal * 1.1; // Calculate total with 10% tax
            
            document.getElementById('paymentTotal').value = `Rp ${totalWithTax.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;
            document.getElementById('paymentAmount').value = '';
            document.getElementById('paymentChange').value = '';
            
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();
            
            document.getElementById('paymentAmount').oninput = function() {
                const paymentAmount = parseFloat(this.value) || 0;
                const change = paymentAmount - totalWithTax; // Use totalWithTax for calculation
                document.getElementById('paymentChange').value = 
                    change >= 0 ? `Rp ${change.toLocaleString('id-ID', { minimumFractionDigits: 2 })}` : 'Kurang bayar';
            };
        }

        async function completePayment() {
            const paymentAmount = parseFloat(document.getElementById('paymentAmount').value) || 0;
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            const total = subtotal * 1.1; // Recalculate total with tax for consistency
            
            if (paymentAmount < total) {
                alert('Jumlah bayar kurang dari total tagihan.');
                return;
            }

            const formData = new FormData();
            formData.append('cart', JSON.stringify(cart));
            formData.append('total', total);

            try {
                const response = await fetch('api.php?action=process_transaction', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    // Sembunyikan modal pembayaran
                    const paymentModalInstance = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    if (paymentModalInstance) {
                        paymentModalInstance.hide();
                    }

                    // Tampilkan modal sukses
                    const successModal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
                    successModal.show();

                    // Siapkan tombol cetak di modal sukses
                    const printButton = document.getElementById('printReceiptFromSuccessButton');
                    
                    // Gunakan cloneNode untuk menghapus listener sebelumnya dan mencegah penumpukan
                    const newPrintButton = printButton.cloneNode(true);
                    printButton.parentNode.replaceChild(newPrintButton, printButton);
                    
                    newPrintButton.onclick = () => {
                        successModal.hide(); // Sembunyikan modal sukses
                        viewTransactionDetails(result.transaction_id, true); // Tampilkan & cetak struk
                    };

                    // Atur ulang state aplikasi setelah modal sukses ditutup
                    document.getElementById('paymentSuccessModal').addEventListener('hidden.bs.modal', () => {
                        cart = []; // Kosongkan keranjang
                        loadAllData();
                        loadTransactionsTable();
                    }, { once: true }); // Listener hanya berjalan sekali

                } else {
                    alert('Terjadi kesalahan saat memproses transaksi: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error completing payment:', error);
                alert('Gagal menghubungi server untuk memproses pembayaran.');
            }
        }

        // Product management
        function showAddProduct() {
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = ''; // Ensure ID is empty for new product
            document.getElementById('productModalTitle').textContent = 'Tambah Produk Baru';
            const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
            modal.show();
        }

        async function saveProduct() {
            // This function now effectively calls addProduct, but if you want to use
            // the addProductModal for both add and edit, you'd need to modify this
            // to check if productId is present and then call addProduct or updateProduct.
            // For simplicity, sticking to the existing addProduct for new items.
            addProduct();
        }

        async function addProduct() {
            const form = document.getElementById('productForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData();
            formData.append('name', document.getElementById('productName').value);
            formData.append('category', document.getElementById('productCategory').value);
            formData.append('price', document.getElementById('productPrice').value);
            formData.append('stock', document.getElementById('productStock').value);
            
            try {
                const response = await fetch('api.php?action=add_product', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    alert('Produk berhasil disimpan');
                    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                    loadAllData();
                } else {
                    alert('Gagal menyimpan produk: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving product:', error);
                alert('Gagal menghubungi server.');
            }
        }

        async function loadProductsTable() {
            const tbody = document.getElementById('products-table');
            tbody.innerHTML = '';
            
            if(!productsCache || productsCache.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Belum ada produk.</td></tr>';
                return;
            }
            
            productsCache.forEach(product => {
                const row = `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.name}</td>
                        <td><span class="badge bg-secondary">${product.category}</span></td>
                        <td>Rp ${parseInt(product.price).toLocaleString()}</td>
                        <td><span class="badge ${product.stock > 10 ? 'bg-success' : product.stock > 0 ? 'bg-warning' : 'bg-danger'}">${product.stock}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="openEditProductModal(${product.id})"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        async function deleteProduct(productId) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini? Aksi ini tidak dapat dibatalkan.')) {
                const formData = new FormData();
                formData.append('id', productId);
                
                try {
                    const response = await fetch('api.php?action=delete_product', { method: 'POST', body: formData });
                    const result = await response.json();
                    if(result.success) {
                        alert('Produk berhasil dihapus');
                        loadAllData();
                    } else {
                        alert('Gagal menghapus produk: ' + (result.error || 'Tidak diketahui'));
                    }
                } catch(error) {
                    console.error('Error deleting product:', error);
                    alert('Gagal menghubungi server untuk menghapus produk.');
                }
            }
        }

        // --- FUNGSI BARU UNTUK EDIT PRODUK ---

        async function openEditProductModal(productId) {
            const product = productsCache.find(p => p.id == productId);
            if (product) {
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editProductName').value = product.name;
                document.getElementById('editProductCategory').value = product.category;
                document.getElementById('editProductPrice').value = product.price;
                document.getElementById('editProductStock').value = product.stock;

                const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                editModal.show();
            } else {
                alert('Product not found for editing.');
            }
        }

        async function updateProduct() {
            const form = document.getElementById('editProductForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData();
            formData.append('id', document.getElementById('editProductId').value);
            formData.append('name', document.getElementById('editProductName').value);
            formData.append('category', document.getElementById('editProductCategory').value);
            formData.append('price', document.getElementById('editProductPrice').value);
            formData.append('stock', document.getElementById('editProductStock').value);
            
            try {
                const response = await fetch('api.php?action=update_product', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    alert('Product updated successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                    loadAllData(); // Reload all data to refresh the table
                } else {
                    alert('Failed to update product: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error updating product:', error);
                alert('Failed to connect to the server for product update.');
            }
        }

        // --- AKHIR FUNGSI BARU UNTUK EDIT PRODUK ---
        
        // --- Transaction Functions ---

        async function loadTransactionsTable() {
            try {
                const response = await fetch('api.php?action=get_transactions');
                const transactions = await response.json();
                const tbody = document.getElementById('transactions-table');
                tbody.innerHTML = '';
                
                if(transactions.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Belum ada transaksi.</td></tr>';
                    return;
                }
                
                transactions.forEach(transaction => {
                    const date = new Date(transaction.transaction_date);
                    const row = `
                        <tr>
                            <td>#${transaction.id.toString().padStart(4, '0')}</td>
                            <td>${date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</td>
                            <td>Rp ${parseInt(transaction.total_amount).toLocaleString()}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewTransactionDetails(${transaction.id})">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } catch (error) {
                console.error('Error loading transactions:', error);
            }
        }

        async function viewTransactionDetails(transactionId, autoPrint = false) {
            try {
                const response = await fetch(`api.php?action=get_transaction_details&id=${transactionId}`);
                const data = await response.json();

                if (data && data.transaction) {
                    const receiptBody = document.getElementById('receiptModalBody');
                    const tx = data.transaction;
                    const items = data.items;
                    const txDate = new Date(tx.transaction_date);

                    let subtotal = 0;
                    let itemsHtml = '';
                    items.forEach(item => {
                        const itemTotal = item.quantity * item.price_per_item;
                        subtotal += itemTotal;
                        itemsHtml += `
                            <tr>
                                <td>${item.name}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-end">Rp ${parseInt(item.price_per_item).toLocaleString()}</td>
                                <td class="text-end">Rp ${parseInt(itemTotal).toLocaleString()}</td>
                            </tr>
                        `;
                    });

                    const tax = subtotal * 0.1;

                    receiptBody.innerHTML = `
                        <div id="printableArea">
                            <h5 class="text-center mb-0">KEDAI BIASANE</h5>
                            <p class="text-center small">Jl. Awan, RT.03/RW.21, No. 0000</p>
                            <hr class="my-2">
                            <p class="mb-0"><strong>ID Transaksi:</strong> #${String(tx.id).padStart(4, '0')}</p>
                            <p><strong>Tanggal:</strong> ${txDate.toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' })}</p>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Jml</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>${itemsHtml}</tbody>
                            </table>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between"><span>Subtotal</span> <strong>Rp ${parseInt(subtotal).toLocaleString()}</strong></div>
                            <div class="d-flex justify-content-between"><span>Pajak (10%)</span> <strong>Rp ${parseInt(tax).toLocaleString()}</strong></div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-5 fw-bold"><span>TOTAL</span> <span>Rp ${parseInt(tx.total_amount).toLocaleString()}</span></div>
                            <hr class="my-2">
                            <p class="text-center small mt-3">Terima kasih telah berbelanja!</p>
                        </div>
                    `;

                    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                    receiptModal.show();
                    
                    if (autoPrint) {
                        setTimeout(printReceipt, 500);
                    }

                } else {
                    alert('Gagal mengambil detail transaksi.');
                }
            } catch (error) {
                console.error('Error fetching transaction details:', error);
                alert('Terjadi kesalahan pada server saat mengambil detail.');
            }
        }
        
        function printReceipt() {
            const printableContent = document.getElementById('printableArea').innerHTML;
            const printWindow = window.open('', '', 'height=800,width=500');
            printWindow.document.write('<html><head><title>Cetak Struk</title>');
            printWindow.document.write('<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">');
            printWindow.document.write(`
                <style>
                    body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: auto; }
                    .table { font-size: 12px; }
                    p, .small { font-size: 12px; }
                    .fs-5 { font-size: 14px !important; }
                </style>
            `);
            printWindow.document.write('</head><body>');
            printWindow.document.write(printableContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        async function updateReports() {
            try {
                const response = await fetch('api.php?action=get_reports');
                const reports = await response.json();

                document.getElementById('daily-sales').textContent = `Rp ${parseInt(reports.daily_sales || 0).toLocaleString()}`;
                document.getElementById('daily-transactions').textContent = reports.daily_transactions || 0;
                document.getElementById('total-products').textContent = reports.total_products || 0;

                const avg = (reports.daily_transactions > 0) ? (reports.daily_sales / reports.daily_transactions) : 0;
                document.getElementById('avg-transaction').textContent = `Rp ${parseInt(avg).toLocaleString()}`;

            } catch(error) {
                console.error('Error updating reports:', error);
            }
        }
    </script>
</body>
</html>
