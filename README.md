# Whisper Messaging App

A simple PHP + MySQL backend and React/JS frontend chat application inspired by WhatsApp.  
Implements login with OTP (via Brevo API), sending messages (text, image, audio, video), and emoji support.

---

## Features

- **Login via OTP** (Brevo API, time-limited codes).
- **Send messages** (text, image, audio, video).
- **Emoji picker** with big single-emoji rendering (like WhatsApp).
- **Message bubbles** styled for sender/receiver.
- **Read receipts** flag in DB.
- **Lazy loading** of old messages on scroll.
- **Delete message (for both sides)** — *currently not working correctly* (see below).

---

## Known Issues

- **Delete Message:**  
  The `delete_msg` route exists in `api.php`, but the functionality is not working correctly.  
  The SQL `UPDATE` runs with `row_id=?` but no rows are affected. Possible causes:
  - Missing `username` or sender check in the `messages` table schema.
  - `require_token_username()` may reject the request if the token/username doesn’t pass validation.
  - The current query doesn’t restrict deletion to the original sender.

  ⚠️ Until fixed, attempting to delete a message will return no error but will not update the database.

---

## Requirements

- PHP 8.x
- MySQL 8.x
- Composer (for dependencies)
- Node.js + npm/yarn (for frontend build)

---

## Installation

1. Clone repository.
2. Import the database schema:
   ```bash
   mysql -u root -p whisper_db < mysql.sql
