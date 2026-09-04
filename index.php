<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Task Marketplace</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Simple PHP + MySQL Demo</p>
                <h1>Mini Task Marketplace</h1>
                <p>Post a small task, browse available work, and claim a task.</p>
            </div>

            <label class="identity">
                Your username
                <input
                    type="text"
                    id="username"
                    maxlength="100"
                    placeholder="e.g. Ama"
                    autocomplete="name"
                >
            </label>
        </header>

        <section class="panel">
            <h2>Post a task</h2>

            <form id="taskForm">
                <div class="field">
                    <label for="title">Title</label>
                    <input
                        type="text"
                        id="title"
                        maxlength="150"
                        placeholder="e.g. Design a logo"
                        required
                    >
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        rows="4"
                        placeholder="Describe the task briefly"
                        required
                    ></textarea>
                </div>

                <div class="field">
                    <label for="budget">Budget (GHS)</label>
                    <input
                        type="number"
                        id="budget"
                        min="0.01"
                        step="0.01"
                        placeholder="300.00"
                        required
                    >
                </div>

                <button type="submit" id="postButton">Post task</button>
            </form>

            <p id="formMessage" class="message" aria-live="polite"></p>
        </section>

        <section class="tasks-section">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Marketplace</p>
                    <h2>Posted tasks</h2>
                </div>
                <button type="button" class="secondary" id="refreshButton">Refresh</button>
            </div>

            <p id="listMessage" class="message" aria-live="polite"></p>
            <div id="taskList" class="task-grid"></div>
        </section>
    </main>

    <script src="assets/js/app.js"></script>
</body>
</html>
