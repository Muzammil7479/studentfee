SchoolM database connection and CRUD notes
=========================================

Database used by this project:
- DB_DATABASE=schoolfeemanagement1
- DB_CONNECTION=mysql
- Existing SQL tables are used. No migration or schema change is required.

Setup steps:
1) Import your SQL dump into phpMyAdmin/MySQL as database: schoolfeemanagement1
2) In .env, use:
   APP_NAME=SchoolM
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=schoolfeemanagement1
   DB_USERNAME=root
   DB_PASSWORD=
3) Run:
   php artisan optimize:clear
   php artisan route:list
   php artisan serve

Do not run migrate:fresh or db:seed on your existing database unless you want to reset data.

Implemented CRUD areas:
- Admin student CRUD: create/admit, list/search, edit/update, delete student with related fee/payment/receipt cleanup.
- Teacher CRUD: create, list/search, view, edit/update, delete.
- Accounts fee structure CRUD: create, update, delete unused fee structures.
- Accounts payment CRUD: create payment, update payment, delete payment, generate/print/download receipt.
- Accounts scholarship update: optional percentage from 0 to 100.
- Student portal: search/open student profile, view fee ledgers, payment history, print/download receipts.
- Principal portal: class metrics, class-wise admitted students, parent contact, address, fee summary.

Important routes:
- /account-section
- /admin/students
- /student
- /teachers
- /principal
- /account-section/payment/{paymentId}/receipt
- /account-section/payment/{paymentId}/receipt/download
- /student/payment/{paymentId}/receipt
- /student/payment/{paymentId}/receipt/download

Latest update:
- Fee structures are now class-based. When Accounts creates/updates a fee structure for a class, it is automatically applied/recalculated for all currently admitted students in that class.
- When Admin admits a new student into a class, all existing fee structures for that class are automatically assigned to that student.
- When Admin updates a student and changes/checks the class, matching class fee structures are checked and assigned automatically.
- The payment method options were kept unchanged: Cash, Bank Transfer, Credit Card, Debit Card, JazzCash, EasyPaisa.
- Accounts fee structure CRUD list is now grouped by class.
- Manual fee-ledger assignment inside a student profile now shows only the selected student's own class fee structures.
- No database table, column, migration, or existing SQL data was changed.
