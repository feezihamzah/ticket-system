# Ticket Reservation System

A simple ticket booking system built with Laravel.

---

## Setup Instructions

1. Clone the repository:
   git clone https://github.com/your-username/ticket-system.git
   cd ticket-system

2. Install dependencies:
   composer install

3. Setup environment file:
   cp .env.example .env
   php artisan key:generate

4. Configure database in `.env`:
   DB_DATABASE=ticket_system
   DB_USERNAME=root
   DB_PASSWORD=

5. Run migrations and seeders:
   php artisan migrate --seed

6. Start the server:
   php artisan serve

7. Open in browser:
   http://127.0.0.1:8000

---

## How to Use

1. Select an event
2. Choose available seats
3. Click Reserve
4. Click Confirm Booking
5. View booking summary

---

## Notes

- Seats are temporarily reserved with expiry time
- Users must confirm before expiration
- Page will reset after booking

---

## Tech Stack

- Laravel
- Blade
- JavaScript
- CSS
