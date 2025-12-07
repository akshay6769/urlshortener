CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    company_id INT NULL,
    role ENUM('superadmin','admin','member') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
);

CREATE TABLE invitations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    role ENUM('admin','member') NOT NULL,
    company_id INT NOT NULL,
    expires_at DATETIME,
    accepted_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE short_urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(64) NOT NULL UNIQUE,
    target_url TEXT NOT NULL,
    company_id INT NOT NULL,
    created_by INT NOT NULL,
    visits_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE short_url_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_url_id INT NOT NULL,
    ip VARCHAR(255),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (short_url_id) REFERENCES short_urls(id)
);
