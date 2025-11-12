# Cinema Booking Application

A cinema booking application where user can choose movies and schedules and choose free seats for it. After choosing seats, user place an order and gets email with order confirmation and PDF tickets
with QR code. When QR code link is used, ticket is marked as used.

# Getting Started

git clone https://github.com/darmis/test-cinema-app.git
cd test-cinema-app

Start the Docker containers:
docker compose up -d

Wait for Installing dependencies inside container and migrations, seeders and everything being prepared.

Start the queue worker:
docker compose exec backend php artisan queue:work

The application will be available at:
   - Frontend: http://localhost:3000
   - Backend API: http://localhost
   - MailHog UI: http://localhost:8025

# Database Schema

users
├── id (PK)
├── name
├── email (unique)
├── email_verified_at
├── password
├── remember_token
└── timestamps

halls
├── id (PK)
├── name
└── timestamps

seats
├── id (PK)
├── hall_id (FK → halls.id)
├── seat_name
├── row
├── column
├── price (nullable)
└── timestamps

movies
├── id (PK)
├── title
├── description (longText)
└── timestamps

schedules
├── id (PK)
├── movie_id (FK → movies.id)
├── hall_id (FK → halls.id)
├── date
├── start_time
└── timestamps

carts
├── id (PK)
├── schedule_id (FK → schedules.id)
├── total_price (default: 0)
├── user_token (unique)
└── timestamps

cart_items
├── id (PK)
├── cart_id (FK → carts.id)
├── seat_id (FK → seats.id)
├── schedule_id (FK → schedules.id)
└── timestamps

orders
├── id (PK)
├── schedule_id (FK → schedules.id)
├── email
├── total_price
└── timestamps

tickets
├── id (PK)
├── uuid (unique)
├── order_id (FK → orders.id)
├── seat_id (FK → seats.id)
├── schedule_id (FK → schedules.id)
├── ticket_used (boolean, default: false)
└── timestamps