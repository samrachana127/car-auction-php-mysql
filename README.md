# Car Auction Website (PHP + MySQL)

A first-year university project — a car auction website built with PHP
and MySQL, using PDO for database access.

## What it does

- Browse auctions and categories
- Search auctions
- User login and registration
- Admin panel: add/edit auctions, manage categories, manage admins

## Tech used

- PHP (PDO for MySQL access)
- MySQL / MariaDB
- HTML/CSS

## Database

Connects to a MySQL database named `assignment1` (see `connectionpage.php`
for connection details). The database schema/seed file isn't included in
this repo — this is early coursework and the original `.sql` export wasn't
kept.

## Project structure

| Path | What it is |
|---|---|
| `index.php` | Homepage |
| `auction.php`, `category.php`, `search.php` | Browsing and searching auctions |
| `login.php`, `register.php`, `logout.php` | User authentication |
| `add*.php`, `edit*.php`, `delete*.php`, `manageAdmins.php` | Admin panel functionality |
| `connectionpage.php` | Database connection (PDO) |
| `public/images/auctions/` | Uploaded auction images |

## Note

This was built in first year, so it's an earlier and simpler project
compared to later coursework (e.g. the jobs portal or Museboard).
