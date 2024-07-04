<?php

require 'vendor/autoload.php';

use App\ChatGPT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$promptTemplate = ' 
Given the following SQL schema:
CREATE TABLE products (
    product_id INT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL,
    description TEXT,
    image_url VARCHAR(255)
);

CREATE TABLE customers (
    customer_id INT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    phone_number VARCHAR(20)
);

CREATE TABLE orders (
    order_id INT PRIMARY KEY,
    customer_id INT,
    order_date DATETIME,
    total_amount DECIMAL(10, 2),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    unit_price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

Generate SQL for the following question:
{prompt}

';

function generatePrompt($prompt, $promptTemplate)
{
    return str_replace("{prompt}", $prompt, $promptTemplate);
}

$prompt = $_POST['query'];
$modifiedPrompt = generatePrompt($prompt, $promptTemplate);

$client = new ChatGPT();
$reply = $client->ask($modifiedPrompt);

echo $reply;