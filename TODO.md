# TODO - Resend + Direct Registration

## Step 1: Implement Resend mailer wrapper
- [x] Create `app/utils/resend_mailer.php` with `send_resend_email(...)` using Resend API

## Step 2: Switch email sending from SMTP to Resend
- [x] Update `app/services/send_otp.php` to use `send_resend_email` instead of `send_smtp_email`

- [x] Update `app/services/send_notifications.php` to use `send_resend_email` instead of `send_smtp_email`


## Step 3: Implement direct registration
- [x] Create `app/services/register.php`:
  - [ ] Validate email format `^[0-9]{9}@std\.kyu\.ac\.ug$`
  - [ ] Validate student exists in `students` table
  - [ ] Hash password and update `students.password_hash`
  - [ ] Send success email via Resend
- [x] Create `public/register.php` registration UI and POST handler route

## Step 4: Wire navigation
- [x] Update `public/login.php` “Register here” button to navigate to `register.php`

## Step 5: Basic testing
- [ ] Register with valid email format and verify password_hash is saved
- [ ] Forgot password sends OTP via Resend

