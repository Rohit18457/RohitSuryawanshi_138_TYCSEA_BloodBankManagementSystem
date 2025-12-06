# Blood Bank Management System

A complete DBMS Mini Project built with PHP and MySQL, featuring comprehensive SQL operations including triggers, stored procedures, functions, and views.

## Features

### SQL Features Implemented
- **Tables**: 5 tables with proper constraints (PK, FK, UNIQUE, CHECK)
- **Triggers**: 4 triggers for automatic stock updates and validation
- **Stored Procedures**: 5 procedures for common operations
- **Functions**: 4 custom functions for calculations
- **Views**: 5 views for complex queries
- **Joins**: INNER JOIN, LEFT JOIN, RIGHT JOIN
- **Subqueries**: Nested queries and correlated subqueries
- **Aggregations**: COUNT, SUM, AVG, GROUP BY, HAVING

### Application Features
- Donor registration and management
- Blood stock tracking with real-time updates
- Blood request system
- Admin dashboard with comprehensive reports
- Donation history tracking
- Eligibility checking (90-day rule)
- Automatic stock updates via triggers

## Database Schema

### Tables

1. **admin** - Admin user management
   - admin_id (PK)
   - username (UNIQUE)
   - password
   - created_at

2. **donor** - Donor information
   - donor_id (PK)
   - name, age, gender, blood_group
   - phone (UNIQUE), city
   - last_donation_date
   - registration_date

3. **blood_stock** - Blood inventory
   - blood_group (PK)
   - units_available
   - last_updated

4. **request** - Blood requests
   - request_id (PK)
   - patient_name, blood_group_needed
   - units_required, doctor_name
   - request_date, status

5. **donation** - Donation records
   - donation_id (PK)
   - donor_id (FK)
   - donation_date, units_donated

## Installation Steps

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Web browser

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP to default location (C:\xampp)
3. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Setup Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click on "Import" tab
3. Choose file: `blood_bank.sql`
4. Click "Go" to import
   - This will create the database, tables, sample data, triggers, procedures, functions, and views

### Step 3: Setup Project Files
1. Copy the entire project folder to: `C:\xampp\htdocs\blood_bank`
2. Ensure the folder structure is:
   ```
   C:\xampp\htdocs\blood_bank\
   ├── config/
   │   └── db.php
   ├── admin/
   │   ├── login.php
   │   ├── dashboard.php
   │   ├── manage_requests.php
   │   ├── add_donation.php
   │   ├── donor_history.php
   │   ├── reports.php
   │   └── logout.php
   ├── css/
   │   └── style.css
   ├── index.php
   ├── donor_registration.php
   ├── blood_request.php
   ├── blood_stock.php
   ├── donor_list.php
   ├── blood_bank.sql
   └── README.md
   ```

### Step 4: Access the Application
1. Open browser and go to: http://localhost/blood_bank
2. You should see the home page

## Default Login Credentials

### Admin Access
- **URL**: http://localhost/blood_bank/admin/login.php
- **Username**: admin
- **Password**: admin123

Alternative accounts:
- Username: manager, Password: manager123
- Username: supervisor, Password: super123

## Usage Guide

### For Public Users

1. **Register as Donor**
   - Navigate to "Register as Donor"
   - Fill in personal details
   - Age must be 18-65 years
   - Phone number must be unique

2. **Request Blood**
   - Navigate to "Request Blood"
   - Fill in patient details and blood group needed
   - Check available stock before requesting
   - Maximum 10 units per request

3. **View Blood Stock**
   - Navigate to "Blood Stock"
   - View current availability
   - See donation statistics

4. **View Donor List**
   - Navigate to "Donor List"
   - Filter by blood group
   - See eligibility status

### For Admin Users

1. **Dashboard**
   - View overall statistics
   - Monitor blood stock levels
   - See recent donations
   - Check pending requests

2. **Manage Requests**
   - Approve or reject blood requests
   - System automatically checks stock availability
   - Triggers update stock on approval

3. **Add Donation**
   - Select eligible donor
   - System checks 90-day eligibility rule
   - Automatically updates stock via trigger
   - Updates donor's last donation date

4. **Donor History**
   - View complete donation history
   - See donor eligibility status
   - Track donation patterns

5. **Reports**
   - Blood stock report with status
   - Donation summary by blood group
   - Request status summary
   - Top donors ranking
   - Monthly statistics
   - City-wise distribution

## SQL Features Demonstration

### Triggers
1. **after_donation_insert** - Updates blood stock when donation is added
2. **after_request_approve** - Deducts stock when request is approved
3. **before_donation_check** - Validates 90-day rule before donation
4. **before_request_update** - Checks stock availability before approval

### Stored Procedures
1. **add_donation(donor_id, date, units)** - Add new donation
2. **approve_request(request_id)** - Approve blood request
3. **reject_request(request_id)** - Reject blood request
4. **get_donor_history(donor_id)** - Get donation history
5. **get_stock_report()** - Generate stock report

### Functions
1. **next_eligible_date(donor_id)** - Calculate next donation date
2. **is_eligible_to_donate(donor_id)** - Check eligibility
3. **total_donations(donor_id)** - Count total donations
4. **get_available_units(blood_group)** - Get stock for blood group

### Views
1. **donor_status_view** - Donor info with eligibility
2. **blood_stock_status** - Stock with status levels
3. **pending_requests_view** - Pending requests with fulfillment status
4. **donation_summary_by_blood_group** - Donation statistics
5. **recent_donations** - Last 30 days donations

### Complex Queries Examples

```sql
-- Donors who can donate now
SELECT * FROM donor_status_view WHERE eligibility_status = 'Eligible';

-- Blood groups with critical stock
SELECT * FROM blood_stock_status WHERE stock_level = 'Critical';

-- Top 5 donors
SELECT name, blood_group, total_donations 
FROM donor_status_view 
ORDER BY total_donations DESC LIMIT 5;

-- Requests by status
SELECT status, COUNT(*) as total 
FROM request 
GROUP BY status;

-- Average age by blood group
SELECT blood_group, AVG(age) as avg_age 
FROM donor 
GROUP BY blood_group 
HAVING COUNT(*) > 1;

-- Donors with above average donations
SELECT d.name, COUNT(don.donation_id) as count
FROM donor d
INNER JOIN donation don ON d.donor_id = don.donor_id
GROUP BY d.donor_id
HAVING count > (SELECT AVG(cnt) FROM (SELECT COUNT(*) as cnt FROM donation GROUP BY donor_id) as subq);
```

## Testing the System

### Test Scenario 1: Add Donation
1. Login as admin
2. Go to "Add Donation"
3. Select an eligible donor
4. Add donation
5. Check blood stock - it should increase automatically (trigger)
6. Check donor list - last donation date should update

### Test Scenario 2: Approve Request
1. Go to "Manage Requests"
2. Approve a pending request
3. Check blood stock - it should decrease automatically (trigger)
4. Try to approve request with insufficient stock - should fail

### Test Scenario 3: Eligibility Check
1. Try to add donation for a donor who donated recently
2. System should prevent if less than 90 days (trigger)
3. Check donor list to see eligibility status

## Troubleshooting

### Database Connection Error
- Check if MySQL is running in XAMPP
- Verify database name is "blood_bank"
- Check credentials in config/db.php

### Triggers Not Working
- Ensure you imported the complete SQL file
- Check if triggers exist: `SHOW TRIGGERS;`
- Verify delimiter was properly set during import

### Stored Procedures Not Found
- Check if procedures exist: `SHOW PROCEDURE STATUS WHERE Db = 'blood_bank';`
- Re-import the SQL file if needed

### Page Not Loading
- Ensure Apache is running
- Check if files are in correct location
- Verify file permissions

## Project Structure

```
blood_bank/
├── config/
│   └── db.php              # Database connection
├── admin/
│   ├── login.php           # Admin login
│   ├── dashboard.php       # Admin dashboard
│   ├── manage_requests.php # Request management
│   ├── add_donation.php    # Add donations
│   ├── donor_history.php   # View donor history
│   ├── reports.php         # Comprehensive reports
│   └── logout.php          # Logout
├── css/
│   └── style.css           # Styling
├── index.php               # Home page
├── donor_registration.php  # Donor registration
├── blood_request.php       # Blood request form
├── blood_stock.php         # Stock display
├── donor_list.php          # Donor listing
├── blood_bank.sql          # Complete SQL file
└── README.md               # This file
```

## Technologies Used
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (XAMPP)

## Key Highlights
- Production-ready code
- Comprehensive SQL features
- Real-time stock updates via triggers
- Automatic eligibility checking
- Responsive design
- Secure admin panel
- Complete error handling
- Sample data included

## Future Enhancements
- Email notifications
- SMS alerts for critical stock
- Donor appointment scheduling
- Blood camp management
- Mobile app integration
- Advanced analytics dashboard

## License
This is an educational project for DBMS coursework.

## Support
For issues or questions, check the troubleshooting section or review the SQL file for query examples.
