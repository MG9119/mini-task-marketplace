CREATE DATABASE IF NOT EXISTS task_marketplace
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE task_marketplace;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    budget DECIMAL(10,2) NOT NULL,
    posted_by VARCHAR(100) NOT NULL,
    claimed_by VARCHAR(100) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    claimed_at DATETIME NULL
);

-- Optional sample data:
-- INSERT INTO tasks (title, description, budget, posted_by)
-- VALUES
-- ('Design a Logo', 'Create a simple logo for a restaurant.', 300.00, 'Ama'),
-- ('Build a Website', 'Develop a small responsive business website.', 800.00, 'Kojo');
