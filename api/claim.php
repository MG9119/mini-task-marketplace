<?php

header("Content-Type: application/json");
require_once __DIR__ . "/../config/database.php";

function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "PATCH") {
    respond(405, [
        "success" => false,
        "message" => "Method not allowed"
    ]);
}

$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    respond(400, [
        "success" => false,
        "message" => "Invalid JSON body"
    ]);
}

$taskId = filter_var($data["task_id"] ?? null, FILTER_VALIDATE_INT);
$username = trim($data["username"] ?? "");

if (!$taskId || $username === "") {
    respond(400, [
        "success" => false,
        "message" => "A valid task ID and username are required"
    ]);
}

if (mb_strlen($username) > 100) {
    respond(400, [
        "success" => false,
        "message" => "Username is too long"
    ]);
}

/*
 * Atomic claim:
 * The task is updated only if it is still open at the moment MySQL executes
 * this statement. This prevents a second claimant from overwriting the first.
 */
$stmt = $pdo->prepare(
    "UPDATE tasks
     SET status = 'claimed',
         claimed_by = ?,
         claimed_at = CURRENT_TIMESTAMP
     WHERE id = ?
       AND status = 'open'"
);

$stmt->execute([$username, $taskId]);

if ($stmt->rowCount() === 1) {
    respond(200, [
        "success" => true,
        "message" => "Task claimed successfully"
    ]);
}

/* Distinguish a missing task from an already-claimed task. */
$check = $pdo->prepare(
    "SELECT id, status, claimed_by
     FROM tasks
     WHERE id = ?"
);
$check->execute([$taskId]);
$task = $check->fetch();

if (!$task) {
    respond(404, [
        "success" => false,
        "message" => "Task not found"
    ]);
}

respond(409, [
    "success" => false,
    "message" => "This task has already been claimed",
    "claimed_by" => $task["claimed_by"]
]);
