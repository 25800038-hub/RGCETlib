# Data Flow Diagram for the Library Management System

This diagram shows the main data movement between users, application processes, the database, and file storage for the project.

```mermaid
flowchart TD
    Student[Student User]
    Admin[Administrator]

    P1[1. Authentication & Session]
    P2[2. Student Operations]
    P3[3. Admin Management]
    P4[4. Book Issue / Return]
    P5[5. Book Reservation & Counter Collection]

    DB[(MySQL Database)]
    FS[(File Storage / Book Images)]
    TXT[(studentid.txt)]

    Student -->|Login / Signup / Profile Update / Password Reset| P1
    Student -->|View books / Borrowed books / History| P2
    Student -->|Reserve book online / Cancel reservation| P5
    Student -->|Request issue / return status| P4

    Admin -->|Admin login / password change| P1
    Admin -->|Manage books, authors, categories, students| P3
    Admin -->|Manage reservations & Fulfill counter collection| P5
    Admin -->|Issue / return books / view overdue records| P4

    P1 -->|Validate credentials and store session| DB
    P2 -->|Read/write student profile and issue history| DB
    P3 -->|Create/update/delete catalog and student records| DB
    P4 -->|Insert/update issue and return details| DB
    P5 -->|Create/cancel/update reservation status & convert to issue| DB

    P3 -->|Upload / update book images| FS
    P2 -->|Read book image reference| FS
    P1 -->|Generate student ID on signup| TXT

    DB -->|Return records and status| P1
    DB -->|Return records and status| P2
    DB -->|Return records and status| P3
    DB -->|Return records and status| P4
    DB -->|Return reservation data and stock counts| P5
```

## Summary of main data flows
- Students log in, sign up, update profile, browse books catalog, reserve books online, and view issued/reserved books.
- Admins log in and manage books, categories, authors, students, manage/fulfill student reservations upon counter collection, and issue/return books.
- The PHP application interacts with MySQL for records like users, books, authors, categories, reservations, and issued books.
- Book images are stored in the file system and referenced from the database.
- Student IDs are generated through the local text file.
