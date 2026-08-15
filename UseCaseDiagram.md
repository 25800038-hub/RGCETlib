# Use Case Diagram

This is a use case diagram for the Library Management System.

```mermaid
flowchart LR
    Student[Student User]
    Admin[Administrator]

    subgraph System[Library Management System]
        UC1[Login]
        UC2[Sign Up]
        UC3[View Books Catalog]
        UC4[View Issued Books]
        UC5[Update Profile]
        UC6[Reset Password]
        UC7[Manage Books]
        UC8[Manage Authors]
        UC9[Manage Categories]
        UC10[Manage Students]
        UC11[Issue Book]
        UC12[Return Book]
        UC13[View Overdue Books]
        UC14[View Reports]
        UC15[Reserve Books Online]
        UC16[Read Full Book PDF Online]
        UC17[Upload & Manage eBook PDFs]
    end

    Student --> UC1
    Student --> UC3
    Student --> UC4
    Student --> UC15
    Student --> UC16
    Student --> UC5
    Student --> UC6

    Admin --> UC1
    Admin --> UC2
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
    Admin --> UC15
    Admin --> UC17
```

## Notes
- **Students**: Log in, browse the book catalog, reserve physical copies for counter pickup, read full eBooks (PDFs) online, view issued books, update profile, and reset password.
- **Administrators**: Manage catalog (books, eBook PDFs, authors, categories), manage registered students, fulfill/manage online reservations, issue and return books, track inventory, and view overdue alerts.
