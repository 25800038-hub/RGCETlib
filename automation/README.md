# Automated Due-Date Reminders

This directory contains the automation scripts for the Library Management System.

## `send_due_reminders.php`

This script processes all currently issued books that have not been returned. It calculates the due date (7 days from the issue date) and dispatches reminder emails using the system's `MailService`.

### Reminder Types:
1. **Three-Day Reminder**: Sent exactly 3 days before the due date.
2. **One-Day Reminder**: Sent exactly 1 day before the due date.
3. **Overdue Reminder**: Sent every day the book remains overdue.

### Duplicate Prevention
The script utilizes the `tblnotifications` table to prevent sending duplicate emails. It checks if a specific reminder type (`Reminder_3Day`, `Reminder_1Day`, `Reminder_Overdue_YYYY-MM-DD`) has already been successfully sent for a given book issue transaction.

## How to Test Locally (Development)
You can manually run this script from the command line (if PHP is installed in your PATH):
```cmd
cd d:\Xampp\htdocs\library
php automation/send_due_reminders.php
```

Alternatively, you can navigate to the script in your web browser for testing purposes:
`http://localhost/library/automation/send_due_reminders.php`

> [!WARNING]  
> In a production environment, you should protect this file from public web access (e.g., using `.htaccess` or by moving the script outside the web root) so that random visitors cannot trigger the script.

## Production Scheduling

To fully automate this process, schedule **both** scripts to run once per day (e.g., at 9:00 AM).

### Linux (Cron Job)
Add the following lines to your crontab (`crontab -e`):
```bash
0 9 * * * php /path/to/your/project/library/automation/send_due_reminders.php >> /path/to/your/project/library/automation/reminders.log 2>&1
5 9 * * * php /path/to/your/project/library/automation/cancel_expired_reservations.php >> /path/to/your/project/library/automation/expired.log 2>&1
```

### Windows (Task Scheduler)
If your production server runs Windows (e.g., XAMPP):
1. Open **Task Scheduler**.
2. Click **Create Basic Task**.
3. Name it "Library Automations" and click Next.
4. Set the trigger to **Daily** and choose a time (e.g., 9:00 AM).
5. Choose **Start a program** for the Action.
6. In **Program/script**, browse to your PHP executable (e.g., `C:\xampp\php\php.exe`).
7. In **Add arguments (optional)**, enter the absolute path to the script: `D:\Xampp\htdocs\library\automation\send_due_reminders.php`.
8. Repeat steps 2-7 to create a second task named "Library Expired Reservations" pointing to `D:\Xampp\htdocs\library\automation\cancel_expired_reservations.php`.
