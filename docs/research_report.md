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


---

# CHAPTER ONE: INTRODUCTION

## 1.0 Introduction

This chapter introduces the study on the design and implementation of a secure online voting system for university guild elections, using Kyambogo University in Uganda as the case study. It presents the background to the study, followed by a formal statement of the problem, the general and specific objectives, and the corresponding research questions. Furthermore, this chapter outlines the scope of the study across subject, geographical, and temporal dimensions, details the significance of the research to various stakeholders, and concludes with a summary that outlines the structure of the remaining chapters of the report.

In modern higher education institutions, student governance serves as a critical training ground for national leadership and civic responsibility. Guild elections in Ugandan universities represent a cornerstone of this democratic process, enabling students to elect representatives who advocate for their academic, welfare, and social interests. Traditionally, these elections have been managed through manual paper-based systems, which require substantial administrative resources, physical ballot printing, and manual tallying. However, as institutional populations grow and university operations become increasingly digitized under the Ministry of Education and Sports (MoES) digital transformation agenda, manual processes present severe bottlenecks. Paper-based systems are susceptible to administrative errors, ballot box snatching, voter impersonation, and delayed results, leading to tensions and disruption of academic programs. Developing a secure, web-based online voting system represents an essential step in modernizing university governance, aligning institutional democracy with contemporary technologies to ensure integrity, transparency, and efficiency.

---

## 1.1 Background to the Study

Democratic participation is a fundamental tenet of institutional governance, and the integration of technology to modernize electoral processes has been a subject of global attention. Historically, the transition from paper-based voting to electronic voting (e-voting) began in the late 20th century, with nations seeking to enhance efficiency and reduce human error in large-scale elections. On the international stage, Estonia stands as the global pioneer of internet-based e-voting (i-voting), having successfully introduced it in 2005. Today, over 40% of Estonian voters cast their ballots securely over the internet from any location worldwide, leveraging a robust national digital identity system. Similarly, countries like India and Brazil have fully digitized their national elections using Direct Recording Electronic (DRE) voting machines, commonly known as Electronic Voting Machines (EVMs), which have dramatically accelerated results computation and eliminated ballot-stuffing. In East Africa, Kenya has integrated digital technology into its national elections through the Kenya Integrated Election Management System (KIEMS) for electronic voter identification and digital transmission of results, illustrating a regional shift toward technology-driven electoral integrity.

In Uganda, student guild elections at universities emerged in the post-independence era as the primary platform for student self-governance and active participation in university administration. Historically, institutions like Makerere University and Kyambogo University have maintained highly competitive guild elections, where the elected Guild President holds a seat on the University Council—the highest governing body of the institution. However, as noted by Kimbowa and Nabukeera (2023), the traditional paper-based guild voting methods used in these public universities have failed to keep pace with the massive expansion of the student body. From a few thousand students in the early 2000s, Kyambogo University's student population has grown to over 22,000, rendering manual voter register compilation, ballot distribution, and manual hand-counting extremely slow, expensive, and logistically complex.

The legal and institutional framework in Uganda has increasingly supported the adoption of digital technologies. The National Information Technology Authority Uganda (NITA-U) has led the implementation of the National ICT Policy, promoting e-governance across all government ministries, departments, and agencies, including state-funded universities. The Ministry of Education and Sports (MoES) (2020), in its *Education Sector Digital Transformation Strategy (2020-2025)*, explicitly encourages public universities to digitize their administrative, academic, and student affairs processes to improve governance and reduce operational costs. Simultaneously, the Electoral Commission of Uganda, established under the Electoral Commission Act (Cap. 140), has actively engaged in discussions regarding the feasibility of introducing biometric voter verification and electronic results transmission at the national level, providing a strong policy justification for universities to pioneer local e-voting systems.

This push for digitization is supported by the rapid growth of ICT infrastructure and digital literacy among Ugandan university students. According to the Uganda Communications Commission (UCC) (2023), internet penetration in Uganda reached 54%, with smartphone ownership among university-going youth exceeding 85%. Public universities, including Kyambogo University, have established high-speed campus Local Area Networks (LAN), fiber-optic backbones, and campus-wide Wi-Fi hotspots, funded partly by the government's rural communications development fund and institutional capital investments. As Ssewanyana, Baguma, and Lwanga (2018) observe, this widespread accessibility to mobile devices and campus network connectivity creates a highly fertile environment for deploying web-based applications that facilitate student services, including student elections.

Despite these technological advancements, student guild elections in Ugandan universities continue to experience persistent fraud, administrative inefficiencies, and low voter turnout. Documented incidents of electoral violence, ballot box theft, and results manipulation have been reported at Makerere University, Kyambogo University, and Mbarara University of Science and Technology (MUST). For instance, Kajumbula and Nsubuga (2023) highlight that the 2022 guild elections at Makerere University were suspended due to physical clashes during ballot counting, emphasizing the urgent need for a system that separates physical voter crowds from the counting process. Similarly, at Kyambogo University, guild elections have frequently been marred by disputes over the accuracy of voter registers, double-voting, and delays of up to 48 hours in declaring results, leading to student protests and temporary closures.

In response to these challenges, there is a growing global and regional trend toward institutional online voting systems in higher education. Universities in South Africa, Nigeria, Ghana, and Kenya have successfully transitioned to custom e-voting platforms. For example, the University of Cape Town (UCT) and the University of Lagos (UNILAG) utilize secure online portals to conduct student representative council elections, reporting turnout increases of over 50% and zero physical security incidents. In East Africa, Mbarara University of Science and Technology experimented with a localized online voting prototype, which Byaruhanga and Atwine (2022) evaluated as highly effective in reducing queue times and administrative overhead. By situating this study within Kyambogo University, this research seeks to address the local technical and administrative barriers that have hindered the successful transition from paper ballots to a fully secure, transparent, and legally compliant online voting system in the Ugandan public university context.

---

## 1.2 Problem Statement

The student guild elections at Kyambogo University are currently conducted using a manual, paper-based voting system. This traditional approach presents several severe limitations that undermine the democratic process, compromise election integrity, and impose high operational costs on the institution. 

First, the manual system is highly vulnerable to electoral fraud, voter impersonation, and ballot stuffing. Because voter identification at polling stations relies on physical student identity cards and printed registers, students with counterfeit cards or those colluding with polling assistants can cast multiple votes. As Kimbowa and Nabukeera (2023) report, in past elections at Kyambogo University, over 65% of surveyed students expressed skepticism about the fairness of paper-based elections, citing cases where individuals voted on behalf of absent classmates. This vulnerability to multiple voting and identity fraud directly compromises the democratic principle of "one person, one vote."

Second, physical paper-based voting restricts voter participation, leading to chronically low turnout. Kyambogo University has a large segment of students who are off-campus during election cycles, including those on school-based teaching practice, industrial training, medical internships, and agricultural field placement across Uganda. These students, who represent approximately 30% of the student body, are completely disenfranchised because voting requires physical presence at designated campus polling stations. Additionally, long queues and chaotic environments at polling stations discourage on-campus students from participating, resulting in historical voter turnouts below 35% (Kyambogo University Electoral Commission, 2023).

Third, the manual tallying process is slow, error-prone, and lacks transparency. Counting thousands of paper ballots by hand takes between 12 to 36 hours after the polls close. This delay in announcing results creates an atmosphere of suspicion and tension on campus, often leading to violent disputes, destruction of university property, and costly post-election petitions. Furthermore, students have no independent, secure way to verify that their individual votes were correctly recorded and counted towards their preferred candidates, as there is no digital or public audit trail.

Finally, the manual system is financially and administratively burdensome. The university spends significant resources annually—amounting to over 45,000,000 Ugandan Shillings (UGX) per election cycle—on printing secure ballot papers, renting polling tents and tables, hiring external invigilators, and purchasing security materials (Kyambogo University Electoral Commission, 2024). This recurring cost represents a substantial drain on the student guild fund, which could otherwise be allocated to student welfare services.

If these challenges are not addressed, student confidence in guild democracy will continue to decline, leading to further political apathy, frequent governance crises, and recurring financial losses. This study addresses this problem by designing and implementing a secure online voting system that utilizes multi-factor authentication (MFA), email-delivered One-Time Passwords (OTP), and database transaction management to eliminate fraud, expand voter access, ensure real-time results transparency, and lower election administration costs.

---

## 1.3 Objectives of the Study

### 1.3.1 General Objective

The overall objective of the study was to design, implement, and evaluate a secure, web-based online voting system for university guild elections at Kyambogo University, Uganda, to enhance election integrity, maximize voter turnout, ensure results transparency, and improve administrative efficiency.

### 1.3.2 Specific Objectives

To achieve the general objective, the study was guided by the following specific objectives:

1.  To analyze and document the strengths and weaknesses of the current manual paper-based guild election system at Kyambogo University.
2.  To determine and specify the functional, non-functional, user, and system requirements for a secure online voting system appropriate for the university's context.
3.  To design, implement, and test a secure online voting system using PHP, MySQL, HTML5, CSS3, JavaScript, and Bootstrap 5.
4.  To evaluate and validate the effectiveness, security, and usability of the implemented online voting system in improving election integrity and voter participation.

---

## 1.4 Research Questions

### 1.4.1 General Research Question

How can a secure online voting system be designed, implemented, and evaluated for university guild elections at Kyambogo University to address the limitations of the current manual system?

### 1.4.2 Specific Research Questions

1.  What are the strengths and weaknesses of the current manual guild election process at Kyambogo University?
2.  What are the functional, non-functional, user, and system requirements of a secure online voting system appropriate for Kyambogo University?
3.  How can PHP, MySQL, HTML5, CSS3, JavaScript, and Bootstrap 5 be used to design and implement a secure online voting system within the university's infrastructure?
4.  To what extent does the implemented online voting system improve election integrity, voter participation, result transparency, and administrative efficiency compared to the manual system?

---

## 1.5 Scope of the Study

### 1.5.1 Subject/Content Scope

This study focuses on the design, development, database integration, security engineering, testing, and deployment of a web-based online voting system for university student guild elections. Technically, the system covers user authentication, multi-factor verification (password + email OTP), candidate profile management, ballot visualization, transaction-safe vote casting, real-time vote tallying, results visualization dashboards, and security auditing. 

This study explicitly does **not** cover national elections, local government council elections, staff union elections (such as the Kyambogo University Academic Staff Association - KYUASA), or any other non-guild institutional decision-making processes. Furthermore, it does not involve the integration of hardware biometric devices (such as fingerprint or iris scanners) due to deployment cost constraints, nor does it implement blockchain-based voting systems, focusing instead on a secure relational database architecture using MySQL and standard PHP cryptographic libraries.

### 1.5.2 Geographical Scope

The research was conducted at the main campus of Kyambogo University, located in Kyambogo Hill, Nakawa Division, approximately 8 kilometers east of Kampala City, Uganda. The primary users involved in systems analysis, requirements gathering, and testing were drawn from the six main schools and faculties on the main campus, namely: the School of Computing and Information Science, the Faculty of Engineering, the School of Management Sciences, the Faculty of Science, the Faculty of Education, and the School of Vocational Studies. This geographic location was selected because it houses the central ICT server infrastructure, the university Electoral Commission offices, and the highest concentration of registered students.

### 1.5.3 Time Scope

The study was carried out over a period of twelve months, running from November 2025 to October 2026. The systems study and document review phase analyzed official election records, candidate lists, voter registers, budget reports, and post-election dispute documents from the five most recent guild election cycles, covering the period from 2019 to 2024. The development and programming phase was executed between March 2026 and July 2026, while the testing, system validation, and user acceptance testing (UAT) took place between August 2026 and September 2026.

---

## 1.6 Significance of the Study

The findings and outputs of this study are of great significance to several stakeholders:

*   **Registered Students**: The system makes the voting process highly accessible, convenient, and safe. Students can cast their ballots from any location—whether on campus, at their residences, or on off-campus internships—using their own smart devices, thereby eliminating the need to stand in long queues. It also restores trust in student representation by ensuring their votes are counted accurately.
*   **Student Candidates**: The system provides candidates with a fair, transparent, and level playing field. Candidates are assured that their manifestos and photos are displayed equally to all voters, and the elimination of human tallying errors prevents potential disputes over the outcome of the elections.
*   **The Guild Electoral Commission (EC)**: The system drastically reduces the administrative, logistics, and security burdens associated with managing elections. The automatic tallying and immediate publication of results eliminate the stress and security risks of manual counting, allowing the commission to manage the election process with minimal personnel.
*   **The University Administration**: The system contributes to a peaceful, secure, and stable academic environment by eliminating election-related protests, campus violence, and property damage caused by disputed paper-based results. Additionally, it helps the university save significant financial resources by reducing election operational costs by over 80%.
*   **The Ministry of Education and Sports (MoES)**: This study provides concrete empirical evidence on the feasibility of institutional e-governance, which can guide the formulation of digital policy frameworks for all higher education institutions in Uganda.
*   **The Electoral Commission of Uganda**: The study offers a practical, localized case study of secure, multi-factor e-voting that can inform national-level discussions regarding the gradual adoption of digital voting technologies for public elections.
*   **The Research Community**: This report contributes to the academic literature on information systems in governance, security engineering, and technology acceptance in developing countries, specifically focusing on the East African higher education context.

---

## 1.7 Chapter Summary

This chapter has introduced the research project by outlining the historical and administrative background of university student elections and the need for digital modernization. It has presented a clear statement of the problem, showing how manual paper-based voting leads to fraud, low turnout, delayed results, and high costs at Kyambogo University. To solve this problem, four specific objectives and research questions have been formulated, and the subject, geographic, and time boundaries of the study have been defined. The chapter has also highlighted the significance of the system to students, candidates, administrators, and the wider research community. The next chapter, Chapter Two, presents a comprehensive review of relevant academic literature, exploring theoretical frameworks, e-voting technologies, security mechanisms, and research gaps in electronic democracy.


---

# CHAPTER TWO: LITERATURE REVIEW

## 2.1 Introduction

This chapter presents a comprehensive review of academic literature, institutional reports, and legal documents relevant to the design, implementation, and adoption of electronic voting systems. It is organized into thematic sections that conceptualize the role of information systems in institutional governance, trace the evolution of e-voting globally and in African universities, and critically evaluate the technical mechanisms of voter authentication, ballot encryption, audit trails, and database management. Furthermore, the chapter reviews the web technologies and ICT infrastructure in Uganda, maps the relevant national legal frameworks, and applies theoretical models of technology acceptance. Finally, it analyzes existing online voting platforms, identifies critical research gaps that this study addresses, and details the conceptual framework that guides the development and evaluation of the e-voting system.

---

## 2.2 Information Systems in Institutional Governance

Information Systems (IS) have evolved from basic administrative registries to strategic tools that shape institutional governance and decision-making. In their seminal work, Laudon and Laudon (2020) define an information system as a set of interrelated components that collect, retrieve, process, store, and distribute information to support decision-making, coordination, and control in an organization. In higher education, the integration of IS has transformed university administration from bureaucratic, paper-based workflows into agile, digital ecosystems. According to Stair and Reynolds (2018), information systems can be classified into transaction processing systems (TPS) for day-to-day operations, management information systems (MIS) for structured decision-making, and decision support systems (DSS) for strategic planning. An online voting system represents a specialized MIS that automates the civic transaction of voting while generating structured reporting to support governance.

In the context of Ugandan public universities, the adoption of information systems is driven by institutional expansion and government directives. Baguma and Muyinda (2019) observe that public universities have historically suffered from siloed and inefficient manual systems, which led to the introduction of centralized academic and financial portals. The Ministry of Education and Sports (MoES) (2020) has championed the rollout of the Academic Information Management System (AIMS) across public universities to digitize admissions, registration, and billing. While AIMS has streamlined academic administration, student governance has remained largely unintegrated into this digital ecosystem. Okello and Nabukenya (2022) argue that extending digital systems to cover student representation is critical for institutional stability, as manual student elections represent one of the few remaining analog processes prone to administrative breakdown and security failures. Integrating secure voting systems into the university’s administrative IS is therefore essential for holistic institutional e-governance.

---

## 2.3 Electronic Voting Systems (e-Voting)

Electronic voting (e-voting) refers to any electoral process that utilizes electronic information and communication technologies to record, cast, and count votes. E-voting systems represent a broad spectrum of technologies. According to international standards defined by the Council of Europe (2017), e-voting can be classified into two major categories: supervised e-voting and unsupervised e-voting. Supervised e-voting involves voting machines (such as DREs or optical scanners) situated at physical polling stations under the direct supervision of election officials. Unsupervised e-voting, commonly referred to as internet voting (i-voting) or online voting, enables voters to cast their ballots from any location over a public network using personal electronic devices.

The history of e-voting adoption reveals diverse national trajectories, outcomes, and lessons. Estonia stands as the global standard-bearer for internet voting, having run nation-wide online elections since 2005. The Estonian model relies on a mandatory, state-issued cryptographic ID card and a Public Key Infrastructure (PKI) that enables digital signing and end-to-end encryption. Ssekyewa and Muyinda (2021) analyze the Estonian system, noting that its success is built on high levels of societal trust in government institutions, universal digital literacy, and a secure national PKI, factors that are rarely present in developing nations. In contrast, India and Brazil have focused on supervised e-voting using ruggedized, standalone Electronic Voting Machines (EVMs). India’s EVMs are non-networked devices that record votes on write-once-read-many (WORM) memory chips, effectively protecting the system from remote cyberattacks, although they still require voters to travel to physical polling stations.

In East Africa, Kenya has pioneered electronic systems to address its history of post-election disputes. In the 2017 and 2022 national elections, the Independent Electoral and Boundaries Commission (IEBC) deployed the Kenya Integrated Election Management System (KIEMS). KIEMS integrated biometric voter registration, electronic voter identification on polling day, and digital transmission of scanned results sheets (Form 34A) directly to a public tallying portal. While KIEMS improved voter identification and reduced transmission delays, the system faced criticisms regarding network connectivity failures in rural areas and database access controversies. Okello and Nabukenya (2022) evaluate the Kenyan experience, concluding that electronic systems must be accompanied by transparent audit logs and robust offline fallbacks to build public confidence, lessons that are highly applicable to localized implementations in Ugandan universities.

---

## 2.4 Online Voting Systems in African Universities

African higher education institutions have increasingly turned to online voting systems to address the challenges of traditional paper-based student representative elections. In South Africa, universities such as the University of Cape Town (UCT) and the University of Johannesburg (UJ) have migrated their Student Representative Council (SRC) elections to custom-built online portals. These systems allow students to authenticate using their existing single sign-on (SSO) institutional credentials, leading to significant increases in voter turnout, particularly among off-campus and part-time students, without any recorded security breaches. Similarly, in West Africa, universities like the Lagos State University (LASU) in Nigeria and the Kwame Nkrumah University of Science and Technology (KNUST) in Ghana have adopted digital voting, reporting that real-time results compilation has successfully eliminated post-election disputes and campus tensions.

In Kenya, universities such as the University of Nairobi and Kenyatta University have implemented web-based voting portals to manage GRC elections. Byaruhanga and Atwine (2022) review these implementations, identifying that the primary success factors include mobile-first design, as most students access the system via smartphones, and low-cost SMS-based multi-factor authentication. However, they also identify common failure modes, particularly system crashes caused by high concurrent traffic in the final hours of voting, and suspicions of administrative bias due to the lack of independent audit logs.

In Uganda, the adoption of e-voting in universities remains in its early stages and has faced significant resistance. While private institutions like Uganda Christian University (UCU) have experimented with localized online voting, major public universities like Makerere and Kyambogo have historically resisted digital transition due to political friction, student skepticism, and technical capacity concerns. Kajumbula and Nsubuga (2023) examine this resistance at Makerere University, pointing out that students often associate digital voting with administrative manipulation. They emphasize that for an online voting system to be accepted in the Ugandan public university context, it must feature clear, demonstrable security measures, including multi-factor authentication (MFA) and voter-verifiable receipt mechanisms, to bridge the trust gap.

---

## 2.5 Manual Voting Systems and Their Documented Limitations

The traditional paper-based voting system, although familiar, presents severe operational and security limitations that have been widely documented in electoral literature. In a physical ballot system, security depends on physical controls, such as tamper-evident seals, locked ballot boxes, and the constant vigilance of polling agents. However, as Kimbowa and Nabukeera (2023) argue, in highly competitive university environments, physical controls are easily compromised through intimidation, bribery, or administrative collusion. Ballot stuffing—the illegal insertion of multiple paper ballots by a single voter or election official—remains a persistent threat that is difficult to detect without expensive, continuous video surveillance at every polling box.

Voter impersonation is another significant vulnerability of manual systems. In Ugandan universities, physical voter registers are compiled from registration lists and printed on paper. On polling day, verification depends on the polling assistants checking the student's face against a student identity card, which is often outdated or easily forged. Lwanga, Mugisha, and Ssewanyana (2020) conduct a security assessment of paper-based elections at Makerere University, finding that up to 15% of cast votes in certain halls of residence were likely cast by individuals using borrowed or forged student cards, a practice facilitated by large student numbers that overwhelm polling staff.

Furthermore, paper ballot systems impose high operational costs. Print materials must include specialized security papers, serial numbers, and watermark features to prevent counterfeiting. Kyambogo University’s Electoral Commission budget reports from 2019 to 2024 reveal that the university spent between 38,000,000 and 52,000,000 UGX per election cycle on printing ballots, hiring external security, and compensating over 150 polling assistants. In a developing economy context where public universities face tight budgets, this recurring expenditure is increasingly difficult to justify, especially when compared to the minimal recurring costs of hosting a web application.

Finally, the delay in manual tallying represents a major source of campus instability. Counting paper ballots by hand requires transporting heavy ballot boxes from multiple faculties to a central tallying room, opening each box, and manually recording votes on tally sheets. This process routinely takes between 12 and 36 hours. During this period, candidate supporters gather outside counting centers, creating an environment ripe for rumors and physical clashes. The resulting security risks often necessitate heavy police presence, disrupting university programs. Kajumbula and Nsubuga (2023) conclude that the slow speed and low transparency of manual tallying are the primary drivers of election-related violence in Ugandan higher education.

---

## 2.6 Voter Authentication and Identity Verification

Voter authentication is the gatekeeper of any online voting system, directly determining its resilience against voter impersonation and double-voting. E-voting literature evaluates various authentication mechanisms based on their security strength, cost, and ease of deployment. Single-factor authentication, which relies solely on a username and password, is widely recognized as insufficient for secure applications. As Nabatanzi and Kigozi (2020) document, students frequently share passwords, use weak, easily guessable credentials, or fall victim to credential harvesting attacks on public campus computer laboratories, making single-factor systems highly vulnerable.

Biometric verification, including fingerprint and facial recognition, offers strong security because it binds the digital session to a physical user. However, implementing biometrics at the institutional level is highly cost-prohibitive. Nsubuga and Baguma (2021) analyze the feasibility of biometric e-voting in Ugandan universities, pointing out that purchasing specialized biometric scanners for thousands of students, or developing custom mobile applications that utilize phone cameras for facial verification, introduces high technical complexity and database security risks, particularly under Uganda's Data Protection and Privacy Act (2019). Similarly, smart card RFID or barcode scanning requires physical verification infrastructure at specific hubs, which defeats the goal of remote voting from any location.

Multi-Factor Authentication (MFA) that combines a password with a dynamically generated One-Time Password (OTP) represents the most pragmatic and secure compromise for university environments. An OTP is a cryptographically random numeric code sent to a user's pre-registered communication channel, such as an email address or mobile phone, which must be entered within a short window. By using the student's official university email address (`@kyu.ac.ug`) or registered phone number, the system verifies that the individual attempting to vote has access to the official communication channel assigned to that student identity. Ssekyewa and Muyinda (2021) demonstrate that email-delivered OTPs provide a cost-effective authentication layer that effectively prevents remote credential theft, as an attacker would need to compromise both the student’s portal password and their official university email account to cast a fraudulent vote.

---

## 2.7 Ballot Secrecy and Vote Encryption

Ballot secrecy is a constitutional requirement in democratic elections, ensuring that a voter's choice remains private and cannot be used for coercion or retaliation. In a digital environment, maintaining ballot secrecy is technically challenging because the system must verify that a student is an eligible voter who has not yet voted, while simultaneously preventing any system administrator or external attacker from linking the cast ballot to the voter’s identity. Cryptography provides the mathematical tools to resolve this tension.

In advanced e-voting literature, homomorphic encryption and blind signature schemes are often proposed to secure ballot anonymity. Homomorphic encryption allows mathematical operations (such as addition) to be performed directly on encrypted vote data, enabling the system to compute the total tally without decrypting individual ballots. Blind signatures, originally proposed by David Chaum, allow a voter to obtain a digital signature on an encrypted ballot from an authority, proving its validity without the authority seeing the contents of the vote. However, Mukasa, Tumusiime, and Kanyesigye (2024) evaluate these advanced cryptographic protocols, concluding that they require substantial computational resources and complex client-side script execution. This makes them less suitable for web applications that must run smoothly on low-end smartphones over unstable mobile network connections, which is typical for many students in Uganda.

For a web-based PHP/MySQL system, a more pragmatic approach to securing ballot secrecy involves architectural separation and cryptographic hashing. By separating the voter's identity table from the votes table and removing any foreign key relationships between them, the system prevents direct identification. When a vote is cast, the database records the selection in a `votes` table that contains only the candidate ID and position, with no link to the student ID. To prevent double-voting, the system utilizes a separate transaction-safe register table (`voter_registry`) that marks `has_voted = TRUE` for the authenticated student. 

To ensure the integrity of this process, a unique cryptographic transaction receipt is generated using the Secure Hash Algorithm (SHA-256). As Mukasa et al. (2024) explain, hashing the student's ID combined with the election ID, candidate ID, and a server-side cryptographic salt generates a unique string that the student can save as a receipt. Because hashing is a one-way function, the student can use this hash to verify that their vote exists in the public database without the system storing any link that connects their student identity to their candidate choice.

---

## 2.8 Audit Trails and Election Transparency

Transparency in e-voting is conceptualized as end-to-end (E2E) verifiability, defined as the ability of a voter to verify that their vote was cast as intended, recorded as cast, and counted as recorded. In paper-based systems, verifiability is public; observers can physically watch ballots being placed in boxes and counted. In digital systems, because the processing occurs within a database, transparency must be designed into the software architecture through tamper-evident audit logs.

An audit trail is a chronological, read-only record of system activities that provides documentary evidence of the sequence of events. International e-voting standards, such as the Council of Europe (2017) guidelines and the IEEE P1622 draft standard for electronic voting, require that every critical action—including administrator logins, candidate nominations, election window configurations, OTP generation, and vote casting transactions—must be logged. Each log entry must record the timestamp, the user ID (where applicable), the IP address, the browser user agent, and the exact action performed.

Designing a secure audit log in a MySQL database requires strict access controls to prevent administrative manipulation. Nsubuga and Baguma (2021) recommend that the audit log table (`audit_logs`) should not support update or delete operations at the database privilege level, ensuring that even if an administrator's credentials are compromised, the historical log cannot be altered without leaving trace anomalies. This tamper-evident log provides the Electoral Commission and independent observers with a reliable trail to audit the election’s technical integrity, building trust in the digital outcome.

---

## 2.9 Security Threats in Online Voting Systems

Web-based online voting applications are exposed to a wide range of cyber security threats that can compromise confidentiality, integrity, and availability. Security engineers must design robust countermeasures within the web application to protect against these vulnerabilities.

| Attack Category | Threat Description | PHP/MySQL Countermeasures |
| :--- | :--- | :--- |
| **SQL Injection (SQLi)** | Attackers insert malicious SQL commands into input fields to bypass authentication or extract database records. | Use PDO prepared statements with parameterized queries; disable multi-queries. |
| **Cross-Site Scripting (XSS)** | Attackers inject malicious JavaScript into web forms, which executes in other users' browsers. | Sanitize all inputs using `htmlspecialchars()`; implement Content Security Policy (CSP) headers. |
| **Cross-Site Request Forgery (CSRF)** | Attackers trick authenticated users into executing unauthorized commands on the application. | Generate cryptographically secure CSRF tokens for every form session; validate tokens on POST requests. |
| **Session Hijacking & Fixation** | Attackers steal session IDs to impersonate authenticated voters or administrators. | Regenerate session IDs on login; set session cookie flags: `HttpOnly`, `Secure`, and `SameSite=Strict`. |
| **Insider Threats** | Database administrators or electoral staff manipulate database records directly. | Implement database-level UNIQUE constraints; design immutable, read-only audit log tables. |

SQL injection (SQLi) remains one of the most critical threats to web-based database applications. In an e-voting context, an attacker could exploit a vulnerable login field to bypass credential verification and access administrative controls. Nabatanzi and Kigozi (2020) state that the primary countermeasure against SQLi is the strict avoidance of dynamic SQL construction. Instead, developers must use PHP Data Objects (PDO) to enforce prepared statements with parameterized queries. Parameterization ensures that user input is treated strictly as data, never as executable code, neutralizing injection attempts.

Cross-Site Scripting (XSS) allows attackers to inject malicious scripts into pages viewed by other users. For example, a candidate could inject a script into their manifesto field that steals the session cookies of any student who views their profile. To prevent this, the application must run strict output encoding. In PHP, applying `htmlspecialchars()` to all dynamically rendered data before it is sent to the browser ensures that characters like `<` and `>` are converted to harmless HTML entities. Furthermore, implementing a Content Security Policy (CSP) header restricts the browser from executing unauthorized inline scripts.

Session hijacking and session fixation present significant risks to voter session integrity. If an attacker intercepts a student's session cookie, they can cast a ballot in their name. Countermeasures include regenerating the session ID immediately upon successful login using PHP's `session_regenerate_id(true)`, and setting cookie attributes to `HttpOnly` (preventing script access), `Secure` (enforcing HTTPS transmission), and `SameSite=Strict` (preventing cross-site request leakage).

---

## 2.10 Database Management Systems for Voting Applications

The choice of a Database Management System (DBMS) is critical for ensuring the data integrity and performance of an online voting application. For a system managing election data, the DBMS must strictly enforce the ACID properties (Atomicity, Consistency, Isolation, Durability) of database transactions.

*   **Atomicity** ensures that a transaction is treated as a single, indivisible unit of work; either all database updates are committed successfully, or none are. In this application, when a vote is cast, the database must write the vote record to the `votes` table and simultaneously update the `has_voted` status in the `students` table. Atomicity guarantees that the system will never record a vote without marking the student as voted, or vice versa.
*   **Consistency** guarantees that a transaction transitions the database from one valid state to another, maintaining all schema constraints.
*   **Isolation** ensures that concurrent transactions (such as hundreds of students voting at the same instant) do not interfere with each other. MySQL addresses this by locking rows during transaction execution, preventing race conditions where two threads attempt to modify the same record simultaneously.
*   **Durability** guarantees that once a transaction is committed, its changes are written to non-volatile storage and will survive any subsequent system crash.

MySQL, using the InnoDB storage engine, is highly suited for this application. Unlike the legacy MyISAM engine, InnoDB fully supports ACID-compliant transactions, foreign key constraints, and row-level locking. Mukasa, Tumusiime, and Kanyesigye (2024) compare MySQL with SQLite and PostgreSQL for web applications. While SQLite is lightweight and easy to configure, it lacks multi-user concurrency support, causing database locks during concurrent write operations. PostgreSQL offers excellent performance and security features but introduces higher administrative and deployment complexity for university systems. MySQL provides a balanced combination of high write concurrency, ease of deployment within standard XAMPP environments, and widespread compatibility with university hosting servers.

---

## 2.11 Web Application Frameworks and Technologies

The development of secure, responsive, and lightweight web applications requires selecting appropriate programming languages and frameworks. For university e-voting systems, PHP remains a highly popular choice for server-side scripting.

PHP is an open-source, server-side scripting language designed for web development. Its primary advantages include native integration with MySQL database engines, excellent performance under Apache web servers, and a low learning curve for institutional IT departments. Critics of PHP often point to security vulnerabilities in legacy codebases; however, as Mukasa, Tumusiime, and Kanyesigye (2024) argue, modern PHP (versions 8.0 and above) features improved type safety, secure session management, and robust cryptographic libraries, making it fully capable of hosting secure applications when built using MVC patterns and PDO database interfaces.

For client-side layout and responsive design, Bootstrap 5 offers a powerful CSS framework. In a student environment, mobile accessibility is critical. According to UCC (2023) statistics, the majority of university students access the internet via mobile devices. Bootstrap 5 utilizes a mobile-first fluid grid system, allowing pages to scale dynamically from desktop screens to tablets and smartphones. By using Bootstrap’s pre-compiled CSS components, the application achieves a clean, professional, and consistent user interface without requiring heavy custom stylesheets that could slow down page load times over slow mobile connections.

For OTP email delivery, PHPMailer represents a reliable, secure utility. The standard PHP `mail()` function often fails to deliver emails to institutional inboxes because it lacks SMTP authentication, leading to messages being flagged as spam. PHPMailer supports secure SMTP authentication over SSL/TLS, enabling the application to send transactional emails using secure mail servers (such as Gmail SMTP), ensuring that students receive their OTP verification codes within seconds of initiating a login request.

---

## 2.12 ICT Infrastructure in Ugandan Universities

Deploying an online voting system in a Ugandan public university requires a realistic assessment of the existing ICT infrastructure and digital readiness. Over the past decade, the government of Uganda, through NITA-U and the Uganda Communications Commission (UCC), has implemented projects to improve connectivity in higher education. Under the National Transmission Backbone Infrastructure (RCIP Uganda) project, public universities have been connected to high-speed fiber-optic networks, significantly reducing bandwidth costs.

According to the National IT Survey reports by NITA-U (2022), public universities have established campus Local Area Networks (LAN) with fiber backbones connecting academic departments, administration blocks, and student halls of residence. Kyambogo University has deployed campus-wide wireless networks (KYU-WiFi) accessible to students and staff. However, these networks face challenges, including intermittent power outages, high congestion during peak hours, and uneven Wi-Fi coverage in residential areas. Baguma and Muyinda (2019) note that while institutional infrastructure is generally adequate, systems must be optimized to run with low bandwidth consumption to remain accessible during network slowdowns.

Student device ownership is another critical factor. Data from the Uganda National Bureau of Statistics (UBOS) (2024) indicates that mobile phone penetration among university-going youth is near-universal, with smartphone ownership exceeding 85%. The remaining 15% of students rely on laptops or university-run computer laboratories. This high level of device access indicates that a web-based voting application, designed to be mobile-responsive and light on data consumption, can achieve near-universal accessibility without requiring the university to purchase additional voting terminal hardware.

---

## 2.13 Legal and Regulatory Framework for E-Voting in Uganda

Implementing an online voting system within a public university in Uganda must comply with national laws governing electronic transactions, data privacy, and higher education governance.

*   **The Electronic Transactions Act (2011)**: This Act provides the legal foundation for electronic transactions and communications in Uganda. It validates the legality of electronic contracts, digital signatures, and electronic records, stating that information shall not be denied legal effect solely because it is in electronic form. In this application, the digital receipt hash generated by the voting system is legally recognized as a valid electronic record under this Act.
*   **The Data Protection and Privacy Act (2019)**: This Act governs the collection, processing, and storage of personal data in Uganda. The online voting system handles sensitive voter data, including student names, registration numbers, email addresses, and phone numbers. Under this Act, Kyambogo University is classified as a data controller. The system design must comply with the principles of data protection: obtaining informed consent, practicing data minimization (only collecting data necessary for voting), ensuring data security through encryption, and deleting temporary transactional data (such as OTP codes) after the election closes.
*   **The Universities and Other Tertiary Institutions Act (2001)**: This Act establishes the legal framework for the governance of public and private universities in Uganda. Section 68 of the Act mandates the establishment of a student guild to represent student interests. While the Act grants the university council the authority to approve guild constitutions, it does not explicitly regulate voting methods. Therefore, implementing an online voting system requires a formal resolution by the Kyambogo University Council and a subsequent amendment to the Kyambogo University Guild Constitution to legally recognize electronic voting as a valid method for electing student leaders.

---

## 2.14 User Experience and Voter Trust in E-Voting Systems

The successful adoption of an online voting system depends not only on its technical security but also on user acceptance and trust. Educational technology research frequently utilizes the Technology Acceptance Model (TAM) developed by Fred Davis in 1989 to study user intentions to adopt new software.

TAM posits that two primary factors determine a user's intention to adopt a system: Perceived Usefulness (PU) and Perceived Ease of Use (PEOU). PU is defined as the degree to which a user believes that using the system will enhance their job performance or experience. In this application, students perceive the system as useful because it saves time, eliminates the need to stand in long queues, and enables voting from off-campus locations. PEOU is the degree to which a user believes that using the system will be free of physical or mental effort. PEOU is addressed through intuitive user interface design, clear navigation, and a minimal number of steps to cast a ballot.

```
       +---------------------------------------------+
       |           Input Variables (External)        |
       | - Manual System Weaknesses                  |
       | - University ICT Infrastructure             |
       | - Legal and Regulatory Context              |
       | - Security Requirements                     |
       +----------------------+- --------------------+
                              |
                              v
       +---------------------------------------------+
       |             Process Variables               |
       | - Systems Analysis & Requirements           |
       | - Web-based Design (PHP/MySQL)              |
       | - OTP Multi-Factor Authentication           |
       | - System Testing and UAT                    |
       +----------------------+- --------------------+
                              |
                              v
       +---------------------------------------------+
       |             Output Variables                |
       | - Secure Online Voting System               |
       | - Increased Voter Turnout                   |
       | - Real-time Results Transparency            |
       | - Improved Institutional Trust              |
       +---------------------------------------------+
```
***Figure 2.1: DeLone and McLean Information Systems Success Conceptual Framework***

To evaluate the overall impact of the system, this study integrates TAM with the DeLone and McLean Information Systems Success Model (2003). As shown in Figure 2.1, the DeLone and McLean model measures system quality, information quality, and service quality as dimensions that influence user satisfaction and system usage, ultimately leading to net benefits for the institution. By combining these two frameworks, the study evaluates how technical design features translate into student trust and adoption.

---

## 2.15 Existing Systems and Their Limitations

Several electronic voting systems and prototypes have been developed globally and locally, each displaying distinct features and limitations.

*   **Helios Voting**: Helios is an open-source, web-based e-voting system that implements advanced cryptographic protocols, including homomorphic encryption, to achieve end-to-end verifiability. While cryptographically secure, Helios presents significant usability challenges. As Byaruhanga and Atwine (2022) point out, the system requires voters to manage digital tracker keys, which is often confusing for users with low technical literacy. Furthermore, Helios does not feature built-in student register synchronization or multi-factor authentication (MFA) tailored to university student databases, requiring manual voter list uploads.
*   **OpaVote and ElectionBuddy**: These are commercial, cloud-based voting platforms widely used by professional organizations and some universities. They offer user-friendly interfaces and automated email invitations. However, their primary limitation is cost, as they charge recurring fees based on the number of registered voters. For a public university like Kyambogo with over 22,000 students, the annual subscription costs are prohibitively high. Additionally, hosting sensitive student data on external, foreign-owned cloud servers raises compliance issues under Uganda's Data Protection and Privacy Act (2019), which restricts the cross-border transfer of personal data.
*   **Local Academic Prototypes**: Previous computer science student projects at Makerere and Kyambogo universities have proposed localized e-voting prototypes. However, these prototypes remained academic exercises with limited security features. Reviewing these systems, Nsubuga and Baguma (2021) observe that they lacked multi-factor authentication, relying solely on simple student number logins. They also lacked tamper-evident audit logs, atomic transaction handling, and mobile-responsive designs, making them unsuitable for real-world deployment.
*   **National Biometric Voter Verification System (BVVS)**: The Electoral Commission of Uganda utilizes biometric verification machines at physical polling stations to check voter credentials. While highly effective at preventing multiple voting in national elections, this system is a supervised hardware-based solution. It does not support remote voting and requires the procurement of specialized, expensive biometric terminals, making it unsuitable for university guild elections.

---

## 2.16 Research Gaps

This study addresses four distinct gaps identified in existing literature and implementations:

1.  **Geographic and Institutional Gap**: While there is research on online student voting in South Africa and West Africa, very few peer-reviewed studies focus on the implementation of e-voting systems within public universities in Uganda. The unique infrastructural and political environments of Ugandan public institutions require localized study.
2.  **Technical Security Gap**: Prior local e-voting prototypes developed in Ugandan institutions lacked robust multi-factor authentication (MFA) and tamper-evident audit logs. Most relied on single-factor student number logins, which are highly susceptible to credential theft and administrative manipulation.
3.  **Legal Compliance Gap**: No previous research has analyzed the alignment of university e-voting systems with Uganda’s Data Protection and Privacy Act (2019). This study addresses this gap by showing how system architectures can meet data protection obligations, such as data minimization and lawful processing.
4.  **Usability and Evaluation Gap**: Existing literature often focuses on the cryptographic security of e-voting without evaluating user acceptance among students. This study addresses this gap by applying the Technology Acceptance Model (TAM) to evaluate usability, ease of use, and student trust during user acceptance testing.

---

## 2.17 Conceptual Framework

The conceptual framework for this study is grounded in the Technology Acceptance Model (TAM) (Davis, 1989) extended by DeLone and McLean's IS Success Model (2003), adapted for the university e-voting context.

```
   +--------------------+
   |  Input Variables   |
   | - System Quality   |
   | - Info Quality     |
   +---------+----------+
             |
             v
   +---------+----------+
   |   Perceived        |     +--------------------+
   |   Ease of Use      +---->| Perceived          |
   |   (PEOU)           |     | Usefulness (PU)    |
   +---------+----------+     +---------+----------+
             |                          |
             +------------+-------------+
                          |
                          v
             +------------+-------------+
             |   Behavioral Intent      |
             |   to Adopt & Use         |
             +------------+-------------+
                          |
                          v
             +------------+-------------+
             |     Actual System        |
             |     Adoption (Voting)    |
             +--------------------------+
```
***Figure 2.2: Extended Technology Acceptance Model (TAM) for Online Voting System***

As illustrated in Figure 2.2, the framework conceptualizes the relationship between the system's design attributes (independent variables) and the user's adoption behavior (dependent variable). The system's quality, determined by security, performance, and responsive design, directly influences the user's Perceived Ease of Use (PEOU) and Perceived Usefulness (PU). When students find the system secure, reliable, and easy to navigate on their mobile devices, their trust and intention to adopt the system increase, leading to actual system adoption and higher voter turnout.

---

## 2.18 Chapter Summary

This chapter has reviewed literature on e-voting technologies, authentication mechanisms, database transactions, security design, and legal frameworks in Uganda. The review has highlighted the limitations of paper-based elections, including vulnerability to fraud, high costs, and tallying delays, and compared existing platforms like Helios and local prototypes. Four distinct research gaps have been identified: geographic, technical, legal, and usability. The conceptual framework, combining TAM and the DeLone & McLean model, has been presented to guide system design and evaluation. The next chapter, Chapter Three, details the research methodology, population sampling, data collection tools, and systems design modeling methods used in this study.


---

# CHAPTER THREE: RESEARCH METHODOLOGY

## 3.1 Introduction

This chapter describes the methodological approach used to design, implement, and evaluate the secure online voting system for university guild elections. It outlines the research design, detailing the target population, sampling strategies, and sample size determination. The chapter explains the research instruments, including reliability and validity testing. It describes the data collection methods—interviews, questionnaires, document review, and observation—and the corresponding data analysis techniques. Furthermore, the chapter details the systems study, systems analysis (SSADM), and requirements elicitation processes, presenting user, functional, non-functional, and hardware/software system requirements. Finally, it outlines the system design modeling, implementation, testing, validation methods, and ethical considerations.

---

## 3.2 Research Design

This study adopted a descriptive case study research design combining qualitative and quantitative methodologies. According to Yin (2018), a case study design is highly appropriate when the researcher seeks to investigate a contemporary phenomenon within its real-life context, particularly when the boundaries between the phenomenon and the context are not clearly defined. In this research, the contemporary phenomenon is online student voting, and the real-life context is Kyambogo University. The case study approach allowed for an in-depth, multi-faceted investigation of the current paper-based voting operations and the technical requirements for a digital solution.

The combination of qualitative and quantitative methods (mixed-methods approach) provided a comprehensive understanding of the research problem (Creswell and Creswell, 2022). Quantitative data gathered from structured questionnaires measured the prevalence of election skepticism, student smartphone ownership, and user acceptance scores. Simultaneously, qualitative data from semi-structured interviews and document reviews provided depth and context regarding administrative workflows, resource allocation, security concerns, and historical election disputes. System development was guided by the Structured Systems Analysis and Design Method (SSADM) during the requirements and design phases, while the implementation phase followed the classic Waterfall System Development Life Cycle (SDLC) model, ensuring a structured transition from analysis to deployment.

---

## 3.3 Population and Sample Selection

### 3.3.1 Sampling Strategy

The target population for this study comprised various stakeholders in the Kyambogo University guild electoral system, including undergraduate and postgraduate students, guild presidential and GRC candidates, university administrators, academic and non-academic staff, and members of the Guild Electoral Commission. To ensure representative feedback, different sampling strategies were applied to each participant category:

*   **Electoral Commission (EC) Officials**: Purposive sampling was used to select EC officials. Purposive sampling involves selecting participants who possess specialized knowledge and experience relevant to the research topic. Since EC officials are directly responsible for organizing and managing guild elections, their input was essential for understanding administrative requirements and security constraints. A census approach was applied to this sub-population, selecting all 5 core commissioners.
*   **University Administrators**: Purposive sampling was also applied to university administrators, selecting officials from the Dean of Students' office, the ICT department, and the security office. These administrators provided insights into institutional policy, network infrastructure readiness, and campus security history.
*   **Academic and Non-Academic Staff**: Simple random sampling was used to select university staff members who participate in election supervision or act as polling invigilators. This ensured that every staff member had an equal opportunity to participate.
*   **Guild Candidates**: Purposive sampling was applied to past and current guild presidential and GRC candidates. Candidates provided a unique perspective on election integrity, ballot security, and the transparency of the tallying process.
*   **Students**: Stratified random sampling was used to select student participants. The student population was stratified by school/faculty (School of Computing and Information Science, Faculty of Engineering, School of Management Sciences, Faculty of Science, and Faculty of Education) to ensure cross-faculty representation. Within each stratum, simple random sampling was used to select students, ensuring that the sample reflected the diverse student body across different academic disciplines.

### 3.3.2 Sample Size Determination

The total sample size was determined based on the need to balance qualitative depth with quantitative representation. Table 3.1 displays the population and sample size breakdown for each participant category:

**Table 3.1: Target Population and Sample Size Breakdown**
| Participant Category | Sampling Method | Target Population | Selected Sample | Data Collection Instrument |
| :--- | :--- | :--- | :--- | :--- |
| Electoral Commission Officials | Purposive (Census) | 5 | 5 | Key Informant Interview Guide |
| University Administrators | Purposive | 8 | 5 | Key Informant Interview Guide |
| Academic & Non-Academic Staff | Simple Random | 80 | 15 | Self-Administered Questionnaire |
| Past/Current Guild Candidates | Purposive | 12 | 5 | Key Informant Interview Guide |
| Students (School of Computing) | Stratified Random | 2,200 | 9 | Self-Administered Questionnaire |
| Students (Faculty of Engineering) | Stratified Random | 3,500 | 9 | Self-Administered Questionnaire |
| Students (School of Management) | Stratified Random | 4,100 | 9 | Self-Administered Questionnaire |
| Students (Faculty of Science) | Stratified Random | 2,800 | 9 | Self-Administered Questionnaire |
| Students (Faculty of Education) | Stratified Random | 3,200 | 9 | Self-Administered Questionnaire |
| **TOTAL** | - | **15,905** | **75** | - |

---

## 3.4 Research Instrument Design and Testing

### 3.4.1 Reliability Testing

Reliability refers to the consistency and stability of a research instrument over time. To ensure that the questionnaires generated reliable data, a pilot test was conducted with 8 students and 3 staff members who were not part of the final sample. The data gathered from this pilot test was analyzed using Cronbach’s Alpha coefficient ($\alpha$) to measure internal consistency. Cronbach’s Alpha is a standard reliability metric where a score of 0.70 or higher indicates acceptable internal consistency (Nunnally, 1978).

The initial reliability analysis of the student questionnaire returned a Cronbach’s Alpha of 0.74. While acceptable, a review of the item-total correlation statistics indicated that two questions regarding cryptographic protocols were confusing to non-technical students, lowering the overall alpha. These two items were revised to use simpler language, focusing on "system security and trust" rather than specific encryption algorithms. After making these adjustments, a second reliability test returned a Cronbach’s Alpha of 0.81, confirming that the instrument was highly reliable and ready for deployment.

### 3.4.2 Validity Testing

Validity refers to the degree to which a research instrument accurately measures what it is designed to measure. To ensure the validity of the data collection instruments, two types of validation were conducted:

*   **Content Validity**: The research instruments were reviewed by the research supervisor and two senior lecturers from the Department of Computer Science at Kyambogo University. These experts evaluated whether the questionnaire items and interview questions adequately covered all objectives of the study. Based on their feedback, several questions regarding system usability and legal compliance were added, and technical terms in the student survey were simplified to improve clarity.
*   **Face Validity**: A pilot group of 5 students reviewed the questionnaires to ensure that the layout was user-friendly, the instructions were clear, and the questions were unambiguous. Based on their input, the layout of the Likert-scale tables was adjusted to improve readability on mobile phone screens, and the wording of the database transaction questions was refined.

---

## 3.5 Data Collection and Analysis Methods

### 3.5.1 Interview Method

Semi-structured interviews were conducted with 15 key informants, including 5 Electoral Commission officials, 5 university administrators (from ICT and Student Affairs), and 5 past/current guild candidates. Semi-structured interviews are valuable because they combine a standardized set of core questions with the flexibility to explore relevant themes that emerge during the conversation. 

The interviews were conducted face-to-face in the participants' offices on the main campus, with each session lasting between 35 and 50 minutes. The conversations focused on five thematic areas: the operational workflows of the current paper-based system, historical security and integrity challenges, the cost of elections in terms of university resources, campus ICT infrastructure readiness, and specific requirement recommendations for a digital online voting system. All interviews were audio-recorded with the participants' permission and subsequently transcribed verbatim for analysis.

### 3.5.2 Questionnaire Method

Self-administered questionnaires were used to gather quantitative and qualitative data from 45 students and 15 staff members, resulting in 60 questionnaire respondents. Questionnaires are effective for gathering data from larger groups because they standardize responses, allowing for statistical comparison. The questionnaires featured closed-ended items using a five-point Likert scale (strongly agree, agree, neutral, disagree, strongly disagree) to measure attitudes toward the current voting system and the perceived ease of use and usefulness of the proposed online system. 

To maximize accessibility, the questionnaires were distributed using a dual approach: an electronic version was built on Google Forms and sent to student email lists and student WhatsApp groups, while printed paper copies were distributed to students in computer laboratories who did not have active internet connections on their devices. The response rate was 100% because the researchers physically collected paper surveys and followed up on digital invitations.

### 3.5.3 Document Review Method

Document review involves systematically analyzing existing documents to gather contextual and historical data. This method was used to analyze the university's electoral history, administrative structures, and cost allocations. The researchers reviewed:

*   **Official Guild Election Results (2019–2024)**: To analyze historical voter turnout figures, candidate vote distributions, and the prevalence of disputed outcomes across different faculties.
*   **Electoral Commission Budget Reports**: To extract exact figures regarding expenditures on ballot printing, security, logistics, and personnel compensation.
*   **The Kyambogo University Guild Constitution (as amended)**: To analyze the legal regulations governing election calendars, candidate eligibility, voting procedures, and results declaration.
*   **Electoral Dispute and Petition Records**: To identify documented cases of ballot stuffing, identity theft, and counting irregularities that occurred in past elections.

Documents from the five most recent election cycles were analyzed, providing a solid historical baseline to justify the transition to a digital system.

### 3.5.4 Observation Method

Non-participant observation was used to document physical workflows and identify operational bottlenecks. Non-participant observation involves the researcher observing activities without participating in them. In this study, the researchers observed the operation of a faculty-level student association election on the main campus. 

The observation focused on three phases: the verification of student IDs at the polling desk, the physical environment of ballot casting inside voting booths, and the manual tallying process after polls closed. The researchers documented queue times, voter verification speeds, physical security measures, and the hand-counting process. The observations confirmed that manual counting was slow and open to transcription errors, and that physical crowds around counting tables created significant security risks.

### 3.5.5 Data Analysis Methods

Quantitative data gathered from questionnaires was entered into Microsoft Excel. Descriptive statistics, including frequencies, percentages, means, and standard deviations, were computed. The results were presented in tables and charts to illustrate student opinions on election integrity, cost, accessibility, and technology acceptance.

Qualitative data from interview transcripts, open-ended questionnaire items, and observation notes was analyzed using thematic analysis, following Braun and Clarke’s (2019) six-phase framework. The thematic analysis process involved:

1.  Familiarization with the data through transcription and active reading.
2.  Generating initial codes to label meaningful segments of text.
3.  Searching for patterns across codes to identify potential themes.
4.  Reviewing and refining themes to ensure they matched the coded data.
5.  Defining and naming themes (such as "Administrative Burden" and "Trust Deficit").
6.  Producing the final report, linking qualitative themes to quantitative statistics in Chapter Four.

---

## 3.6 Systems Study and Analysis Methods

### 3.6.1 Systems Study Methods

The systems study involved analyzing the workflows and data flows of the current manual guild election system at Kyambogo University. The study traced how data moves through the current system, starting from voter registration updates, through ballot printing, physical distribution, voter identification, vote casting, ballot transport, manual counting, and results declaration. Actor mapping was used to identify all participants in the process and their responsibilities, including the Guild EC Chairperson, Returning Officers, Polling Agents, Candidates, and Voters. By documenting these process flows, the researchers identified vulnerabilities and bottlenecks, which formed the basis for designing the new system.

### 3.6.2 Systems Analysis Methods

Systems analysis was guided by the Structured Systems Analysis and Design Method (SSADM). SSADM is a structured, data-driven systems analysis methodology that uses DFDs, entity relationship models, and process histories to specify software requirements. 

SSADM was selected because it is highly effective for analyzing complex, multi-stakeholder administrative processes. By using SSADM, the researchers decomposed the complex guild election system into logical processes, identifying data inputs, storage points, and outputs. This systematic analysis helped translate the qualitative requirements of the Electoral Commission and students into functional requirements, database tables, and system constraints.

---

## 3.7 System Requirements and Specification

### 3.7.1 User Requirements

User requirements describe what different user roles expect from the system. The online voting system supports three distinct user roles:

*   **Voter (Registered Student)**: Must be able to register easily, login securely, receive a verification OTP, view candidate profiles, cast their ballot securely, receive a transaction receipt hash, and view published results after the polls close.
*   **Candidate**: Must be able to upload a profile, including their manifesto and photo, view candidate listings, and view their own vote counts in real-time once the voting window closes.
*   **Electoral Commission Administrator**: Must have administrative control to create and schedule elections, define positions, verify candidates, manage the student register, monitor voter turnout in real-time, view the system audit logs, and publish election results.

### 3.7.2 Functional Requirements

Functional requirements define the specific features and behaviors that the software system must provide. Based on the requirements analysis, fifteen core functional requirements were specified, as shown in Table 3.2:

**Table 3.2: Functional Requirements Specification Table**
| Req. ID | Functional Requirement | Requirement Description |
| :--- | :--- | :--- |
| **FR-01** | Student Authentication | Validate student identity using student numbers and passwords. |
| **FR-02** | OTP Generation & Delivery | Generate a random 6-digit OTP and deliver it to the student's email. |
| **FR-03** | OTP Verification | Validate the OTP entered by the student within a 10-minute expiry window. |
| **FR-04** | Ballot Visualization | Display candidates per position with manifestos and photos. |
| **FR-05** | Double-Voting Prevention | Prevent students who have already voted from accessing the ballot page. |
| **FR-06** | Vote Casting & Hashing | Record votes securely using transaction-safe queries and database hashes. |
| **FR-07** | Transaction Receipt | Generate a unique SHA-256 transaction receipt hash for each voter. |
| **FR-08** | Real-Time Tallying | Maintain running vote counts per candidate, updated automatically upon vote write. |
| **FR-09** | Results Dashboard | Display election results (counts and percentages) after the voting window closes. |
| **FR-10** | Election Scheduling | Allow administrators to set start and end dates/times for elections. |
| **FR-11** | Candidate Management | Enable administrators to add, edit, and verify candidates and positions. |
| **FR-12** | Student Register Management| Allow administrators to import and update voter registers using CSV files. |
| **FR-13** | Audit Logging | Log all administrative actions, logins, and vote castings in read-only tables. |
| **FR-14** | Notification Delivery | Send email confirmations to students upon successful vote submission. |
| **FR-15** | Role-Based Access Control | Restrict access to features based on roles: Admin, Candidate, and Voter. |

---

### 3.7.3 Non-Functional Requirements

Non-functional requirements specify the quality attributes, security constraints, and performance targets of the software system. Table 3.3 presents the non-functional requirements for the online voting system:

**Table 3.3: Non-Functional Requirements Specification Table**
| Req. ID | Quality Attribute | Target Specification |
| :--- | :--- | :--- |
| **NFR-01** | System Security | All database interactions must use PDO prepared statements to prevent SQL injection. |
| **NFR-02** | Data Sanitization | Application must sanitize input fields using `htmlspecialchars()` to prevent XSS. |
| **NFR-03** | CSRF Protection | Every form submission must validate a unique, session-based anti-CSRF token. |
| **NFR-04** | System Availability | System must maintain 99.5% uptime during the active voting window. |
| **NFR-05** | Performance Speed | Vote submissions must process in under 3 seconds under concurrent loads. |
| **NFR-06** | Mobile Responsiveness | The user interface must scale to mobile phone screens using Bootstrap 5. |
| **NFR-07** | ACID Compliance | Database transactions must use the InnoDB storage engine to ensure integrity. |
| **NFR-08** | Audit Trail Integrity | System log tables must be configured to prevent updates or deletions. |

---

### 3.7.4 System Requirements

System requirements define the minimum hardware and software environments required to develop, host, and run the online voting application.

**Table 3.4: Server-Side and Client-Side Hardware Specifications**
| Hardware Component | Server-Side Minimum Specification | Client-Side Minimum Specification |
| :--- | :--- | :--- |
| **Processor** | Intel Core i5 or AMD Ryzen 5, 2.5 GHz or higher | Quad-Core ARM or Intel Celeron, 1.1 GHz or higher |
| **Random Access Memory** | 16 GB RAM minimum | 2 GB RAM (Mobile) / 4 GB RAM (Desktop) |
| **Storage Capacity** | 500 GB Solid State Drive (SSD) | 100 MB free space (for browser cache) |
| **Network Interface** | 1 Gbps Gigabit Ethernet port | Wi-Fi 4 or 4G LTE mobile data capability |
| **Power Backup** | Uninterruptible Power Supply (UPS) with generator | Battery backup for mobile/laptop devices |

**Table 3.5: System Software Environment Specifications**
| Software Component | Development/Server Environment | User/Client Environment |
| :--- | :--- | :--- |
| **Operating System** | Windows 10/11 or Ubuntu Server 22.04 LTS | Android 9.0+, iOS 12+, Windows 10/11, macOS |
| **Web Server** | Apache 2.4 (configured via XAMPP) | Chrome Mobile, Safari, Microsoft Edge, Firefox |
| **Database Engine** | MySQL 8.0 / MariaDB 10.4 | Web browser (no local database required) |
| **Programming Language**| PHP 8.1 server-side, JavaScript (ES6) client-side | HTML5, CSS3, JavaScript engines |
| **Mail Library** | PHPMailer 6.8 (with Gmail SMTP configuration) | Standard email client (Gmail, Yahoo, Outlook) |
| **Development Tools** | Visual Studio Code, Git, phpMyAdmin | Web Browser |

---

## 3.8 Systems Design and Modeling Methods

### 3.8.1 Using Data Flow Diagrams (DFDs)

Data Flow Diagrams (DFDs) were used to model the logical flow of data through the voting system. The modeling was conducted at three hierarchical levels:

*   **Context Diagram (Level 0)**: Defined the boundaries of the voting system, identifying the four main external entities (Voter, Candidate, EC Administrator, University Registrar) and their high-level data inputs and outputs.
*   **Level 0 DFD**: Decomposed the system into six core processes: authentication, voter registration, election management, ballot display/voting, vote tallying, and audit logging. It also mapped data flows to the six main database stores.
*   **Level 1 DFD**: Decomposed the vote casting process to show how voter eligibility is checked, selections are accepted, encryption is applied, records are written, and receipt hashes are generated.

### 3.8.2 Using Entity-Relationship Diagrams (ERDs)

Entity-Relationship Diagrams (ERDs) were used to design the normalized relational database schema. The design process mapped the entities (Users, Elections, Positions, Candidates, Votes, Voter Registry, Audit Logs, Schools, Notifications), defined their attributes, identified primary and foreign keys, and established relationship cardinalities (e.g., one-to-many relationships between elections and positions, and many-to-many relationships modeled through associative tables). This ensured referential integrity and optimized database performance.

### 3.8.3 Using Unified Modeling Language (UML)

Unified Modeling Language (UML) diagrams were used to model the behavioral and interactive aspects of the system:

*   **Use Case Diagram**: Mapped the interactions between actors (Voters, Candidates, EC Administrators) and system use cases, illustrating role-based boundaries.
*   **Activity Diagram**: Documented the step-by-step workflow of vote casting, including decision nodes for authentication checks, OTP verification, and double-voting prevention.
*   **Sequence Diagram**: Modeled the sequential interactions between system objects (Browser, Auth Controller, OTP Service, Database, and Audit Logger) during an OTP-authenticated vote submission.

---

## 3.9 System Implementation, Testing and Validation Methods

### 3.9.1 System Implementation Method

The software was developed using the Waterfall SDLC model. The Waterfall model represents a sequential development process where progress flows steadily through distinct phases: requirements analysis, system design, implementation, testing, deployment, and maintenance. This model was selected because the requirements for a guild election system are well-defined, stable, and subject to strict institutional regulations.

The code was structured following the Model-View-Controller (MVC) architectural pattern. MVC separates application data (Model) from the user interface (View) and the control logic (Controller), improving code maintainability. Visual Studio Code was used as the primary text editor, Git for version control, XAMPP for hosting local Apache and MySQL environments, and PHPMailer for SMTP email delivery.

### 3.9.2 System Testing Method

System testing was conducted using a three-stage testing framework:

1.  **Black-Box Functional Testing**: The researchers developed a test suite based on the functional requirements. Each feature (such as login, candidate creation, and vote casting) was tested with valid and invalid inputs to confirm it produced the expected outputs.
2.  **Security Testing**: Penetration testing simulations were performed, including attempting SQL injection attacks in input fields, injecting XSS scripts in candidate manifestos, and simulating CSRF requests. These tests verified that the implemented security filters (prepared statements, HTML encoding, and CSRF tokens) were working correctly.
3.  **Performance Testing**: Apache JMeter was used to simulate concurrent users. The performance testing evaluated system response times and database transaction integrity under simulated concurrent loads of up to 500 users.

### 3.9.3 System Validation Method

System validation was performed through User Acceptance Testing (UAT). A mock guild election was simulated on campus with 10 student volunteers representing voters and 3 Electoral Commission officials representing administrators. The participants completed end-to-end tasks, including registering candidates, logging in, verifying OTPs, casting votes, generating receipts, and viewing the results dashboard. After the simulation, participants completed a feedback survey based on the Technology Acceptance Model (TAM) to measure usability, ease of use, and overall satisfaction.

---

## 3.10 Ethical Considerations

To ensure the research conformed to ethical standards, several measures were implemented. First, informed consent was obtained from all participants before administering questionnaires or conducting interviews. Participants were informed about the study's purpose, their right to withdraw at any time without penalty, and how their data would be used.

Second, participant anonymity and data confidentiality were maintained. Questionnaires did not collect personally identifiable information, and interview transcripts were anonymized. For the system simulation, mock student registration numbers and fake names were used to prevent the leakage of real student data.

Third, all collected data was stored on a secure, password-protected local server, accessible only to the primary researchers. The database files will be permanently deleted upon successful submission of the report. This study received ethical clearance from the Kyambogo University School of Computing Research Ethics Board (Approval Reference Number: **KYU-SCIS-REC-2026-084**). The system design complies with Uganda's Data Protection and Privacy Act (2019) by implementing database security, collecting only necessary data, and encrypting voter information.

---

## 3.11 Chapter Summary

This chapter has detailed the research design and methodology used in this study, justifying a descriptive mixed-methods case study approach for Kyambogo University. It has presented the target population and the sampling strategy that selected 75 participants, and detailed the reliability and validity testing of the research instruments. The methods for data collection—interviews, questionnaires, document review, and observation—and the corresponding data analysis techniques have been explained. The chapter has detailed the systems study, systems analysis (SSADM), and requirements specifications, presenting functional and non-functional requirements alongside hardware and software environments. Finally, it has outlined the modeling, implementation, testing, validation methods, and ethical considerations. The next chapter, Chapter Four, presents the findings from the systems study, analyzes the current paper-based system's weaknesses, and details the feasibility studies for the new system.


---

# CHAPTER FOUR: SYSTEM STUDY, ANALYSIS AND REQUIREMENTS ELICITATION

## 4.1 Introduction

This chapter presents the system study, detailed analysis, and requirements elicitation for the student guild elections at Kyambogo University. It describes the operations of the current manual paper-based voting system, outlining its workflows, strengths, and weaknesses based on empirical findings. A SWOT analysis is presented to compare the current manual system with the opportunities offered by digital technologies. Furthermore, the chapter details the functional, non-functional, and hardware/software requirements of the new system. Finally, it presents a feasibility analysis across technical, economic, operational, and legal dimensions, demonstrating the viability of the proposed online voting system.

---

## 4.2 Description of the Current System

The student guild elections at Kyambogo University are managed by a temporary administrative body known as the Guild Electoral Commission (EC), which is appointed annually in accordance with the university’s guild constitution. The election process is conducted using a manual, paper-based ballot system. The operation of this manual system follows a sequence of phases, beginning with the announcement of the election calendar and concluding with the declaration of the winning candidates.

The process begins when the Chairperson of the Guild Electoral Commission, in coordination with the University Registrar, publishes the official election roadmap. This roadmap details candidate nomination dates, campaign periods, and polling schedules. Following the announcement, the University Registrar compiles the voter register, which is extracted from the central student database and printed as physical registration books for each faculty. These printed registers are distributed to polling stations on election day to verify voter eligibility.

During the campaign period, candidates print posters, hold rallies, and visit lecture halls to present their manifestos. On the eve of polling day, the Electoral Commission coordinates the procurement and distribution of election materials, including wooden ballot boxes, printed paper ballots with serial numbers, ink pads, and security seals. Polling stations are set up at designated sites across campus, categorized by faculty (such as the School of Computing, Faculty of Engineering, and School of Education). Each polling station is supervised by a Returning Officer, assisted by Polling Agents who represent individual candidates.

On polling day, voting is conducted between 8:00 AM and 5:00 PM. The step-by-step workflow of a voter at a physical polling station is described as follows:

```
   [ Student approaches Polling Desk ]
                  |
                  v
   [ Presents Physical ID or Registration Slip ]
                  |
                  v
   [ Polling Agent verifies name in printed Register ]
                  |
                  v
   [ Voter dipped in Indelible Ink / receives Paper Ballot ]
                  |
                  v
   [ Enters Voting Booth & marks ballot physically ]
                  |
                  v
   [ Folds ballot & inserts into physical Ballot Box ]
                  |
                  v
   [ Departs Polling Station ]
```

Once a voter approaches the verification desk, they present their physical student identity card or an official registration slip. The Polling Agent searches for the student’s name in the printed register. If the name is found, a line is drawn through it to indicate that the voter is present. The voter's thumb is then dipped in indelible ink to prevent double-voting, and they are issued a paper ballot. The voter proceeds to a private booth, marks their candidate selection, folds the ballot, and inserts it into the physical ballot box.

At 5:00 PM, the polling stations are closed. The Returning Officers seal the ballot boxes and transport them under security escort to the central university tallying center. Tallying is conducted by hand. The EC officials open each ballot box in the presence of candidates' agents, count the physical ballots to verify they match the number of voters marked in the register, and record votes on tally sheets. Once counting is completed across all faculties, the totals are summed, and the Chairperson of the Guild Electoral Commission declares the winners. This manual tallying process routinely takes between 12 and 36 hours.

---

### 4.2.1 Strengths of the Current System

Despite its operational limitations, the current manual paper-based system possesses several features that explain its continued usage:

*   **Universal Accessibility**: The manual system does not require voters to own smart devices or have internet access. Any registered student who physically presents themselves at a polling station can vote, ensuring that students with limited ICT resources are not excluded.
*   **Cultural Familiarity**: Paper-based voting is familiar to the student body, mimicking the national voting processes managed by the Electoral Commission of Uganda. The simplicity of marking a paper ballot requires no technical training, minimizing errors caused by user confusion.
*   **Offline Independence**: The system operates independently of network connectivity, server availability, and power supply. Physical voting is unaffected by bandwidth congestion or database server failures, which are common challenges on campus.
*   **Tangible Audit Trail**: The physical ballot paper serves as a tangible, verifiable artifact of democratic participation. In the event of a dispute, ballots can be physically re-counted in the presence of candidate representatives, providing visible proof of the tally.
*   **Constitutional Compliance**: The current manual workflows comply with the existing Kyambogo University Guild Constitution, which defines voting in terms of ballot papers and physical polling centers, avoiding legal challenges from candidates.

---

### 4.2.2 Weaknesses of the Current System

The manual paper-based system presents critical weaknesses that compromise the integrity, accessibility, and efficiency of the elections:

*   **Identity Fraud and Impersonation**: Verification is limited by the quality of physical student cards and the vigilance of polling assistants. Students can forge registration slips or use cards belonging to absent classmates. The quantitative survey conducted for this study reveals that 67% of student respondents reported witnessing or hearing credible reports of voter impersonation in previous guild elections.
*   **Low Voter Turnout**: The physical voting requirement disenfranchises students who are off-campus during the election cycle, such as those on school-based teaching practice or industrial internships. This exclusion, combined with long queues, results in low voter turnout. For instance, in the 2023 guild elections, only 24% of the 8,000 registered students in the participating faculties cast their votes.
*   **Delayed Announcements**: Hand-counting thousands of paper ballots across multiple polling stations is a slow process, taking between 12 to 36 hours after polls close. This delay creates an atmosphere of suspicion and tension, often resulting in student protests and security issues.
*   **High Financial Cost**: Printing secure ballots, renting tents and tables, hiring external security, and paying polling staff imposes a significant financial burden. Document review shows that the university spends approximately 45,000,000 UGX per election cycle, representing a recurring cost on the student guild fund.
*   **Lack of Individual Verifiability**: Once a paper ballot is dropped into the box, the voter has no way to verify that their vote is counted correctly. The counting process is conducted out of sight of individual voters, leaving them to rely on candidate agents to detect manipulation.
*   **Administrative Overhead**: Compiling printed registers, managing logistics, and coordinating security for multiple polling stations requires significant time and effort from the Electoral Commission and university administration.
*   **Environmental Cost**: Printing thousands of ballots, registers, and manifestos consumes significant quantities of paper, creating substantial waste after the election concludes.

---

### 4.2.3 Comparative Analysis (SWOT)

A SWOT analysis is conducted to evaluate the current manual system and assess the viability of implementing the new online voting system:

**Table 4.1: SWOT Analysis Table for the Current Manual System**
| Strengths (S) | Weaknesses (W) |
| :--- | :--- |
| • Universal accessibility for all physical students.<br>• Cultural familiarity and ease of understanding.<br>• Independence from internet and power outages.<br>• Tangible physical ballots for manual verification. | • High vulnerability to ballot stuffing and fraud.<br>• Disenfranchisement of off-campus students.<br>• Slow, error-prone manual vote counting.<br>• High operational and print costs (45m UGX/year). |
| **Opportunities (O)** | **Threats (T)** |
| • High student smartphone penetration (>85%).<br>• Presence of university fiber LAN and Wi-Fi networks.<br>• MoES push for digital transformation in education.<br>• Existence of student academic registers on AIMS. | • Cyberattacks, including SQL injection and XSS.<br>• Server crashes caused by high concurrent traffic.<br>• Student resistance due to digital trust issues.<br>• Power outages disrupting server operations. |

The comparative analysis demonstrates that the weaknesses and threats of the manual system outweigh its strengths. The weaknesses—such as identity fraud, low turnout, delayed results, and high operational costs—directly undermine the integrity of the elections. The opportunities, such as high smartphone penetration and existing university network infrastructure, provide the technical foundation for a secure online voting system. The threats, including cyberattacks and server crashes, can be addressed through software engineering design, security auditing, and database transaction optimization, confirming the viability of the new system.

---

## 4.3 Requirements of the New System

### 4.3.1 User Requirements

The new online voting system is designed to meet the requirements of three primary user groups:

*   **Voter (Student)**: The voter requires a simple interface that works on mobile devices. They need to log in securely, receive a verification OTP via email, view candidate profiles, cast their ballot, receive a secure transaction receipt, and view published results.
*   **Candidate**: The candidate requires a dedicated portal to upload their profile information, manifesto, and photo. They also need to view candidate listings and access their own vote tallies once the polls close.
*   **Electoral Commission Administrator**: The administrator requires control panels to manage the election lifecycle. They must be able to schedule voting windows, add candidates, upload student registers, monitor voter turnout, access audit logs, and publish results.

---

### 4.3.2 Functional Requirements

Functional requirements describe the specific operations that the software system must perform:

**Table 4.2: Functional Requirements Specification Table for the New System**
| Req. ID | System Process | Functional Requirement |
| :--- | :--- | :--- |
| **FR-01** | Authentication | The system must validate student login attempts using student numbers and passwords. |
| **FR-02** | OTP Delivery | The system must generate a random 6-digit OTP and deliver it to the student's email. |
| **FR-03** | OTP Verification | The system must validate the OTP entered by the student within 10 minutes of generation. |
| **FR-04** | Ballot Display | The system must display candidates categorized by position, showing photos and manifestos. |
| **FR-05** | Double-Voting Block| The system must prevent students who have already voted from accessing the ballot page. |
| **FR-06** | Vote Casting | The system must record votes securely using transaction-safe queries and database hashes. |
| **FR-07** | Receipt Generation | The system must generate a unique SHA-256 transaction receipt hash for each voter. |
| **FR-08** | Real-Time Tallying | The system must maintain running vote counts per candidate, updated automatically. |
| **FR-09** | Results Dashboard | The system must display election results (counts and percentages) after the polls close. |
| **FR-10** | Election Scheduling | The system must allow administrators to set start and end dates/times for elections. |
| **FR-11** | Candidate Admin | The system must enable administrators to add, edit, and verify candidates and positions. |
| **FR-12** | Register Import | The system must allow administrators to import student registers using CSV files. |
| **FR-13** | Audit Logging | The system must log all administrative actions, logins, and vote castings in read-only tables. |
| **FR-14** | Email Notification | The system must send email confirmations to students upon successful vote submission. |
| **FR-15** | Access Control | The system must restrict access based on roles: Admin, Candidate, and Voter. |

---

### 4.3.3 Non-Functional Requirements

Non-functional requirements specify the quality attributes and security constraints of the software system:

**Table 4.3: Non-Functional Requirements Specification Table for the New System**
| Req. ID | Quality Attribute | Technical Specification |
| :--- | :--- | :--- |
| **NFR-01** | SQLi Prevention | All database interactions must use PDO prepared statements with parameterized queries. |
| **NFR-02** | XSS Prevention | The system must sanitize user input using `htmlspecialchars()` before rendering. |
| **NFR-03** | CSRF Protection | The system must validate unique, session-based anti-CSRF tokens for all POST forms. |
| **NFR-04** | System Availability| The system must maintain 99.5% uptime during the active voting window. |
| **NFR-05** | Performance Speed | Vote submissions must process in under 3 seconds under concurrent loads. |
| **NFR-06** | Responsiveness | The user interface must scale to mobile phone screens using Bootstrap 5. |
| **NFR-07** | ACID Transactions | Database transactions must use the InnoDB storage engine to ensure integrity. |
| **NFR-08** | Log Integrity | System log tables must be configured to prevent updates or deletions. |

---

### 4.3.4 System Requirements

The system requirements define the hardware and software specifications needed to host and run the online voting application:

**Table 4.4: Server-Side and Client-Side Hardware Specifications**
| Hardware Component | Server-Side Minimum Specification | Client-Side Minimum Specification |
| :--- | :--- | :--- |
| **Processor** | Intel Core i5 or AMD Ryzen 5, 2.5 GHz or higher | Quad-Core ARM or Intel Celeron, 1.1 GHz or higher |
| **Random Access Memory** | 16 GB RAM minimum | 2 GB RAM (Mobile) / 4 GB RAM (Desktop) |
| **Storage Capacity** | 500 GB Solid State Drive (SSD) | 100 MB free space (for browser cache) |
| **Network Interface** | 1 Gbps Gigabit Ethernet port | Wi-Fi 4 or 4G LTE mobile data capability |
| **Power Backup** | Uninterruptible Power Supply (UPS) with generator | Battery backup for mobile/laptop devices |

**Table 4.5: System Software Environment Specifications**
| Software Component | Development/Server Environment | User/Client Environment |
| :--- | :--- | :--- |
| **Operating System** | Windows 10/11 or Ubuntu Server 22.04 LTS | Android 9.0+, iOS 12+, Windows 10/11, macOS |
| **Web Server** | Apache 2.4 (configured via XAMPP) | Chrome Mobile, Safari, Microsoft Edge, Firefox |
| **Database Engine** | MySQL 8.0 / MariaDB 10.4 | Web browser (no local database required) |
| **Programming Language**| PHP 8.1 server-side, JavaScript (ES6) client-side | HTML5, CSS3, JavaScript engines |
| **Mail Library** | PHPMailer 6.8 (with Gmail SMTP configuration) | Standard email client (Gmail, Yahoo, Outlook) |
| **Development Tools** | Visual Studio Code, Git, phpMyAdmin | Web Browser |

---

## 4.4 Feasibility Analysis

### 4.4.1 Technical Feasibility

The deployment of the online voting system is technically feasible within the current infrastructure of Kyambogo University. The university has a dedicated ICT department that manages central servers, a campus-wide Local Area Network (LAN), and high-speed Wi-Fi hotspots across all faculties. The web application is built using open-source, standardized web technologies, including PHP, MySQL, and Bootstrap 5, which are natively supported by the university’s Apache server environment.

The system is developed and configured using XAMPP, which simplifies deployment and database management. Integration with the university's mail infrastructure for OTP delivery is achieved using PHPMailer, utilizing secure SMTP configurations. On the client side, the high penetration of smartphones among the student body (exceeding 85% based on UCC data) ensures that the majority of students can access the voting portal using their personal devices, minimizing the need for the university to procure specialized voting terminal hardware.

---

### 4.4.2 Economic Feasibility

The development and deployment of the online voting system is economically feasible, presenting substantial cost savings compared to the manual system. The software is built using open-source languages and frameworks, resulting in zero licensing costs. A comparison of expenditures shows that the manual system requires a recurring annual budget of approximately 45,000,000 UGX, as detailed in Table 4.1.

In contrast, the online voting system requires an initial setup cost of approximately 8,000,000 UGX, covering developer compensation, server configuration, and administrator training. The recurring annual maintenance costs—covering server hosting, email API services, and minor system updates—are estimated at 2,000,000 UGX. The break-even point is reached within the first election cycle:

$$\text{Break-Even} = \frac{\text{Initial Development Cost}}{\text{Annual Operational Savings}} = \frac{8,000,000 \text{ UGX}}{43,000,000 \text{ UGX}} \approx 0.19 \text{ Years}$$

This calculation confirms that the system pays for itself within the first year of deployment, making it a highly cost-effective investment for the university.

---

### 4.4.3 Operational Feasibility

The online voting system is operationally feasible, receiving strong support from key stakeholders. Interviews with Electoral Commission officials and university administrators confirmed that they are eager to adopt digital solutions to eliminate manual counting delays and resolve security challenges.

An ICT competency assessment conducted with the 5 core EC commissioners showed that 4 of them possessed sufficient computer literacy to operate the administrator dashboard after receiving basic training. The university plans to conduct a one-day training workshop for EC staff prior to deployment. The voter interface is designed to be highly intuitive, requiring no technical training for students. The step-by-step navigation and clean layout ensure that a student can complete the voting process in under five minutes.

---

### 4.4.4 Legal Feasibility

The online voting system complies with national laws and regulations in Uganda:

*   **Electronic Transactions Act (2011)**: The Act recognizes electronic transactions and digital signatures as legally binding. The transaction receipt hash generated by the system is classified as a valid electronic record under this framework.
*   **Data Protection and Privacy Act (2019)**: The system complies with the Act by implementing strict security safeguards, practicing data minimization, and deleting temporary OTP codes after the election closes, ensuring voter data privacy.
*   **Universities and Other Tertiary Institutions Act (2001)**: While the Act mandates the establishment of student representation, it does not restrict the choice of voting methods. To ensure formal compliance, it is recommended that the University Council passes a resolution and amends the Guild Constitution to officially recognize electronic voting as a valid electoral method.

---

## 4.5 Chapter Summary

This chapter has presented the system study, detailed analysis, and requirements elicitation for the guild elections at Kyambogo University. It has described the current paper-based voting system, mapping its workflows and identifying strengths, such as accessibility, and weaknesses, including impersonation fraud and high costs. A SWOT analysis has compared manual voting with the opportunities offered by digital technologies. Furthermore, the chapter has detailed fifteen functional and eight non-functional requirements, alongside hardware and software environments. Finally, it has demonstrated technical, economic, operational, and legal feasibility. The next chapter, Chapter Five, details the system design, database schemas, UML modeling, implementation, testing, and security architecture of the developed online voting system.


---

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


---

# CHAPTER SIX: DISCUSSIONS, CONCLUSIONS AND RECOMMENDATIONS

## 6.1 Introduction

This chapter presents the discussions, conclusions, and recommendations based on the findings of this study. It discusses the outcomes of the research in relation to the four specific objectives: analyzing the current manual system, specifying requirements, implementing the secure online voting application, and validating its usability and security. The discussion aligns the empirical findings with the theoretical models and literature reviewed in Chapter Two. Furthermore, this chapter presents conclusions on the feasibility and impact of e-voting in public universities, offers actionable recommendations for various institutional stakeholders, and outlines areas for future research.

---

## 6.2 Discussion of Findings

The first objective of this study was to analyze and document the strengths and weaknesses of the current manual paper-based guild election system at Kyambogo University. The systems study and empirical data confirmed that while the paper-based system is universally accessible to physical students and culturally accepted, its security, operational, and financial limitations are no longer sustainable. The findings showed that the manual system is highly vulnerable to identity fraud and voter impersonation, with 67% of surveyed students reporting awareness of ballot-stuffing or impersonation in past elections. This outcome aligns with Lwanga, Mugisha, and Ssewanyana (2020), who noted that manual voter verification in public universities is easily bypassed due to large student populations and the ease of counterfeiting printed ID slips. 

Furthermore, the disenfranchisement of off-campus students and the high operational costs (approximately 45,000,000 UGX per cycle) represent significant barriers to democratic participation and financial sustainability. The observed delay of 12 to 36 hours in manual counting matches the findings of Kajumbula and Nsubuga (2023), who identified slow results announcement as the primary driver of post-election tension and campus security incidents.

The second objective was to specify the requirements for a secure online voting system appropriate for the university’s context. The systems analysis identified fifteen functional and eight non-functional requirements. The key user requirement was a mobile-responsive interface, as over 85% of students access services via smartphones. To address security concerns, multi-factor authentication (MFA) combining portal passwords with email-delivered One-Time Passwords (OTPs) was specified. This technical requirement is supported by Ssekyewa and Muyinda (2021), who argue that MFA is the most effective and cost-efficient mechanism for preventing remote credential theft in developing institutions. For data integrity, the system required ACID-compliant database transactions using the MySQL InnoDB engine and the implementation of a UNIQUE constraint on the combination of transaction receipt hashes and contested positions, ensuring that duplicate votes are blocked at the database level.

The third objective was to design, implement, and test the secure online voting system using PHP, MySQL, HTML5, CSS3, JavaScript, and Bootstrap 5. The implementation followed a three-tier MVC architecture, separating data access, business logic, and presentation views. Testing using a 15-case functional test matrix confirmed that the application successfully met all specifications. The security filters blocked SQL injection attempts through PDO prepared statements and parameterized queries, and sanitized inputs using PHP's `htmlspecialchars()` function to prevent XSS script injection, matching the security guidelines recommended by Nabatanzi and Kigozi (2020). Performance testing showed that the database successfully processed votes in under 2.4 seconds under simulated concurrent loads, proving that the lightweight architecture is suitable for deployment on standard university servers.

The fourth objective was to evaluate and validate the effectiveness, security, and usability of the implemented system. The User Acceptance Testing (UAT) conducted via a mock guild election with 10 students and 3 EC officials demonstrated positive performance outcomes. The task completion rate reached 100%, with students taking an average of 2.4 minutes to complete the voting process. 

The evaluation of user acceptance was guided by the Technology Acceptance Model (TAM) (Davis, 1989) and the DeLone and McLean (2003) IS Success Model. The survey results indicated high user satisfaction, with Perceived Ease of Use (PEOU) scoring 4.6 and Perceived Usefulness (PU) scoring 4.7 out of 5.0. These high scores confirm that students find the mobile-responsive interface easy to navigate and highly useful, as it eliminates the need to stand in long queues. The integration of email OTPs and secure transaction receipt hashes successfully addressed the "trust deficit" identified in digital systems by Kajumbula and Nsubuga (2023), proving that transparent technical design can build student trust and increase participation.

---

## 6.3 Conclusions

This study has demonstrated the feasibility, security, and operational viability of implementing a secure online voting system for university guild elections at Kyambogo University. The development of this web application addresses the key vulnerabilities of the traditional manual paper-based system:

*   **Elimination of Electoral Fraud**: The integration of multi-factor authentication (MFA) using portal passwords and email-delivered One-Time Passwords (OTPs) ensures that only eligible, registered students can access the ballot. The database-level UNIQUE constraint on vote records prevents double-voting, while the separation of voter identities from vote hashes protects ballot secrecy.
*   **Expansion of Voter Participation**: By enabling secure voting from any internet-connected device, the system removes the requirement for physical presence, allowing students on off-campus internships, industrial placements, and teaching practices to vote, thereby increasing overall turnout.
*   **Real-time Transparency and Trust**: The system tallies votes automatically and displays results immediately upon the closure of the voting window, eliminating the delay and security risks associated with manual counting. The generation of a unique SHA-256 transaction receipt hash enables students to verify that their votes are recorded correctly without compromising anonymity.
*   **Operational Cost Reduction**: Transitioning to the digital portal reduces election operational expenditures by over 80%, saving the university and the student guild fund significant resources that can be redirected to welfare services.

The positive evaluation scores from the UAT confirm that the system is accepted by both students and election administrators, indicating its readiness to modernize university governance and support digital democracy in public institutions.

---

## 6.4 Recommendations

Based on the findings and conclusions of this study, the following recommendations are proposed for key institutional stakeholders:

*   **To the Kyambogo University Senate and Council**: Pass a formal resolution to adopt electronic voting and approve amendments to the Kyambogo University Guild Constitution to legally recognize online voting as a valid electoral method.
*   **To the Guild Electoral Commission (EC)**: Establish an ICT governance subcommittee to oversee system administration, coordinate candidate profile verification, and manage the student voter register. Implement a one-day training program for EC officials prior to each election cycle.
*   **To the University ICT Department**: Host the online voting application on the university’s central servers, ensuring secure configuration, active SSL/TLS certificates, and bandwidth allocation. Integrate the voting database with the central Academic Information Management System (AIMS) to automate voter register synchronization.
*   **To the Ministry of Education and Sports (MoES)**: Utilize the findings of this study to formulate policy guidelines for digital e-governance across all higher education institutions in Uganda, promoting e-voting as a mechanism to improve governance.
*   **To the Student Body and Candidates**: Participate in pre-election sensitization campaigns and mock voting exercises organized by the Electoral Commission to build operational familiarity and trust in the digital system.

---

## 6.5 Future Work

While the implemented online voting system successfully addressed the security and usability requirements of guild elections, several areas are suggested for future research:

*   **Integration of Biometric Multi-Factor Authentication**: Future iterations of the system should explore integrating biometric verification, such as fingerprint or facial recognition, using mobile phone biometric APIs (Android Biometrics and iOS FaceID), to further secure voter authentication.
*   **Blockchain-Based Voting Architecture**: Research should investigate the feasibility of implementing decentralized blockchain ledger architectures, such as Ethereum or Hyperledger, to create immutable, decentralized audit trails that prevent administrative manipulation.
*   **Integration with the National Identification System**: Explore the feasibility of linking the university voter register with the National Identification and Registration Authority (NIRA) database using the National Identification Number (NIN) to verify student identities.
*   **Expanded Usability and Accessibility Testing**: Conduct larger-scale usability testing across multiple campuses and branches of the university to assess system performance under varying network speeds and accessibility requirements for students with disabilities.

---

## 6.6 Chapter Summary

This chapter has presented the discussions, conclusions, and recommendations of this study. The findings have been discussed in relation to the four research objectives, showing how the secure online voting system addresses identity fraud, voter turnout, results delays, and operational costs. The empirical results have been aligned with the TAM and DeLone & McLean theoretical frameworks, confirming high user acceptance and satisfaction. Actionable recommendations have been proposed for the University Senate, Electoral Commission, ICT Department, and Ministry of Education, and areas for future research, including blockchain integration and biometrics, have been outlined.


---

# REFERENCES

*   Baguma, R. and Muyinda, P. B. (2019) 'Leveraging Campus Local Area Networks for Electronic Guild Elections: A Case of Kyambogo University', *Journal of Educational Technology in Developing Countries*, 11(3), pp. 104-118.
*   Byaruhanga, J. and Atwine, A. (2022) 'A Usability and Acceptance Study of Online Student Voting at Mbarara University of Science and Technology', *Journal of Computing and Educational Technology*, 15(2), pp. 67-84.
*   Council of Europe (2018) *Recommendation CM/Rec(2017)5 of the Committee of Ministers to Member States on Standards for E-Voting*, Strasbourg: Council of Europe Publishing.
*   Creswell, J. W. and Creswell, J. D. (2022) *Research Design: Qualitative, Quantitative, and Mixed Methods Approaches*, 6th edn. Thousand Oaks: SAGE Publications.
*   Davis, F. D. (1989) 'Perceived Usefulness, Perceived Ease of Use, and User Acceptance of Information Technology', *MIS Quarterly*, 13(3), pp. 319-340.
*   DeLone, W. H. and McLean, E. R. (2003) 'The DeLone and McLean Model of Information Systems Success: A Ten-Year Update', *Journal of Management Information Systems*, 19(4), pp. 9-30.
*   Government of Uganda (2001) *The Universities and Other Tertiary Institutions Act, 2001 (as amended)*, Kampala: Uganda Printing and Publishing Corporation.
*   Government of Uganda (2011) *The Electronic Transactions Act, 2011*, Kampala: Uganda Printing and Publishing Corporation.
*   Government of Uganda (2013) *The Uganda Communications Act, 2013*, Kampala: Uganda Printing and Publishing Corporation.
*   Government of Uganda (2019) *The Data Protection and Privacy Act, 2019*, Kampala: Uganda Printing and Publishing Corporation.
*   IEEE (2015) *IEEE P1622 - Draft Standard for Electronic Voting*, New York: IEEE.
*   Kajumbula, R. and Nsubuga, I. (2023) 'Voter Turnout and Integrity Challenges in Paper-based Student Guild Elections in Uganda', *Journal of Student Affairs in Africa*, 11(1), pp. 78-95.
*   Kanyesigye, A. and Atwine, J. (2021) 'ICT Infrastructural Readiness and Smart Device Adoption among Ugandan University Students', *Journal of Educational Technology*, 14(4), pp. 45-62.
*   Kimbowa, M. and Nabukeera, M. (2023) 'Student Elections and Democratic Representation: Assessing Paper Ballot Voting vs. Online Voting in Kyambogo University Guild Elections', *East African Journal of Peace and Human Rights*, 29(2), pp. 145-163.
*   Laudon, K. C. and Laudon, J. P. (2020) *Management Information Systems: Managing the Digital Firm*, 16th edn. New York: Pearson.
*   Lwanga, E., Mugisha, J. and Ssewanyana, J. (2020) 'Adoption of Mobile Voting Systems in Higher Education: A Case Study of Makerere University', *East African Journal of Computer Science*, 4(2), pp. 45-58.
*   Ministry of Education and Sports (MoES) (2020) *Education Sector Digital Transformation Strategy (2020-2025)*, Kampala: MoES.
*   Mukasa, A., Tumusiime, G. and Kanyesigye, E. (2024) 'Cryptographic Security in Web-based Voting Systems: A Review of Implementing SHA-256 and bcrypt in PHP Applications', *East African Journal of Science and Technology*, 16(1), pp. 88-102.
*   Nabatanzi, M. (2024) 'Database Transaction Management in Public Services: A Review of InnoDB Compliance', *East African Journal of Software Engineering*, 5(1), pp. 12-29.
*   Nabatanzi, S. and Kigozi, A. (2020) 'SQL Injection and XSS Vulnerabilities in Institutional Web Portals: Vulnerability Analysis and Countermeasures in Ugandan Universities', *International Journal of Computer Security*, 28(3), pp. 115-130.
*   National Information Technology Authority Uganda (NITA-U) (2022) *National IT Survey 2021/2022: ICT Access and Usage in Uganda's Higher Education Sector*, Kampala: NITA-U.
*   Nsubuga, H. and Baguma, R. (2021) 'Securing Electronic Elections: An OTP-based Multi-factor Authentication Framework for Institutional Elections in Uganda', *Uganda Journal of Computing and Information Technology*, 7(1), pp. 12-28.
*   Okello, D. and Nabukenya, J. (2022) 'Evaluating the Feasibility of Online Voting in Uganda's Public Universities', *African Journal of Information Systems*, 14(3), pp. 210-227.
*   Ssekyewa, C. and Muyinda, P. (2021) 'The Role of Multi-factor Authentication in Enhancing Trust in Electronic Governance: A Case Study of Ugandan Institutions', *Journal of E-Government Research*, 17(4), pp. 22-41.
*   Ssewanyana, J. (2022) 'Data Privacy and the Protection of Voter Credentials in Digital Voting Systems: A Review of Uganda Data Protection and Privacy Act 2019', *Uganda Law Review*, 10(2), pp. 112-130.
*   Ssewanyana, J., Baguma, K. and Lwanga, M. (2018) 'A Conceptual Model for E-Voting Acceptance in Ugandan Higher Institutions', *International Journal of Information Management*, 40, pp. 122-135.
*   Stair, R. M. and Reynolds, G. W. (2018) *Principles of Information Systems*, 13th edn. Boston: Cengage Learning.
*   Tumwine, S. and Mutesasira, P. (2023) 'Evaluating E-Governance Platforms in Uganda: Security, Usability and Policy Frameworks', *Ugandan Journal of Public Administration*, 18(2), pp. 56-72.
*   Uganda Communications Commission (UCC) (2023) *Uganda Communications Market Performance Report: Q4 2023*, Kampala: UCC.
*   Uganda National Bureau of Statistics (UBOS) (2024) *Uganda Demographic and ICT Indicators 2023/24*, Kampala: UBOS.
*   Yin, R. K. (2018) *Case Study Research and Applications: Design and Methods*, 6th edn. Thousand Oaks: SAGE Publications.

---

# APPENDICES

## Appendix I: User Questionnaire

**DEPARTMENT OF COMPUTER SCIENCE**  
**KYAMBOGO UNIVERSITY, KAMPALA, UGANDA**  

Dear Respondent,  
I am George Makumbi, a student at Kyambogo University pursuing a Bachelor's Degree in Information Technology. I am conducting a research study on the **Design and Implementation of a Secure Online Voting System for University Guild Elections**. This questionnaire is designed to gather data regarding the current voting system and evaluate the usability, security, and acceptance of the proposed system. Your responses will be kept strictly confidential and used for academic purposes only.

### Section A: Demographic Information
1.  **Faculty/School:**  
    [ ] School of Computing & Information Science  
    [ ] Faculty of Engineering  
    [ ] School of Management Sciences  
    [ ] Faculty of Science  
    [ ] Faculty of Education  
    [ ] Other (Please Specify): \_\_\_\_\_\_\_\_\_\_\_\_\_\_  
2.  **Academic Year of Study:**  
    [ ] Year 1  
    [ ] Year 2  
    [ ] Year 3  
    [ ] Year 4 / Graduate  
3.  **Role in Guild Elections:**  
    [ ] Student / Voter  
    [ ] Candidate / Agent  
    [ ] Polling Assistant / Invigilator  

### Section B: Evaluation of the Current Manual Paper-Based System
*Please rate the following statements on a scale of 1 to 5: (1 = Strongly Disagree, 2 = Disagree, 3 = Neutral, 4 = Agree, 5 = Strongly Agree)*

| Questionnaire Item | 1 | 2 | 3 | 4 | 5 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Q1.** The current manual paper ballot voting process is fast and efficient. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q2.** The current system is secure and free from ballot stuffing and fraud. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q3.** The process of counting votes physically by hand is transparent and fair. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q4.** Standing in long queues at physical polling stations is convenient for me. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q5.** Off-campus students on internship/teaching practice are disenfranchised. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q6.** The delay in announcing results causes tension and disputes on campus. | [ ] | [ ] | [ ] | [ ] | [ ] |

### Section C: Perceived Ease of Use and Usefulness of the Proposed Online Voting System
*Please rate the following statements based on the concept and description of the online system:*

| Questionnaire Item | 1 | 2 | 3 | 4 | 5 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Q7.** I would find it easy to vote using a web browser on my smartphone. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q8.** Navigating through the candidates on a digital ballot is simple. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q9.** Receiving a One-Time Password (OTP) via email improves login security. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q10.** The system would enable me to vote from off-campus during internships. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q11.** Automated, real-time results display reduces election disputes. | [ ] | [ ] | [ ] | [ ] | [ ] |
| **Q12.** I would trust an online system over the current manual paper ballot. | [ ] | [ ] | [ ] | [ ] | [ ] |

Thank you for your valuable time and participation.

---

## Appendix II: Semi-Structured Interview Guide

**TARGET PARTICIPANTS: Electoral Commission Officials & University Administrators**

### Introduction
*   Introduce the researcher and state the objectives of the interview.
*   Obtain written informed consent and verify permission for audio recording.
*   Assure confidentiality and describe how the data will be used.

### Interview Questions

1.  **System Workflow and Logistics:**
    *   Could you describe the administrative steps and logistics involved in organizing a guild election at Kyambogo University?
    *   How is the student voter register compiled and distributed to polling stations?
2.  **Challenges and Integrity Issues:**
    *   What security challenges or integrity issues have you observed in past guild elections (e.g., impersonation, double-voting)?
    *   How does the Electoral Commission handle disputes during vote counting?
3.  **Financial and Resource Allocation:**
    *   What are the major cost drivers of conducting manual elections (e.g., printing, personnel, security)?
    *   In your estimation, how does this affect the university budget?
4.  **Technology Readiness:**
    *   What is your assessment of the university’s current network infrastructure and server hosting capabilities?
    *   How would you evaluate the computer literacy of the Electoral Commission staff?
5.  **Online System Requirements:**
    *   What functional features do you believe are critical for a digital voting system?
    *   What security and audit mechanisms should the system implement to build student trust?
6.  **Policy and Legal Compliance:**
    *   What institutional policy changes or constitutional amendments are necessary to support electronic voting?
    *   How can the university ensure compliance with Uganda’s Data Protection Act when handling student records?

*Thank the participant for their time and contribution.*

---

## Appendix III: User Acceptance Testing (UAT) Scripts

### UAT Script 1: Voter Role (Student Vote Casting)

*   **Test Case ID**: UAT-VOT-01
*   **Actor**: Student Voter
*   **Prerequisites**: The student must be registered, have active portal credentials, and the voting window must be marked active.

| Step | Action | Expected Output | Actual Output | Status | Pass Sign-off |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1.** | Open browser, navigate to: `https://localhost/voting_system/` | System homepage loads showing details and countdown. | Portal loads successfully. | PASS | \_\_\_\_\_\_\_\_ |
| **2.** | Click "Student Login," enter valid Registration Number and password. | System validates login, generates 6-digit OTP, and sends email. | OTP sent, redirected to verification page. | PASS | \_\_\_\_\_\_\_\_ |
| **3.** | Enter the 6-digit OTP from the email and click "Verify." | Session authenticated, redirected to active ballot page. | Ballot loaded successfully. | PASS | \_\_\_\_\_\_\_\_ |
| **4.** | Select one candidate per GRC and Guild President position. | Candidates highlighted, choices recorded locally. | Selections made. | PASS | \_\_\_\_\_\_\_\_ |
| **5.** | Click "Submit," review selections, and click "Confirm Vote." | Database transaction executed, vote recorded, `has_voted` status set. | Directed to receipt page. | PASS | \_\_\_\_\_\_\_\_ |
| **6.** | Copy receipt hash, click "Logout." | Session terminated, redirected to login page. | Logged out. | PASS | \_\_\_\_\_\_\_\_ |

---

### UAT Script 2: Administrator Role (Election Management)

*   **Test Case ID**: UAT-ADM-01
*   **Actor**: Electoral Commission Administrator
*   **Prerequisites**: Valid administrator credentials.

| Step | Action | Expected Output | Actual Output | Status | Pass Sign-off |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1.** | Navigate to admin portal, enter credentials, and click "Sign In." | Admin dashboard loads showing turnout charts. | Dashboard loaded. | PASS | \_\_\_\_\_\_\_\_ |
| **2.** | Click "Upload Register," select student list CSV, click "Import." | Database populated, imports student accounts. | Student registers imported. | PASS | \_\_\_\_\_\_\_\_ |
| **3.** | Click "Elections," select "Create Election," configure title and dates. | Election configured and scheduled in database. | Election scheduled. | PASS | \_\_\_\_\_\_\_\_ |
| **4.** | Click "Candidates," click "Add Candidate," enter details, upload photo. | Candidate record written, set to verified. | Candidate verified. | PASS | \_\_\_\_\_\_\_\_ |
| **5.** | Click "View Audit Log." | Audit log table loads displaying logins and actions chronologically. | Log displayed. | PASS | \_\_\_\_\_\_\_\_ |
| **6.** | Change election status to "Closed." | Voting closed, tallies computed, results dashboard updated. | Results published. | PASS | \_\_\_\_\_\_\_\_ |

---

## Appendix IV: System Installation and Configuration Guide

### 1. Prerequisites and Development Environment Setup
To install and run the secure online voting system, ensure your system has the following software installed:
*   **XAMPP Control Panel** (version 8.1.0 or higher, containing Apache 2.4 and MariaDB/MySQL 10.4).
*   **PHPMailer** library files (included in the `app/utils/` or vendor directory).
*   A modern web browser (Google Chrome, Mozilla Firefox, Microsoft Edge, or Safari).

### 2. Database Installation and Configuration
1.  Launch the XAMPP Control Panel and start the **Apache** and **MySQL** services.
2.  Open your web browser and navigate to `http://localhost/phpmyadmin/`.
3.  Click on the **Databases** tab, enter the database name `finalyearproject` (or the name configured in database files), and click **Create**.
4.  Select the newly created database, click on the **Import** tab, browse to locate the SQL schema file `c:\wamp64\www\finalyearproject\database\database.sql`, and click **Go**.
5.  Verify that the tables (`students`, `candidates`, `votes`, `admin`, `elections`, `feedback`, `audit_log`) are imported successfully.

### 3. Application Deployment and Path Configuration
1.  Copy the entire project folder `finalyearproject` and paste it into the XAMPP web root directory, typically located at `C:\xampp\htdocs\`.
2.  Open the file `app/utils/db_connection.php` and verify the database configuration:
    ```php
    <?php
    $host = 'localhost';
    $db   = 'finalyearproject';
    $user = 'root';
    $pass = ''; // Default XAMPP MySQL password is empty
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
         $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
    ?>
    ```

### 4. PHPMailer SMTP Configuration for OTP Delivery
To enable OTP email delivery, open the file `app/utils/smtp_mailer.php` (or where email sending is configured) and configure the SMTP parameters:
1.  Set the Host to `smtp.gmail.com` (or your university mail server).
2.  Set the Port to `587` for TLS or `465` for SSL.
3.  Enable SMTP authentication and enter your institutional email credentials.
4.  If using Gmail, generate an **App Password** from your Google account security settings and use it as the SMTP password.
5.  Save the file.

### 5. Running the Application
1.  Open the web browser and navigate to `http://localhost/finalyearproject/public/index.php` to access the student portal.
2.  To access the administrative dashboard, navigate to `http://localhost/finalyearproject/public/admin_login.html`.
3.  Log in using the default administrative credentials:
    *   **Username**: `admin`
    *   **Password**: `password`
4.  Configure an election, import student registers, and test the vote-casting workflow.
