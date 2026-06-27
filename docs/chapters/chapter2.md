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
