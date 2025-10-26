# Circus Booking M-Commerce Web Application

## Project Overview
This is a Full Stack M-Commerce Web Application for booking circus shows.  
It is built using **PHP** (backend), **MySQL** (database), and deployed on **AWS EC2**.  
The project demonstrates a complete integration of Frontend, Backend, Database, and Cloud deployment.

---

## Features
- User registration and login
- Browse and book shows
- Admin panel to manage shows and bookings
- Responsive design for desktop and mobile
- Secure connection to MySQL database
- Mobile commerce concepts applied: ticket booking, payments simulation

---

## Project Structure

---

## Database
- **Database Name:** `circus_booking`
- **Tables:**
  - **users:** `user_id, name, email, password, phone`
  - **shows:** `show_id, show_name, image, show_date, seats_available, ticket_price, caption`
  - **bookings:** `booking_id, user_id, show_id, tickets_booked, total_price, booking_date`
- **MySQL Password (for EC2 deployment):** `4x(tomIoBUhV)`
- **Sample Data:** Included in `circus_booking.sql`

---
## Setup Instructions

Copy project folder to C:/xampp/htdocs/

Import circus_booking.sql into MySQL using phpMyAdmin or CLI

Update config.php:

$host = "localhost";
$user = "root";
$pass = ""; // blank for local XAMPP
$dbname = "circus_booking";
Run the site in browser: http://13.235.33.182/

3. AWS EC2 Deployment

Launch Amazon Linux 2023 EC2 instance
Open ports 22 (SSH) and 80 (HTTP) in Security Group
Connect via SSH:ssh -i "Circus_Mania_Key.pem" ec2-user@13.235.33.182
Install Apache and PHP:sudo dnf install httpd php php-mysqli -y
sudo systemctl start httpd
sudo systemctl enable httpd
Install Apache and PHP:sudo dnf install httpd php php-mysqli -y
sudo systemctl start httpd
sudo systemctl enable httpd
Upload project files to EC2:scp -i "Circus_Mania_Key.pem" -r C:/xampp/htdocs/circus_booking/* ec2-user@<EC2_PUBLIC_IP>:/home/ec2-user/circus_booking/
Move files to web root and set permissions:sudo rm -rf /var/www/html/*
sudo mv /home/ec2-user/circus_booking/* /var/www/html/
sudo chown -R apache:apache /var/www/html
sudo chmod -R 755 /var/www/html
sudo systemctl restart httpd
Import SQL on EC2 MySQL:mysql -u root -p circus_booking < /var/www/html/circus_booking.sql
Enter password: 4x(tomIoBUhV
Update config.php on EC2:

$host = "localhost";
$user = "root";
$pass = "4x(tomIoBUhV";
$dbname = "circus_booking";

Access the Live Site

Live Site on AWS EC2

git clone https://github.com/YourUsername/circus_booking.git
cd circus_booking
