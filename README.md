# Mini Task Marketplace

A small full-stack task marketplace built with PHP, MySQL, HTML, CSS and vanilla JavaScript. A user can post a task with a title, description and budget, browse the current task list, and claim an open task using a plain username.

## Stack

- Frontend: HTML, CSS, vanilla JavaScript
- Backend API: PHP 8+
- Database: MySQL / MariaDB
- Local environment: XAMPP

## Features

- Post a task
- View all posted tasks
- Claim an open task
- Persist task data in MySQL
- Basic client-side and server-side validation
- JSON API consumed by the frontend
- Conflict response when an already-claimed task is claimed again

## API

### `GET /api/tasks.php`
Returns all tasks, newest first.

### `POST /api/tasks.php`
Creates a task.

Example body:

```json
{
  "username": "Ama",
  "title": "Design a flyer",
  "description": "Create a simple promotional flyer",
  "budget": 250
}
```

### `PATCH /api/claim.php`
Claims an open task.

Example body:

```json
{
  "task_id": 1,
  "username": "Kojo"
}
```

## Technical decision: preventing two people claiming the same task

I chose to prevent concurrent claims with a conditional SQL `UPDATE` rather than reading the task status first and then performing a separate update. The claim query updates a row only when its current status is still `open`, so MySQL performs the state check and update together as one atomic operation. This avoids a race condition where two users could both read an open task before either write completes, because only the first matching update can affect the row. A transaction with explicit row locking would provide more control for a more complex workflow, but it would add unnecessary complexity for this small application. If claiming later involved payments, multiple related tables, or other multi-step operations, I would move to an explicit transaction and locking strategy.

## Local setup with XAMPP

1. Copy the project folder into:
   `C:\xampp\htdocs\mini-task-marketplace`
2. Start **Apache** and **MySQL** in XAMPP.
3. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
4. Import `database.sql`, or run its SQL manually.
5. Confirm `config/database.php` matches your local MySQL credentials.
6. Open:
   `http://localhost/mini-task-marketplace/`

The default XAMPP configuration used here is:

- Host: `localhost`
- Database: `task_marketplace`
- Username: `root`
- Password: empty

I did not use those default credentials in production.

## Project structure

```text
mini-task-marketplace/
├── api/
│   ├── claim.php
│   └── tasks.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── config/
│   └── database.php
├── database.sql
├── index.php
├── .gitignore
└── README.md
```

## What I would do next

With more time I would add authentication, automated API tests, pagination for a larger task list, stronger database constraints for allowed task states, and deployment configuration for a hosted environment.
