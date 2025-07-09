
# Chess Tournament Management System by Nursena Taşköprü and Hakan Çiftçi

This is a basic PHP-based web application designed to help manage a chess tournament. It allows user registration, login, profile management, and viewing of tournament details. It also includes separate sections for admins and moderators.

## 🔧 Features

- User registration and login system  
- Profile viewing and editing  
- Tournament details listing  
- Admin and moderator dashboard  
- Basic page styling with custom CSS  

## 💻 Technologies Used

- PHP  
- HTML & CSS  
- MySQL (database connection configuration required)  

## 🚀 Getting Started

1. Clone the repository:


2. Configure your database connection in `config.php`:
```php
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';
```

3. Run the project on a local server (e.g., XAMPP, WAMP).

## 👥 Contributors

- **Nursena Taşköprü**
- **Hakan Çiftçi** 



```
/admin             → Admin-specific files  
/moderator         → Moderator-specific files  
/images            → User profile images  
config.php         → Database connection settings (do not share sensitive info)  
index.php          → Main entry point  
login.php / register.php / logout.php  
profile.php        → User profile page  
edit_profile.php   → Profile edit page  
tournament_details.php  
*.css              → Styling for each page  
```
