# 📘 School Management System — Full Technical Documentation

> **Version:** 1.0.0  
> **Author:** Elias Abdurrahman (original) — Documentation by AI Assistant  
> **Stack:** PHP 8.x · MySQL/MariaDB · Bootstrap 5.2 · jQuery 3.6 · Font Awesome 4.7  
> **License:** Open Source (Educational)

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [System Requirements](#2-system-requirements)
3. [Architecture Overview](#3-architecture-overview)
4. [Directory Structure](#4-directory-structure)
5. [Database Schema](#5-database-schema)
6. [Authentication & Authorization](#6-authentication--authorization)
7. [Module Documentation](#7-module-documentation)
   - [7.1 Public Module](#71-public-module)
   - [7.2 Admin Module](#72-admin-module)
   - [7.3 Teacher Module](#73-teacher-module)
   - [7.4 Student Module](#74-student-module)
   - [7.5 Registrar Office Module](#75-registrar-office-module)
8. [Data Access Layer](#8-data-access-layer)
9. [Frontend & UI](#9-frontend--ui)
10. [Security Considerations](#10-security-considerations)
11. [Known Issues & Bugs](#11-known-issues--bugs)
12. [Local Development Setup](#12-local-development-setup)
13. [Deployment Guide — Hostinger](#13-deployment-guide--hostinger)
14. [Post-Deployment Checklist](#14-post-deployment-checklist)
15. [Troubleshooting](#15-troubleshooting)

---

## 1. Project Overview

The **School Management System (SMS)** is a web-based application designed to manage school operations including student enrollment, teacher management, grade tracking, course management, and inter-role communication. It features four distinct user roles, each with a dedicated dashboard and set of permissions.

### Key Features

| Feature | Description |
|---------|-------------|
| **Multi-Role Login** | Admin, Teacher, Student, and Registrar Office roles |
| **Student Management** | Full CRUD — enroll, edit, search, view, delete students |
| **Teacher Management** | Full CRUD — add, edit, search, view, delete teachers |
| **Grade & Score Tracking** | Teachers input scores per subject; students view their grades |
| **Class Organization** | Manage grades, sections, and class assignments |
| **Course/Subject Management** | Create and assign subjects per grade level |
| **Contact Messages** | Public contact form stored in database |
| **School Settings** | Dynamic school name, slogan, about text, year, and semester |
| **Registrar Enrollment** | Registrar staff can independently enroll students |
| **Password Management** | Teachers and students can change their passwords |

---

## 2. System Requirements

### Server Requirements

| Requirement | Minimum Version |
|-------------|----------------|
| **PHP** | 8.0+ (tested on 8.1.12) |
| **MySQL / MariaDB** | 10.4+ (tested on MariaDB 10.4.27) |
| **Web Server** | Apache 2.4+ with `mod_rewrite` |
| **PHP Extensions** | `pdo`, `pdo_mysql`, `mbstring`, `openssl` |

### Client Requirements

| Requirement | Details |
|-------------|---------|
| **Browser** | Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ |
| **JavaScript** | Required (jQuery dependency) |
| **Screen** | Responsive — works on desktop and mobile |

### External CDN Dependencies

| Library | Version | CDN URL |
|---------|---------|---------|
| Bootstrap CSS | 5.2.0 | `cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css` |
| Bootstrap JS | 5.2.0 | `cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js` |
| jQuery | 3.6.0 | `ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js` |
| Font Awesome | 4.7.0 | `cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css` |
| Google Fonts | Lobster | `fonts.googleapis.com/css2?family=Lobster` |

---

## 3. Architecture Overview

### Architecture Pattern

The application follows a **procedural PHP architecture** without any MVC framework. Each module is self-contained with its own:
- **Pages** (`.php` files in module root) — contain both presentation and logic
- **Data Layer** (`data/` subdirectory) — reusable database query functions
- **Request Handlers** (`req/` subdirectory) — process form submissions (POST)
- **Includes** (`inc/` subdirectory) — shared UI components (navbar)

### Request Flow

```
User Browser
    │
    ▼
┌─────────────────┐
│   index.php     │◄── Public homepage
│   login.php     │◄── Role-based login
└────────┬────────┘
         │ POST to req/login.php
         ▼
┌─────────────────┐
│  req/login.php  │ ── Validates credentials
│                 │ ── Sets $_SESSION
│                 │ ── Redirects to role dashboard
└────────┬────────┘
         │
    ┌────┴────┬──────────┬──────────────┐
    ▼         ▼          ▼              ▼
 admin/    Teacher/   Student/    RegistrarOffice/
 index.php index.php  index.php   index.php
    │         │          │              │
    ▼         ▼          ▼              ▼
 [CRUD      [Grade     [View         [Enroll
  Pages]     Entry]     Grades]       Students]
    │         │          │              │
    ▼         ▼          ▼              ▼
 req/*.php  req/*.php  req/*.php     req/*.php
 (handlers) (handlers) (handlers)   (handlers)
    │         │          │              │
    ▼         ▼          ▼              ▼
┌─────────────────────────────────────────┐
│           DB_connection.php             │
│        PDO → MySQL (sms_db)             │
└─────────────────────────────────────────┘
```

### Session Management

- Sessions are started with `session_start()` at the top of every protected page
- Session variables stored:
  - `$_SESSION['role']` — `'Admin'`, `'Teacher'`, `'Student'`, or `'Registrar Office'`
  - `$_SESSION['admin_id']` / `$_SESSION['teacher_id']` / `$_SESSION['student_id']` / `$_SESSION['r_user_id']`
- Logout destroys the session via `session_unset()` + `session_destroy()`
- Every protected page checks both `role` and corresponding `*_id` session variables

---

## 4. Directory Structure

```
school-management-system-php/
│
├── index.php                          # Public homepage (school info + contact form)
├── login.php                          # Unified login page (role selector)
├── logout.php                         # Session destroy → redirect to login
├── DB_connection.php                  # PDO database connection configuration
├── sms_db.sql                         # MySQL database dump (schema + sample data)
├── logo.png                           # School logo (284 KB)
│
├── css/
│   └── style.css                      # Custom stylesheet (85 lines)
│
├── img/                               # Static image assets
│   ├── bg.jpg                         # Homepage background (536 KB)
│   ├── student-Male.png               # Male student avatar
│   ├── student-Female.png             # Female student avatar
│   ├── teacher-Male.png               # Male teacher avatar
│   ├── teacher-Female.png             # Female teacher avatar
│   ├── registrar-office-Male.jpg      # Male registrar avatar
│   └── registrar-office-Female.jpg    # Female registrar avatar
│
├── data/
│   └── setting.php                    # getSetting() — shared by public pages
│
├── req/                               # Public request handlers
│   ├── login.php                      # Authentication logic (POST handler)
│   └── contact.php                    # Contact form submission handler
│
├── admin/                             # ══════ ADMIN MODULE ══════
│   ├── index.php                      # Admin dashboard
│   ├── teacher.php                    # List all teachers
│   ├── teacher-add.php                # Add new teacher form
│   ├── teacher-edit.php               # Edit teacher form
│   ├── teacher-delete.php             # Delete teacher handler
│   ├── teacher-search.php             # Search teachers
│   ├── teacher-view.php               # View teacher details
│   ├── student.php                    # List all students
│   ├── student-add.php                # Add new student form
│   ├── student-edit.php               # Edit student form
│   ├── student-delete.php             # Delete student handler
│   ├── student-search.php             # Search students
│   ├── student-view.php               # View student details
│   ├── registrar-office.php           # List registrar staff
│   ├── registrar-office-add.php       # Add registrar staff
│   ├── registrar-office-edit.php      # Edit registrar staff
│   ├── registrar-office-delete.php    # Delete registrar staff
│   ├── registrar-office-view.php      # View registrar details
│   ├── class.php                      # List classes
│   ├── class-add.php                  # Create class
│   ├── class-edit.php                 # Edit class
│   ├── class-delete.php               # Delete class
│   ├── grade.php                      # List grades
│   ├── grade-add.php                  # Create grade
│   ├── grade-edit.php                 # Edit grade
│   ├── grade-delete.php               # Delete grade
│   ├── section.php                    # List sections
│   ├── section-add.php                # Create section
│   ├── section-edit.php               # Edit section
│   ├── section-delete.php             # Delete section
│   ├── course.php                     # List courses/subjects
│   ├── course-add.php                 # Create course
│   ├── course-edit.php                # Edit course
│   ├── course-delete.php              # Delete course
│   ├── message.php                    # View contact messages
│   ├── settings.php                   # Edit school settings
│   ├── data/                          # Data access layer (10 files)
│   │   ├── admin.php                  #   getAdminById()
│   │   ├── student.php                #   getAllStudents(), getStudentById(), etc.
│   │   ├── teacher.php                #   getAllTeachers(), getTeacherById(), etc.
│   │   ├── registrar_office.php       #   getAllRegistrar(), etc.
│   │   ├── class.php                  #   getAllClasses(), etc.
│   │   ├── grade.php                  #   getAllGrades(), getGradeById()
│   │   ├── section.php                #   getAllSections(), getSectionById()
│   │   ├── subject.php                #   getAllSubjects(), etc.
│   │   ├── message.php                #   getAllMessages()
│   │   └── setting.php                #   getSetting()
│   ├── inc/
│   │   └── navbar.php                 # Admin navigation bar
│   └── req/                           # Request handlers (18 files)
│       ├── student-add.php            #   Handle student creation
│       ├── student-edit.php           #   Handle student update
│       ├── student-change.php         #   Handle student password change
│       ├── teacher-add.php            #   Handle teacher creation
│       ├── teacher-edit.php           #   Handle teacher update
│       ├── teacher-change.php         #   Handle teacher password change
│       ├── registrar-office-add.php   #   Handle registrar creation
│       ├── registrar-office-edit.php  #   Handle registrar update
│       ├── registrar-office-change.php#   Handle registrar password change
│       ├── class-add.php              #   Handle class creation
│       ├── class-edit.php             #   Handle class update
│       ├── grade-add.php              #   Handle grade creation
│       ├── grade-edit.php             #   Handle grade update
│       ├── section-add.php            #   Handle section creation
│       ├── section-edit.php           #   Handle section update
│       ├── course-add.php             #   Handle course creation
│       ├── course-edit.php            #   Handle course update
│       └── setting-edit.php           #   Handle settings update
│
├── Teacher/                           # ══════ TEACHER MODULE ══════
│   ├── index.php                      # Teacher dashboard
│   ├── classes.php                    # View assigned classes
│   ├── students.php                   # View all students
│   ├── students_of_class.php          # Students filtered by class
│   ├── student-grade.php              # Grade entry (score input per subject)
│   ├── pass.php                       # Change password
│   ├── data/                          # Data access layer
│   ├── inc/navbar.php                 # Teacher navigation bar
│   └── req/                           # Request handlers
│
├── Student/                           # ══════ STUDENT MODULE ══════
│   ├── index.php                      # Student dashboard
│   ├── grade.php                      # View personal grades
│   ├── pass.php                       # Change password
│   ├── data/                          # Data access layer
│   ├── inc/navbar.php                 # Student navigation bar
│   └── req/                           # Request handlers
│
└── RegistrarOffice/                   # ══════ REGISTRAR MODULE ══════
    ├── index.php                      # Registrar dashboard
    ├── student.php                    # List students
    ├── student-add.php                # Enroll new student
    ├── student-search.php             # Search students
    ├── student-view.php               # View student details
    ├── data/                          # Data access layer
    └── req/                           # Request handlers
```

**File Count Summary:**
| Component | Files | Directories |
|-----------|-------|-------------|
| Root | 7 | 7 |
| Admin Module | 36 | 3 |
| Teacher Module | 6 | 3 |
| Student Module | 3 | 3 |
| Registrar Module | 5 | 2 |
| Assets (CSS + Images) | 8 | 2 |
| **Total** | **~70+** | **~20** |

---

## 5. Database Schema

### Database Name: `sms_db`

### 5.1 Entity-Relationship Overview

The database consists of **11 tables** organized into three categories:

**User Tables:** `admin`, `teachers`, `students`, `registrar_office`  
**Academic Tables:** `grades`, `section`, `class`, `subjects`, `student_score`  
**System Tables:** `setting`, `message`

### 5.2 Table Definitions

#### `admin` — System administrators

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `admin_id` | INT(11) | PK, AUTO_INCREMENT | Unique admin identifier |
| `username` | VARCHAR(127) | UNIQUE, NOT NULL | Login username |
| `password` | VARCHAR(255) | NOT NULL | bcrypt hashed password |
| `fname` | VARCHAR(127) | NOT NULL | First name |
| `lname` | VARCHAR(127) | NOT NULL | Last name |

**Sample Data:** Username `elias`, password is bcrypt-hashed.

---

#### `teachers` — Teaching staff

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `teacher_id` | INT(11) | PK, AUTO_INCREMENT | Unique teacher identifier |
| `username` | VARCHAR(127) | UNIQUE, NOT NULL | Login username |
| `password` | VARCHAR(255) | NOT NULL | bcrypt hashed password |
| `class` | VARCHAR(31) | NOT NULL | Assigned class IDs (concatenated string) |
| `fname` | VARCHAR(127) | NOT NULL | First name |
| `lname` | VARCHAR(127) | NOT NULL | Last name |
| `subjects` | VARCHAR(31) | NOT NULL | Assigned subject IDs (concatenated string) |
| `address` | VARCHAR(31) | NOT NULL | Address |
| `employee_number` | INT(11) | NOT NULL | Employee ID number |
| `date_of_birth` | DATE | DEFAULT NULL | Date of birth |
| `phone_number` | VARCHAR(31) | NOT NULL | Phone number |
| `qualification` | VARCHAR(127) | NOT NULL | Academic qualification |
| `gender` | VARCHAR(7) | NOT NULL | `'Male'` or `'Female'` |
| `email_address` | VARCHAR(255) | NOT NULL | Email address |
| `date_of_joined` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Registration date |

> ⚠️ **Design Note:** `class` and `subjects` columns store concatenated IDs (e.g., `"1234"`, `"1245"`) parsed with `str_split()`. This breaks for IDs ≥ 10.

---

#### `students` — Enrolled students

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `student_id` | INT(11) | PK, AUTO_INCREMENT | Unique student identifier |
| `username` | VARCHAR(127) | UNIQUE, NOT NULL | Login username |
| `password` | VARCHAR(255) | NOT NULL | bcrypt hashed password |
| `fname` | VARCHAR(127) | NOT NULL | First name |
| `lname` | VARCHAR(255) | NOT NULL | Last name |
| `grade` | INT(11) | NOT NULL, FK→grades | Grade level ID |
| `section` | INT(11) | NOT NULL, FK→section | Section ID |
| `address` | VARCHAR(31) | NOT NULL | Address |
| `gender` | VARCHAR(7) | NOT NULL | `'Male'` or `'Female'` |
| `email_address` | VARCHAR(255) | NOT NULL | Email address |
| `date_of_birth` | DATE | NOT NULL | Date of birth |
| `date_of_joined` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Enrollment date |
| `parent_fname` | VARCHAR(127) | NOT NULL | Parent/Guardian first name |
| `parent_lname` | VARCHAR(127) | NOT NULL | Parent/Guardian last name |
| `parent_phone_number` | VARCHAR(31) | NOT NULL | Parent/Guardian phone |

---

#### `registrar_office` — Registrar staff

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `r_user_id` | INT(11) | PK, AUTO_INCREMENT | Unique registrar identifier |
| `username` | VARCHAR(127) | NOT NULL | Login username |
| `password` | VARCHAR(255) | NOT NULL | bcrypt hashed password |
| `fname` | VARCHAR(31) | NOT NULL | First name |
| `lname` | VARCHAR(31) | NOT NULL | Last name |
| `address` | VARCHAR(31) | NOT NULL | Address |
| `employee_number` | INT(11) | NOT NULL | Employee ID |
| `date_of_birth` | DATE | NOT NULL | Date of birth |
| `phone_number` | VARCHAR(31) | NOT NULL | Phone number |
| `qualification` | VARCHAR(31) | NOT NULL | Qualification |
| `gender` | VARCHAR(7) | NOT NULL | `'Male'` or `'Female'` |
| `email_address` | VARCHAR(255) | NOT NULL | Email |
| `date_of_joined` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Join date |

---

#### `grades` — Academic grade levels

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `grade_id` | INT(11) | PK, AUTO_INCREMENT | Unique grade identifier |
| `grade` | VARCHAR(31) | NOT NULL | Grade number (e.g., `'1'`, `'2'`) |
| `grade_code` | VARCHAR(7) | NOT NULL | Grade type code (e.g., `'G'`, `'KG'`) |

**Display Format:** `{grade_code}-{grade}` → e.g., `KG-1`, `G-3`

---

#### `section` — Class sections

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `section_id` | INT(11) | PK, AUTO_INCREMENT | Unique section identifier |
| `section` | VARCHAR(7) | NOT NULL | Section letter (e.g., `'A'`, `'B'`, `'C'`) |

---

#### `class` — Grade-section combinations

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `class_id` | INT(11) | PK, AUTO_INCREMENT | Unique class identifier |
| `grade` | INT(11) | NOT NULL, FK→grades | Grade ID |
| `section` | INT(11) | NOT NULL, FK→section | Section ID |

**Example:** Class `{grade_id=1, section_id=2}` → Grade G-1, Section B

---

#### `subjects` — Courses/subjects

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `subject_id` | INT(11) | PK, AUTO_INCREMENT | Unique subject identifier |
| `subject` | VARCHAR(31) | NOT NULL | Full subject name |
| `subject_code` | VARCHAR(31) | NOT NULL | Short code (e.g., `'En'`, `'Math-01'`) |
| `grade` | INT(11) | NOT NULL, FK→grades | Grade level this subject belongs to |

---

#### `student_score` — Student grade records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT(11) | PK, AUTO_INCREMENT | Unique score record ID |
| `semester` | VARCHAR(100) | NOT NULL | Semester (e.g., `'I'`, `'II'`) |
| `year` | INT(11) | NOT NULL | Academic year |
| `student_id` | INT(11) | NOT NULL, FK→students | Student ID |
| `teacher_id` | INT(11) | NOT NULL, FK→teachers | Teacher who graded |
| `subject_id` | INT(11) | NOT NULL, FK→subjects | Subject ID |
| `results` | VARCHAR(512) | NOT NULL | Comma-separated scores |

> ⚠️ **Design Note:** The `results` column stores scores in format `"score1 outof1,score2 outof2,..."` — e.g., `"10 15,15 20,10 10"`. This is denormalized; a proper design would use a separate `score_entries` table.

---

#### `message` — Contact form messages

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `message_id` | INT(11) | PK, AUTO_INCREMENT | Unique message ID |
| `sender_full_name` | VARCHAR(100) | NOT NULL | Sender's name |
| `sender_email` | VARCHAR(255) | NOT NULL | Sender's email |
| `message` | TEXT | NOT NULL | Message body |
| `date_time` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Timestamp |

---

#### `setting` — School configuration

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INT(11) | PK, AUTO_INCREMENT | Setting record ID |
| `current_year` | INT(11) | NOT NULL | Current academic year |
| `current_semester` | VARCHAR(11) | NOT NULL | Current semester |
| `school_name` | VARCHAR(100) | NOT NULL | School name |
| `slogan` | VARCHAR(300) | NOT NULL | School slogan |
| `about` | TEXT | NOT NULL | About text |

> **Note:** Only one row exists in this table. The application fetches `rowCount() == 1`.

---

## 6. Authentication & Authorization

### 6.1 Login Process

1. User visits `login.php` and selects role from dropdown (Admin/Teacher/Student/Registrar Office)
2. Form POSTs to `req/login.php` with `uname`, `pass`, `role`
3. Backend maps role to database table:
   - `1` → `admin` table
   - `2` → `teachers` table
   - `3` → `students` table
   - `4` → `registrar_office` table
4. Uses **prepared statement** to find user by username
5. Verifies password using `password_verify()` against bcrypt hash
6. On success: sets `$_SESSION['role']` and role-specific ID, redirects to dashboard
7. On failure: redirects back to `login.php?error=Incorrect Username or Password`

### 6.2 Password Storage

- All passwords hashed with `password_hash($pass, PASSWORD_DEFAULT)` (bcrypt)
- Hash format: `$2y$10$...` (60 characters)
- Verified with `password_verify($input, $stored_hash)`

### 6.3 Page Protection Pattern

Every protected page follows this pattern:

```php
<?php 
session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        // ── Page content here ──
    } else {
        header("Location: ../login.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>
```

### 6.4 Logout

```php
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
```

---

## 7. Module Documentation

### 7.1 Public Module

**Files:** `index.php`, `login.php`, `logout.php`

| Page | URL | Description |
|------|-----|-------------|
| Homepage | `/index.php` | Displays school name, slogan, about section, contact form |
| Login | `/login.php` | Role-selector login form |
| Logout | `/logout.php` | Destroys session, redirects to login |

**Homepage Sections:**
- Navigation bar (Home, About, Contact, Login)
- Welcome hero with school logo and name
- About Us card
- Contact form (email, name, message)
- Copyright footer

---

### 7.2 Admin Module

**Path:** `/admin/`  
**Access:** `$_SESSION['role'] == 'Admin'`

#### Dashboard (`admin/index.php`)

Grid of icon buttons linking to all management areas:
- Teachers, Students, Registrar Office, Class, Section, Grade, Course, Message, Settings, Logout

#### CRUD Operations

Each entity (Teacher, Student, Registrar, Class, Grade, Section, Course) follows the same pattern:

| Action | Page File | Handler File | HTTP Method |
|--------|-----------|-------------|-------------|
| **List** | `{entity}.php` | — | GET |
| **Add** | `{entity}-add.php` | `req/{entity}-add.php` | POST |
| **Edit** | `{entity}-edit.php` | `req/{entity}-edit.php` | POST |
| **Delete** | `{entity}-delete.php` | `req/{entity}-delete.php` (via GET `?id=`) | GET |
| **Search** | `{entity}-search.php` | — | POST |
| **View** | `{entity}-view.php` | — | GET |

#### Settings Page (`admin/settings.php`)

Editable fields:
- School Name
- Slogan
- About text
- Current Year
- Current Semester

#### Messages Page (`admin/message.php`)

Displays all contact form submissions in a table.

---

### 7.3 Teacher Module

**Path:** `/Teacher/`  
**Access:** `$_SESSION['role'] == 'Teacher'`

| Page | Description |
|------|-------------|
| `index.php` | Dashboard showing teacher profile info |
| `classes.php` | List of classes assigned to this teacher |
| `students.php` | List of all students |
| `students_of_class.php` | Students filtered by selected class |
| `student-grade.php` | **Core feature** — enter scores for a student per subject |
| `pass.php` | Change own password |

#### Grading System

The grade entry page (`student-grade.php`):
1. Shows student info (name, grade, section)
2. Dropdown to select subject (filtered to teacher's assigned subjects)
3. Up to 5 score input fields per subject (score / out-of format)
4. Scores saved as comma-separated string: `"10 15,15 20,10 10,10 20,30 35"`

---

### 7.4 Student Module

**Path:** `/Student/`  
**Access:** `$_SESSION['role'] == 'Student'`

| Page | Description |
|------|-------------|
| `index.php` | Dashboard showing student profile info |
| `grade.php` | View personal grades across all subjects and semesters |
| `pass.php` | Change own password |

---

### 7.5 Registrar Office Module

**Path:** `/RegistrarOffice/`  
**Access:** `$_SESSION['role'] == 'Registrar Office'`

| Page | Description |
|------|-------------|
| `index.php` | Dashboard |
| `student.php` | List all students |
| `student-add.php` | Enroll new students |
| `student-search.php` | Search students |
| `student-view.php` | View student details |

---

## 8. Data Access Layer

Each module has a `data/` directory with PHP files containing reusable functions. All functions accept `$conn` (PDO connection) as a parameter.

### Common Function Patterns

```php
// Get All Records
function getAllStudents($conn) {
    $sql = "SELECT * FROM students";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() >= 1) {
        return $stmt->fetchAll();
    } else {
        return 0;  // Returns integer 0 on empty result
    }
}

// Get By ID
function getStudentById($id, $conn) {
    $sql = "SELECT * FROM students WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    if ($stmt->rowCount() == 1) {
        return $stmt->fetch();
    } else {
        return 0;
    }
}

// Delete
function removeStudent($id, $conn) {
    $sql = "DELETE FROM students WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    $re = $stmt->execute([$id]);
    return $re ? 1 : 0;
}

// Search (LIKE queries across multiple columns)
function searchStudents($key, $conn) {
    $key = preg_replace('/(?<!\\)([%_])/', '\\$1', $key);
    $sql = "SELECT * FROM students
            WHERE student_id LIKE ? OR fname LIKE ? ...";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$key, $key, ...]);
    // ...
}

// Check Unique Username
function unameIsUnique($uname, $conn, $student_id=0) {
    // Returns 1 if unique, 0 if taken
    // Optional $student_id excludes current user during edit
}
```

### Data Files per Module

| Module | Data Files |
|--------|-----------|
| `admin/data/` | `admin.php`, `student.php`, `teacher.php`, `registrar_office.php`, `class.php`, `grade.php`, `section.php`, `subject.php`, `message.php`, `setting.php` |
| `Teacher/data/` | `student.php`, `teacher.php`, `class.php`, `grade.php`, `section.php`, `subject.php`, `student_score.php`, `setting.php` |
| `Student/data/` | `student.php`, `grade.php`, `section.php`, `subject.php`, `student_score.php`, `setting.php` |
| `RegistrarOffice/data/` | `student.php`, `grade.php`, `section.php` |

> ⚠️ **Code Duplication:** Many data files are duplicated across modules with identical or near-identical content.

---

## 9. Frontend & UI

### CSS Architecture

Single stylesheet: `css/style.css` (85 lines)

| Class | Usage |
|-------|-------|
| `.body-home`, `.body-login` | Background image (`img/bg.jpg`) with cover |
| `.black-fill` | Semi-transparent black overlay (`rgba(0,0,0, 0.7)`) |
| `#homeNav` | Translucent white navbar (`rgba(255,255,255, 0.5)`) |
| `.welcome-text` | Hero section (80vh height, Lobster font) |
| `#about .card-1` | About card (max 600px, translucent) |
| `#contact form` | Contact form container |
| `.login` | Login card styling |
| `.form-w` | Form wrapper (max 600px) |
| `.n-table` | Table wrapper (max 800px) |

### UI Components

- **Navbar:** Bootstrap 5 dark navbar (admin) / role-specific navbars
- **Dashboard:** Grid of Bootstrap button cards with Font Awesome icons
- **Forms:** Bootstrap 5 form controls
- **Tables:** Bootstrap 5 striped tables for data listing
- **Alerts:** Bootstrap alerts for success/error messages via `$_GET` parameters
- **Avatars:** Gender-based static images from `img/` directory

---

## 10. Security Considerations

### ✅ What's Done Right

| Practice | Implementation |
|----------|---------------|
| **SQL Injection Prevention** | PDO prepared statements with parameterized queries |
| **Password Hashing** | bcrypt via `password_hash()` / `password_verify()` |
| **Session-Based Auth** | Role and ID verification on every protected page |
| **Search Input Escaping** | `preg_replace` escapes LIKE wildcards in search |

### ❌ Critical Vulnerabilities

| # | Vulnerability | Risk | Location | Fix |
|---|--------------|------|----------|-----|
| 1 | **Reflected XSS** | Critical | All `<?=$_GET['error']?>` and `<?=$_GET['success']?>` outputs | Use `htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8')` |
| 2 | **No CSRF Tokens** | High | All forms | Add hidden CSRF token field, validate on submission |
| 3 | **Hardcoded DB Creds** | High | `DB_connection.php` | Move to `.env` file or environment variables |
| 4 | **No HTTPS Enforcement** | Medium | Entire app | Add `.htaccess` redirect to HTTPS |
| 5 | **Session Fixation** | Medium | `req/login.php` | Call `session_regenerate_id(true)` after login |
| 6 | **No Rate Limiting** | Medium | Login form | Implement login attempt throttling |
| 7 | **Weak Random Passwords** | Low | `student-add.php` JS | `Math.random()` is not cryptographically secure |

---

## 11. Known Issues & Bugs

| # | Issue | File | Description |
|---|-------|------|-------------|
| 1 | **Typo in exception class** | `DB_connection.php:11` | `PDOExeption` should be `PDOException` |
| 2 | **Search returns 0 for multiple results** | `admin/data/student.php:91` | `rowCount() == 1` should be `>= 1` |
| 3 | **Teacher subject ID parsing** | `Teacher/student-grade.php:29` | `str_split()` breaks subject IDs ≥ 10 |
| 4 | **No pagination** | All list pages | All records loaded at once |
| 5 | **Inconsistent directory casing** | Root | `admin/` (lowercase) vs `Student/`, `Teacher/`, `RegistrarOffice/` (PascalCase) |
| 6 | **Score format fragility** | `student_score.results` | Comma/space parsing is fragile and denormalized |

---

## 12. Local Development Setup

This section explains how to run the project on your local Windows machine for development and testing.

### 12.1 Install XAMPP

1. Download XAMPP (PHP 8.x version) from [apachefriends.org](https://www.apachefriends.org/)
2. Run the installer — ensure these components are selected:
   - ✅ **Apache** (web server)
   - ✅ **MySQL/MariaDB** (database)
   - ✅ **PHP** (runtime)
   - ✅ **phpMyAdmin** (database admin UI)
3. Install to the default location: `C:\xampp`

### 12.2 Start Services

1. Open **XAMPP Control Panel** (run as Administrator for best results)
2. Click **Start** next to **Apache** — should turn green
3. Click **Start** next to **MySQL** — should turn green
4. If ports conflict (e.g., Skype using port 80), change Apache port in `httpd.conf` or close the conflicting application

### 12.3 Copy Project Files

1. Copy the entire `school-management-system-php` folder into the XAMPP web root:
   ```
   C:\xampp\htdocs\school-management-system-php\
   ```
2. Alternatively, create a symbolic link from your working directory:
   ```powershell
   New-Item -ItemType SymbolicLink -Path "C:\xampp\htdocs\school-management-system-php" -Target "C:\AWS-Certification\school-management\school-management-system-php"
   ```

### 12.4 Create and Import the Database

1. Open your browser and navigate to `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar
3. Enter database name: **`sms_db`**
4. Set collation to: **`utf8mb4_general_ci`**
5. Click **Create**
6. Select the `sms_db` database → click the **Import** tab
7. Click **Choose File** → select `sms_db.sql` from the project root
8. Click **Go** — you should see "Import has been successfully finished" with 11 tables created

### 12.5 Verify Database Connection

The default `DB_connection.php` already matches XAMPP defaults — **no changes needed**:

| Setting | Default Value |
|---------|---------------|
| Host | `localhost` |
| Username | `root` |
| Password | *(empty string)* |
| Database | `sms_db` |

> ⚠️ **Bug Fix Required:** On line 11 of `DB_connection.php`, change `PDOExeption` to `PDOException` (typo in the original code).

### 12.6 Reset Default Passwords

The SQL dump contains bcrypt-hashed passwords. To set a known password, create a temporary file:

**File:** `C:\xampp\htdocs\school-management-system-php\reset.php`

```php
<?php
include "DB_connection.php";

$new_password = "admin123";
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Reset admin password
$conn->prepare("UPDATE admin SET password = ? WHERE username = 'elias'")->execute([$hashed]);

// Reset teacher passwords
$conn->prepare("UPDATE teachers SET password = ? WHERE username = 'oliver'")->execute([$hashed]);
$conn->prepare("UPDATE teachers SET password = ? WHERE username = 'abas'")->execute([$hashed]);

// Reset student passwords
$conn->prepare("UPDATE students SET password = ?")->execute([$hashed]);

// Reset registrar passwords
$conn->prepare("UPDATE registrar_office SET password = ?")->execute([$hashed]);

echo "All passwords reset to: $new_password";
echo "<br><br><strong style='color:red;'>⚠️ DELETE THIS FILE IMMEDIATELY AFTER USE!</strong>";
?>
```

1. Visit `http://localhost/school-management-system-php/reset.php`
2. **Delete `reset.php` immediately after use**

### 12.7 Access the Application

| Page | URL |
|------|-----|
| **Homepage** | `http://localhost/school-management-system-php/` |
| **Login** | `http://localhost/school-management-system-php/login.php` |
| **phpMyAdmin** | `http://localhost/phpmyadmin` |

### 12.8 Test Login Credentials

After running the password reset script:

| Role | Username | Password |
|------|----------|----------|
| Admin | `elias` | `admin123` |
| Teacher | `oliver` | `admin123` |
| Teacher | `abas` | `admin123` |
| Student | `john` | `admin123` |
| Student | `jo` | `admin123` |
| Registrar | `james` | `admin123` |

### 12.9 Alternative Local Server Options

| Tool | Pros | Install |
|------|------|---------|
| **XAMPP** | Most popular, full-featured, cross-platform | [apachefriends.org](https://www.apachefriends.org/) |
| **WAMP** | Windows-only, clean UI, easy to manage | [wampserver.com](https://www.wampserver.com/) |
| **Laragon** | Lightweight, auto virtual hosts, fast startup | [laragon.org](https://laragon.org/) |
| **Docker** | Containerized, reproducible, team-friendly | See Docker Compose example below |

#### Docker Compose (Optional)

```yaml
version: '3.8'
services:
  web:
    image: php:8.2-apache
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
  db:
    image: mariadb:10.4
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: sms_db
    ports:
      - "3306:3306"
    volumes:
      - ./sms_db.sql:/docker-entrypoint-initdb.d/sms_db.sql
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8081:80"
    environment:
      PMA_HOST: db
```

Run with: `docker-compose up -d` → access at `http://localhost:8080`

---

## 13. Deployment Guide — Hostinger

### Step-by-Step Guide to Deploy on Hostinger

---

### Step 1: Purchase Hostinger Hosting Plan

1. Go to [hostinger.com](https://www.hostinger.com)
2. Purchase a **Premium Web Hosting** or **Business Web Hosting** plan
   - Ensure the plan includes:
     - ✅ PHP 8.0+ support
     - ✅ MySQL databases
     - ✅ phpMyAdmin access
     - ✅ File Manager or FTP access
3. If you need a domain, register one during checkout (or connect an existing domain)

---

### Step 2: Access Hostinger Control Panel (hPanel)

1. Log in to [hpanel.hostinger.com](https://hpanel.hostinger.com)
2. Select your hosting plan from the dashboard
3. You'll see the **hPanel** dashboard — this is your hosting control panel

---

### Step 3: Set Up the MySQL Database

1. In hPanel, navigate to **Databases → MySQL Databases**
2. Create a new database:
   - **Database Name:** `sms_db` (or any name — note it down)
   - **Username:** Create a new database user (e.g., `sms_user`)
   - **Password:** Set a **strong password** (note it down!)
3. Click **Create**
4. Note down these values — you'll need them:
   ```
   DB Host:     (shown in hPanel, usually something like mysql.hostinger.com or localhost)
   DB Name:     u123456789_sms_db    (Hostinger prepends your account prefix)
   DB User:     u123456789_sms_user
   DB Password: YourStrongPassword123!
   ```

---

### Step 4: Import the Database Schema

1. In hPanel, navigate to **Databases → phpMyAdmin**
2. Click **Enter phpMyAdmin** next to your newly created database
3. In phpMyAdmin:
   - Select your database from the left sidebar
   - Click the **Import** tab
   - Click **Choose File** and select `sms_db.sql` from the project folder
   - Click **Go** to import
4. You should see 11 tables created with sample data
5. **Verify:** Click on the `admin` table to confirm data was imported

---

### Step 5: Update Database Connection Credentials

**Before uploading**, edit `DB_connection.php` with your Hostinger database credentials:

```php
<?php  

$sName = "localhost";                    // Usually "localhost" on Hostinger
$uName = "u123456789_sms_user";         // Your Hostinger DB username
$pass  = "YourStrongPassword123!";       // Your Hostinger DB password
$db_name = "u123456789_sms_db";         // Your Hostinger DB name

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {               // ← Fixed typo: was PDOExeption
    echo "Connection failed: ". $e->getMessage();
    exit;
}
```

> ⚠️ **Important:** The DB host on Hostinger is usually `localhost`. Check hPanel → Databases → MySQL Databases for the exact hostname displayed under your database.

---

### Step 6: Upload Project Files

#### Option A: Using Hostinger File Manager (Easiest)

1. In hPanel, navigate to **Files → File Manager**
2. Navigate to the `public_html` folder
3. If you want the app at your root domain (`yourdomain.com`):
   - Delete the default `index.html` if present
   - Click **Upload** (top toolbar)
   - Upload all project files **directly into** `public_html/`
4. If you want the app at a subdirectory (`yourdomain.com/school/`):
   - Create a new folder `school` inside `public_html/`
   - Upload all project files into `public_html/school/`

> 💡 **Tip:** The easiest way is to zip the entire project folder on your local machine, upload the `.zip` file, then use File Manager's **Extract** feature.

**Upload as ZIP:**
1. On your local machine, navigate to `C:\AWS-Certification\school-management\`
2. Right-click `school-management-system-php` folder → **Send to → Compressed (zipped) folder**
3. In Hostinger File Manager → `public_html/` → Upload the `.zip` file
4. Right-click the uploaded `.zip` → **Extract**
5. Move all extracted files from the subfolder to `public_html/` (or your desired directory)

#### Option B: Using FTP (FileZilla)

1. In hPanel, navigate to **Files → FTP Accounts**
2. Note the FTP credentials:
   - **Host:** Your FTP hostname (e.g., `ftp.yourdomain.com`)
   - **Username:** Your FTP username
   - **Password:** Your FTP password
   - **Port:** 21
3. Download and install [FileZilla](https://filezilla-project.org/)
4. Connect to your Hostinger server
5. Navigate to `public_html/` on the remote side
6. Upload all project files from your local directory

#### Option C: Using Git (SSH Access — Business Plan)

If you have SSH access (Business plan):

```bash
# SSH into your Hostinger server
ssh u123456789@yourdomain.com

# Navigate to public_html
cd public_html

# Clone the repository
git clone https://github.com/pradeipk/school-management-system-php.git .

# Edit DB_connection.php with your credentials
nano DB_connection.php
```

---

### Step 7: Set PHP Version

1. In hPanel, navigate to **Advanced → PHP Configuration**
2. Set PHP version to **8.0**, **8.1**, or **8.2**
3. Ensure these extensions are enabled:
   - ✅ `pdo`
   - ✅ `pdo_mysql`
   - ✅ `mbstring`
   - ✅ `openssl`
4. Click **Save**

---

### Step 8: Configure File Permissions

1. In File Manager, ensure the following permissions:
   - **Folders:** `755` (rwxr-xr-x)
   - **PHP Files:** `644` (rw-r--r--)
   - **`DB_connection.php`:** `640` (rw-r-----) — restrict read access
2. Right-click on files/folders → **Permissions** to change

---

### Step 9: Set Up SSL Certificate (HTTPS)

1. In hPanel, navigate to **Security → SSL**
2. Hostinger provides **free SSL** (Let's Encrypt)
3. Click **Install SSL** for your domain
4. Wait for propagation (usually a few minutes)

#### Force HTTPS Redirect

Create or edit `.htaccess` in `public_html/`:

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

### Step 10: Create Admin Account (Reset Password)

Since the SQL dump contains bcrypt-hashed passwords, you may not know the original passwords. You have two options:

#### Option A: Use phpMyAdmin to Set a Known Password

1. Generate a bcrypt hash for your desired password. In phpMyAdmin, run:
   ```sql
   SELECT PASSWORD('your_desired_password');
   ```
   Or use an online bcrypt generator to hash your password.

2. Update the admin password:
   ```sql
   UPDATE admin SET password = '$2y$10$YOUR_NEW_HASH_HERE' WHERE admin_id = 1;
   ```

#### Option B: Create a Temporary PHP Script

Create a file called `reset_password.php` in `public_html/`:

```php
<?php
include "DB_connection.php";

$new_password = "admin123";  // Set your desired password
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE admin SET password = ? WHERE username = 'elias'";
$stmt = $conn->prepare($sql);
$stmt->execute([$hashed]);

echo "Admin password reset to: $new_password";
echo "<br>Hash: $hashed";
echo "<br><br><strong>DELETE THIS FILE IMMEDIATELY!</strong>";
?>
```

1. Upload and visit `https://yourdomain.com/reset_password.php`
2. Note the password
3. **Delete the file immediately after use!**

---

### Step 11: Test the Deployment

1. Visit `https://yourdomain.com/` (or `https://yourdomain.com/school/` if in subdirectory)
2. You should see the school homepage
3. Click **Login** and test with:
   - **Role:** Admin
   - **Username:** `elias`
   - **Password:** (the password you set in Step 10)
4. Test each section:
   - ✅ Admin dashboard loads
   - ✅ Can view/add/edit students
   - ✅ Can view/add/edit teachers
   - ✅ Settings page works
   - ✅ Contact form submits
   - ✅ Logout works

---

### Step 12: Domain & DNS Setup (If Using Custom Domain)

If you purchased a domain elsewhere:

1. In hPanel, go to **Domains → yourdomain.com**
2. Note the Hostinger nameservers:
   ```
   ns1.dns-parking.com
   ns2.dns-parking.com
   ```
3. Go to your domain registrar and update nameservers
4. Wait for DNS propagation (up to 48 hours, usually much less)

---

## 14. Post-Deployment Checklist

### Security Hardening

- [ ] **Change all default passwords** — Update admin, teacher, student passwords
- [ ] **Delete `sms_db.sql`** from server — Exposes your schema to anyone
- [ ] **Delete `reset_password.php`** if created
- [ ] **Restrict `DB_connection.php`** permissions to `640`
- [ ] **Add `.htaccess` protection** to sensitive directories:

```apache
# Add to admin/req/.htaccess, Teacher/req/.htaccess, etc.
# Block direct access to data files
<Files "*.php">
    # Only allow from same server
    Order Deny,Allow
    Deny from all
    Allow from 127.0.0.1
</Files>
```

> ⚠️ Be careful with the above — the `req/` handlers need to be accessible via form submissions. A better approach is to verify HTTP referrer or use CSRF tokens.

- [ ] **Enable error logging** — Add to top of `DB_connection.php`:

```php
ini_set('display_errors', 0);           // Don't show errors to users
ini_set('log_errors', 1);               // Log errors instead
ini_set('error_log', '/home/u123456789/logs/php_errors.log');
```

### Functional Verification

- [ ] Homepage loads with school info
- [ ] Login works for all 4 roles
- [ ] Admin can CRUD students, teachers, registrar staff
- [ ] Admin can manage classes, grades, sections, courses
- [ ] Teacher can enter student grades
- [ ] Student can view grades
- [ ] Registrar can enroll students
- [ ] Contact form submits successfully
- [ ] Settings changes reflect on homepage
- [ ] Logout works correctly
- [ ] SSL/HTTPS is active

### Performance

- [ ] Enable Hostinger's **LiteSpeed Cache** if available
- [ ] Enable **GZIP compression** in `.htaccess`:

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

---

## 15. Troubleshooting

### Common Issues on Hostinger

| Problem | Cause | Solution |
|---------|-------|----------|
| **500 Internal Server Error** | PHP syntax error or `.htaccess` issue | Check `error_log` in hPanel → Files → File Manager |
| **Database connection failed** | Wrong credentials in `DB_connection.php` | Verify DB host, name, user, password in hPanel |
| **Blank white page** | PHP fatal error with `display_errors` off | Enable error display temporarily: `ini_set('display_errors', 1)` |
| **Login redirects back to login** | Session not persisting | Check PHP version; ensure `session_start()` is first line |
| **CSS/images not loading** | Wrong file paths | Check that `css/`, `img/` directories are present; paths are relative |
| **"Access denied for user"** | DB user doesn't have permissions | In hPanel → Databases, ensure user is assigned to the database |
| **PDOExeption error** | Typo in `DB_connection.php:11` | Change `PDOExeption` to `PDOException` |
| **Homepage shows login** | Settings table empty | Ensure `sms_db.sql` was imported with data (not just schema) |
| **File upload fails** | Directory permissions | Set upload directories to `755` |
| **Mixed content warnings** | HTTP resources on HTTPS page | CDN links already use HTTPS ✅ |

### Hostinger Support

- **Live Chat:** Available 24/7 in hPanel
- **Knowledge Base:** [support.hostinger.com](https://support.hostinger.com)
- **Community Forum:** [hostinger.com/community](https://www.hostinger.com/community)

---

## Quick Reference Card

| Item | Value |
|------|-------|
| **Default Admin** | Username: `elias` (password must be reset) |
| **Database** | `sms_db` (11 tables) |
| **PHP Version** | 8.0+ required |
| **Framework** | None (procedural PHP) |
| **CSS Framework** | Bootstrap 5.2.0 |
| **Auth Method** | Session-based, bcrypt passwords |
| **DB Driver** | PDO (MySQL) |

---

*Documentation generated on June 23, 2026*
