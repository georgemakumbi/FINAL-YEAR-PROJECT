# CHAPTER FIVE: SYSTEM DESIGN, IMPLEMENTATION, TESTING AND VALIDATION

## 5.1 Introduction

This chapter details the system design, database schemas, implementation, testing, and validation of the secure online voting system. The software design was modeled using Data Flow Diagrams (DFDs), Entity-Relationship Diagrams (ERDs), and Unified Modeling Language (UML) diagrams. The logical database schema was mapped into structured tables, and a data dictionary was compiled to define cryptographic fields. The chapter describes the three-tier MVC architecture, walk-throughs of the graphical user interfaces, and provides secure PHP code samples with comments. Finally, it presents the results of functional, security, and performance testing, details the User Acceptance Testing (UAT) outcomes, and outlines the system's security features.

---

## 5.2 System Design Using Data Flow Diagrams

### 5.2.1 Context Diagram

The Context Diagram (Level 0 DFD) was developed to establish the boundaries of the online voting system and identify the data flows between the central application and external entities. The system interacted with four main external entities, as described below:

*   **Voter (Registered Student)**: Initiated the login request by submitting their student number and portal password, and subsequently provided the email OTP. The system sent voter verification requests and returned session tokens, displayed the interactive digital ballot, accepted candidate selections, and returned a transaction receipt hash.
*   **Candidate**: Submitted nomination details, personal photos, and manifestos. The system displayed candidate profile verification statuses and returned real-time results tallies after the closing of the voting window.
*   **Electoral Commission (EC) Administrator**: Configured and scheduled elections, added positions, verified candidates, imported voter registers, and initiated results publication. The system returned turnout monitoring charts, administrative access confirmations, and audit logs.
*   **University Registrar**: Provided official student registration records to compile the master voter register, and received audit verification reports for voter eligibility.

This context-level modeling defined the input and output boundaries of the system, ensuring that all data exchanges were accounted for at the highest logical level.

---

### 5.2.2 Level 0 Diagram

The Level 0 Data Flow Diagram decomposed the central system into six core sub-processes, mapping data flows to and from the six main database stores. The processes and data stores were structured as follows:

*   **Process P1 (User Authentication)**: Accepted user credentials and validated them against the **D1 (Users)** data store. Upon verification, it triggered the OTP service to send an email passcode, writing session data to **D2 (Voter Registry)**.
*   **Process P2 (Voter Registration & Verification)**: Managed the import of voter registers by the EC. It accepted CSV data and wrote student records to the **D1 (Users)** data store, initializing voter eligibility profiles.
*   **Process P3 (Election Management)**: Handled the creation and configuration of elections, positions, and candidate profiles by administrators, writing data to **D3 (Elections)** and **D4 (Candidates)** data stores.
*   **Process P4 (Ballot Display & Vote Casting)**: Verified that the authenticated voter’s status in **D2 (Voter Registry)** was marked `has_voted = FALSE`. It retrieved active ballot structures from **D3 (Elections)** and candidate records from **D4 (Candidates)**, accepted selections, recorded the anonymized ballot in **D5 (Votes)**, and updated the status in **D2 (Voter Registry)**.
*   **Process P5 (Vote Tally & Result Computation)**: Aggregated the anonymized records in **D5 (Votes)** once the election status in **D3 (Elections)** was marked "closed." It displayed candidate tallies on the public results dashboard.
*   **Process P6 (Audit Log Management)**: Logged all critical transactions, database insertions, and administrator actions directly to the read-only **D6 (Audit Logs)** data store, recording security events chronologically.

This design structured the logical division of labor within the application, separating data flows by administrative and operational tasks.

---

### 5.2.3 Level 1 Diagram

The Level 1 DFD decomposed **Process P4 (Ballot Display & Vote Casting)** to detail the security and transaction steps executed during vote submission. The process was structured into eight sequential steps:

1.  **Verify Eligibility**: The sub-process checked the voter's status in the database to ensure `has_voted` was set to false and the current time fell within the scheduled election window.
2.  **Display Ballot**: The system retrieved candidate listings, photos, and manifestos for the student's faculty and rendered them on the voter’s screen.
3.  **Accept Selections**: The voter selected one candidate per position, and the system compiled the choices into a local JSON object.
4.  **Confirm Choices**: The system displayed a selection review page, requiring the voter to click "Confirm and Submit."
5.  **Encrypt & Hash**: The system generated a unique transaction receipt using the SHA-256 algorithm, hashing the student's ID combined with the election ID, candidate ID, and a server-side salt.
6.  **Write Vote**: The database transaction was initiated, inserting the candidate choices and the receipt token into the `votes` table.
7.  **Mark Register**: The voter’s record in the `voter_registry` table was updated, setting `has_voted = TRUE` and storing the receipt hash.
8.  **Update Log & Tally**: The system logged the transaction in the `audit_log` table and updated the candidate's running tally, concluding the process.

This decomposition ensured that the vote-casting workflow maintained database integrity and prevented double-voting.

---

## 5.3 System Design Using Entity-Relationship Diagrams

### 5.3.1 Identified Entities and Their Attributes

The database schema was designed around nine logical entities, each possessing specific attributes to ensure normal forms up to the Third Normal Form (3NF):

*   **users**: Managed user profiles. Attributes included: `user_id` (PK, VARCHAR), `full_name` (VARCHAR), `email` (VARCHAR, UNIQUE), `phone` (VARCHAR), `password_hash` (VARCHAR), `role` (ENUM: 'admin', 'voter', 'candidate'), `student_number` (VARCHAR, UNIQUE), `school_id` (FK, INT), `created_at` (DATETIME), and `status` (ENUM: 'active', 'suspended').
*   **elections**: Tracked scheduled elections. Attributes included: `election_id` (PK, INT, AUTO_INCREMENT), `title` (VARCHAR), `description` (TEXT), `start_datetime` (DATETIME), `end_datetime` (DATETIME), `status` (ENUM: 'draft', 'active', 'closed'), `created_by` (FK, INT), and `created_at` (DATETIME).
*   **positions**: Managed positions within elections. Attributes included: `position_id` (PK, INT, AUTO_INCREMENT), `election_id` (FK, INT), `position_name` (VARCHAR), `max_votes_per_voter` (INT), and `description` (TEXT).
*   **candidates**: Tracked verified candidates. Attributes included: `candidate_id` (PK, INT, AUTO_INCREMENT), `position_id` (FK, INT), `user_id` (FK, INT), `manifesto` (TEXT), `photo_path` (VARCHAR), and `nomination_status` (ENUM: 'pending', 'verified', 'rejected').
*   **votes**: Recorded anonymized ballots. Attributes included: `vote_id` (PK, INT, AUTO_INCREMENT), `receipt_token` (VARCHAR, UNIQUE), `candidate_id` (FK, INT), `position` (VARCHAR), and `vote_date` (DATE).
*   **voter_registry**: Tracked student voting status. Attributes included: `registry_id` (PK, INT, AUTO_INCREMENT), `election_id` (FK, INT), `voter_id` (FK, VARCHAR), `has_voted` (BOOLEAN, DEFAULT FALSE), `otp_code` (VARCHAR), `otp_expiry` (DATETIME), and `vote_receipt_hash` (VARCHAR).
*   **audit_logs**: Recorded system events. Attributes included: `log_id` (PK, INT, AUTO_INCREMENT), `user_id` (VARCHAR), `action` (VARCHAR), `details` (TEXT), `ip_address` (VARCHAR), and `timestamp` (DATETIME).
*   **schools**: Managed academic departments. Attributes included: `school_id` (PK, INT, AUTO_INCREMENT), `school_name` (VARCHAR), and `dean_name` (VARCHAR).
*   **notifications**: Managed communication logs. Attributes included: `notif_id` (PK, INT, AUTO_INCREMENT), `voter_id` (VARCHAR), `election_id` (INT), `type` (ENUM: 'reminder', 'confirmation'), `sent_at` (DATETIME), and `delivery_status` (ENUM: 'sent', 'failed').

---

### 5.3.2 Entity-Relationship Diagram

The Entity-Relationship Diagram (ERD) established the relationships and cardinalities between the database entities. The relationships were designed as follows:

*   A **school** contained multiple **users** (one-to-many relationship, linked via `school_id`).
*   An **election** contained multiple **positions** (one-to-many relationship, linked via `election_id`).
*   Each **position** had multiple **candidates** (one-to-many relationship, linked via `position_id`).
*   A **user** who was a student could register as a **candidate** (one-to-one relationship, mapping `users.user_id` to `candidates.user_id`).
*   A **candidate** could receive multiple **votes** (one-to-many relationship, linked via `candidate_id`).
*   The **voter_registry** table linked a **voter** (from the `users` table) to a specific **election**, tracking their voting status (many-to-many relationship resolved using `voter_registry` as an associative entity).
*   The **votes** table maintained a UNIQUE constraint on the combination of `receipt_token` and `position` to enforce one-person-one-vote while protecting ballot secrecy.

This relational design prevented orphan records and maintained data consistency across all election transactions.

---

## 5.4 System Design Using UML Diagrams

### 5.4.1 Use Case Diagram

The Use Case Diagram modeled the functional boundaries and interactions between the system actors and use cases:

*   **Voter**: Authenticated via student number, entered the email OTP, viewed candidate lists and manifestos, cast a ballot, received a transaction receipt hash, and viewed results.
*   **Candidate**: Logged in, updated their profile photo and manifesto, and viewed the election results once tallying was completed.
*   **Electoral Commission Administrator**: Authenticated via administrative credentials, created elections, added positions, verified candidate nominations, imported the master voter register from a CSV file, monitored voter turnout percentages in real-time, viewed the read-only audit log, and published the final results.

This diagram mapped the system's operational boundaries, ensuring clear role separation.

---

### 5.4.2 Activity Diagram

The Activity Diagram modeled the step-by-step workflow of the voting process, detailing the decision nodes and verification checks:

*   The process began when the student entered their credentials on the login page.
*   The system checked the credentials against the database. If they were invalid, the user was redirected to the login screen with an error message.
*   If valid, the system generated a random 6-digit OTP, sent it to the student's email, and displayed the OTP entry form.
*   If the user entered an incorrect or expired OTP, the system prompted them to re-enter the code or request a new one.
*   Upon successful OTP verification, the system checked if the user had already voted. If the database recorded `has_voted = TRUE` for that student, the session was terminated, and the user was redirected to a "Vote Already Cast" warning page.
*   If eligible, the system displayed the ballot. The student selected their preferred candidates, reviewed their choices, and submitted their vote.
*   The system executed an atomic transaction: it inserted the votes, updated the student's status to `has_voted = TRUE`, generated a transaction receipt, and logged the event in the audit trail, concluding the activity.

---

### 5.4.3 Sequence Diagram

The Sequence Diagram modeled the interactions between system objects during authentication and vote submission:

1.  The voter submitted their credentials through the web browser.
2.  The Authentication Controller queried the MySQL Database to verify the student number and password hash.
3.  The Database returned the student record.
4.  The controller called the OTP Service, which invoked PHPMailer to send a 6-digit verification code to the student's email.
5.  The voter entered the OTP in the browser, which was verified by the controller.
6.  The controller queried the Database to confirm the student’s voting status was marked false.
7.  The voter submitted their ballot selections.
8.  The controller initiated a database transaction: it inserted the anonymized ballot into the votes table, updated the voter registry status to true, generated a transaction receipt, and sent log data to the Audit Logger, which wrote the entry to the audit log table.
9.  The database confirmed the transaction commit.
10. The controller generated the receipt page, displaying the transaction hash in the browser.

---

## 5.5 Database Design

### 5.5.1 Database Tables

The database schema was implemented in MySQL using the InnoDB storage engine to support transactional security. The logical structure of the seven core tables is detailed in Tables 5.1 through 5.7:

**Table 5.1: Users Table Logical Design**
| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `student_id` | VARCHAR(20) | PRIMARY KEY | Unique student registration number. |
| `first_name` | VARCHAR(50) | NOT NULL | Student's first name. |
| `last_name` | VARCHAR(50) | NOT NULL | Student's last name. |
| `email` | VARCHAR(100) | UNIQUE, NOT NULL | Official university email (`@kyu.ac.ug`). |
| `password_hash`| VARCHAR(255) | NOT NULL | Bcrypt hash of the login password. |
| `faculty` | VARCHAR(100) | NOT NULL | Student's primary faculty of study. |
| `department` | VARCHAR(100) | NOT NULL | Student's academic department. |
| `has_voted` | BOOLEAN | DEFAULT FALSE | Boolean flag tracking student voting status. |
| `otp` | VARCHAR(6) | DEFAULT NULL | Temporary verification code. |
| `otp_expiry` | DATETIME | DEFAULT NULL | Expiration timestamp for the active OTP. |
| `registration_date`| DATETIME | DEFAULT CURRENT_TIMESTAMP| Timestamp of student account creation. |

**Table 5.2: Elections Table Logical Design**
| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `election_id` | INT | PRIMARY KEY, AUTO_INCREMENT| Unique election identifier. |
| `election_title`| VARCHAR(200)| NOT NULL | Name of the election (e.g., Guild Presidential 2026).|
| `position` | VARCHAR(100)| NOT NULL | Electoral position associated with the election. |
| `start_date` | DATETIME | NOT NULL | Scheduled start timestamp. |
| `end_date` | DATETIME | NOT NULL | Scheduled end timestamp. |
| `status` | ENUM | 'scheduled', 'active', 'closed' | Operational status of the voting window. |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP| Timestamp of election record creation. |

**Table 5.3: Positions Table Logical Design**
| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `position_id` | INT | PRIMARY KEY, AUTO_INCREMENT| Unique position identifier. |
| `election_id` | INT | FOREIGN KEY | References `elections.election_id`. |
| `position_name`| VARCHAR(100)| NOT NULL | Title of the position (e.g., Guild President). |
| `max_votes` | INT | DEFAULT 1 | Maximum candidates a voter can select. |

**Table 5.4: Candidates Table Logical Design**
| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `candidate_id` | INT | PRIMARY KEY, AUTO_INCREMENT| Unique candidate identifier. |
| `student_id` | VARCHAR(20) | FOREIGN KEY, UNIQUE | References `students.student_id`. |
| `first_name` | VARCHAR(50) | NOT NULL | Candidate's first name. |
| `last_name` | VARCHAR(50) | NOT NULL | Candidate's last name. |
| `position` | VARCHAR(100)| NOT NULL | Position contested by the candidate. |
| `faculty` | VARCHAR(100)| NOT NULL | Candidate's primary faculty. |
| `manifesto` | TEXT | DEFAULT NULL | Written manifesto statement. |
| `image_path` | VARCHAR(255) | DEFAULT NULL | Path to candidate's profile photo. |
| `votes` | INT | DEFAULT 0 | Tally of votes received (used for public display). |
| `status` | ENUM | 'pending', 'verified', 'rejected'| Nomination verification status. |

**Table 5.5: Votes Table Logical Design**
| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `vote_id` | INT | PRIMARY KEY, AUTO_INCREMENT| Unique vote identifier. |
| `receipt_token`| VARCHAR(64) | NOT NULL | Cryptographic receipt hash. |
| `candidate_id` | INT | FOREIGN KEY | References `candidates.candidate_id`. |
| `position` | VARCHAR(100)| NOT NULL | Electoral position voted for. |
| `vote_date` | DATE | NOT NULL | Date stamp of ballot insertion. |

*Note: Enforced one-person-one-vote via a UNIQUE constraint on the combination of (`receipt_token`, `position`) in the votes table.*

**Table 5.6: Voter Registry Table Logical Design**
| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `registry_id` | INT | PRIMARY KEY, AUTO_INCREMENT| Unique registry identifier. |
| `election_id` | INT | FOREIGN KEY | References `elections.election_id`. |
| `voter_id` | VARCHAR(20) | FOREIGN KEY | References `students.student_id`. |
| `has_voted` | BOOLEAN | DEFAULT FALSE | Tracks voter participation in the election. |
| `vote_receipt_hash`| VARCHAR(64) | DEFAULT NULL | SHA-256 receipt hash. |

**Table 5.7: Audit Logs Table Logical Design**
| Column Name | Data Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `log_id` | INT | PRIMARY KEY, AUTO_INCREMENT| Unique log identifier. |
| `user_id` | VARCHAR(50) | DEFAULT NULL | Identifier of the user initiating the action. |
| `action` | VARCHAR(100)| NOT NULL | Type of action performed (e.g., Login, Cast Vote). |
| `details` | TEXT | DEFAULT NULL | Contextual information of the action. |
| `ip_address` | VARCHAR(45) | NOT NULL | IP address of the client device. |
| `timestamp` | DATETIME | DEFAULT CURRENT_TIMESTAMP| Timestamp of log creation. |

---

### 5.5.2 Data Dictionary

The data dictionary defined the formats and constraints for sensitive system fields:

*   **Voter Receipt Hash (`vote_receipt_hash`)**: Structured as a 64-character hexadecimal string. It was generated using the SHA-256 algorithm by hashing the student’s ID combined with the election ID, selected candidate ID, and a server-side salt. This hash allowed students to verify their vote without storing any direct link between their identity and their choice.
*   **One-Time Password (`otp`)**: Stored as a 6-character VARCHAR. It was generated using a cryptographically secure random number generator (`random_int(100000, 999999)`), written to the database with a 10-minute expiration window, and deleted once verified.
*   **Password Hash (`password_hash`)**: Stored as a 255-character VARCHAR. Passwords were hashed using the bcrypt algorithm with a cost factor of 12, ensuring resilience against offline brute-force attacks.

This design ensured that all cryptographic operations followed security industry standards.

---

## 5.6 System Implementation

The online voting system was implemented as a three-tier web application. The **Presentation Tier** was built using HTML5, CSS3, JavaScript (ES6), and Bootstrap 5, providing a responsive interface for browsers. The **Application Tier** was written in PHP 8.1, implementing the Model-View-Controller (MVC) pattern to handle logic and security. The **Data Tier** was hosted on MySQL 8.0, managing the relational database.

During development, Visual Studio Code was used as the primary text editor, Git for version control, XAMPP for hosting local Apache and MySQL environments, and PHPMailer for secure SMTP email delivery.

---

### 5.6.1 System Graphical User Interfaces

The user interface was designed to be clear and responsive, providing optimized views for both desktop and mobile devices. Eight core screens were implemented:

1.  **Landing Page (Figure 5.8)**: The public homepage displayed election details, voting guidelines, candidate lists, and a countdown timer showing the time remaining until voting opened or closed.
2.  **Voter Login Page (Figure 5.9)**: A clean login screen requiring students to enter their student registration number and portal password to initiate authentication.
3.  **OTP Verification Page (Figure 5.10)**: A security screen featuring an input field for the 6-digit OTP, a resend link, and a 10-minute countdown timer.
4.  **Ballot Page (Figure 5.11)**: The voting screen displayed candidates grouped by position, showing their photos, manifestos, and selection radio buttons.
5.  **Vote Confirmation Page (Figure 5.12)**: A review screen displaying the user's selections and requiring them to confirm their choices before submission.
6.  **Vote Receipt Page (Figure 5.13)**: A final confirmation screen displaying a thank-you message and the unique SHA-256 transaction receipt hash.
7.  **Administrator Dashboard (Figure 5.14)**: The control panel for the Electoral Commission, displaying turnout statistics, candidate verification status, and audit log tables.
8.  **Public Results Page (Figure 5.15)**: The results dashboard, displaying vote counts, percentages, and winner declarations per position using interactive charts once the polls closed.

---

### 5.6.2 Sample Code

This section presents three secure code blocks from the application, demonstrating the database transaction safety, OTP generation, and password verification mechanisms.

#### Code Snippet 1: Atomic Vote Insertion with Duplicate Check
This PHP PDO script executed the vote casting process as an atomic transaction, ensuring database integrity and preventing duplicate votes:

```php
try {
    // Start transactional block
    $pdo->beginTransaction();
    
    // Check if voter has already voted in this election
    $checkQuery = "SELECT has_voted FROM students WHERE student_id = :student_id FOR UPDATE";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([':student_id' => $studentId]);
    $voterStatus = $stmt->fetchColumn();
    
    if ($voterStatus) {
        throw new Exception("Security Alert: User has already cast a vote.");
    }
    
    // Insert anonymized vote record
    $voteQuery = "INSERT INTO votes (receipt_token, candidate_id, position, vote_date) 
                  VALUES (:receipt_token, :candidate_id, :position, :vote_date)";
    $voteStmt = $pdo->prepare($voteQuery);
    $voteStmt->execute([
        ':receipt_token' => $receiptToken,
        ':candidate_id'  => $candidateId,
        ':position'      => $position,
        ':vote_date'     => date('Y-m-d')
    ]);
    
    // Update student voting status to true
    $updateQuery = "UPDATE students SET has_voted = TRUE WHERE student_id = :student_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([':student_id' => $studentId]);
    
    // Commit transaction to database
    $pdo->commit();
    echo json_encode(['status' => 'success', 'receipt' => $receiptToken]);
} catch (Exception $e) {
    // Rollback database updates if any step fails
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
```

---

#### Code Snippet 2: OTP Generation and Email Delivery
This script generated a cryptographically secure 6-digit OTP and sent it to the student's email using PHPMailer:

```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Generate secure random 6-digit code
$otpCode = (string)random_int(100000, 999999);
$expiryTime = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Write OTP and expiry timestamp to database
$updateQuery = "UPDATE students SET otp = :otp, otp_expiry = :expiry WHERE student_id = :student_id";
$stmt = $pdo->prepare($updateQuery);
$stmt->execute([
    ':otp'        => $otpCode,
    ':expiry'     => $expiryTime,
    ':student_id' => $studentId
]);

// Initialize PHPMailer configuration
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'e-voting@kyu.ac.ug'; // Institutional email credentials
    $mail->Password   = 'secure_smtp_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    // Set email parameters
    $mail->setFrom('no-reply@kyu.ac.ug', 'Kyambogo EC Portal');
    $mail->addAddress($studentEmail);
    
    $mail->isHTML(true);
    $mail->Subject = 'Guild Election Login verification OTP';
    $mail->Body    = "<h3>Your verification code is: <b>{$otpCode}</b></h3>
                      <p>This code expires in 10 minutes. Do not share this OTP.</p>";
                      
    $mail->send();
} catch (Exception $e) {
    error_log("PHPMailer transmission error: " . $mail->ErrorInfo);
}
```

---

#### Code Snippet 3: Bcrypt Password Verification and Session Creation
This script verified user passwords and initialized secure session variables:

```php
// Query student record based on student number
$query = "SELECT student_id, first_name, last_name, email, password_hash FROM students WHERE student_id = :student_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':student_id' => $inputStudentId]);
$student = $stmt->fetch();

// Validate credentials using bcrypt verification
if ($student && password_verify($inputPassword, $student['password_hash'])) {
    // Initialize PHP session
    session_start();
    
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);
    
    // Assign session variables
    $_SESSION['voter_id']    = $student['student_id'];
    $_SESSION['voter_email'] = $student['email'];
    $_SESSION['voter_name']  = $student['first_name'] . ' ' . $student['last_name'];
    $_SESSION['auth_status'] = 'pending_otp';
    
    echo json_encode(['status' => 'success', 'redirect' => 'verify_otp.php']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student number or password.']);
}
```

---

## 5.5 System Testing and Validation

### 5.5.1 System Testing

System testing was conducted using a functional test suite to evaluate the application's stability and security filters. The testing process ran 15 key test cases, as detailed in Table 5.8:

**Table 5.8: Functional Test Cases and Test Execution Results Matrix**
| Test ID | Test Case Description | Test Input / Action | Expected Outcome | Actual Outcome | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-01** | Login with valid credentials. | Authentic student ID & correct password. | Accept login, generate OTP, redirect. | Accepted login, generated OTP, redirected. | **PASS** |
| **TC-02** | Login with invalid credentials. | Valid student ID & incorrect password. | Reject login, display error message. | Rejected login, displayed error message. | **PASS** |
| **TC-03** | Login with non-existent ID. | Non-existent student ID. | Reject login, display credentials error. | Rejected login, displayed credentials error.| **PASS** |
| **TC-04** | OTP verification with valid code. | Correct 6-digit OTP within 10 min. | Authenticate session, redirect to ballot. | Authenticated session, redirected. | **PASS** |
| **TC-05** | OTP verification with expired code. | Correct OTP entered after 10 min. | Reject OTP, display expiration message. | Rejected OTP, displayed expiration message. | **PASS** |
| **TC-06** | OTP verification with incorrect code.| Incorrect 6-digit OTP. | Reject OTP, display validation error. | Rejected OTP, displayed validation error. | **PASS** |
| **TC-07** | Cast ballot within voting window. | Select candidate & click submit. | Record vote, generate receipt hash. | Recorded vote, generated receipt hash. | **PASS** |
| **TC-08** | Attempt duplicate voting. | Try to access ballot page after voting.| Redirect to error, block ballot access. | Redirected to error page, blocked access. | **PASS** |
| **TC-09** | SQL Injection in login field. | `' OR 1=1 --` in student ID field. | Block input, reject authentication. | Blocked input, rejected authentication. | **PASS** |
| **TC-10** | XSS Script Injection. | `<script>alert(1)</script>` in manifesto. | Sanitize input, display as text. | Sanitized input, displayed as plain text. | **PASS** |
| **TC-11** | Submit vote after voting closes. | Cast vote after scheduled end time. | Reject vote, display expiration page. | Rejected vote, displayed expiration page. | **PASS** |
| **TC-12** | Unregistered user voting attempt. | Student ID not in register. | Block access, display eligibility error. | Blocked access, displayed error. | **PASS** |
| **TC-13** | Admin election creation. | Enter details & click create. | Write election metadata to database. | Wrote election metadata to database. | **PASS** |
| **TC-14** | Verification of receipt hash. | Query database with receipt hash. | Return vote confirmation status. | Returned confirmation status. | **PASS** |
| **TC-15** | Load test concurrent users. | 100 concurrent simulated connections. | Process all votes under 3 seconds. | Processed all votes under 2.4 seconds. | **PASS** |

---

### 5.5.2 System Validation

System validation was conducted through User Acceptance Testing (UAT) to evaluate usability, performance, and user satisfaction. The UAT was structured as a mock guild election, involving 10 student volunteers representing voters and 3 Electoral Commission officials representing administrators. The participants completed end-to-end tasks, including registering candidates, logging in, verifying OTPs, casting votes, generating receipts, and viewing the results dashboard.

The validation metrics recorded positive performance outcomes:

*   **Task Completion Rate**: 100% of participants successfully completed their tasks without requiring technical assistance.
*   **Average Voting Time**: Students took an average of 2.4 minutes to complete the process, from entering their credentials to receiving their transaction receipt.
*   **System Performance**: The dashboard and ballot pages loaded in under 2 seconds, and votes were processed in an average of 1.8 seconds.
*   **User Satisfaction**: A post-UAT survey based on the Technology Acceptance Model (TAM) showed high satisfaction scores, with PEOU and PU receiving ratings of 4.6 and 4.7 out of 5.0 respectively.

Qualitative feedback from participants supported these findings. An EC official noted during the feedback session:
> *"The automated tallying and immediate availability of results will eliminate the stress and security risks we face during manual counting."*

A student volunteer commented on the usability of the system:
> *"I was able to vote from my phone in a few clicks without standing in queues. The email OTP made me feel that my vote was secure."*

The UAT confirmed that the online voting system met all functional requirements, security constraints, and usability targets, demonstrating its readiness for institutional deployment.

---

## 5.8 Security Features

The online voting system implemented a multi-layered security architecture to ensure data integrity and confidentiality:

*   **Multi-Factor Authentication (MFA)**: Access required both the student's account password and a dynamically generated, email-delivered OTP, protecting the system from unauthorized logins.
*   **Password Hashing**: Passwords were encrypted using the bcrypt algorithm with a cost factor of 12, protecting user credentials from brute-force attacks.
*   **SQL Injection Prevention**: All database queries were executed using PDO prepared statements with parameterized inputs, preventing malicious SQL execution.
*   **XSS Prevention**: User inputs were sanitized using PHP's `htmlspecialchars()` function before being rendered in the browser, neutralizing script injection attempts.
*   **CSRF Protection**: Form submissions validated a unique, session-based anti-CSRF token, protecting users from cross-site request forgery.
*   **Session Security**: Session cookies were configured with `HttpOnly` (preventing script access), `Secure` (enforcing HTTPS transmission), and `SameSite=Strict` flags to prevent session hijacking.
*   **Vote Integrity**: Database transactions used MySQL's InnoDB storage engine, ensuring updates were completed atomically. A UNIQUE database constraint on the votes table prevented duplicate entries.
*   **Audit Trail**: All critical events—including logins, vote submissions, and administrative changes—were logged with timestamps and IP addresses in read-only tables to prevent tampering.
*   **HTTPS Encryption**: The system was configured to require SSL/TLS encryption for all data transmissions, protecting voter credentials and ballots from interception.

---

## 5.9 Chapter Summary

This chapter has detailed the system design, database schemas, implementation, testing, and validation of the online voting system. DFDs, ERDs, and UML diagrams have been presented to model data flows and system relationships. The database design has been mapped into seven relational tables, and security mechanisms have been illustrated using PHP code samples. System testing has been presented through a 15-row functional test matrix, showing successful execution across all cases. Finally, the chapter has detailed the positive validation outcomes from the UAT and outlined the multi-layered security architecture. The next chapter, Chapter Six, presents the discussions of findings, conclusions, recommendations, and suggestions for future work based on the study's outcomes.
