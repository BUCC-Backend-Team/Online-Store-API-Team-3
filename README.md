# Online Store API - Team 3

This repository contains the monolithic backend REST API for an online store. We build it with **Laravel** and **PostgreSQL**.

## Architecture and Technology Stack
*   **Framework:** PHP / Laravel
*   **Database:** PostgreSQL
*   **Authentication:** JWT (JSON Web Tokens)
*   **Authorization:** Role-Based Access Control (RBAC: `CUSTOMER` and `ADMIN`)
*   **Caching and Rate Limiting:** Redis
*   **Logging:** Structured JSON logs with unique request IDs

## Core Modules

### 1. User Module
*   Register and log in users.
*   Hash passwords and generate JSON Web Tokens (JWT).
*   Manage user profiles and roles (`CUSTOMER`, `ADMIN`).

### 2. Product and Inventory Module
*   Create, read, update, and delete products. Only `ADMIN` users can create, update, and delete products.
*   Manage SKUs, prices, and stock.
*   Index the database on the SKU and fields that users search frequently to improve performance.

### 3. Order Module
*   Create and retrieve orders.
*   Decrease inventory when a user creates an order.
*   *Critical:* The system must create orders and update inventory in one ACID database transaction. This prevents oversold stock.

## Team Onboarding

We use Laravel Sail (Docker) for local development. You **do not** need PHP or Composer on your local machine.

Follow these steps to start the project locally:

1. **Clone the code repository:**
   ```bash
   git clone <repo-url>
   cd online-store-api
   ```
2. **Install the Composer dependencies with a temporary Docker container:**
   ```bash
   docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install
   ```
3. **Prepare your environment variables:**
   ```bash
   cp .env.example .env
   ```
4. **Start the Sail containers (PHP, PostgreSQL, Redis):**
   ```bash
   ./vendor/bin/sail up -d
   ```
5. **Generate the application key:**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```
6. **Run the database migrations:**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

## Agent Skills

### ASD-STE100 (Simplified Technical English)
This repository uses the `asd-skill` to enforce Simplified Technical English. Use it for agent communication, error messages, and documentation. Read `.github/workflows/skills/asd-skill/SKILL.md` to learn the rules.
