DROP DATABASE IF EXISTS shop_app;

CREATE DATABASE shop_app;

USE shop_app;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(9) UNIQUE NOT NULL,
    name VARCHAR(45) NOT NULL,
    address VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    price FLOAT NOT NULL,
    category_id INT NOT NULL,

    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE requests  (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_time DATETIME NOT NULL,
    shipped BOOLEAN DEFAULT FALSE,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,

   FOREIGN KEY (customer_id) REFERENCES customers(id),
   FOREIGN KEY (product_id) REFERENCES products(id)
);
