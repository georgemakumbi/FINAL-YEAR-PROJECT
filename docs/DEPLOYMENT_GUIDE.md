# 🌐 Deployment Guide: Taking Your Project Online

## Your Goal
Right now your project runs on `localhost` (your computer only).
To access it via the internet, you need to put it on a **web server** —
a computer that's always on and connected to the internet.

---

## Table of Contents

1. [Deployment Options Compared](#1-deployment-options-compared)
2. [Option A: InfinityFree (FREE — Recommended for Students)](#option-a-infinityfree-free)
3. [Option B: Render + PlanetScale (FREE Tier)](#option-b-render--planetscale)
4. [Option C: Shared Hosting (Paid — Most Reliable)](#option-c-shared-hosting-paid)
5. [Pre-Deployment Checklist](#2-pre-deployment-checklist)
6. [Environment Configuration](#3-environment-configuration)
7. [Common Deployment Problems](#4-common-problems)

---

## 1. Deployment Options Compared

| Option | Cost | PHP Support | MySQL | SSL (HTTPS) | Best For |
|--------|------|-------------|-------|-------------|----------|
| **InfinityFree** | Free | ✅ PHP 8+ | ✅ Free MySQL | ✅ Free SSL | 🏆 Student projects |
| **000WebHost** | Free | ✅ PHP 8+ | ✅ Free MySQL | ✅ Free SSL | Student projects |
| **Render** | Free tier | ❌ No native PHP | Via Docker | ✅ | Complex setup |
| **Namecheap Shared** | ~$2/mo | ✅ PHP 8+ | ✅ MySQL | ✅ | Affordable paid |
| **Hostinger** | ~$3/mo | ✅ PHP 8+ | ✅ MySQL | ✅ | Best value paid |
| **DigitalOcean** | $4-6/mo | ✅ (manual) | ✅ (manual) | ✅ (manual) | Full control |

> **My Recommendation:** Start with **InfinityFree** (free) for your defense demo.
> If you want a custom domain and better performance, use **Hostinger** (~$3/mo).

---

## Option A: InfinityFree (FREE)

### Step 1: Create an Account
1. Go to [infinityfree.com](https://www.infinityfree.com)
2. Sign up with your email
3. Click **"Create Account"** to get a free hosting account

### Step 2: Create a Hosting Account
1. Choose a subdomain (e.g., `kyuvoting.infinityfreeapp.com`)
2. Set a password
3. Wait for account to be activated (usually instant)

### Step 3: Create the MySQL Database
1. Go to **Control Panel → MySQL Databases**
2. Create a new database (e.g., `kyuvoting_db`)
3. Note down:
   - **Database Host**: `sqlXXX.infinityfree.com` (shown in panel)
   - **Database Name**: `if0_XXXXXX_kyuvoting_db`
   - **Database User**: `if0_XXXXXX`
   - **Database Password**: (the one you set)

### Step 4: Import Your Database
1. Go to **phpMyAdmin** (link in control panel)
2. Select your database
3. Click **Import** tab
4. Upload your `database/schema.sql` file
5. Click **Go** to import

### Step 5: Upload Your Files
1. Go to **Control Panel → File Manager** (or use FileZilla FTP)
2. Navigate to the `htdocs/` folder
3. Upload your ENTIRE project folder contents:
   ```
   htdocs/
   ├── bootstrap.php
   ├── composer.json
   ├── .env              ← Create this on the server!
   ├── .htaccess
   ├── app/
   ├── assets/
   ├── public/
   ├── views/
   ├── vendor/           ← Must upload this too!
   └── ...
   ```

### Step 6: Configure `.env` on the Server
Create a `.env` file on the server with the hosting credentials:
```env
DB_HOST=sqlXXX.infinityfree.com
DB_USER=if0_XXXXXX
DB_PASS=your_database_password
DB_NAME=if0_XXXXXX_kyuvoting_db

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@gmail.com
SMTP_PASS="your_app_password"
SMTP_FROM=your_email@gmail.com
```

### Step 7: Access Your Site!
Visit: `https://kyuvoting.infinityfreeapp.com/public/index.php`

---

## Option C: Shared Hosting (Paid)

### Recommended: Hostinger (~$3/month)

1. Go to [hostinger.com](https://www.hostinger.com)
2. Choose **"Single Web Hosting"** (~$2.99/mo)
3. Register a domain (or use their free subdomain)
4. In **hPanel**:
   - Create MySQL database
   - Upload files via File Manager or FTP
   - Set up `.env` with database credentials
5. Point your domain's document root to `public/` folder

### Using FTP (FileZilla) to Upload Files:

1. Download [FileZilla](https://filezilla-project.org/)
2. Enter your FTP credentials (from hosting panel):
   - **Host**: `ftp.yourdomain.com`
   - **Username**: your FTP username
   - **Password**: your FTP password
   - **Port**: 21
3. Navigate to `public_html/` on the server
4. Drag your project files from left (local) to right (server)

---

## 2. Pre-Deployment Checklist

Before uploading, make sure you've done these:

### Security
- [ ] `.env` file has production database credentials
- [ ] `.env` is in `.gitignore` (NEVER upload to GitHub!)
- [ ] `.htaccess` blocks access to `.env`, `.git/`, `vendor/`
- [ ] All passwords use `password_hash()` (bcrypt)
- [ ] CSRF tokens are on all forms ✅ (done in Phase 3)
- [ ] Rate limiting is active ✅ (done in Phase 3)

### Database
- [ ] Export your local database: phpMyAdmin → Export → SQL
- [ ] Import on the hosting server's phpMyAdmin
- [ ] Verify all tables were created
- [ ] Create at least one admin account for testing

### Files
- [ ] `vendor/` folder is included (Composer dependencies)
- [ ] All file paths use constants (`PROJECT_ROOT`, `ASSETS_CSS`, etc.)
- [ ] No hardcoded `localhost` URLs in your code

### Test
- [ ] Homepage loads
- [ ] Login works
- [ ] Voting flow works
- [ ] Admin dashboard loads
- [ ] Results page works

---

## 3. Environment Configuration

### Your `.env` file differences:

```env
# ─── LOCAL (development) ─────────────
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=voting_system

# ─── PRODUCTION (server) ─────────────
DB_HOST=sqlXXX.infinityfree.com
DB_USER=if0_XXXXXX
DB_PASS=your_actual_password
DB_NAME=if0_XXXXXX_voting_system
```

### Why `.env` Files Matter:
You keep ONE codebase but different configurations per environment.
The code reads `$_ENV['DB_HOST']` — it doesn't care if it's
`localhost` or `sqlXXX.infinityfree.com`.

---

## 4. Common Problems

### Problem: "500 Internal Server Error"
**Cause**: Usually a PHP error.
**Fix**: 
- Check error logs in your hosting control panel
- Temporarily add to the top of `index.php`:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
- Remove these lines after fixing!

### Problem: "Database connection failed"
**Cause**: Wrong credentials in `.env`
**Fix**: Double-check DB_HOST, DB_USER, DB_PASS, DB_NAME in `.env`

### Problem: "Class not found" or "require failed"
**Cause**: Missing `vendor/` folder
**Fix**: Upload the `vendor/` directory, or run `composer install` via SSH

### Problem: CSS/Images not loading
**Cause**: Path issues
**Fix**: Check that asset paths are relative (e.g., `../assets/css/theme.css`)

### Problem: Sessions not working
**Cause**: Some free hosts have session restrictions
**Fix**: Add to `bootstrap.php`:
```php
ini_set('session.save_path', __DIR__ . '/storage/sessions');
```

---

## Quick Deploy Summary

```
1. Sign up for InfinityFree (free)
2. Create MySQL database
3. Import schema.sql via phpMyAdmin
4. Upload all project files via File Manager
5. Create .env with production database credentials
6. Test: https://your-subdomain.infinityfreeapp.com/public/
7. Done! Share the URL with your panel! 🎉
```

---

## For Your Defense

When your panel asks "Can this system be deployed to production?":

> "Yes. The system uses environment variables (.env) for configuration,
> so switching from localhost to a production server only requires
> updating the database credentials — no code changes needed.
> The application uses prepared statements for SQL injection prevention,
> CSRF tokens for form security, and bcrypt for password hashing,
> making it production-ready. I've tested deployment on [hosting name]
> and it's accessible at [your URL]."
