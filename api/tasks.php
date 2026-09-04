<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../config/database.php";

function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $stmt = $pdo->query(
        "SELECT id, title, description, budget, posted_by, claimed_by, status, created_at, claimed_at
         FROM tasks
         ORDER BY created_at DESC"
    );

    respond(200, [
        "success" => true,
        "tasks" => $stmt->fetchAll()
    ]);
}

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        respond(400, [
            "success" => false,
            "message" => "Invalid JSON body"
        ]);
    }

    $username = trim($data["username"] ?? "");
    $title = trim($data["title"] ?? "");
    $description = trim($data["description"] ?? "");
    $budget = $data["budget"] ?? null;

    if ($username === "" || $title === "" || $description === "" || $budget === null) {
        respond(400, [
            "success" => false,
            "message" => "Username, title, description and budget are required"
        ]);
    }

    if (!is_numeric($budget) || (float)$budget <= 0) {
        respond(400, [
            "success" => false,
            "message" => "Budget must be a number greater than zero"
        ]);
    }

    if (mb_strlen($username) > 100 || mb_strlen($title) > 150) {
        respond(400, [
            "success" => false,
            "message" => "Username or title is too long"
        ]);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO tasks (title, description, budget, posted_by)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->execute([
        $title,
        $description,
        (float)$budget,
        $username
    ]);

    $taskId = (int)$pdo->lastInsertId();

    $stmt = $pdo->prepare(
        "SELECT id, title, description, budget, posted_by, claimed_by, status, created_at, claimed_at
         FROM tasks
         WHERE id = ?"
    );
    $stmt->execute([$taskId]);

    respond(201, [
        "success" => true,
        "message" => "Task created successfully",
        "task" => $stmt->fetch()
    ]);
}

respond(405, [
    "success" => false,
    "message" => "Method not allowed"
]);
