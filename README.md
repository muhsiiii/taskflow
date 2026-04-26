# ⚡ TaskFlow — Project Management SaaS

A full-stack project management application built with **Laravel 13**, **Vue 3**, **Inertia.js**, **Redis**, and **Docker**. Features a real-time Kanban board, role-based access control, async email notifications, and AWS S3 file uploads.

> Built as a portfolio project to demonstrate full-stack SaaS architecture skills.

---

## 🚀 Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Vue 3, Inertia.js, Pinia, Tailwind CSS |
| Database | MySQL 8.0 |
| Cache & Queue | Redis 7 |
| Storage | AWS S3 (local disk for dev) |
| Auth | Laravel Sanctum (token-based) |
| Email | Mailpit (dev), SMTP (prod) |
| Infrastructure | Docker, Nginx, PHP-FPM |
| Version Control | Git Flow |

---

## ✨ Features

### Backend API
- ✅ Token-based authentication (register, login, logout) via Laravel Sanctum
- ✅ Multi-tenant workspace system with role-based access (Admin / Manager / Member)
- ✅ Project CRUD with Laravel API Resources transformation layer
- ✅ Task CRUD with **self-referencing subtasks** (parent_id foreign key)
- ✅ Kanban status moves via dedicated PATCH endpoint
- ✅ File uploads to AWS S3 with **30-minute signed URLs**
- ✅ Async email notifications via **Redis-backed queues**
- ✅ Laravel Policies for fine-grained authorization

### Frontend SPA
- ✅ Dark theme Kanban board with **drag-and-drop** (HTML5 Drag API)
- ✅ Optimistic UI updates — board updates instantly on drop
- ✅ Stats dashboard — total tasks, completion %, overdue count
- ✅ Projects management page with status tracking
- ✅ My Tasks page with filter by status + one-click complete
- ✅ Auth guard — protected routes redirect to login
- ✅ Logout clears history — back button cannot access protected pages
- ✅ Global 401 interceptor — auto logout on expired token

### Infrastructure
- ✅ Fully Dockerized — 6 containers (Nginx, PHP-FPM, MySQL, Redis, Mailpit, phpMyAdmin)
- ✅ Git Flow branching — feature/* → develop → release/v1.0.0 → main
- ✅ Semantic commits throughout

---

## 🗄️ Database Schema

users
workspaces (owner_id → users)
workspace_user (workspace_id, user_id, role) ← pivot
projects (workspace_id, created_by)
tasks (project_id, workspace_id, assignee_id, parent_id ← subtasks)
attachments (task_id, uploaded_by, s3_key)

---

## 🐳 Quick Start (Docker)

### Requirements
- Docker Desktop
- Node.js 18+
- Git

### Setup

```bash
# 1. Clone the repo
git clone https://github.com/muhsiiii/taskflow.git
cd taskflow

# 2. Copy environment file
cp .env.example .env

# 3. Start all containers
docker-compose up -d --build

# 4. Generate app key + run migrations
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed

# 5. Build frontend assets
npm install
npm run build

# 6. Open the app
open http://localhost:8090
```

### Default Ports

| Service | URL |
|---------|-----|
| App | http://localhost:8090 |
| phpMyAdmin | http://localhost:8081 |
| Mailpit (email inbox) | http://localhost:8026 |

---

## 🔑 API Endpoints

### Auth
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout     (🔒 requires token)
GET  /api/auth/me         (🔒 requires token)

### Workspaces
GET    /api/workspaces
POST   /api/workspaces
GET    /api/workspaces/{id}
PUT    /api/workspaces/{id}
DELETE /api/workspaces/{id}
POST   /api/workspaces/{id}/members
DELETE /api/workspaces/{id}/members/{user}

### Projects
GET    /api/workspaces/{ws}/projects
POST   /api/workspaces/{ws}/projects
GET    /api/workspaces/{ws}/projects/{id}
PUT    /api/workspaces/{ws}/projects/{id}
DELETE /api/workspaces/{ws}/projects/{id}

### Tasks
GET    /api/workspaces/{ws}/projects/{pr}/tasks
POST   /api/workspaces/{ws}/projects/{pr}/tasks
GET    /api/workspaces/{ws}/projects/{pr}/tasks/{id}
PUT    /api/workspaces/{ws}/projects/{pr}/tasks/{id}
DELETE /api/workspaces/{ws}/projects/{pr}/tasks/{id}
PATCH  /api/workspaces/{ws}/projects/{pr}/tasks/{id}/move

### Attachments
GET    /api/workspaces/{ws}/projects/{pr}/tasks/{task}/attachments
POST   /api/workspaces/{ws}/projects/{pr}/tasks/{task}/attachments
DELETE /api/workspaces/{ws}/projects/{pr}/tasks/{task}/attachments/{id}

---

## 🌿 Git Flow
main          ← production releases
develop       ← integration branch
release/v1.0.0
feature/docker-setup
feature/migrations-seeders
feature/auth-sanctum
feature/workspace-crud
feature/project-crud
feature/task-crud
feature/s3-uploads
feature/queue-notifications
feature/vue-frontend

---

## 📁 Project Structure
taskflow/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    ← AuthController, WorkspaceController,
│   │   │                          ProjectController, TaskController,
│   │   │                          AttachmentController
│   │   ├── Requests/           ← FormRequest validation classes
│   │   └── Resources/          ← API Resource transformers
│   ├── Models/                 ← Workspace, Project, Task, Attachment, User
│   ├── Policies/               ← WorkspacePolicy (RBAC)
│   ├── Jobs/                   ← SendTaskNotification (Redis queue)
│   ├── Mail/                   ← TaskAssigned Mailable
│   └── Services/               ← S3UploadService
├── resources/js/
│   ├── pages/                  ← Dashboard, Login, Projects, Tasks
│   ├── components/             ← AppLayout
│   ├── composables/            ← useAuth
│   └── stores/                 ← taskStore (Pinia)
├── routes/
│   ├── api.php                 ← All API routes
│   └── web.php                 ← Inertia SPA routes
├── database/migrations/        ← 8 migration files
├── docker-compose.yml
├── Dockerfile
└── nginx.conf

---

## 🔒 Security Features

- Sanctum token authentication — tokens expire and can be revoked
- Route-level auth guard — `onBeforeMount` check on every protected page
- Global Axios 401 interceptor — auto logout on expired token
- `window.location.replace()` on logout — removes protected pages from browser history
- Laravel Policies — only workspace owners/admins can perform destructive actions
- S3 signed URLs — download links expire after 30 minutes

---

## 📧 Queue System

Tasks are assigned emails are sent asynchronously via Redis queues:

```bash
# Start the queue worker
docker-compose exec app php artisan queue:work redis --verbose

# View failed jobs
docker-compose exec app php artisan queue:failed

# Retry failed jobs
docker-compose exec app php artisan queue:retry all
```

---

## 👨‍💻 Author

**Muhsi** — [github.com/muhsiiii](https://github.com/muhsiiii)

Built with Laravel 13 + Vue 3 + Docker · April 2026