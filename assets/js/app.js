const usernameInput = document.getElementById("username");
const taskForm = document.getElementById("taskForm");
const taskList = document.getElementById("taskList");
const formMessage = document.getElementById("formMessage");
const listMessage = document.getElementById("listMessage");
const refreshButton = document.getElementById("refreshButton");
const postButton = document.getElementById("postButton");

usernameInput.value = localStorage.getItem("taskMarketplaceUsername") || "";

usernameInput.addEventListener("input", () => {
    localStorage.setItem("taskMarketplaceUsername", usernameInput.value.trim());
});

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function money(value) {
    return Number(value).toLocaleString("en-GH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

async function loadTasks() {
    listMessage.textContent = "Loading tasks...";

    try {
        const response = await fetch("api/tasks.php");
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Unable to load tasks");
        }

        renderTasks(data.tasks);
        listMessage.textContent = data.tasks.length
            ? ""
            : "No tasks have been posted yet.";
    } catch (error) {
        listMessage.textContent = error.message;
    }
}

function renderTasks(tasks) {
    taskList.innerHTML = "";

    tasks.forEach((task) => {
        const card = document.createElement("article");
        card.className = "task-card";

        const isOpen = task.status === "open";

        card.innerHTML = `
            <div class="task-top">
                <span class="status ${isOpen ? "open" : "claimed"}">
                    ${escapeHtml(task.status)}
                </span>
                <span class="budget">GHS ${money(task.budget)}</span>
            </div>

            <h3>${escapeHtml(task.title)}</h3>
            <p class="description">${escapeHtml(task.description)}</p>

            <div class="meta">
                <span>Posted by <strong>${escapeHtml(task.posted_by)}</strong></span>
                ${
                    isOpen
                        ? ""
                        : `<span>Claimed by <strong>${escapeHtml(task.claimed_by || "Unknown")}</strong></span>`
                }
            </div>

            ${
                isOpen
                    ? `<button type="button" class="claim-button" data-id="${task.id}">Claim task</button>`
                    : `<button type="button" disabled>Already claimed</button>`
            }
        `;

        taskList.appendChild(card);
    });

    document.querySelectorAll(".claim-button").forEach((button) => {
        button.addEventListener("click", () => claimTask(Number(button.dataset.id), button));
    });
}

taskForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const username = usernameInput.value.trim();
    const title = document.getElementById("title").value.trim();
    const description = document.getElementById("description").value.trim();
    const budget = document.getElementById("budget").value;

    if (!username) {
        formMessage.textContent = "Enter your username first.";
        usernameInput.focus();
        return;
    }

    postButton.disabled = true;
    formMessage.textContent = "Posting task...";

    try {
        const response = await fetch("api/tasks.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                username,
                title,
                description,
                budget
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Unable to create task");
        }

        taskForm.reset();
        formMessage.textContent = "Task posted successfully.";
        await loadTasks();
    } catch (error) {
        formMessage.textContent = error.message;
    } finally {
        postButton.disabled = false;
    }
});

async function claimTask(taskId, button) {
    const username = usernameInput.value.trim();

    if (!username) {
        listMessage.textContent = "Enter your username before claiming a task.";
        usernameInput.focus();
        return;
    }

    button.disabled = true;
    button.textContent = "Claiming...";
    listMessage.textContent = "";

    try {
        const response = await fetch("api/claim.php", {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                task_id: taskId,
                username
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Unable to claim task");
        }

        listMessage.textContent = "Task claimed successfully.";
        await loadTasks();
    } catch (error) {
        listMessage.textContent = error.message;
        await loadTasks();
    }
}

refreshButton.addEventListener("click", loadTasks);

loadTasks();
