# 🔒 Phase 3: Security Hardening

## What You'll Learn in This Phase

Security is the MOST IMPORTANT aspect of a voting system. If your system
can be hacked, the entire election is compromised. In your final year defense,
your panel WILL ask about security — this phase prepares you.

---

## Table of Contents

1. [Security Audit: What's Missing?](#1-security-audit-whats-missing)
2. [CSRF Protection for All Forms](#2-csrf-protection-for-all-forms)
3. [Login Rate Limiting](#3-login-rate-limiting)
4. [Input Validation & Sanitization](#4-input-validation--sanitization)
5. [Security Headers](#5-security-headers)

---

## 1. Security Audit: What's Missing?

I audited every POST form in your project. Here's the current state:

| Form | File | Has CSRF? | Needs Fix? |
|------|------|-----------|-----------|
| Student Login | login.html | ❌ No | ⚠️ Yes (but login forms are lower risk) |
| Admin Login | admin_login.html | ❌ No | ⚠️ Yes |
| Send OTP | login.html | ❌ No | ✅ Yes — someone could spam OTPs |
| Verify OTP | login.html | ❌ No | ✅ Yes |
| Cast Vote | voting.php | ✅ Yes | ✅ Already protected! |
| Submit Feedback | feedback.php | ❌ No | ✅ Yes — could spam feedback |
| Logout | voting.php, results.php | ❌ No | ⚠️ Low risk but good practice |

### The Challenge with login.html:
`login.html` is a plain HTML file — it can't run PHP to generate CSRF tokens.
**Solution:** Convert it to `login.php` so PHP can generate tokens.

---

## 2. CSRF Protection for All Forms

### How CSRF Tokens Work (Recap):

```
1. Page loads → PHP generates random token → stores in SESSION
2. Token is embedded in form as hidden field
3. User submits form → token sent with POST data
4. Server compares: POST token === SESSION token?
   ✅ Match → Process the form
   ❌ No match → Reject (403 Forbidden)
```

Evil websites can't know your random token → their fake forms are rejected!

---

## 3. Login Rate Limiting

### The Problem:
Without rate limiting, an attacker can try thousands of passwords per second:
```
POST authenticate.php: student_id=23/U/001, password=aaa     ❌
POST authenticate.php: student_id=23/U/001, password=aab     ❌
POST authenticate.php: student_id=23/U/001, password=aac     ❌
... 1000 attempts per second until they guess right!
```

### The Solution:
Track failed login attempts. After 5 failures, lock the account for 15 minutes.

```
Attempt 1: Wrong password → "Invalid credentials" (attempts: 1)
Attempt 2: Wrong password → "Invalid credentials" (attempts: 2)
...
Attempt 5: Wrong password → "Account locked. Try again in 15 minutes."
Attempt 6: Even correct password → "Account locked." (still locked!)
... 15 minutes pass ...
Attempt 7: Correct password → ✅ Login success! (attempts reset to 0)
```

---

## 4. Input Validation & Sanitization

### Rule: NEVER Trust User Input!

Every piece of data from the user could be malicious:
- A student ID could be: `'; DROP TABLE students; --`
- A feedback message could be: `<script>alert('hacked')</script>`
- A candidate name could be: `<img src=x onerror=steal_cookies()>`

### Defense in Depth:
1. **Validate**: Check format (is this a valid student ID?)
2. **Sanitize**: Clean the data (remove dangerous characters)
3. **Prepared Statements**: Let the database handle escaping
4. **Output Escaping**: Use `htmlspecialchars()` when displaying

---

## 5. Security Headers

HTTP headers that tell the browser extra security rules.
Already added in `.htaccess` (Phase 1), but worth understanding:

| Header | What It Does |
|--------|-------------|
| `X-Content-Type-Options: nosniff` | Prevents MIME-type sniffing attacks |
| `X-Frame-Options: SAMEORIGIN` | Prevents clickjacking (embedding in iframes) |
| `X-XSS-Protection: 1; mode=block` | Enables browser XSS filter |
| `Referrer-Policy: strict-origin` | Controls referrer info leakage |

---

## Files We'll Create/Modify

```
NEW:  app/middleware/rate_limiter.php    ← Login rate limiting
NEW:  app/middleware/input_validator.php ← Input validation helpers
MOD:  public/login.html → login.php     ← Convert to PHP for CSRF
MOD:  public/feedback.php               ← Add CSRF protection
MOD:  app/middleware/authenticate.php    ← Add rate limiting
MOD:  app/services/send_otp.php         ← Add CSRF + rate limiting
```
