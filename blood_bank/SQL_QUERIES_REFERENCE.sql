-- ============================================
-- SQL QUERIES REFERENCE
-- Blood Bank Management System
-- ============================================

-- ============================================
-- BASIC SELECT QUERIES
-- ============================================

-- 1. View all donors
SELECT * FROM donor;

-- 2. View all blood stock
SELECT * FROM blood_stock;

-- 3. View all requests
SELECT * FROM request;

-- 4. View all donations
SELECT * FROM donation;

-- 5. View specific blood group donors
SELECT * FROM donor WHERE blood_group = 'O+';

-- ============================================
-- USING VIEWS
-- ============================================

-- 6. View donor status with eligibility
SELECT * FROM donor_status_view;

-- 7. View blood stock status
SELECT * FROM blood_stock_status;

-- 8. View pending requests
SELECT * FROM pending_requests_view;

-- 9. View donation summary by blood group
SELECT * FROM donation_summary_by_blood_group;

-- 10. View recent donations (last 30 days)
SELECT * FROM recent_donations;

-- ============================================
-- USING FUNCTIONS
-- ============================================

-- 11. Get next eligible donation date for donor
SELECT next_eligible_date(1) as next_date;

-- 12. Check if donor is eligible
SELECT is_eligible_to_donate(1) as eligibility;

-- 13. Get total donations by donor
SELECT total_donations(1) as total;

-- 14. Get available units for blood group
SELECT get_available_units('O+') as available;

-- 15. Use function in query
SELECT 
    donor_id,
    name,
    blood_group,
    next_eligible_date(donor_id) as next_eligible,
    is_eligible_to_donate(donor_id) as status
FROM donor;

-- ============================================
-- USING STORED PROCEDURES
-- ============================================

-- 16. Add new donation
CALL add_donation(1, '2024-11-26', 1);

-- 17. Approve blood request
CALL approve_request(3);

-- 18. Reject blood request
CALL reject_request(4);

-- 19. Get donor history
CALL get_donor_history(1);

-- 20. Get stock report
CALL get_stock_report();

-- ============================================
-- INNER JOIN QUERIES
-- ============================================

-- 21. Donors with their donations
SELECT 
    d.donor_id,
    d.name,
    d.blood_group,
    don.donation_date,
    don.units_donated
FROM donor d
INNER JOIN donation don ON d.donor_id = don.donor_id
ORDER BY don.donation_date DESC;

-- 22. Donations with donor details
SELECT 
    don.donation_id,
    don.donation_date,
    don.units_donated,
    d.name,
    d.blood_group,
    d.phone
FROM donation don
INNER JOIN donor d ON don.donor_id = d.donor_id;

-- 23. Requests with available stock
SELECT 
    r.request_id,
    r.patient_name,
    r.blood_group_needed,
    r.units_required,
    bs.units_available,
    r.status
FROM request r
INNER JOIN blood_stock bs ON r.blood_group_needed = bs.blood_group;

-- ============================================
-- LEFT JOIN QUERIES
-- ============================================

-- 24. All donors with their last donation (including those who never donated)
SELECT 
    d.donor_id,
    d.name,
    d.blood_group,
    d.last_donation_date,
    don.units_donated
FROM donor d
LEFT JOIN donation don ON d.donor_id = don.donor_id 
    AND d.last_donation_date = don.donation_date;

-- 25. Donors with donation count (including zero)
SELECT 
    d.donor_id,
    d.name,
    d.blood_group,
    COUNT(don.donation_id) as donation_count
FROM donor d
LEFT JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.donor_id;

-- ============================================
-- RIGHT JOIN QUERIES
-- ============================================

-- 26. All donations with donor info (if donor exists)
SELECT 
    don.donation_id,
    don.donation_date,
    don.units_donated,
    d.name,
    d.blood_group
FROM donor d
RIGHT JOIN donation don ON d.donor_id = don.donor_id;

-- ============================================
-- SUBQUERIES
-- ============================================

-- 27. Donors who donated more than average
SELECT 
    d.name,
    d.blood_group,
    (SELECT COUNT(*) FROM donation WHERE donor_id = d.donor_id) as donation_count
FROM donor d
WHERE (SELECT COUNT(*) FROM donation WHERE donor_id = d.donor_id) > 
      (SELECT AVG(cnt) FROM (SELECT COUNT(*) as cnt FROM donation GROUP BY donor_id) as subq);

-- 28. Blood groups with no pending requests
SELECT blood_group 
FROM blood_stock 
WHERE blood_group NOT IN (
    SELECT blood_group_needed 
    FROM request 
    WHERE status = 'Pending'
);

-- 29. Donors from cities with most donors
SELECT * FROM donor
WHERE city IN (
    SELECT city 
    FROM donor 
    GROUP BY city 
    HAVING COUNT(*) >= 2
);

-- 30. Requests that cannot be fulfilled
SELECT * FROM request
WHERE status = 'Pending' 
AND units_required > (
    SELECT units_available 
    FROM blood_stock 
    WHERE blood_group = request.blood_group_needed
);

-- ============================================
-- AGGREGATION QUERIES
-- ============================================

-- 31. Count donors by blood group
SELECT blood_group, COUNT(*) as donor_count
FROM donor
GROUP BY blood_group
ORDER BY donor_count DESC;

-- 32. Total units by blood group
SELECT blood_group, units_available
FROM blood_stock
ORDER BY units_available DESC;

-- 33. Average age by blood group
SELECT 
    blood_group, 
    AVG(age) as avg_age,
    MIN(age) as min_age,
    MAX(age) as max_age,
    COUNT(*) as donor_count
FROM donor
GROUP BY blood_group;

-- 34. Total donations and units by blood group
SELECT 
    d.blood_group,
    COUNT(don.donation_id) as total_donations,
    SUM(don.units_donated) as total_units
FROM donor d
INNER JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.blood_group
ORDER BY total_units DESC;

-- 35. Request statistics by status
SELECT 
    status,
    COUNT(*) as total_requests,
    SUM(units_required) as total_units_requested
FROM request
GROUP BY status;

-- ============================================
-- HAVING CLAUSE QUERIES
-- ============================================

-- 36. Blood groups with more than 2 donors
SELECT blood_group, COUNT(*) as donor_count
FROM donor
GROUP BY blood_group
HAVING COUNT(*) > 2;

-- 37. Donors with more than 1 donation
SELECT 
    d.donor_id,
    d.name,
    d.blood_group,
    COUNT(don.donation_id) as donation_count
FROM donor d
INNER JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.donor_id
HAVING COUNT(don.donation_id) > 1;

-- 38. Cities with average donor age > 30
SELECT 
    city,
    COUNT(*) as donor_count,
    AVG(age) as avg_age
FROM donor
GROUP BY city
HAVING AVG(age) > 30;

-- ============================================
-- DATE FUNCTIONS
-- ============================================

-- 39. Donations in last 30 days
SELECT * FROM donation
WHERE donation_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
ORDER BY donation_date DESC;

-- 40. Donors who can donate today
SELECT * FROM donor
WHERE last_donation_date IS NULL 
   OR DATEDIFF(CURDATE(), last_donation_date) >= 90;

-- 41. Days since last donation
SELECT 
    donor_id,
    name,
    blood_group,
    last_donation_date,
    DATEDIFF(CURDATE(), last_donation_date) as days_since_donation
FROM donor
WHERE last_donation_date IS NOT NULL
ORDER BY days_since_donation;

-- 42. Monthly donation statistics
SELECT 
    DATE_FORMAT(donation_date, '%Y-%m') as month,
    DATE_FORMAT(donation_date, '%M %Y') as month_name,
    COUNT(*) as total_donations,
    SUM(units_donated) as total_units
FROM donation
GROUP BY month
ORDER BY month DESC;

-- ============================================
-- COMPLEX QUERIES
-- ============================================

-- 43. Top 5 donors by donation count
SELECT 
    d.donor_id,
    d.name,
    d.blood_group,
    d.city,
    COUNT(don.donation_id) as donation_count,
    SUM(don.units_donated) as total_units
FROM donor d
INNER JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.donor_id
ORDER BY donation_count DESC, total_units DESC
LIMIT 5;

-- 44. Blood groups with critical stock and pending requests
SELECT 
    bs.blood_group,
    bs.units_available,
    COUNT(r.request_id) as pending_requests,
    SUM(r.units_required) as units_needed
FROM blood_stock bs
LEFT JOIN request r ON bs.blood_group = r.blood_group_needed 
    AND r.status = 'Pending'
WHERE bs.units_available < 15
GROUP BY bs.blood_group;

-- 45. Donor activity summary
SELECT 
    d.donor_id,
    d.name,
    d.blood_group,
    d.age,
    d.city,
    COUNT(don.donation_id) as total_donations,
    MAX(don.donation_date) as last_donation,
    DATEDIFF(CURDATE(), MAX(don.donation_date)) as days_since_last,
    CASE 
        WHEN MAX(don.donation_date) IS NULL THEN 'Never Donated'
        WHEN DATEDIFF(CURDATE(), MAX(don.donation_date)) >= 90 THEN 'Eligible'
        ELSE 'Not Eligible'
    END as eligibility
FROM donor d
LEFT JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.donor_id;

-- 46. Request fulfillment analysis
SELECT 
    r.blood_group_needed,
    COUNT(*) as total_requests,
    SUM(CASE WHEN r.status = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN r.status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN r.status = 'Pending' THEN 1 ELSE 0 END) as pending,
    bs.units_available as current_stock
FROM request r
INNER JOIN blood_stock bs ON r.blood_group_needed = bs.blood_group
GROUP BY r.blood_group_needed;

-- 47. City-wise donor and donation statistics
SELECT 
    d.city,
    COUNT(DISTINCT d.donor_id) as total_donors,
    COUNT(don.donation_id) as total_donations,
    SUM(don.units_donated) as total_units,
    AVG(d.age) as avg_donor_age
FROM donor d
LEFT JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.city
ORDER BY total_donations DESC;

-- 48. Blood group compatibility analysis
SELECT 
    bs.blood_group,
    bs.units_available,
    COUNT(DISTINCT d.donor_id) as registered_donors,
    COUNT(DISTINCT CASE 
        WHEN DATEDIFF(CURDATE(), d.last_donation_date) >= 90 
        OR d.last_donation_date IS NULL 
        THEN d.donor_id 
    END) as eligible_donors,
    COUNT(r.request_id) as total_requests
FROM blood_stock bs
LEFT JOIN donor d ON bs.blood_group = d.blood_group
LEFT JOIN request r ON bs.blood_group = r.blood_group_needed
GROUP BY bs.blood_group;

-- ============================================
-- UPDATE QUERIES
-- ============================================

-- 49. Update donor information
UPDATE donor 
SET phone = '9999999999', city = 'Mumbai'
WHERE donor_id = 1;

-- 50. Update blood stock manually (not recommended, use triggers)
UPDATE blood_stock 
SET units_available = units_available + 5
WHERE blood_group = 'O+';

-- 51. Update request status
UPDATE request 
SET status = 'Approved'
WHERE request_id = 1;

-- ============================================
-- DELETE QUERIES
-- ============================================

-- 52. Delete a specific request
DELETE FROM request WHERE request_id = 1;

-- 53. Delete donations older than 2 years
DELETE FROM donation 
WHERE donation_date < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);

-- ============================================
-- CASE STATEMENTS
-- ============================================

-- 54. Categorize donors by age
SELECT 
    name,
    age,
    blood_group,
    CASE 
        WHEN age < 25 THEN 'Young'
        WHEN age BETWEEN 25 AND 40 THEN 'Middle-aged'
        ELSE 'Senior'
    END as age_category
FROM donor;

-- 55. Stock status with recommendations
SELECT 
    blood_group,
    units_available,
    CASE 
        WHEN units_available < 10 THEN 'URGENT: Need donors immediately'
        WHEN units_available < 20 THEN 'LOW: Schedule donation camp'
        WHEN units_available < 30 THEN 'MODERATE: Monitor closely'
        ELSE 'GOOD: Stock adequate'
    END as recommendation
FROM blood_stock;

-- ============================================
-- UNION QUERIES
-- ============================================

-- 56. Combine eligible and non-eligible donors
SELECT donor_id, name, blood_group, 'Eligible' as status
FROM donor
WHERE last_donation_date IS NULL 
   OR DATEDIFF(CURDATE(), last_donation_date) >= 90
UNION
SELECT donor_id, name, blood_group, 'Not Eligible' as status
FROM donor
WHERE last_donation_date IS NOT NULL 
  AND DATEDIFF(CURDATE(), last_donation_date) < 90;

-- ============================================
-- WINDOW FUNCTIONS (MySQL 8.0+)
-- ============================================

-- 57. Rank donors by donation count
SELECT 
    d.name,
    d.blood_group,
    COUNT(don.donation_id) as donation_count,
    RANK() OVER (ORDER BY COUNT(don.donation_id) DESC) as donor_rank
FROM donor d
LEFT JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.donor_id;

-- ============================================
-- TRANSACTION EXAMPLES
-- ============================================

-- 58. Transaction for adding donation and updating stock
START TRANSACTION;
INSERT INTO donation (donor_id, donation_date, units_donated) 
VALUES (1, CURDATE(), 1);
UPDATE blood_stock 
SET units_available = units_available + 1 
WHERE blood_group = (SELECT blood_group FROM donor WHERE donor_id = 1);
COMMIT;

-- ============================================
-- USEFUL ADMINISTRATIVE QUERIES
-- ============================================

-- 59. Show all triggers
SHOW TRIGGERS;

-- 60. Show all stored procedures
SHOW PROCEDURE STATUS WHERE Db = 'blood_bank';

-- 61. Show all functions
SHOW FUNCTION STATUS WHERE Db = 'blood_bank';

-- 62. Show all views
SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW';

-- 63. Describe table structure
DESCRIBE donor;

-- 64. Show table creation statement
SHOW CREATE TABLE donor;

-- 65. Show database size
SELECT 
    table_schema as 'Database',
    SUM(data_length + index_length) / 1024 / 1024 as 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'blood_bank'
GROUP BY table_schema;

-- ============================================
-- END OF SQL QUERIES REFERENCE
-- ============================================
