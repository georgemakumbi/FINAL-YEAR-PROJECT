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
