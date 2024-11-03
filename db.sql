drop database if exists pharmacy_system;
create database pharmacy_system;
use pharmacy_system;
-- Create tables
CREATE TABLE CUSTOMER (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_info VARCHAR(200),
    registration_date DATE
);

CREATE TABLE DOCTOR (
    doctor_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    contact_info VARCHAR(200)
);

CREATE TABLE DRUG_CATEGORY (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(200)
);

CREATE TABLE DRUG (
    drug_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(200),
    dosage_form VARCHAR(50),
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES DRUG_CATEGORY(category_id)
);

CREATE TABLE SUPPLIER (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_info VARCHAR(200),
    payment_terms VARCHAR(100)
);

CREATE TABLE USER (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20),
    last_login DATETIME
);

CREATE TABLE STOCK_ITEM (
    stock_item_id INT PRIMARY KEY AUTO_INCREMENT,
    drug_id INT,
    supplier_id INT,
    quantity INT NOT NULL,
    expiry_date DATE,
    unit_price DECIMAL(10, 2) NOT NULL,
    user_id INT,
    FOREIGN KEY (drug_id) REFERENCES DRUG(drug_id),
    FOREIGN KEY (supplier_id) REFERENCES SUPPLIER(supplier_id),
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

CREATE TABLE PRESCRIPTION (
    prescription_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    doctor_id INT,
    prescription_date DATE,
    status VARCHAR(20),
    user_id INT,
    FOREIGN KEY (customer_id) REFERENCES CUSTOMER(customer_id),
    FOREIGN KEY (doctor_id) REFERENCES DOCTOR(doctor_id),
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

CREATE TABLE PRESCRIPTION_ITEM (
    prescription_item_id INT PRIMARY KEY AUTO_INCREMENT,
    prescription_id INT,
    drug_id INT,
    quantity INT NOT NULL,
    dosage_instructions VARCHAR(200),
    FOREIGN KEY (prescription_id) REFERENCES PRESCRIPTION(prescription_id),
    FOREIGN KEY (drug_id) REFERENCES DRUG(drug_id)
);

CREATE TABLE INVOICE (
    invoice_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_id INT,
    invoice_date DATE,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20),
    user_id INT,
    FOREIGN KEY (supplier_id) REFERENCES SUPPLIER(supplier_id),
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

CREATE TABLE INVOICE_ITEM (
    invoice_item_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT,
    stock_item_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES INVOICE(invoice_id),
    FOREIGN KEY (stock_item_id) REFERENCES STOCK_ITEM(stock_item_id)
);

CREATE TABLE PAYMENT (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT,
    payment_date DATE,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    user_id INT,
    FOREIGN KEY (invoice_id) REFERENCES INVOICE(invoice_id),
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

CREATE TABLE COUNTER_SALE (
    sale_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    sale_date DATE,
    total_amount DECIMAL(10, 2) NOT NULL,
    user_id INT,
    FOREIGN KEY (customer_id) REFERENCES CUSTOMER(customer_id),
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

CREATE TABLE COUNTER_SALE_ITEM (
    sale_item_id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT,
    stock_item_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES COUNTER_SALE(sale_id),
    FOREIGN KEY (stock_item_id) REFERENCES STOCK_ITEM(stock_item_id)
);

CREATE TABLE AUDIT_LOG (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    timestamp DATETIME,
    action VARCHAR(100),
    table_affected VARCHAR(50),
    record_id INT,
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);

-- Insert dummy data
INSERT INTO CUSTOMER (name, contact_info, registration_date) VALUES
('John Doe', 'john@email.com', '2023-01-15'),
('Jane Smith', 'jane@email.com', '2023-02-20');

INSERT INTO DOCTOR (name, specialization, contact_info) VALUES
('Dr. Brown', 'General Practitioner', 'dr.brown@hospital.com'),
('Dr. White', 'Cardiologist', 'dr.white@hospital.com');

INSERT INTO DRUG_CATEGORY (name, description) VALUES
('Antibiotics', 'Medicines that inhibit the growth of or destroy microorganisms'),
('Painkillers', 'Medicines that relieve pain');

INSERT INTO DRUG (name, description, dosage_form, category_id) VALUES
('Amoxicillin', 'Broad-spectrum antibiotic', 'Capsule', 1),
('Ibuprofen', 'Nonsteroidal anti-inflammatory drug', 'Tablet', 2);

INSERT INTO SUPPLIER (name, contact_info, payment_terms) VALUES
('PharmaCorp', 'contact@pharmacorp.com', 'Net 30'),
('MediSupply', 'info@medisupply.com', 'Net 45');

INSERT INTO USER (username, password_hash, role, last_login) VALUES
('admin', 'hashed_password_here', 'Administrator', '2023-05-01 09:00:00'),
('cashier1', 'hashed_password_here', 'Cashier', '2023-05-01 08:30:00');

INSERT INTO STOCK_ITEM (drug_id, supplier_id, quantity, expiry_date, unit_price, user_id) VALUES
(1, 1, 1000, '2024-12-31', 0.50, 1),
(2, 2, 500, '2025-06-30', 0.25, 1);

INSERT INTO PRESCRIPTION (customer_id, doctor_id, prescription_date, status, user_id) VALUES
(1, 1, '2023-05-01', 'Filled', 1),
(2, 2, '2023-05-02', 'Pending', 1);

INSERT INTO PRESCRIPTION_ITEM (prescription_id, drug_id, quantity, dosage_instructions) VALUES
(1, 1, 20, 'Take one capsule three times a day'),
(2, 2, 30, 'Take one tablet as needed for pain');

INSERT INTO INVOICE (supplier_id, invoice_date, total_amount, status, user_id) VALUES
(1, '2023-04-15', 1000.00, 'Paid', 1),
(2, '2023-04-20', 750.00, 'Pending', 1);

INSERT INTO INVOICE_ITEM (invoice_id, stock_item_id, quantity, unit_price) VALUES
(1, 1, 2000, 0.45),
(2, 2, 3000, 0.20);

INSERT INTO PAYMENT (invoice_id, payment_date, amount, payment_method, user_id) VALUES
(1, '2023-05-15', 1000.00, 'Bank Transfer', 1);

INSERT INTO COUNTER_SALE (customer_id, sale_date, total_amount, user_id) VALUES
(1, '2023-05-03', 25.00, 2),
(2, '2023-05-04', 12.50, 2);

INSERT INTO COUNTER_SALE_ITEM (sale_id, stock_item_id, quantity, unit_price) VALUES
(1, 1, 50, 0.50),
(2, 2, 50, 0.25);

INSERT INTO AUDIT_LOG (user_id, timestamp, action, table_affected, record_id) VALUES
(1, '2023-05-01 10:00:00', 'INSERT', 'PRESCRIPTION', 1),
(2, '2023-05-03 14:30:00', 'INSERT', 'COUNTER_SALE', 1);