# DESIGN AND IMPLEMENTATION OF A SECURE ONLINE VOTING SYSTEM FOR UNIVERSITY GUILD ELECTIONS: A CASE STUDY OF KYAMBOGO UNIVERSITY, UGANDA

<br>
<br>
<br>
<br>

### BY

### MAKUMBI GEORGE
### REG. NO: 23/U/ITD/07818/PD

<br>
<br>
<br>
<br>

### A RESEARCH REPORT SUBMITTED TO THE DEPARTMENT OF COMPUTER SCIENCE, SCHOOL OF COMPUTING AND INFORMATION SCIENCE IN PARTIAL FULFILMENT OF THE REQUIREMENTS FOR THE AWARD OF THE BACHELOR'S DEGREE IN INFORMATION TECHNOLOGY OF KYAMBOGO UNIVERSITY

<br>
<br>
<br>
<br>

### OCTOBER 2026

---
page: i
---

## DECLARATION

I, MAKUMBI GEORGE, declare that the work presented in this research project/report is my original work and has not been submitted to any University or Institution of Higher Learning for any academic award. All work from other authors has been fully and properly acknowledged and cited.

<br>
<br>

Signature: ............................................................  
Date: ...................................................................  
**MAKUMBI GEORGE**  
(Student)

---
page: ii
---

## APPROVAL

This is to certify that this research project/report titled: **"Design and Implementation of a Secure Online Voting System for University Guild Elections: A Case Study of Kyambogo University, Uganda"** has been carried out under my/our supervision and is now ready for submission to the Examinations Board and Senate of Kyambogo University.

<br>
<br>

Signature: ............................................................  
Date: ...................................................................  
**Dr. Nabukenya Agnes**  
(Main Supervisor)  

<br>
<br>

Signature: ............................................................  
Date: ...................................................................  
**Mr. Okello Patrick**  
(Co-Supervisor)  

---
page: iii
---

## DEDICATION

This research report is dedicated to my beloved parents, whose prayers, encouragement, and tireless financial sacrifices laid the foundation of my education. I also dedicate this work to my siblings for their persistent belief in my potential, to my fellow classmates in the Bachelor of Information Technology program for their collaboration, and to the student community of Kyambogo University for their enthusiasm for democratic and transparent leadership.

---
page: iv
---

## ACKNOWLEDGEMENTS

First and foremost, I render my deepest thanks and praise to the Almighty God for granting me the life, health, wisdom, and resilience required to undertake and complete this academic journey and research project successfully.

I express my profound gratitude to my research supervisors, Dr. Nabukenya Agnes and Mr. Okello Patrick, for their invaluable guidance, scholarly advice, constructive criticisms, and encouragement throughout the processes of conceptualization, design, implementation, and reporting of this system.

I extend special appreciation to the academic and administrative staff of the Department of Computer Science, and the School of Computing and Information Science of Kyambogo University, for equipping me with the core information technology skills and logical foundations that made this development possible.

I am also highly indebted to the Kyambogo University Guild Electoral Commission (EC) officials, student representatives, and all the research respondents who generously spared their time to participate in the interviews and fill out the questionnaires, providing the critical empirical data that justified this system.

Finally, my heartfelt appreciation goes to my family members and dear friends for their unwavering emotional and material support, and to my peer group who engaged in constant constructive arguments and peer-review during the coding and debugging of this web application.

---
page: v
---

## LIST OF ACRONYMS

| Acronym | Full Meaning |
| :--- | :--- |
| **AES** | Advanced Encryption Standard |
| **API** | Application Programming Interface |
| **CSS** | Cascading Style Sheets |
| **DBMS** | Database Management System |
| **DFD** | Data Flow Diagram |
| **EC** | Electoral Commission |
| **ERD** | Entity-Relationship Diagram |
| **GUI** | Graphical User Interface |
| **HTML** | HyperText Markup Language |
| **HTTP** | Hypertext Transfer Protocol |
| **HTTPS** | Hypertext Transfer Protocol Secure |
| **ICT** | Information and Communications Technology |
| **IT** | Information Technology |
| **JS** | JavaScript |
| **MoES** | Ministry of Education and Sports |
| **MySQL** | Structured Query Language (My) |
| **OTP** | One-Time Password |
| **PHP** | Hypertext Preprocessor |
| **RSA** | Rivest-Shamir-Adleman (Cryptosystem) |
| **SDLC** | System Development Life Cycle |
| **SHA** | Secure Hash Algorithm |
| **SMS** | Short Message Service |
| **SQL** | Structured Query Language |
| **SSL** | Secure Sockets Layer |
| **SWOT** | Strengths, Weaknesses, Opportunities, and Threats |
| **TLS** | Transport Layer Security |
| **UAT** | User Acceptance Testing |
| **UCC** | Uganda Communications Commission |
| **UML** | Unified Modeling Language |
| **URA** | Uganda Revenue Authority |
| **UNEB** | Uganda National Examinations Board |
| **XAMPP** | Cross-Platform, Apache, MariaDB, PHP, and Perl |
| **XSS** | Cross-Site Scripting |
| **CSRF** | Cross-Site Request Forgery |
| **MFA** | Multi-Factor Authentication |
| **PDO** | PHP Data Objects |
| **UBOS** | Uganda Bureau of Statistics |
| **NITA-U**| National Information Technology Authority Uganda |
| **TAM** | Technology Acceptance Model |
| **SSADM**| Structured Systems Analysis and Design Method |

---
page: vi
---

## DEFINITION OF TERMS USED

The following terms are operationally defined within the context of this study:

*   **Online Voting System**: A web-based digital platform that enables registered voters to securely cast their votes over the internet using smart devices, such as laptops, smartphones, or tablets, without physical presence at a polling station.
*   **Electronic Voting (e-voting)**: A broad term encompassing all forms of voting that utilize electronic means, including direct-recording electronic (DRE) voting machines, optical scanners, and web-based internet voting.
*   **Ballot Encryption**: The cryptographic process of locking and transforming vote data into an unreadable format before transmission and storage to prevent unauthorized access and ensure ballot confidentiality.
*   **Digital Authentication**: The electronic process of verifying the identity of a system user (such as a voter or administrator) before granting access to system resources or actions.
*   **One-Time Password (OTP)**: A dynamically generated, cryptographically random, numeric or alphanumeric passcode that is valid for a single login session or transaction and expires after a short, predefined duration.
*   **Multi-Factor Authentication (MFA)**: A security system that requires more than one distinct method of authentication to verify a user's identity, specifically combining something the user knows (password) with something the user has access to (OTP sent to registered email/phone).
*   **Audit Trail**: A chronological, tamper-evident log of system activities and events, providing documentary evidence of sequence, actions, and security status to facilitate post-election verification and auditing.
*   **Voter Anonymity**: The structural separation of a voter’s identity from their cast ballot, ensuring that no individual vote can be traced back to the student who cast it.
*   **Ballot Stuffing**: The fraudulent practice of introducing unauthorized, duplicate, or fake votes into the voting system to alter the outcome of an election.
*   **Electoral Commission (EC)**: The administrative body tasked with organizing, supervising, and declaring the results of the student guild elections at Kyambogo University.
*   **Guild Election**: The annual democratic process through which undergraduate and postgraduate students at Kyambogo University elect their student leaders, including the Guild President, Hall Chairpersons, and Members of the Guild Representative Council (GRC).
*   **Role-Based Access Control (RBAC)**: A security mechanism that restricts system access to authorized users based on their specific operational role within the organization, categorized in this system as Super Administrator, Candidate, and Voter.
*   **SQL Injection**: A database vulnerability where malicious SQL commands are injected into data input fields, allowing unauthorized access, manipulation, or destruction of the backend database.
*   **Cross-Site Scripting (XSS)**: A web security vulnerability that allows attackers to inject malicious scripts into trusted websites, which are then executed in the browsers of innocent users.
*   **Voter Turnout**: The percentage of eligible registered students who successfully cast their votes in a given election cycle.

---
page: vii
---

## TABLE OF CONTENTS

*   **DECLARATION** .................................................................................................................................... ii
*   **APPROVAL** .......................................................................................................................................... iii
*   **DEDICATION** ...................................................................................................................................... iv
*   **ACKNOWLEDGEMENTS** ................................................................................................................. v
*   **LIST OF ACRONYMS** ......................................................................................................................... vi
*   **DEFINITION OF TERMS USED** ....................................................................................................... vii
*   **TABLE OF CONTENTS** ....................................................................................................................... viii
*   **LIST OF TABLES** ................................................................................................................................ x
*   **LIST OF FIGURES** .............................................................................................................................. xi
*   **ABSTRACT** ........................................................................................................................................... xii
*   **CHAPTER ONE: INTRODUCTION** ............................................................................................... 1
    *   1.0 Introduction ................................................................................................................................... 1
    *   1.1 Background to the Study ............................................................................................................. 1
    *   1.2 Problem Statement ........................................................................................................................ 3
    *   1.3 Objectives of the Study ................................................................................................................ 4
    *   1.3.1 General Objective .................................................................................................................... 4
    *   1.3.2 Specific Objectives .................................................................................................................. 4
    *   1.4 Research Questions ...................................................................................................................... 5
    *   1.4.1 General Research Question ................................................................................................... 5
    *   1.4.2 Specific Research Questions .................................................................................................. 5
    *   1.5 Scope of the Study ........................................................................................................................ 6
    *   1.5.1 Subject/Content Scope ........................................................................................................... 6
    *   1.5.2 Geographical Scope ................................................................................................................ 6
    *   1.5.3 Time Scope .............................................................................................................................. 6
    *   1.6 Significance of the Study ............................................................................................................. 7
    *   1.7 Chapter Summary ........................................................................................................................ 8
*   **CHAPTER TWO: LITERATURE REVIEW** .................................................................................... 9
    *   2.1 Introduction ................................................................................................................................... 9
    *   2.2 Information Systems in Institutional Governance ................................................................... 9
    *   2.3 Electronic Voting Systems (e-Voting) ....................................................................................... 10
    *   2.4 Online Voting Systems in African Universities ......................................................................... 11
    *   2.5 Manual Voting Systems and Their Documented Limitations ................................................. 13
    *   2.6 Voter Authentication and Identity Verification ......................................................................... 14
    *   2.7 Ballot Secrecy and Vote Encryption ........................................................................................... 15
    *   2.8 Audit Trails and Election Transparency ................................................................................... 17
    *   2.9 Security Threats in Online Voting Systems ................................................................................ 18
    *   2.10 Database Management Systems for Voting Applications ..................................................... 20
    *   2.11 Web Application Frameworks and Technologies .................................................................... 21
    *   2.12 ICT Infrastructure in Ugandan Universities ........................................................................... 22
    *   2.13 Legal and Regulatory Framework for E-Voting in Uganda ................................................... 24
    *   2.14 User Experience and Voter Trust in E-Voting Systems ........................................................... 25
    *   2.15 Existing Systems and Their Limitations .................................................................................... 27
    *   2.16 Research Gaps ............................................................................................................................ 28
    *   2.17 Conceptual Framework .............................................................................................................. 29
    *   2.18 Chapter Summary ........................................................................................................................ 31
*   **CHAPTER THREE: RESEARCH METHODOLOGY** .................................................................... 32
    *   3.1 Introduction ................................................................................................................................... 32
    *   3.2 Research Design ............................................................................................................................. 32
    *   3.3 Population and Sample Selection ............................................................................................... 33
    *   3.3.1 Sampling Strategy ................................................................................................................... 33
    *   3.3.2 Sample Size Determination .................................................................................................... 34
    *   3.4 Research Instrument Design and Testing .................................................................................. 34
    *   3.4.1 Reliability Testing .................................................................................................................. 34
    *   3.4.2 Validity Testing ....................................................................................................................... 35
    *   3.5 Data Collection and Analysis Methods ....................................................................................... 35
    *   3.5.1 Interview Method ................................................................................................................... 35
    *   3.5.2 Questionnaire Method ........................................................................................................... 36
    *   3.5.3 Document Review Method .................................................................................................... 36
    *   3.5.4 Observation Method ............................................................................................................... 37
    *   3.5.5 Data Analysis Methods ........................................................................................................... 37
    *   3.6 Systems Study and Analysis Methods ........................................................................................ 38
    *   3.6.1 Systems Study Methods ......................................................................................................... 38
    *   3.6.2 Systems Analysis Methods ..................................................................................................... 38
    *   3.7 System Requirements and Specification .................................................................................... 39
    *   3.7.1 User Requirements .................................................................................................................. 39
    *   3.7.2 Functional Requirements ....................................................................................................... 39
    *   3.7.3 Non-Functional Requirements .............................................................................................. 41
    *   3.7.4 System Requirements .............................................................................................................. 42
    *   3.8 Systems Design and Modeling Methods .................................................................................... 43
    *   3.8.1 Using Data Flow Diagrams (DFDs) ....................................................................................... 43
    *   3.8.2 Using Entity-Relationship Diagrams (ERDs) ....................................................................... 43
    *   3.8.3 Using Unified Modeling Language (UML) .......................................................................... 43
    *   3.9 System Implementation, Testing and Validation Methods ..................................................... 44
    *   3.9.1 System Implementation Method ........................................................................................... 44
    *   3.9.2 System Testing Method .......................................................................................................... 44
    *   3.9.3 System Validation Method .................................................................................................... 45
    *   3.10 Ethical Considerations ................................................................................................................ 45
    *   3.11 Chapter Summary ........................................................................................................................ 46
*   **CHAPTER FOUR: SYSTEM STUDY, ANALYSIS AND REQUIREMENTS ELICITATION** ... 47
    *   4.1 Introduction ................................................................................................................................... 47
    *   4.2 Description of the Current System ............................................................................................. 47
    *   4.2.1 Strengths of the Current System ............................................................................................ 48
    *   4.2.2 Weaknesses of the Current System ....................................................................................... 49
    *   4.2.3 Comparative Analysis (SWOT) ............................................................................................. 50
    *   4.3 Requirements of the New System .............................................................................................. 52
    *   4.3.1 User Requirements .................................................................................................................. 52
    *   4.3.2 Functional Requirements ....................................................................................................... 52
    *   4.3.3 Non-Functional Requirements .............................................................................................. 54
    *   4.3.4 System Requirements .............................................................................................................. 55
    *   4.4 Feasibility Analysis ....................................................................................................................... 56
    *   4.4.1 Technical Feasibility ................................................................................................................ 56
    *   4.4.2 Economic Feasibility ................................................................................................................ 57
    *   4.4.3 Operational Feasibility ............................................................................................................ 58
    *   4.4.4 Legal Feasibility ....................................................................................................................... 58
    *   4.5 Chapter Summary ........................................................................................................................ 59
*   **CHAPTER FIVE: SYSTEM DESIGN, IMPLEMENTATION, TESTING AND VALIDATION** ... 60
    *   5.1 Introduction ................................................................................................................................... 60
    *   5.2 System Design Using Data Flow Diagrams .............................................................................. 60
    *   5.2.1 Context Diagram ..................................................................................................................... 60
    *   5.2.2 Level 0 Diagram ..................................................................................................................... 61
    *   5.2.3 Level 1 Diagram ..................................................................................................................... 62
    *   5.3 System Design Using Entity-Relationship Diagrams ............................................................... 63
    *   5.3.1 Identified Entities and Their Attributes ................................................................................. 63
    *   5.3.2 Entity-Relationship Diagram ................................................................................................. 65
    *   5.4 System Design Using UML Diagrams ....................................................................................... 66
    *   5.4.1 Use Case Diagram ................................................................................................................... 66
    *   5.4.2 Activity Diagram ..................................................................................................................... 67
    *   5.4.3 Sequence Diagram ................................................................................................................... 68
    *   5.5 Database Design ........................................................................................................................... 69
    *   5.5.1 Database Tables ....................................................................................................................... 69
    *   5.5.2 Data Dictionary ....................................................................................................................... 72
    *   5.6 System Implementation ............................................................................................................... 73
    *   5.6.1 System Graphical User Interfaces .......................................................................................... 74
    *   5.6.2 Sample Code ........................................................................................................................... 76
    *   5.7 System Testing and Validation ................................................................................................... 79
    *   5.7.1 System Testing ........................................................................................................................ 79
    *   5.7.2 System Validation .................................................................................................................... 81
    *   5.8 Security Features .......................................................................................................................... 82
    *   5.9 Chapter Summary ........................................................................................................................ 84
*   **CHAPTER SIX: DISCUSSIONS, CONCLUSIONS AND RECOMMENDATIONS** ................ 85
    *   6.1 Introduction ................................................................................................................................... 85
    *   6.2 Discussion of Findings ................................................................................................................. 85
    *   6.3 Conclusions .................................................................................................................................... 87
    *   6.4 Recommendations ......................................................................................................................... 88
    *   6.5 Future Work ................................................................................................................................... 89
    *   6.6 Chapter Summary ........................................................................................................................ 90
*   **REFERENCES** .................................................................................................................................... 91
*   **APPENDICES** ...................................................................................................................................... 96
    *   Appendix I: User Questionnaire ......................................................................................................... 96
    *   Appendix II: Semi-Structured Interview Guide .............................................................................. 98
    *   Appendix III: User Acceptance Testing (UAT) Scripts .................................................................. 99
    *   Appendix IV: System Installation and Configuration Guide .......................................................... 101

---
page: ix
---

## LIST OF TABLES

*   **Table 3.1: Target Population and Sample Size Breakdown** ......................................................... 34
*   **Table 3.2: Functional Requirements Specification Table** ............................................................ 40
*   **Table 3.3: Non-Functional Requirements Specification Table** .................................................... 41
*   **Table 3.4: Server-Side and Client-Side Hardware Specifications** ............................................. 42
*   **Table 3.5: System Software Environment Specifications** ............................................................ 42
*   **Table 4.1: SWOT Analysis Table for the Current Manual System** ............................................. 51
*   **Table 5.1: Users Table Logical Design** ............................................................................................ 69
*   **Table 5.2: Elections Table Logical Design** ...................................................................................... 70
*   **Table 5.3: Positions Table Logical Design** ...................................................................................... 70
*   **Table 5.4: Candidates Table Logical Design** .................................................................................. 70
*   **Table 5.5: Votes Table Logical Design** ............................................................................................ 71
*   **Table 5.6: Voter Registry Table Logical Design** ............................................................................ 71
*   **Table 5.7: Audit Logs Table Logical Design** .................................................................................. 72
*   **Table 5.8: Functional Test Cases and Test Execution Results Matrix** ...................................... 80

---
page: x
---

## LIST OF FIGURES

*   **Figure 2.1: DeLone and McLean Information Systems Success Conceptual Framework** .......... 30
*   **Figure 2.2: Extended Technology Acceptance Model (TAM) for Online Voting System** ........... 31
*   **Figure 5.1: Context Diagram (Level 0) of the Secure Online Voting System** .......................... 61
*   **Figure 5.2: Level 0 DFD of the Secure Online Voting System** .................................................. 62
*   **Figure 5.3: Level 1 DFD of Process P4 (Ballot Display and Vote Casting)** ............................ 63
*   **Figure 5.4: Complete Entity-Relationship Diagram (ERD) with Database Constraints** ........ 65
*   **Figure 5.5: Use Case Diagram for the Online Voting System** ................................................... 66
*   **Figure 5.6: Activity Diagram showing the Vote Casting and Verification Flow** ....................... 68
*   **Figure 5.7: Sequence Diagram of OTP-authenticated Login and Vote Submission** ................ 69
*   **Figure 5.8: Web Interface - Public Landing Page and General Information** ............................ 74
*   **Figure 5.9: Web Interface - Student Secure Login Portal** ........................................................... 75
*   **Figure 5.10: Web Interface - Email OTP Verification Prompt** ..................................................... 75
*   **Figure 5.11: Web Interface - Interactive Digital Ballot Page** ........................................................ 76
*   **Figure 5.12: Web Interface - Pre-Submission Selection Review Screen** ..................................... 76
*   **Figure 5.13: Web Interface - Final Vote Receipt and Transaction Hash** .................................. 76
*   **Figure 5.14: Web Interface - Electoral Commission Administrator Control Center** .................. 76
*   **Figure 5.15: Web Interface - Public Real-Time Election Results Dashboard** ........................... 76

---
page: xi
---

## ABSTRACT

The administration of student guild elections at Kyambogo University has historically relied on a manual paper-based ballot system. This traditional approach is increasingly constrained by systemic vulnerabilities, including high administrative expenditures, prolonged periods of manual tallying, vulnerability to ballot-stuffing, disenfranchisement of students away on off-campus internships, and frequent election disputes that disrupt the university's academic calendar. The main objective of this study was to design, implement, and evaluate a secure web-based online voting system for university guild elections at Kyambogo University, aimed at enhancing election integrity, maximizing voter participation, reducing operational costs, and providing transparent, real-time results.

The research employed a descriptive case study design combining quantitative and qualitative methodologies. A sample of 75 participants—including students, student candidates, university administrators, and members of the Guild Electoral Commission—was selected using a combination of stratified, simple random, and purposive sampling techniques. Data collection was carried out through questionnaires, semi-structured interviews, document reviews of past election records (2019–2024), and direct observation of electoral operations. Systems analysis and requirements elicitation were guided by the Structured Systems Analysis and Design Method (SSADM), which helped define fifteen core functional requirements and eight non-functional security and performance constraints.

The developed system is a three-tier web application built using PHP, MySQL, HTML5, CSS3, JavaScript (ES6), and Bootstrap 5, hosted on a local Apache server environment configured using XAMPP. Security was addressed at both the application and database levels: multi-factor authentication (MFA) was enforced by combining student credential validation with a cryptographically generated, email-delivered One-Time Password (OTP) using PHPMailer; ballot secrecy was secured through hashing; database transactions were executed atomically using MySQL InnoDB engine to ensure integrity; and SQL injection, Cross-Site Scripting (XSS), and Cross-Site Request Forgery (CSRF) were prevented using parameterized queries, output sanitization, and anti-CSRF tokens respectively. Functional testing using a test case suite confirmed that the system successfully validated authentic voter attempts, blocked duplicate voting through a UNIQUE constraint on the vote records, and updated tallies immediately upon ballot closure.

Based on the research findings, it is recommended that Kyambogo University transitions from the manual paper ballot system to this secure online voting system to permanently eliminate manual tallying errors and election fraud. The Guild Electoral Commission should establish a robust ICT governance framework to oversee system administration, and the University Senate should institute policy and constitutional reforms that officially recognize electronic voting. Furthermore, continuous training programs should be organized for both students and election administrators to ensure operational familiarity, and the system should eventually be integrated with the Central Academic Information Management System (ACIMS) for automated voter register synchronization.
