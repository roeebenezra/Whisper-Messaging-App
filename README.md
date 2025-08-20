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
   mysql -u root -p local_db < mysql.sql
