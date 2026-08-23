# Online Library Management System

A modern, robust PHP-based Library Management System (LMS) designed to handle book catalogs, reservations, waitlists, and borrowing constraints for different roles within an educational institution.

## Features

- **Role-Based Access Control**:
  - **Admin**: Full control over library operations, books, authors, categories, and users.
  - **Students**: Can browse the catalog, reserve books, and join waitlists. Subject to a strict limit of 3 active books (borrowed + reserved) at a time.
  - **Faculty (Teachers)**: Similar to students, but with a higher limit of 5 active books.

- **Dynamic Waitlist & Auto-Promotion**:
  - If a book is completely out of stock, users can join a waitlist rather than seeing a disabled "Out of Stock" button.
  - **Auto-Promotion Engine**: The moment a book is returned by any user, or a reservation is cancelled, the system automatically promotes the next person on the waitlist to `Reserved` status, giving them a fresh 3-day pickup window.

- **Borrowing Constraints**:
  - The system dynamically checks a user's total active holdings before allowing them to reserve or borrow a new book.
  - Books are marked as "Expired Pickup" if they are not collected within the designated timeframe.

- **Modern UI / UX**:
  - Styled with a cohesive warm-tone color palette (Dark Brown `#3d382e`, warm beige backgrounds, and purple accents).
  - Clean landing pages, responsive dashboards, and interactive alerts for form submissions.

## Tech Stack
- **Frontend**: HTML5, Vanilla CSS, JavaScript, Bootstrap (for legacy grid compatibility), FontAwesome.
- **Backend**: PHP (using PDO for secure database interactions).
- **Database**: MySQL.

## Installation / Setup

1. **Prerequisites**: Ensure you have XAMPP, WAMP, or any standard LAMP stack installed.
2. **Move to Server Directory**: Place the `library` directory inside your `htdocs` or `www` folder.
3. **Database Configuration**:
   - Create a MySQL database named `library`.
   - Import the provided `library.sql` file to set up the schema.
4. **Environment File**:
   - Edit `includes/config.php` and `admin/includes/config.php` if you need to adjust your database credentials (default is `root` with no password).
5. **Access**:
   - Access the homepage at `http://localhost/library`
   - Admin Panel at `http://localhost/library/admin`

## Project Structure
- `/admin` - Contains all admin-facing pages (adding books, managing users, issuing/collecting returns).
- `/includes` - Shared UI components (header, footer) and core functionality scripts (like `promote_waitlist.php` and `config.php`).
- `/bookimg` - Directory where uploaded book covers are stored.
- `index.php` - The main landing page.
- `listed-books.php` - The primary book catalog for users.

## Workflow Overview
1. Admin adds categories, authors, and books.
2. Users (Students/Teachers) register and login.
3. Users browse `listed-books.php` to reserve books or join the waitlist.
4. Admin reviews active reservations in `manage-reservations.php` and issues physical copies.
5. Users return books at the counter, the Admin marks them as returned, and the waitlist engine seamlessly routes the freed book to the next person in line.
