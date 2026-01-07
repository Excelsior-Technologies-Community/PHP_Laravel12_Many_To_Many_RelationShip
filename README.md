# PHP_Laravel12_Many_To_Many_RelationShip

## Project Overview

This project demonstrates a complete implementation of a Many-to-Many relationship in Laravel 12 using **Students** and **Courses**. Each student can enroll in multiple courses, and each course can have multiple students.

The project includes database design, Eloquent relationships, controllers, seeders, and a fully styled UI using Blade and Tailwind CSS. It is suitable for learning, interviews, academic projects, and portfolio use.

---

## Features

* Laravel 12 Many-to-Many relationship implementation
* Pivot table with extra fields
* Student enrollment and course dropping
* Sample data generation
* Clean MVC structure
* Blade templates with Tailwind CSS
* Seeder support
* Validation and flash messages

---

## Requirements

* PHP 8.1 or higher
* Laravel 12.x
* MySQL
* Composer
* Web browser

---

## Installation

### Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel laravel12-many-to-many
cd laravel12-many-to-many
```

### Step 2: Database Configuration

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_many
DB_USERNAME=root
DB_PASSWORD=
```

Create the database:

```sql
CREATE DATABASE laravel12_many;
```

---

## Database Structure

### Tables

* students
* courses
* course_student (pivot table)

The pivot table stores:

* student_id
* course_id
* enrolled_at

---

## Migrations

Run migrations:

```bash
php artisan migrate
```

---

## Models and Relationships

### Student Model

* belongsToMany Course
* enroll(), drop(), isEnrolled() helper methods

### Course Model

* belongsToMany Student
* enrolledCount() helper method

---

## Controller Logic

### StudentController

Handles:

* Listing students with courses
* Listing courses with students
* Enrolling students
* Dropping students from courses
* Generating sample data

---

## Routes

```php
Route::get('/', [StudentController::class, 'index'])->name('students.index');
Route::get('/courses', [StudentController::class, 'courses'])->name('courses.index');
Route::get('/create-sample-data', [StudentController::class, 'createSampleData'])->name('create.sample');
Route::post('/enroll', [StudentController::class, 'enroll'])->name('student.enroll');
Route::delete('/drop/{student}/{course}', [StudentController::class, 'drop'])->name('student.drop');
```

---

## Seeder (Optional)

Generate sample data using a seeder:

```bash
php artisan make:seeder StudentCourseSeeder
php artisan db:seed --class=StudentCourseSeeder
```

You can also reset and seed:

```bash
php artisan migrate:fresh --seed
```
---
## Screenshot
<img width="1850" height="967" alt="image" src="https://github.com/user-attachments/assets/39f47bb3-91b9-49db-9cf9-c51e6ffa3f09" />
<img width="1789" height="955" alt="image" src="https://github.com/user-attachments/assets/039951a7-836e-4b93-817b-125f26512aea" />
<img width="617" height="636" alt="image" src="https://github.com/user-attachments/assets/841656ee-44ef-4687-8d46-5a4cec825b41" />

---

## Views

* Layout file with Tailwind CSS
* Students listing with enrolled courses
* Courses listing with enrolled students
* Enrollment form
* Flash success and error messages

---

## Usage

Start the application:

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

Use the **Generate Sample Data** option to populate the database.

---

## Project Structure

```
app/
 ├── Models/
 │    ├── Student.php
 │    └── Course.php
 ├── Http/Controllers/
 │    └── StudentController.php

database/
 ├── migrations/
 ├── seeders/
 │    └── StudentCourseSeeder.php

resources/views/
 ├── layouts/app.blade.php
 ├── students/index.blade.php
 └── courses/index.blade.php

routes/web.php
```

---

## Best Practices Used

* Proper naming conventions
* Pivot table constraints
* Validation before database actions
* Reusable Blade layout
* Clean controller methods

---

## Learning Outcomes

* Understand Many-to-Many relationships
* Work with pivot tables
* Use Eloquent relationship methods
* Build real-world Laravel CRUD logic
* Structure a Laravel project professionally

---

## Use Cases

* Student Course Management
* Role and Permission systems
* Product and Category mapping
* User and Group management

---

## License

This project is open-source and free to use for learning and development.

---

## Author

Developed using Laravel 12 for learning and demonstration purposes.
