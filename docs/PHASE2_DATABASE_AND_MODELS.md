# 🗄️ Phase 2: Database Design & Model Classes

## What You'll Learn in This Phase

In Phase 1, you learned HOW the web works. Now you'll learn how to
**organize your database code** properly using **Model classes**.

---

## Table of Contents

1. [The Problem: Scattered Database Code](#1-the-problem-scattered-database-code)
2. [The Solution: Model Classes](#2-the-solution-model-classes)
3. [What is a Model?](#3-what-is-a-model)
4. [Building Your First Model](#4-building-your-first-model)
5. [The Repository Pattern](#5-the-repository-pattern)
6. [How Models Connect to MVC](#6-how-models-connect-to-mvc)

---

## 1. The Problem: Scattered Database Code

Right now, database queries are scattered throughout your project:

```php
// In authenticate.php:
$stmt = $conn->prepare("SELECT student_id, first_name, ... FROM students WHERE student_id = ?");

// In voting.php:
$check_voted = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ?");

// In results.php:
$check_voted = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ?");

// In processvote.php:
$check_voted = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ? FOR UPDATE");
```

**See the problem?** The same query appears in 3 different files!

If you need to change the `students` table (add a column, rename a field),
you have to find and update EVERY file that queries that table. 😱

---

## 2. The Solution: Model Classes

Instead of writing SQL everywhere, we create a **Model class** — a single
file that handles ALL database operations for one table.

```php
// BEFORE (scattered code):
// authenticate.php, voting.php, results.php all have their own queries

// AFTER (Model class):
$student = Student::findById($conn, '23/U/001');
if ($student && $student['has_voted']) {
    // Already voted!
}
```

**Benefits:**
- ✅ **ONE place** for all student queries → easier to maintain
- ✅ **Reusable** → call `Student::findById()` from any file
- ✅ **Testable** → you can test database logic independently
- ✅ **Consistent** → same query runs the same way everywhere

---

## 3. What is a Model?

In MVC architecture:

```
┌─────────────┐      ┌──────────────┐      ┌────────────┐
│   MODEL     │      │  CONTROLLER  │      │    VIEW    │
│             │      │              │      │            │
│ Talks to    │◄────►│ Uses Models  │◄────►│ Displays   │
│ database    │      │ to get data  │      │ the data   │
└─────────────┘      └──────────────┘      └────────────┘
```

A **Model** is a PHP class that represents a database table.

| Concept | Real World Analogy |
|---------|-------------------|
| Model class | A filing cabinet for one type of document |
| Model method | A specific action: "find a document", "add a document" |
| $conn parameter | The key to unlock the filing cabinet |

---

## 4. Building Your First Model

Here's what a Student Model looks like:

```php
class Student {
    // Find a student by their ID
    public static function findById(mysqli $conn, string $student_id): ?array {
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc(); // Returns array or null
        $stmt->close();
        return $student ?: null;
    }
}
```

**Breaking this down:**

- `class Student` → A class is a blueprint/template. Like a recipe book.
- `public static function` → "static" means you call it on the class itself:
  `Student::findById(...)` instead of needing to create an object first.
- `mysqli $conn` → Type hint: $conn MUST be a mysqli object (catches bugs early)
- `?array` → Return type: returns an array OR null (the ? means "nullable")

---

## 5. The Repository Pattern

Our Models follow the **Repository Pattern** — each Model provides
a clean API for database operations:

| Method | SQL Operation | Example |
|--------|--------------|---------|
| `findById()` | SELECT ... WHERE id = ? | Get one student |
| `findAll()` | SELECT * | Get all students |
| `findByEmail()` | SELECT ... WHERE email = ? | Find by email |
| `create()` | INSERT INTO ... | Register a student |
| `update()` | UPDATE ... SET ... | Change student data |
| `delete()` | DELETE FROM ... | Remove a student |
| `hasVoted()` | SELECT has_voted ... | Check voting status |

**The pattern:** All database logic lives in Model classes.
Controllers NEVER write raw SQL — they call Model methods instead.

---

## 6. How Models Connect to MVC

### BEFORE (current code):
```php
// authenticate.php does EVERYTHING:
// 1. Reads POST data (Controller job)
// 2. Queries database (Model job)
// 3. Manages session (Controller job)
// 4. Redirects (Controller job)
```

### AFTER (with Models):
```php
// authenticate.php (Controller) — handles logic flow
$student = Student::findById($conn, $_POST['student_id']);

if ($student && password_verify($_POST['password'], $student['password_hash'])) {
    // Create session...
}
```

```php
// Student.php (Model) — handles ALL database queries
class Student {
    public static function findById($conn, $id) { ... }
    public static function hasVoted($conn, $id) { ... }
    public static function markAsVoted($conn, $id) { ... }
}
```

**The Controller asks "what do I need?"**
**The Model answers "here's the data!"**

---

## Files We'll Create

```
app/
├── models/              ← NEW! Model classes
│   ├── Student.php      ← All student database operations
│   ├── Candidate.php    ← All candidate database operations
│   ├── Vote.php         ← All vote database operations
│   ├── Election.php     ← All election database operations
│   └── Admin.php        ← All admin database operations
```

Each Model will have detailed comments explaining every method.
Let's build them! 🚀
