# Duplicate Handling

## Duplicate Definition

In this application, an event is considered a duplicate when the same `event_id` is received more than once.

The reason for choosing `event_id` as the duplicate identifier is that the event ID is provided by the external event source and represents a single business event.

Example:

First request:

```json
{
    "id": "evt_001",
    "account_id": "account_001",
    "amount": 500,
    "occurred_at": "2026-07-19 10:00:00"
}
```

If the same event ID (`evt_001`) is received again, it will be treated as a duplicate.


## Duplicate Handling Logic

When an event is received:

1. The system checks whether the `event_id` already exists in the database.

2. If the event exists:
   - The event is not processed again.
   - Account balance is not updated.
   - A response is returned indicating that the event was already processed.

3. If the event does not exist:
   - The event is stored.
   - The account balance is updated.


## Handling Conflicting Duplicate Data

If the same `event_id` is received with different data, it is still considered a duplicate.

Example:

Existing stored event:

```json
{
    "id": "evt_001",
    "account_id": "account_001",
    "amount": 500
}
```

New incoming event:

```json
{
    "id": "evt_001",
    "account_id": "account_001",
    "amount": 1000
}
```

The new event will not overwrite the existing event.

The original stored event remains the source of truth.


## Reasoning

Using `event_id` as the unique identifier provides idempotent event processing.

This approach ensures:

- The same event cannot affect the account balance multiple times.
- Retried or replayed events do not create incorrect balances.
- Duplicate batch submissions produce the same final result.

A unique database constraint on `event_id` provides an additional layer of protection against duplicate records.

# Account Event Processor

## Project Overview

Account Event Processor is a Laravel-based web application that processes account events and maintains account balances reliably. The system accepts event submissions, prevents duplicate processing, updates account balances, and provides APIs and a responsive web interface to view account balances and event history.

It is designed to handle duplicate events, concurrent requests, and application restarts while ensuring data consistency and correctness.

# Technology Stack

- Backend: PHP Laravel 12
- Frontend: Laravel Blade + Bootstrap
- Database: MySQL
- Deployment: Railway

# Architecture

The application follows Laravel MVC architecture.

## Application Flow

Client Request

↓

Laravel Routes

↓

Controllers

↓

Eloquent Models

↓

MySQL Database


## Main Components

### EventController

Responsible for:

- Receiving account events
- Validating incoming requests
- Checking duplicate events
- Updating account balances
- Storing event history


### AccountController

Responsible for:

- Retrieving account balance
- Retrieving account event history


### Models

The application contains two main models:

### Account

Stores:

- Account ID
- Current balance

### Event

Stores:

- Event ID
- Account ID
- Transaction amount
- Event occurrence time



# Database Design

## accounts table

Stores current account information.
Fields:

- id
- account_id
- balance
- timestamps


## events table

Stores processed events.

Fields:

- id
- event_id
- account_id
- amount
- occurred_at
- timestamps

# Important Technical Decisions

## Balance Calculation Approach

The current balance is stored in the accounts table.

When a new event is processed:

- Positive amount increases the balance.
- Negative amount decreases the balance.


This avoids recalculating the balance from all events every time.

## Database Transaction

Event processing uses database transactions.

This ensures:

- Event creation and balance update happen together.
- Failed operations do not leave partial data.

## Concurrent Request Handling

Multiple events can affect the same account at the same time.

To handle this, row locking is implemented using:

lockForUpdate()


This prevents incorrect balance updates during concurrent requests.


## Validation

Laravel Form Request validation is used.

Validation rules:

- Event ID is required.
- Account ID is required.
- Amount must be numeric.
- Occurred date must be valid.

# Assumptions

The following assumptions were made:

1. Event ID is provided by the event source and uniquely identifies an event.

2. The same event should not update account balance more than once.

3. Events may arrive out of chronological order.

4. Positive amount means money added.

5. Negative amount means money deducted.

6. If an event arrives for a new account, the account is created automatically.

7. Database records are considered the source of truth.


# Duplicate-Handling Approach

## Duplicate Definition

An event is considered duplicate when the same `event_id` is received again.


Example:
First request:
event_id = evt_001
amount = 500

Second request:
event_id = evt_001
amount = 500

The second request will not be processed again.

## Duplicate Processing

When an event arrives:

1. System checks existing event ID.
2. If event exists:
   - Balance is not updated.
   - New event record is not created.
   - Existing event response is returned.

3. If event does not exist:
   - Event is stored.
   - Balance is updated.

## Conflicting Duplicate Data

If the same event ID arrives with different information:

Example:

Existing:
event_id = evt_001
amount = 500

New:
event_id = evt_001
amount = 1000



The new request is rejected.

The original stored event remains the source of truth.


Reason:

Using event ID as unique identity ensures idempotent processing and prevents incorrect balance changes.



# Deployment Approach


The application is deployed using Railway.


Deployment process:

1. Code is stored in GitHub repository.
2. Railway connects with repository.
3. Application is deployed automatically.
4. Environment variables are configured separately.


## Security

Secrets are not committed to repository.

Examples:

- APP_KEY
- Database credentials
- Passwords


Only `.env.example` is included.

Actual `.env` file remains private.


## Data Persistence

Database is stored separately from application deployment.

Therefore data remains available after:

- Application restart
- New deployment


# Known Limitations

- No authentication system.
- No queue based processing.
- No API rate limiting.
- No advanced monitoring system.
- Event history pagination is not implemented.

# Improvements With Additional Time

Future improvements:

- Add authentication and authorization.
- Add Laravel queue workers.
- Add automated API and feature tests.
- Add Swagger API documentation.
- Add monitoring and logging.
- Add pagination for large event history.
- Improve scalability for high volume event processing.

# Instructions for Running the Project Locally

## Prerequisites

Make sure the following are installed:

- PHP 8.2 or later
- Composer
- MySQL
- Node.js and npm
- Git

## Steps

### 1. Clone the repository

```bash
git clone https://github.com/harshlala01/account-event-processor.git
cd account-event-processor
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Create the environment file

Copy the example environment file:

```bash
cp .env.example .env
```

(For Windows Command Prompt)

```cmd
copy .env.example .env
```

### 5. Generate the application key

```bash
php artisan key:generate
```

### 6. Configure the database

Update the following values in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 7. Run database migrations

```bash
php artisan migrate
```

### 8. Start the development server

```bash
php artisan serve
```

The application will be available at:

```
http://127.0.0.1:8000
```


# Laravel Features and Design Choices

The application uses Laravel features where they improve code quality, reliability, and maintainability. The following Laravel features were implemented:

## Implemented Features

- **Form Request Validation**
  - Used to validate incoming event requests.
  - Keeps validation logic separate from controllers.

- **Eloquent ORM**
  - Used for database operations on the `accounts` and `events` tables.
  - Improves readability and maintainability.

- **Database Transactions**
  - Event processing is wrapped in a database transaction.
  - Ensures that balance updates and event creation are committed together or rolled back on failure.

- **Database Row Locking (`lockForUpdate`)**
  - Prevents race conditions when multiple requests update the same account concurrently.

- **Blade**
  - Used to build the responsive web interface for searching accounts, viewing balances, viewing event history, and submitting events.

---

## Features Intentionally Not Implemented

The following Laravel features were intentionally not used because they were not required for the assignment:

- **Authentication**
  - No user authentication or authorization was implemented since the application only exposes public assignment endpoints.

- **Queues**
  - Event processing is synchronous. Queue workers were not introduced to keep the solution simple for the assignment scope.

- **Events & Listeners**
  - Business logic is straightforward and does not require event broadcasting or asynchronous listeners.

- **Repository / Service Pattern**
  - The business logic is relatively small, so keeping it in controllers with Eloquent models keeps the project simple and easy to understand.

- **Caching**
  - Not used because account balances must always reflect the latest processed events.


 # API Route Structure

POST
/api/events

Used for submitting account events.

GET
/api/accounts/{account_id}/balance

Used for retrieving current account balance.

GET
/api/accounts/{account_id}/events

Used for retrieving account event history.

# Request and Response Format

## Event Submit Request

Example:

{
    "id": "evt_123",
    "account_id": "account_456",
    "amount": 250.50,
    "occurred_at": "2026-07-16T10:30:00Z"
}


## Successful Response

{
    "message": "Event processed successfully",
    "data": {}
}


## Duplicate Response

{
    "message": "Event already processed",
    "data": {}
}

# Error Handling Strategy

The application uses Laravel validation and exception handling.

Validation errors return proper HTTP status codes.

Examples:

- 201 - Event successfully processed
- 200 - Duplicate event detected
- 404 - Account not found
- 422 - Validation failed
- 500 - Internal server error

Database operations are wrapped inside transactions to prevent inconsistent data.


# Live URLs

## Live Application URL

https://account-event-processor-production.up.railway.app


## Live API Endpoints

### Submit Event (POST)

https://account-event-processor-production.up.railway.app/api/events


### Get Account Balance (GET)

https://account-event-processor-production.up.railway.app/api/accounts/{account_id}/balance


### Get Account Events (GET)

https://account-event-processor-production.up.railway.app/api/accounts/{account_id}/events
