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
