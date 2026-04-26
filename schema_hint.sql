-- Optional schema hint (only run if your /mnt/data/pharmacy.sql does not create these tables)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(150),
  email VARCHAR(150) UNIQUE,
  password_hash VARCHAR(255),
  role VARCHAR(50) DEFAULT 'customer',
  active TINYINT DEFAULT 1,
  created_at DATETIME
);

CREATE TABLE IF NOT EXISTS medicines (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  category VARCHAR(100),
  price DECIMAL(10,2) DEFAULT 0,
  stock INT DEFAULT 0,
  created_at DATETIME
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  total DECIMAL(10,2),
  address TEXT,
  prescription_path VARCHAR(255),
  status VARCHAR(50),
  created_at DATETIME
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  medicine_id INT,
  name VARCHAR(255),
  price DECIMAL(10,2),
  qty INT
);
