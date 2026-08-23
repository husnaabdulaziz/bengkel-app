-- =====================================================================
-- SISTEM MANAJEMEN BENGKEL - DATABASE SCHEMA v2 (REBUILD FROM SCRATCH)
-- Engine   : InnoDB
-- Charset  : utf8mb4
-- Model    : Single-database multi-tenant (company_id + branch_id scoping)
-- Target   : MySQL 8.0+ (cPanel shared hosting compatible)
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- =====================================================================
-- MODULE 1: CORE / MULTI-TENANT (Super Admin domain)
-- =====================================================================

-- companies = "member" website (1 row = 1 toko/bengkel owner account)
CREATE TABLE companies (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap        VARCHAR(150)    NOT NULL COMMENT 'Nama pemilik/PIC',
    nama_toko           VARCHAR(150)    NOT NULL,
    alamat_toko         TEXT            NULL,
    telpon              VARCHAR(30)     NULL,
    email               VARCHAR(150)    NOT NULL UNIQUE,
    logo_path           VARCHAR(255)    NULL,
    license_start_date  DATE            NOT NULL,
    license_end_date    DATE            NOT NULL,
    license_status      ENUM('active','expiring_soon','expired','suspended') NOT NULL DEFAULT 'active',
    consolidated_report_enabled BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Gabung laporan semua cabang',
    created_at          TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at          TIMESTAMP       NULL,
    INDEX idx_companies_license_end (license_end_date),
    INDEX idx_companies_status (license_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- branches = cabang toko
CREATE TABLE branches (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id   BIGINT UNSIGNED NOT NULL,
    nama_cabang  VARCHAR(150)    NOT NULL,
    alamat       TEXT            NULL,
    telpon       VARCHAR(30)     NULL,
    is_main      BOOLEAN         NOT NULL DEFAULT FALSE,
    status       ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at   TIMESTAMP       NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_branches_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 2: USERS, AUTH & ROLE MANAGEMENT (Spatie Permission - team mode)
-- =====================================================================

CREATE TABLE users (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id          BIGINT UNSIGNED NULL COMMENT 'NULL untuk Super Admin',
    name                VARCHAR(150)    NOT NULL,
    email               VARCHAR(150)    NOT NULL,
    phone               VARCHAR(30)     NULL,
    password            VARCHAR(255)    NOT NULL,
    is_super_admin      BOOLEAN         NOT NULL DEFAULT FALSE,
    status              ENUM('active','inactive') NOT NULL DEFAULT 'active',
    last_login_at       TIMESTAMP       NULL,
    email_verified_at   TIMESTAMP       NULL,
    remember_token      VARCHAR(100)    NULL,
    created_at          TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at          TIMESTAMP       NULL,
    UNIQUE KEY uq_users_email_company (email, company_id),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_users_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- pivot: 1 akun (Admin Toko) bisa akses banyak cabang
CREATE TABLE user_branches (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    branch_id   BIGINT UNSIGNED NOT NULL,
    is_default  BOOLEAN NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_branch (user_id, branch_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --- Spatie laravel-permission (team feature, team_foreign_key = company_id) ---
CREATE TABLE permissions (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    guard_name  VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    UNIQUE KEY uq_permissions (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id  BIGINT UNSIGNED NULL COMMENT 'team_foreign_key; NULL = global role (super admin)',
    name        VARCHAR(150) NOT NULL COMMENT 'admin_toko, karyawan_toko, teknisi, super_admin',
    guard_name  VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    UNIQUE KEY uq_roles (company_id, name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type    VARCHAR(150) NOT NULL,
    model_id      BIGINT UNSIGNED NOT NULL,
    company_id    BIGINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (company_id, permission_id, model_id, model_type),
    INDEX idx_mhp_model (model_id, model_type),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE model_has_roles (
    role_id     BIGINT UNSIGNED NOT NULL,
    model_type  VARCHAR(150) NOT NULL,
    model_id    BIGINT UNSIGNED NOT NULL,
    company_id  BIGINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (company_id, role_id, model_id, model_type),
    INDEX idx_mhr_model (model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id       BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 3: MASTER DATA PRODUK
-- =====================================================================

CREATE TABLE product_categories (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id  BIGINT UNSIGNED NOT NULL,
    nama        VARCHAR(100) NOT NULL COMMENT 'Oli, Ban, Sparepart, Jasa, dll',
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY uq_category (company_id, nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_brands (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id  BIGINT UNSIGNED NOT NULL,
    nama        VARCHAR(100) NOT NULL COMMENT 'Shell, Honda, dll',
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY uq_brand (company_id, nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE suppliers (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    nama            VARCHAR(150) NOT NULL,
    contact_person  VARCHAR(100) NULL,
    telpon          VARCHAR(30)  NULL,
    alamat          TEXT NULL,
    created_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id          BIGINT UNSIGNED NOT NULL,
    category_id         BIGINT UNSIGNED NULL,
    brand_id            BIGINT UNSIGNED NULL,
    default_supplier_id BIGINT UNSIGNED NULL,
    sku                 VARCHAR(60)  NULL,
    nama                VARCHAR(180) NOT NULL,
    satuan              VARCHAR(30)  NOT NULL DEFAULT 'pcs',
    is_jasa             BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'TRUE jika item jasa (tidak punya stock)',
    harga_modal         DECIMAL(15,2) NOT NULL DEFAULT 0,
    harga_jual          DECIMAL(15,2) NOT NULL DEFAULT 0,
    harga_jual_jasa     DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'harga jual + jasa pasang',
    harga_online        DECIMAL(15,2) NOT NULL DEFAULT 0,
    harga_ojol          DECIMAL(15,2) NOT NULL DEFAULT 0,
    garansi_aktif       BOOLEAN NOT NULL DEFAULT FALSE,
    garansi_durasi_hari INT UNSIGNED NULL,
    minimum_stock       INT NOT NULL DEFAULT 0 COMMENT 'default global, bisa dioverride per cabang',
    status              ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at          TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES product_brands(id) ON DELETE SET NULL,
    FOREIGN KEY (default_supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    INDEX idx_products_company (company_id),
    INDEX idx_products_category (category_id),
    INDEX idx_products_brand (brand_id),
    FULLTEXT INDEX ftx_products_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Denormalized stock cache per cabang (source of truth = stock_movements)
CREATE TABLE product_branch_stocks (
    id                     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id             BIGINT UNSIGNED NOT NULL,
    branch_id              BIGINT UNSIGNED NOT NULL,
    stock_qty              INT NOT NULL DEFAULT 0,
    minimum_stock_override INT NULL,
    updated_at             TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_branch (product_id, branch_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    INDEX idx_pbs_stock (branch_id, stock_qty)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 4: INVENTORY & STOCK MOVEMENT (ledger)
-- =====================================================================

CREATE TABLE stock_movements (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    branch_id       BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    type            ENUM('in','out','adjustment_in','adjustment_out','transfer_in','transfer_out') NOT NULL,
    quantity        INT NOT NULL COMMENT 'selalu positif, arah ditentukan oleh type',
    reference_type  ENUM('purchase','sale','opname','transfer','manual') NOT NULL,
    reference_id    BIGINT UNSIGNED NULL,
    notes           VARCHAR(255) NULL,
    created_by      BIGINT UNSIGNED NULL,
    created_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sm_product_branch (product_id, branch_id),
    INDEX idx_sm_reference (reference_type, reference_id),
    INDEX idx_sm_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE purchases (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    branch_id       BIGINT UNSIGNED NOT NULL,
    supplier_id     BIGINT UNSIGNED NOT NULL,
    invoice_number  VARCHAR(60) NOT NULL,
    purchase_date   DATE NOT NULL,
    total_amount    DECIMAL(15,2) NOT NULL DEFAULT 0,
    status          ENUM('draft','completed','cancelled') NOT NULL DEFAULT 'completed',
    created_by      BIGINT UNSIGNED NULL,
    created_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uq_purchase_invoice (company_id, invoice_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE purchase_items (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id      BIGINT UNSIGNED NOT NULL,
    product_id       BIGINT UNSIGNED NOT NULL,
    quantity         INT NOT NULL,
    price_per_unit   DECIMAL(15,2) NOT NULL,
    subtotal         DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE stock_opnames (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id   BIGINT UNSIGNED NOT NULL,
    branch_id    BIGINT UNSIGNED NOT NULL,
    kode_opname  VARCHAR(60) NOT NULL,
    opname_date  DATE NOT NULL,
    category_id  BIGINT UNSIGNED NULL COMMENT 'filter jenis produk saat create',
    brand_id     BIGINT UNSIGNED NULL COMMENT 'filter merek saat create',
    status       ENUM('draft','completed') NOT NULL DEFAULT 'draft',
    is_adjusted  BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'apakah stock sudah disesuaikan otomatis',
    notes        TEXT NULL,
    created_by   BIGINT UNSIGNED NULL,
    completed_at TIMESTAMP NULL,
    created_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES product_brands(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE stock_opname_items (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stock_opname_id BIGINT UNSIGNED NOT NULL,
    product_id      BIGINT UNSIGNED NOT NULL,
    system_stock    INT NOT NULL,
    real_stock      INT NOT NULL,
    difference      INT GENERATED ALWAYS AS (real_stock - system_stock) STORED,
    FOREIGN KEY (stock_opname_id) REFERENCES stock_opnames(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Transfer stock antar cabang: requested -> approved -> shipped -> received
CREATE TABLE stock_transfers (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    from_branch_id  BIGINT UNSIGNED NOT NULL,
    to_branch_id    BIGINT UNSIGNED NOT NULL,
    kode_transfer   VARCHAR(60) NOT NULL,
    status          ENUM('requested','approved','shipped','received','rejected','cancelled') NOT NULL DEFAULT 'requested',
    requested_by    BIGINT UNSIGNED NULL,
    approved_by     BIGINT UNSIGNED NULL,
    shipped_by      BIGINT UNSIGNED NULL,
    received_by     BIGINT UNSIGNED NULL,
    requested_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    approved_at     TIMESTAMP NULL,
    shipped_at      TIMESTAMP NULL,
    received_at     TIMESTAMP NULL,
    notes           TEXT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (from_branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (to_branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (shipped_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_st_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE stock_transfer_items (
    id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stock_transfer_id  BIGINT UNSIGNED NOT NULL,
    product_id         BIGINT UNSIGNED NOT NULL,
    qty_requested      INT NOT NULL DEFAULT 0,
    qty_approved       INT NULL,
    qty_shipped        INT NULL,
    qty_received       INT NULL,
    FOREIGN KEY (stock_transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 5: PELANGGAN
-- =====================================================================

CREATE TABLE customers (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id        BIGINT UNSIGNED NOT NULL,
    branch_id         BIGINT UNSIGNED NULL COMMENT 'cabang pertama kali didaftarkan',
    nama              VARCHAR(150) NOT NULL,
    telpon            VARCHAR(30)  NULL,
    plat_nomor        VARCHAR(20)  NULL,
    alamat            TEXT NULL,
    jenis_kendaraan   VARCHAR(60)  NULL COMMENT 'motor/mobil',
    merk_kendaraan    VARCHAR(60)  NULL,
    model_kendaraan   VARCHAR(60)  NULL,
    last_visit_at     TIMESTAMP NULL COMMENT 'update otomatis saat work order selesai',
    created_at        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at        TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    INDEX idx_customers_search (company_id, nama, telpon, plat_nomor),
    INDEX idx_customers_last_visit (company_id, last_visit_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 6: POS / WORK ORDER (invoice servis, 3 tahap)
-- =====================================================================

CREATE TABLE work_orders (
    id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id         BIGINT UNSIGNED NOT NULL,
    branch_id          BIGINT UNSIGNED NOT NULL,
    invoice_number     VARCHAR(60) NULL COMMENT 'digenerate saat pembayaran selesai',
    customer_id        BIGINT UNSIGNED NOT NULL,
    stage              ENUM('draft','queue','payment','completed','cancelled') NOT NULL DEFAULT 'draft',
    queue_number        INT NULL,
    queue_date          DATE NULL,
    customer_price_tier ENUM('harga_jual','harga_jual_jasa','harga_online','harga_ojol','custom') NOT NULL DEFAULT 'harga_jual',
    subtotal           DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount_type      ENUM('percent','fixed') NULL,
    discount_value     DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_amount       DECIMAL(15,2) NOT NULL DEFAULT 0,
    payment_method     ENUM('tunai','transfer','debit') NULL,
    payment_status     ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid',
    paid_at            TIMESTAMP NULL,
    notes              TEXT NULL,
    created_by         BIGINT UNSIGNED NULL,
    created_at         TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at         TIMESTAMP NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uq_wo_invoice (company_id, invoice_number),
    INDEX idx_wo_stage (branch_id, stage),
    INDEX idx_wo_queue (branch_id, queue_date, queue_number),
    INDEX idx_wo_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE work_order_items (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    work_order_id     BIGINT UNSIGNED NOT NULL,
    product_id        BIGINT UNSIGNED NULL,
    item_name         VARCHAR(180) NOT NULL COMMENT 'snapshot nama saat transaksi',
    price_tier_used   ENUM('harga_jual','harga_jual_jasa','harga_online','harga_ojol','custom') NOT NULL,
    unit_price        DECIMAL(15,2) NOT NULL COMMENT 'harga aktual dikenakan, bisa diedit manual',
    quantity          INT NOT NULL DEFAULT 1,
    subtotal          DECIMAL(15,2) NOT NULL,
    is_warranty_claim BOOLEAN NOT NULL DEFAULT FALSE,
    warranty_id       BIGINT UNSIGNED NULL,
    created_at        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_woi_wo (work_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- mendukung banyak teknisi per 1 item pekerjaan, masing2 dengan fee sendiri
CREATE TABLE work_order_item_technicians (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    work_order_item_id  BIGINT UNSIGNED NOT NULL,
    user_id             BIGINT UNSIGNED NOT NULL COMMENT 'teknisi',
    fee_amount          DECIMAL(15,2) NOT NULL DEFAULT 0,
    fee_notes           VARCHAR(255) NULL,
    FOREIGN KEY (work_order_item_id) REFERENCES work_order_items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_woit_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 7: FEE TEKNISI (setting per produk)
-- =====================================================================

CREATE TABLE product_fees (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id  BIGINT UNSIGNED NOT NULL,
    product_id  BIGINT UNSIGNED NOT NULL,
    fee_type    ENUM('percent','fixed') NOT NULL DEFAULT 'fixed',
    fee_value   DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uq_product_fee (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 8: GARANSI
-- =====================================================================

CREATE TABLE warranties (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id          BIGINT UNSIGNED NOT NULL,
    branch_id           BIGINT UNSIGNED NOT NULL,
    work_order_item_id  BIGINT UNSIGNED NOT NULL,
    product_id          BIGINT UNSIGNED NOT NULL,
    customer_id         BIGINT UNSIGNED NOT NULL,
    kode_garansi        VARCHAR(60) NOT NULL,
    warranty_start_date DATE NOT NULL,
    warranty_end_date   DATE NOT NULL,
    duration_days       INT NOT NULL,
    status              ENUM('active','claimed','expired') NOT NULL DEFAULT 'active',
    created_at          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (work_order_item_id) REFERENCES work_order_items(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    INDEX idx_warranty_search (company_id, customer_id, warranty_end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE warranty_claims (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    warranty_id     BIGINT UNSIGNED NOT NULL,
    claim_date      DATE NOT NULL,
    work_order_id   BIGINT UNSIGNED NULL COMMENT 'WO baru untuk proses klaim, jika ada',
    notes           TEXT NULL,
    created_by      BIGINT UNSIGNED NULL,
    created_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (warranty_id) REFERENCES warranties(id) ON DELETE CASCADE,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 9: KEUANGAN
-- =====================================================================

CREATE TABLE expenses (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id   BIGINT UNSIGNED NOT NULL,
    branch_id    BIGINT UNSIGNED NOT NULL,
    category     VARCHAR(100) NOT NULL,
    description  VARCHAR(255) NULL,
    amount       DECIMAL(15,2) NOT NULL,
    expense_date DATE NOT NULL,
    created_by   BIGINT UNSIGNED NULL,
    created_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_expenses_date (branch_id, expense_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE petty_cash_transactions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      BIGINT UNSIGNED NOT NULL,
    branch_id       BIGINT UNSIGNED NOT NULL,
    type            ENUM('in','out') NOT NULL,
    amount          DECIMAL(15,2) NOT NULL,
    description     VARCHAR(255) NULL,
    reference_type  ENUM('sale','expense','purchase','manual') NOT NULL DEFAULT 'manual',
    reference_id    BIGINT UNSIGNED NULL,
    created_by      BIGINT UNSIGNED NULL,
    created_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_petty_branch_date (branch_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 10: LOG AKTIVITAS & NOTIFIKASI
-- =====================================================================

CREATE TABLE activity_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id  BIGINT UNSIGNED NULL,
    branch_id   BIGINT UNSIGNED NULL,
    user_id     BIGINT UNSIGNED NULL,
    action      VARCHAR(100) NOT NULL COMMENT 'create_invoice, payment, stock_opname, delete_customer, dll',
    model_type  VARCHAR(150) NULL,
    model_id    BIGINT UNSIGNED NULL,
    description VARCHAR(255) NULL,
    ip_address  VARCHAR(45) NULL,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_log_company_date (company_id, created_at),
    INDEX idx_log_model (model_type, model_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notifications (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id  BIGINT UNSIGNED NULL COMMENT 'NULL jika ditujukan ke Super Admin',
    user_id     BIGINT UNSIGNED NULL COMMENT 'NULL jika broadcast ke role tertentu',
    type        VARCHAR(60) NOT NULL COMMENT 'license_expiring, stock_low, transfer_request, dll',
    title       VARCHAR(150) NOT NULL,
    message     VARCHAR(255) NOT NULL,
    data        JSON NULL,
    is_read     BOOLEAN NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notif_user (user_id, is_read),
    INDEX idx_notif_company (company_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 11: PENGATURAN TOKO
-- =====================================================================

CREATE TABLE store_settings (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id    BIGINT UNSIGNED NOT NULL,
    branch_id     BIGINT UNSIGNED NULL COMMENT 'NULL = berlaku company-wide',
    setting_key   VARCHAR(100) NOT NULL COMMENT 'printer_paper_size, invoice_prefix, dll',
    setting_value TEXT NULL,
    created_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    UNIQUE KEY uq_setting (company_id, branch_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- MODULE 12: INTEGRASI E-COMMERCE (fase pengembangan lanjutan)
-- =====================================================================

CREATE TABLE ecommerce_integrations (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id    BIGINT UNSIGNED NOT NULL,
    branch_id     BIGINT UNSIGNED NOT NULL,
    platform      ENUM('shopee','tokopedia') NOT NULL,
    store_id      VARCHAR(100) NULL,
    access_token  TEXT NULL,
    refresh_token TEXT NULL,
    status        ENUM('connected','disconnected','error') NOT NULL DEFAULT 'disconnected',
    last_sync_at  TIMESTAMP NULL,
    created_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ecommerce_product_mappings (
    id                       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ecommerce_integration_id BIGINT UNSIGNED NOT NULL,
    product_id               BIGINT UNSIGNED NOT NULL,
    ecommerce_product_id     VARCHAR(150) NOT NULL,
    ecommerce_sku            VARCHAR(150) NULL,
    created_at                TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ecommerce_integration_id) REFERENCES ecommerce_integrations(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uq_ecom_mapping (ecommerce_integration_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- CATATAN:
-- 1. Tabel default Laravel (password_resets, sessions, cache, jobs,
--    failed_jobs, personal_access_tokens) TIDAK ditulis manual di sini -
--    biarkan Laravel generate via `php artisan migrate` bawaan.
-- 2. Total tabel bisnis kustom: 35 tabel, terbagi 12 modul.
-- 3. Semua tabel transaksional wajib di-scope company_id (dan branch_id
--    jika relevan) di level Eloquent Global Scope, BUKAN hanya andalkan
--    foreign key.
-- =====================================================================
