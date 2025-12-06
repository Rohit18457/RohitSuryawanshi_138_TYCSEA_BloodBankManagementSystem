-- ============================================
-- BLOOD BANK MANAGEMENT SYSTEM - SQL FILE
-- ============================================

-- Drop database if exists and create new
DROP DATABASE IF EXISTS blood_bank;
CREATE DATABASE blood_bank;
USE blood_bank;

-- ============================================
-- TABLE CREATION WITH CONSTRAINTS
-- ============================================

-- Admin Table
CREATE TABLE admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donor Table
CREATE TABLE donor (
    donor_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    city VARCHAR(50) NOT NULL,
    last_donation_date DATE,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_age CHECK (age >= 18 AND age <= 65),
    CONSTRAINT chk_phone CHECK (phone REGEXP '^[0-9]{10,15}$')
);

-- Blood Stock Table
CREATE TABLE blood_stock (
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') PRIMARY KEY,
    units_available INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_units CHECK (units_available >= 0)
);

-- Request Table
CREATE TABLE request (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_name VARCHAR(100) NOT NULL,
    blood_group_needed ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units_required INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    CONSTRAINT chk_units_required CHECK (units_required > 0 AND units_required <= 10)
);

-- Donation Table
CREATE TABLE donation (
    donation_id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT NOT NULL,
    donation_date DATE NOT NULL,
    units_donated INT DEFAULT 1,
    FOREIGN KEY (donor_id) REFERENCES donor(donor_id) ON DELETE CASCADE,
    CONSTRAINT chk_units_donated CHECK (units_donated > 0 AND units_donated <= 2)
);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert Admin Users
INSERT INTO admin (username, password) VALUES
('admin', MD5('admin123')),
('manager', MD5('manager123')),
('supervisor', MD5('super123'));

-- Initialize Blood Stock
INSERT INTO blood_stock (blood_group, units_available) VALUES
('A+', 25),
('A-', 15),
('B+', 30),
('B-', 10),
('AB+', 12),
('AB-', 8),
('O+', 40),
('O-', 18);

-- Insert Donors
INSERT INTO donor (name, age, gender, blood_group, phone, city, last_donation_date) VALUES
('Rajesh Kumar', 28, 'Male', 'O+', '9876543210', 'Mumbai', '2024-09-15'),
('Priya Sharma', 32, 'Female', 'A+', '9876543211', 'Delhi', '2024-10-20'),
('Amit Patel', 25, 'Male', 'B+', '9876543212', 'Ahmedabad', '2024-08-10'),
('Sneha Reddy', 30, 'Female', 'AB+', '9876543213', 'Hyderabad', '2024-11-01'),
('Vikram Singh', 35, 'Male', 'O-', '9876543214', 'Jaipur', '2024-07-25'),
('Anjali Verma', 27, 'Female', 'A-', '9876543215', 'Pune', '2024-09-30'),
('Rahul Gupta', 40, 'Male', 'B-', '9876543216', 'Bangalore', '2024-10-15'),
('Kavita Joshi', 29, 'Female', 'AB-', '9876543217', 'Chennai', '2024-08-20'),
('Suresh Yadav', 33, 'Male', 'O+', '9876543218', 'Kolkata', '2024-11-10'),
('Meera Nair', 26, 'Female', 'A+', '9876543219', 'Kochi', '2024-10-05'),
('Arjun Mehta', 31, 'Male', 'B+', '9876543220', 'Surat', NULL),
('Pooja Desai', 24, 'Female', 'O-', '9876543221', 'Nagpur', NULL),
('Karan Malhotra', 38, 'Male', 'A-', '9876543222', 'Lucknow', '2024-09-12'),
('Divya Iyer', 28, 'Female', 'AB+', '9876543223', 'Coimbatore', '2024-10-28'),
('Rohit Kapoor', 34, 'Male', 'B-', '9876543224', 'Chandigarh', '2024-08-05');

-- Insert Donations
INSERT INTO donation (donor_id, donation_date, units_donated) VALUES
(1, '2024-09-15', 1),
(2, '2024-10-20', 1),
(3, '2024-08-10', 1),
(4, '2024-11-01', 1),
(5, '2024-07-25', 1),
(6, '2024-09-30', 1),
(7, '2024-10-15', 1),
(8, '2024-08-20', 1),
(9, '2024-11-10', 1),
(10, '2024-10-05', 1),
(13, '2024-09-12', 1),
(14, '2024-10-28', 1),
(15, '2024-08-05', 1),
(1, '2024-06-10', 1),
(2, '2024-07-15', 1);

-- Insert Blood Requests
INSERT INTO request (patient_name, blood_group_needed, units_required, doctor_name, status) VALUES
('Ramesh Tiwari', 'O+', 2, 'Dr. Anil Kapoor', 'Approved'),
('Sunita Devi', 'A+', 1, 'Dr. Priya Menon', 'Approved'),
('Manoj Kumar', 'B+', 3, 'Dr. Suresh Reddy', 'Pending'),
('Geeta Rani', 'AB-', 1, 'Dr. Kavita Shah', 'Rejected'),
('Vijay Sharma', 'O-', 2, 'Dr. Rajesh Gupta', 'Approved'),
('Lakshmi Bai', 'A-', 1, 'Dr. Meera Joshi', 'Pending'),
('Harish Chandra', 'B-', 2, 'Dr. Amit Verma', 'Approved'),
('Radha Krishna', 'AB+', 1, 'Dr. Sneha Patel', 'Pending'),
('Mohan Lal', 'O+', 1, 'Dr. Vikram Singh', 'Approved'),
('Sita Devi', 'A+', 2, 'Dr. Anjali Nair', 'Pending');

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger 1: Update blood stock after donation
DELIMITER //
CREATE TRIGGER after_donation_insert
AFTER INSERT ON donation
FOR EACH ROW
BEGIN
    DECLARE donor_blood_group VARCHAR(5);
    
    SELECT blood_group INTO donor_blood_group
    FROM donor
    WHERE donor_id = NEW.donor_id;
    
    UPDATE blood_stock
    SET units_available = units_available + NEW.units_donated
    WHERE blood_group = donor_blood_group;
    
    UPDATE donor
    SET last_donation_date = NEW.donation_date
    WHERE donor_id = NEW.donor_id;
END//
DELIMITER ;

-- Trigger 2: Update blood stock when request is approved
DELIMITER //
CREATE TRIGGER after_request_approve
AFTER UPDATE ON request
FOR EACH ROW
BEGIN
    IF NEW.status = 'Approved' AND OLD.status != 'Approved' THEN
        UPDATE blood_stock
        SET units_available = units_available - NEW.units_required
        WHERE blood_group = NEW.blood_group_needed;
    END IF;
END//
DELIMITER ;

-- Trigger 3: Prevent donation if donor donated within 90 days
DELIMITER //
CREATE TRIGGER before_donation_check
BEFORE INSERT ON donation
FOR EACH ROW
BEGIN
    DECLARE last_date DATE;
    DECLARE days_diff INT;
    
    SELECT last_donation_date INTO last_date
    FROM donor
    WHERE donor_id = NEW.donor_id;
    
    IF last_date IS NOT NULL THEN
        SET days_diff = DATEDIFF(NEW.donation_date, last_date);
        IF days_diff < 90 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Donor must wait 90 days between donations';
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger 4: Prevent request approval if insufficient stock
DELIMITER //
CREATE TRIGGER before_request_update
BEFORE UPDATE ON request
FOR EACH ROW
BEGIN
    DECLARE available_units INT;
    
    IF NEW.status = 'Approved' AND OLD.status != 'Approved' THEN
        SELECT units_available INTO available_units
        FROM blood_stock
        WHERE blood_group = NEW.blood_group_needed;
        
        IF available_units < NEW.units_required THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Insufficient blood stock available';
        END IF;
    END IF;
END//
DELIMITER ;

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure 1: Add new donation
DELIMITER //
CREATE PROCEDURE add_donation(
    IN p_donor_id INT,
    IN p_donation_date DATE,
    IN p_units_donated INT
)
BEGIN
    INSERT INTO donation (donor_id, donation_date, units_donated)
    VALUES (p_donor_id, p_donation_date, p_units_donated);
    
    SELECT 'Donation added successfully' AS message;
END//
DELIMITER ;

-- Procedure 2: Approve blood request
DELIMITER //
CREATE PROCEDURE approve_request(IN p_request_id INT)
BEGIN
    DECLARE v_blood_group VARCHAR(5);
    DECLARE v_units_required INT;
    DECLARE v_available_units INT;
    
    SELECT blood_group_needed, units_required INTO v_blood_group, v_units_required
    FROM request
    WHERE request_id = p_request_id;
    
    SELECT units_available INTO v_available_units
    FROM blood_stock
    WHERE blood_group = v_blood_group;
    
    IF v_available_units >= v_units_required THEN
        UPDATE request
        SET status = 'Approved'
        WHERE request_id = p_request_id;
        
        SELECT 'Request approved successfully' AS message;
    ELSE
        SELECT 'Insufficient blood stock' AS message;
    END IF;
END//
DELIMITER ;

-- Procedure 3: Reject blood request
DELIMITER //
CREATE PROCEDURE reject_request(IN p_request_id INT)
BEGIN
    UPDATE request
    SET status = 'Rejected'
    WHERE request_id = p_request_id;
    
    SELECT 'Request rejected' AS message;
END//
DELIMITER ;

-- Procedure 4: Get donor donation history
DELIMITER //
CREATE PROCEDURE get_donor_history(IN p_donor_id INT)
BEGIN
    SELECT 
        d.donation_id,
        d.donation_date,
        d.units_donated,
        don.name,
        don.blood_group
    FROM donation d
    INNER JOIN donor don ON d.donor_id = don.donor_id
    WHERE d.donor_id = p_donor_id
    ORDER BY d.donation_date DESC;
END//
DELIMITER ;

-- Procedure 5: Get blood stock report
DELIMITER //
CREATE PROCEDURE get_stock_report()
BEGIN
    SELECT 
        blood_group,
        units_available,
        CASE 
            WHEN units_available < 10 THEN 'Critical'
            WHEN units_available < 20 THEN 'Low'
            ELSE 'Adequate'
        END AS stock_status,
        last_updated
    FROM blood_stock
    ORDER BY units_available ASC;
END//
DELIMITER ;

-- ============================================
-- FUNCTIONS
-- ============================================

-- Function 1: Calculate next eligible donation date
DELIMITER //
CREATE FUNCTION next_eligible_date(p_donor_id INT)
RETURNS DATE
DETERMINISTIC
BEGIN
    DECLARE last_date DATE;
    DECLARE next_date DATE;
    
    SELECT last_donation_date INTO last_date
    FROM donor
    WHERE donor_id = p_donor_id;
    
    IF last_date IS NULL THEN
        RETURN CURDATE();
    ELSE
        SET next_date = DATE_ADD(last_date, INTERVAL 90 DAY);
        RETURN next_date;
    END IF;
END//
DELIMITER ;

-- Function 2: Check if donor is eligible to donate
DELIMITER //
CREATE FUNCTION is_eligible_to_donate(p_donor_id INT)
RETURNS VARCHAR(10)
DETERMINISTIC
BEGIN
    DECLARE last_date DATE;
    DECLARE days_diff INT;
    
    SELECT last_donation_date INTO last_date
    FROM donor
    WHERE donor_id = p_donor_id;
    
    IF last_date IS NULL THEN
        RETURN 'Eligible';
    END IF;
    
    SET days_diff = DATEDIFF(CURDATE(), last_date);
    
    IF days_diff >= 90 THEN
        RETURN 'Eligible';
    ELSE
        RETURN 'Not Eligible';
    END IF;
END//
DELIMITER ;

-- Function 3: Get total donations by donor
DELIMITER //
CREATE FUNCTION total_donations(p_donor_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE total INT;
    
    SELECT COUNT(*) INTO total
    FROM donation
    WHERE donor_id = p_donor_id;
    
    RETURN total;
END//
DELIMITER ;

-- Function 4: Get available units for blood group
DELIMITER //
CREATE FUNCTION get_available_units(p_blood_group VARCHAR(5))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE units INT;
    
    SELECT units_available INTO units
    FROM blood_stock
    WHERE blood_group = p_blood_group;
    
    RETURN IFNULL(units, 0);
END//
DELIMITER ;

-- ============================================
-- VIEWS
-- ============================================

-- View 1: Donor status with eligibility
CREATE VIEW donor_status_view AS
SELECT 
    d.donor_id,
    d.name,
    d.age,
    d.gender,
    d.blood_group,
    d.phone,
    d.city,
    d.last_donation_date,
    next_eligible_date(d.donor_id) AS next_eligible_date,
    is_eligible_to_donate(d.donor_id) AS eligibility_status,
    total_donations(d.donor_id) AS total_donations
FROM donor d;

-- View 2: Blood stock status
CREATE VIEW blood_stock_status AS
SELECT 
    blood_group,
    units_available,
    CASE 
        WHEN units_available < 10 THEN 'Critical'
        WHEN units_available < 20 THEN 'Low'
        WHEN units_available < 30 THEN 'Moderate'
        ELSE 'Good'
    END AS stock_level,
    last_updated
FROM blood_stock
ORDER BY units_available ASC;

-- View 3: Pending requests
CREATE VIEW pending_requests_view AS
SELECT 
    r.request_id,
    r.patient_name,
    r.blood_group_needed,
    r.units_required,
    r.doctor_name,
    r.request_date,
    bs.units_available,
    CASE 
        WHEN bs.units_available >= r.units_required THEN 'Can Fulfill'
        ELSE 'Insufficient Stock'
    END AS fulfillment_status
FROM request r
INNER JOIN blood_stock bs ON r.blood_group_needed = bs.blood_group
WHERE r.status = 'Pending';

-- View 4: Donation summary by blood group
CREATE VIEW donation_summary_by_blood_group AS
SELECT 
    don.blood_group,
    COUNT(d.donation_id) AS total_donations,
    SUM(d.units_donated) AS total_units_donated,
    COUNT(DISTINCT d.donor_id) AS unique_donors
FROM donation d
INNER JOIN donor don ON d.donor_id = don.donor_id
GROUP BY don.blood_group
ORDER BY total_units_donated DESC;

-- View 5: Recent donations (last 30 days)
CREATE VIEW recent_donations AS
SELECT 
    d.donation_id,
    don.name AS donor_name,
    don.blood_group,
    don.phone,
    d.donation_date,
    d.units_donated
FROM donation d
INNER JOIN donor don ON d.donor_id = don.donor_id
WHERE d.donation_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
ORDER BY d.donation_date DESC;

-- ============================================
-- COMPLEX QUERIES
-- ============================================

-- Query 1: Donors who can donate now (eligible)
-- SELECT * FROM donor_status_view WHERE eligibility_status = 'Eligible';

-- Query 2: Blood groups with critical stock
-- SELECT * FROM blood_stock_status WHERE stock_level = 'Critical';

-- Query 3: Top 5 donors by donation count
-- SELECT name, blood_group, total_donations FROM donor_status_view ORDER BY total_donations DESC LIMIT 5;

-- Query 4: Total requests by status
-- SELECT status, COUNT(*) as total_requests FROM request GROUP BY status;

-- Query 5: Donors with their last donation details using JOIN
-- SELECT d.name, d.blood_group, d.phone, don.donation_date, don.units_donated
-- FROM donor d
-- LEFT JOIN donation don ON d.donor_id = don.donor_id AND d.last_donation_date = don.donation_date;

-- Query 6: Blood groups with no pending requests
-- SELECT blood_group FROM blood_stock 
-- WHERE blood_group NOT IN (SELECT blood_group_needed FROM request WHERE status = 'Pending');

-- Query 7: Average age of donors by blood group
-- SELECT blood_group, AVG(age) as avg_age, COUNT(*) as donor_count 
-- FROM donor GROUP BY blood_group HAVING donor_count > 1;

-- Query 8: Donors who donated more than average
-- SELECT d.name, d.blood_group, COUNT(don.donation_id) as donation_count
-- FROM donor d
-- INNER JOIN donation don ON d.donor_id = don.donor_id
-- GROUP BY d.donor_id
-- HAVING donation_count > (SELECT AVG(cnt) FROM (SELECT COUNT(*) as cnt FROM donation GROUP BY donor_id) as subq);

-- Query 9: Monthly donation statistics
-- SELECT 
--     DATE_FORMAT(donation_date, '%Y-%m') as month,
--     COUNT(*) as total_donations,
--     SUM(units_donated) as total_units
-- FROM donation
-- GROUP BY month
-- ORDER BY month DESC;

-- Query 10: Requests that cannot be fulfilled
-- SELECT r.*, bs.units_available
-- FROM request r
-- INNER JOIN blood_stock bs ON r.blood_group_needed = bs.blood_group
-- WHERE r.status = 'Pending' AND bs.units_available < r.units_required;

-- ============================================
-- END OF SQL FILE
-- ============================================
