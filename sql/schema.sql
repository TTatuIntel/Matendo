-- Matendo Medics — unified schema (replaces the two divergent DBs).
-- Run once:
--   mysql -u root -p < sql/schema.sql
-- Then create the app user (least-privilege) — adjust password!
--   CREATE USER 'matendo_app'@'localhost' IDENTIFIED BY 'change_me_strong_password';
--   GRANT SELECT, INSERT, UPDATE, DELETE ON matendo.* TO 'matendo_app'@'localhost';
--   FLUSH PRIVILEGES;

CREATE DATABASE IF NOT EXISTS matendo
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE matendo;

-- =========================================================================
-- USERS & AUTH
-- =========================================================================
CREATE TABLE IF NOT EXISTS users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role            ENUM('client','professional','admin','facility') NOT NULL DEFAULT 'client',
    email           VARCHAR(190) NOT NULL UNIQUE,
    email_verified_at DATETIME NULL,
    password_hash   VARCHAR(255) NOT NULL,
    first_name      VARCHAR(100) NULL,
    last_name       VARCHAR(100) NULL,
    phone           VARCHAR(40)  NULL,
    status          ENUM('active','suspended','deleted') NOT NULL DEFAULT 'active',
    last_login_at   DATETIME NULL,
    failed_logins   TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until    DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS password_resets (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    token_hash  CHAR(64) NOT NULL UNIQUE,
    expires_at  DATETIME NOT NULL,
    used_at     DATETIME NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================================
-- TALENT (professionals) — Toptal-style profile
-- =========================================================================
CREATE TABLE IF NOT EXISTS professionals (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             BIGINT UNSIGNED NULL UNIQUE,
    reference_number    VARCHAR(20) NOT NULL UNIQUE,
    first_name          VARCHAR(100) NOT NULL,
    last_name           VARCHAR(100) NOT NULL,
    email               VARCHAR(190) NOT NULL,
    phone               VARCHAR(40)  NULL,
    address             VARCHAR(255) NULL,
    location            VARCHAR(190) NULL,
    coordinates         VARCHAR(80)  NULL,

    profession          VARCHAR(120) NOT NULL,
    other_profession    VARCHAR(120) NULL,
    specialization      VARCHAR(190) NULL,
    years_experience    INT UNSIGNED NOT NULL DEFAULT 0,
    license_number      VARCHAR(120) NULL,

    bio                 TEXT NULL,
    headline            VARCHAR(200) NULL,
    hourly_rate_kes     DECIMAL(10,2) NULL,
    languages           VARCHAR(255) NULL,
    avatar_path         VARCHAR(255) NULL,

    work_type           JSON NULL,
    shift_type          JSON NULL,
    preferred_location  VARCHAR(190) NULL,
    available_from      DATE NULL,

    status              ENUM('pending','screening','approved','rejected','active','paused')
                        NOT NULL DEFAULT 'pending',
    verified_license    TINYINT(1) NOT NULL DEFAULT 0,
    verified_id         TINYINT(1) NOT NULL DEFAULT 0,
    background_check    TINYINT(1) NOT NULL DEFAULT 0,
    rating_avg          DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    rating_count        INT UNSIGNED NOT NULL DEFAULT 0,

    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_prof_search (profession, status, preferred_location),
    FULLTEXT KEY ft_prof_search (first_name, last_name, headline, bio, specialization)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS professional_documents (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    professional_id   BIGINT UNSIGNED NOT NULL,
    kind              ENUM('resume','license','certification','id','other') NOT NULL,
    storage_path      VARCHAR(255) NOT NULL,
    original_name     VARCHAR(255) NOT NULL,
    mime_type         VARCHAR(120) NOT NULL,
    size_bytes        BIGINT UNSIGNED NOT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professional_id) REFERENCES professionals(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================================
-- HIRING REQUESTS
-- =========================================================================
CREATE TABLE IF NOT EXISTS facility_requests (
    id                       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_number         VARCHAR(20) NOT NULL UNIQUE,
    user_id                  BIGINT UNSIGNED NULL,
    facility_name            VARCHAR(190) NOT NULL,
    contact_person           VARCHAR(190) NOT NULL,
    email                    VARCHAR(190) NOT NULL,
    phone                    VARCHAR(40)  NOT NULL,
    coordinates              VARCHAR(80)  NULL,

    facility_type            VARCHAR(255) NULL,
    other_facility_type      VARCHAR(190) NULL,
    positions                VARCHAR(500) NULL,
    other_position           VARCHAR(190) NULL,
    duration                 VARCHAR(120) NULL,
    shift_type               VARCHAR(120) NULL,
    staff_number             INT UNSIGNED NOT NULL DEFAULT 1,
    start_date               DATE NULL,

    job_requirement_option   ENUM('none','upload','manual') NOT NULL DEFAULT 'none',
    qualifications           TEXT NULL,
    experience               TEXT NULL,
    job_description          TEXT NULL,
    job_description_path     VARCHAR(255) NULL,
    job_description_name     VARCHAR(255) NULL,
    job_description_mime     VARCHAR(120) NULL,

    status                   ENUM('new','reviewing','matched','closed','cancelled')
                             NOT NULL DEFAULT 'new',
    submitted_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Personal-care / home-care requests. PHI columns are AES-256-GCM encrypted at app layer.
CREATE TABLE IF NOT EXISTS care_requests (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_number    VARCHAR(20) NOT NULL UNIQUE,
    user_id             BIGINT UNSIGNED NULL,
    full_name           VARCHAR(190) NOT NULL,
    email               VARCHAR(190) NOT NULL,
    phone               VARCHAR(40)  NOT NULL,
    address             VARCHAR(255) NULL,
    care_type           VARCHAR(120) NOT NULL,
    other_care_type     VARCHAR(120) NULL,
    care_requirements   TEXT NULL,
    schedule            VARCHAR(120) NULL,

    -- ENCRYPTED PHI (AES-256-GCM, base64). Do not query directly.
    medical_conditions_enc  MEDIUMTEXT NULL,
    medications_enc         MEDIUMTEXT NULL,
    allergies_enc           MEDIUMTEXT NULL,

    emergency_contact   VARCHAR(190) NULL,
    emergency_phone     VARCHAR(40)  NULL,
    status              ENUM('new','reviewing','matched','closed','cancelled')
                        NOT NULL DEFAULT 'new',
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================================
-- ENGAGEMENTS / MATCHING (Toptal-style)
-- =========================================================================
CREATE TABLE IF NOT EXISTS engagements (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_number    VARCHAR(20) NOT NULL UNIQUE,
    request_kind        ENUM('facility','care') NOT NULL,
    request_id          BIGINT UNSIGNED NOT NULL,
    professional_id     BIGINT UNSIGNED NULL,
    client_user_id      BIGINT UNSIGNED NULL,
    status              ENUM('shortlisted','offered','accepted','declined',
                             'in_progress','completed','cancelled') NOT NULL DEFAULT 'shortlisted',
    started_at          DATETIME NULL,
    ended_at            DATETIME NULL,
    notes               TEXT NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (professional_id) REFERENCES professionals(id) ON DELETE SET NULL,
    FOREIGN KEY (client_user_id)  REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_eng_request (request_kind, request_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    engagement_id     BIGINT UNSIGNED NOT NULL,
    author_user_id    BIGINT UNSIGNED NOT NULL,
    target_user_id    BIGINT UNSIGNED NOT NULL,
    rating            TINYINT UNSIGNED NOT NULL,
    body              TEXT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (engagement_id) REFERENCES engagements(id) ON DELETE CASCADE,
    FOREIGN KEY (author_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- =========================================================================
-- MESSAGING (lightweight; replace with realtime later)
-- =========================================================================
CREATE TABLE IF NOT EXISTS conversations (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    engagement_id BIGINT UNSIGNED NULL,
    subject       VARCHAR(190) NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (engagement_id) REFERENCES engagements(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS conversation_participants (
    conversation_id BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (conversation_id, user_id),
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_user_id  BIGINT UNSIGNED NOT NULL,
    body            TEXT NOT NULL,
    read_at         DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_user_id)  REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_msg_convo (conversation_id, created_at)
) ENGINE=InnoDB;

-- =========================================================================
-- CONTACT, NEWSLETTER, RATE LIMITING, AUDIT
-- =========================================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(190) NOT NULL,
    email       VARCHAR(190) NOT NULL,
    message     TEXT NOT NULL,
    user_agent  VARCHAR(255) NULL,
    ip          VARCHAR(64)  NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS newsletter_subscriptions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(190) NOT NULL UNIQUE,
    confirmed_at    DATETIME NULL,
    unsubscribed_at DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rate_limits (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip          VARCHAR(64) NOT NULL,
    route       VARCHAR(120) NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rl (ip, route, created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_log (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NULL,
    actor_ip    VARCHAR(64)  NULL,
    action      VARCHAR(120) NOT NULL,
    target_type VARCHAR(80)  NULL,
    target_id   BIGINT UNSIGNED NULL,
    metadata    JSON NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_action (action, created_at)
) ENGINE=InnoDB;

-- =========================================================================
-- CAREERS POSTINGS (simple, admin-managed)
-- =========================================================================
CREATE TABLE IF NOT EXISTS career_postings (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(190) NOT NULL,
    employment  VARCHAR(60)  NOT NULL,
    location    VARCHAR(190) NOT NULL,
    description TEXT NOT NULL,
    posted_at   DATE NOT NULL,
    is_open     TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO career_postings (title, employment, location, description, posted_at)
VALUES
  ('Registered Nurse',     'Full-time', 'Mbarara, Uganda',
   'Seeking a compassionate Registered Nurse to provide patient care at our regional hospital in Mbarara. Must be licensed with the Uganda Nurses and Midwives Council.',
   '2025-05-04'),
  ('Medical Intern',       'Internship','Mulago, Kampala',
   'Internship opportunities for final-year medical students at Mulago National Referral Hospital. Hands-on training under senior clinicians.',
   '2025-05-01'),
  ('Laboratory Technician','Full-time', 'Jinja, Uganda',
   'Join our diagnostics team as a Laboratory Technician. Responsible for specimen collection, testing, and reporting results in accordance with MOH standards.',
   '2025-04-28');
