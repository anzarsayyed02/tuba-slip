# Tuba Money Transfer System

This is a PHP-based money transfer receipt system that connects to a MySQL database through XAMPP.

## Setup Instructions

1. Install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services from the XAMPP Control Panel
3. Copy all these files to the `htdocs` folder in your XAMPP installation directory
   - Usually located at `C:\xampp\htdocs\tuba` (Windows) or `/Applications/XAMPP/htdocs/tuba` (Mac)
4. Save the logo image as `logo.jpg` in the same directory
5. Open your browser and navigate to `http://localhost/tuba`

The system will automatically:
- Create the database if it doesn't exist
- Create all required tables
- Set up the relationships between tables

## Features

- Customer management (add, search)
- Account management (add multiple accounts per customer)
- Transaction processing
- Receipt generation
- Print receipts
- Export receipts to Excel

## Database Structure

The system uses three main tables:

1. **customers** - Stores customer information
   - id (Primary Key)
   - name
   - mobile
   - created_at

2. **accounts** - Stores account information
   - id (Primary Key)
   - customer_id (Foreign Key)
   - account_number
   - account_holder
   - bank
   - created_at

3. **transactions** - Stores transaction information
   - id (Primary Key)
   - transaction_id
   - customer_id (Foreign Key)
   - account_id (Foreign Key)
   - amount
   - date_time
   - created_at

