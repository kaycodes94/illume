-- ============================================================
--  ILLUME by Light Peace — Full Database Schema
--  Generated from: https://tinyurl.com/2wnzz8zn
--  Created: 2026-05-13
-- ============================================================

CREATE DATABASE IF NOT EXISTS illume_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE illume_db;

-- Clear old tables for fresh migration
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS site_settings;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS revenue_category_breakdown;
DROP TABLE IF EXISTS revenue_snapshots;
DROP TABLE IF EXISTS fitting_requests;
DROP TABLE IF EXISTS guide_downloads;
DROP TABLE IF EXISTS leads;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS payment_milestones;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS consultations;
DROP TABLE IF EXISTS design_submissions;
DROP TABLE IF EXISTS order_timeline;
DROP TABLE IF EXISTS portfolio;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. ADMIN / STAFF USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(120)  NOT NULL,
    email         VARCHAR(180)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('founder','manager','staff','receptionist') NOT NULL DEFAULT 'staff',
    atelier       ENUM('Abuja','Ebonyi','Virtual','All') DEFAULT 'All',
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    last_login    DATETIME     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. CLIENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS clients (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(120)  NOT NULL,
    email         VARCHAR(180)  DEFAULT NULL,
    phone         VARCHAR(30)   DEFAULT NULL,
    whatsapp      VARCHAR(30)   DEFAULT NULL,
    city          VARCHAR(80)   DEFAULT NULL,
    country       VARCHAR(80)   DEFAULT 'Nigeria',
    source        ENUM('Instagram','WhatsApp','Referral','Walk-In','Website','Other') DEFAULT 'Website',
    notes         TEXT          DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 3. SERVICES
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(120)  NOT NULL,
    category      ENUM('Bridals & Asoebi','Suits & Dinner','African Luxury','Consultancy') NOT NULL,
    description   TEXT          DEFAULT NULL,
    base_price    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency      VARCHAR(5)   NOT NULL DEFAULT 'NGN',
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed default services
INSERT INTO services (name, category, description, base_price) VALUES
('Bridal Gown Bespoke',   'Bridals & Asoebi',  'Full bespoke bridal gown with custom beading',        350000.00),
('Asoebi Coordination',   'Bridals & Asoebi',  'Group asoebi fabric selection & dressmaking',         120000.00),
('Executive Suit',        'Suits & Dinner',    'Power-corridor mastered silhouette suit',             180000.00),
('Dinner Ensemble',       'Suits & Dinner',    'Sharp dinner-wear with intentional tailoring',        150000.00),
('African Luxury Outfit', 'African Luxury',    'Traditional forms reimagined for the global stage',   200000.00),
('Visual Identity Session','Consultancy',      'Wardrobe audit and visual identity consultation',      80000.00),
('Fashion Illustration',  'Consultancy',       'Custom fashion illustration for your look',            50000.00);

-- ============================================================
-- 4. BESPOKE PIECES / ORDERS
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id       INT UNSIGNED NOT NULL,
    service_id      INT UNSIGNED NOT NULL,
    atelier         ENUM('Abuja','Ebonyi','Virtual') NOT NULL DEFAULT 'Abuja',
    status          ENUM('Consultation','In Progress','Beading','Final Review','Completed','Cancelled') NOT NULL DEFAULT 'Consultation',
    description     TEXT         DEFAULT NULL,
    total_amount    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency        VARCHAR(5)   NOT NULL DEFAULT 'NGN',
    deadline        DATE          DEFAULT NULL,
    assigned_to     INT UNSIGNED  DEFAULT NULL,   -- admin_users.id
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id)   REFERENCES clients(id)     ON DELETE RESTRICT,
    FOREIGN KEY (service_id)  REFERENCES services(id)    ON DELETE RESTRICT,
    FOREIGN KEY (assigned_to) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 5. INVOICES & PAYMENT MILESTONES
-- ============================================================
CREATE TABLE IF NOT EXISTS invoices (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        INT UNSIGNED NOT NULL,
    invoice_number  VARCHAR(30)  NOT NULL UNIQUE,
    total_amount    DECIMAL(12,2) NOT NULL,
    amount_paid     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency        VARCHAR(5)   NOT NULL DEFAULT 'NGN',
    status          ENUM('Draft','Sent','Partial','Paid','Overdue','Cancelled') NOT NULL DEFAULT 'Draft',
    due_date        DATE          DEFAULT NULL,
    issued_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_milestones (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id      INT UNSIGNED NOT NULL,
    label           VARCHAR(80)  NOT NULL,          -- e.g. "30% Deposit", "Final Payment"
    percentage      TINYINT UNSIGNED DEFAULT NULL,
    amount          DECIMAL(12,2) NOT NULL,
    currency        VARCHAR(5)   NOT NULL DEFAULT 'NGN',
    status          ENUM('Pending','Paid','Waived') NOT NULL DEFAULT 'Pending',
    due_date        DATE          DEFAULT NULL,
    paid_at         DATETIME      DEFAULT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 6. APPOINTMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS appointments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id       INT UNSIGNED NOT NULL,
    order_id        INT UNSIGNED DEFAULT NULL,
    type            ENUM('Initial Consultation','First Fitting','Second Fitting','Final Review','Bead-Work Review','Virtual Call','Other') NOT NULL,
    atelier         ENUM('Abuja','Ebonyi','Virtual') NOT NULL DEFAULT 'Abuja',
    appointment_date DATE         NOT NULL,
    appointment_time TIME         NOT NULL,
    duration_mins   SMALLINT UNSIGNED DEFAULT 60,
    assigned_staff  INT UNSIGNED  DEFAULT NULL,
    notes           TEXT          DEFAULT NULL,
    status          ENUM('Scheduled','Completed','Cancelled','No-Show') NOT NULL DEFAULT 'Scheduled',
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id)      REFERENCES clients(id)     ON DELETE RESTRICT,
    FOREIGN KEY (order_id)       REFERENCES orders(id)      ON DELETE SET NULL,
    FOREIGN KEY (assigned_staff) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 7. LEADS / INBOUND PIPELINE
-- ============================================================
CREATE TABLE IF NOT EXISTS leads (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120)  DEFAULT NULL,
    email           VARCHAR(180)  DEFAULT NULL,
    phone           VARCHAR(30)   DEFAULT NULL,
    service_interest ENUM('Bridals & Asoebi','Suits & Dinner','African Luxury','Consultancy','Unknown') DEFAULT 'Unknown',
    source          ENUM('Instagram DM','WhatsApp','Website Form','Referral','Other') DEFAULT 'Website Form',
    trigger_keyword VARCHAR(80)   DEFAULT NULL,     -- e.g. "LEGACY"
    timeline        VARCHAR(120)  DEFAULT NULL,      -- e.g. "December 2026"
    notes           TEXT          DEFAULT NULL,
    status          ENUM('New','Contacted','Diagnosed','Converted','Lost') NOT NULL DEFAULT 'New',
    converted_client_id INT UNSIGNED DEFAULT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (converted_client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 8. SILHOUETTE GUIDE DOWNLOADS
-- ============================================================
CREATE TABLE IF NOT EXISTS guide_downloads (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(180) NOT NULL,
    name        VARCHAR(120) DEFAULT NULL,
    source      ENUM('Website','Instagram DM','WhatsApp','Other') DEFAULT 'Website',
    trigger_kw  VARCHAR(80)  DEFAULT NULL,
    ip_address  VARCHAR(45)  DEFAULT NULL,
    downloaded_at DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 9. FITTING REQUEST FORM SUBMISSIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS fitting_requests (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(120) NOT NULL,
    email           VARCHAR(180) NOT NULL,
    phone           VARCHAR(30)  DEFAULT NULL,
    service_type    ENUM('Bridals & Asoebi','Suits & Dinner','African Luxury','Consultancy') DEFAULT NULL,
    preferred_atelier ENUM('Abuja','Ebonyi','Virtual') DEFAULT 'Abuja',
    preferred_date  DATE         DEFAULT NULL,
    message         TEXT         DEFAULT NULL,
    status          ENUM('Pending','Routed','Converted','Archived') NOT NULL DEFAULT 'Pending',
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 10. REVENUE TRACKING (Monthly Snapshots)
-- ============================================================
CREATE TABLE IF NOT EXISTS revenue_snapshots (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    year            YEAR         NOT NULL,
    month           TINYINT(2)   NOT NULL,                 -- 1–12
    gross_revenue   DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    cogs            DECIMAL(14,2) NOT NULL DEFAULT 0.00,   -- Cost of Goods Sold
    net_revenue     DECIMAL(14,2) GENERATED ALWAYS AS (gross_revenue - cogs) STORED,
    currency        VARCHAR(5)   NOT NULL DEFAULT 'NGN',
    notes           TEXT         DEFAULT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_year_month (year, month)
) ENGINE=InnoDB;

-- Seed current month snapshot from site data
INSERT INTO revenue_snapshots (year, month, gross_revenue, cogs, notes) VALUES
(2026, 5, 4250000.00, 840000.00, 'May 2026 — +12% vs April. Premium Fabrics & Custom Beading COGS.');

-- ============================================================
-- 11. SERVICE MIX / CATEGORY BREAKDOWN (Per Snapshot)
-- ============================================================
CREATE TABLE IF NOT EXISTS revenue_category_breakdown (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    snapshot_id     INT UNSIGNED NOT NULL,
    category        ENUM('Bridals & Asoebi','Suits & Dinner','African Luxury','Consultancy') NOT NULL,
    percentage      DECIMAL(5,2) NOT NULL,
    amount          DECIMAL(14,2) DEFAULT NULL,
    FOREIGN KEY (snapshot_id) REFERENCES revenue_snapshots(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed May 2026 service mix
INSERT INTO revenue_category_breakdown (snapshot_id, category, percentage, amount) VALUES
(1, 'Bridals & Asoebi', 42.00, 1785000.00),
(1, 'Suits & Dinner',   28.00, 1190000.00),
(1, 'African Luxury',   18.00,  765000.00),
(1, 'Consultancy',      12.00,  510000.00);

-- ============================================================
-- 12. ACTIVITY LOG (Audit Trail)
-- ============================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT UNSIGNED  DEFAULT NULL,
    action      VARCHAR(120)  NOT NULL,
    entity_type VARCHAR(60)   DEFAULT NULL,   -- e.g. 'order', 'client', 'invoice'
    entity_id   INT UNSIGNED  DEFAULT NULL,
    details     TEXT          DEFAULT NULL,
    ip_address  VARCHAR(45)   DEFAULT NULL,
    logged_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 13. SITE SETTINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS site_settings (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(80)  NOT NULL UNIQUE,
    value       TEXT         DEFAULT NULL,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO site_settings (setting_key, value) VALUES
('brand_name',        'ILLUME by Light Peace'),
('tagline',           'Crafted in Light'),
('whatsapp_number',   '+2349039963415'),
('email_contact',     'lightpeacelimited@gmail.com'),
('atelier_abuja',     'Kubwa, Abuja'),
('atelier_ebonyi',    'Abakaliki, Ebonyi State'),
('active_regions',    'Enugu, Owerri'),
('global_shipping',   '1'),
('instagram_handle',  '@illume_lightpeace'),
('founder_name',      'Ikedichukwu Peace'),
('founded_year',      '2018'),
('lead_conversion_rate', '68');

-- ============================================================
-- DEFAULT ADMIN SEED (password: admin123 — CHANGE IN PRODUCTION)
-- ============================================================
INSERT INTO admin_users (full_name, email, password_hash, role, atelier) VALUES
('Ikedichukwu Peace', 'lightpeacelimited@gmail.com',
 '$2y$12$examplehashchangebeforegoingliveinproduction111111111',
 'founder', 'All'),
('Receptionist Desk', 'reception@illume.com',
 '$2y$12$examplehashchangebeforegoingliveinproduction222222222',
 'receptionist', 'Abuja');
