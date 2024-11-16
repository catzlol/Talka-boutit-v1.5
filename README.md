# Talka-boutit Website Configuration Guide

This website will probably not be worked on, as such, it is open sourced.

## Setup Requirements

To establish Talka-boutit on your web server, ensure that you have the following:

- A web host with PHP support
- PHPMyAdmin installed
- MySQL database
- A Functional Braincell

Talka-boutit runs on serv00 by default. XAMPP and WAMP will work. Drag the files to the public_html folder in domains of the file manager. WAMP works, but XAMPP is easier to set up.

1. **Database Configuration:**

   Set up MySQL and PHPMyAdmin, and then download the provided SQL file. Navigate to `SiT_3\config.php` and establish a connection to the database.

   ```php
   $conn = mysqli_connect("localhost", "dbusername", "dbpassword", "dbname");
   ```

2. **User Permissions Editing:**

   Access PHPMyAdmin, locate the "Users" tab, and select the user you wish to modify.

   - Adjust the user's power level to 6 for complete administrative privileges.

Follow these steps meticulously to ensure a seamless setup and efficient management of Talka-boutit on your chosen hosting platform.
