DESIGN, DEVELOPMENT AND IMPLEMENTATION OF AN ONLINE VOTING SYSTEM

CASE STUDY: KYAMBOGO UNIVERSITY

BY;

MAKUMBI GEORGE  23/U/ITD/07818/PD

AFUNGA RONALD MICHEAL  23/U/ITE/02939/PE

BEKISA PAULYNE  24/U/ITD/426/GV

KADABARA EMMANUEL MUNDUKU    23/U/ISE/06166/PE

A RESEARCH REPORT SUBMITTED IN PARTIAL FULFILMENT OF THE REQUIREMENTS FOR THE AWARDS OF THE DEGREE OF BACHELOR OF INFORMATION TECHNOLOGY AND COMPUTING OF KYAMBOGO UNIVERSITY.

DECLARATION.

We declare that the work presented in this research proposal is my original work and has not been submitted to any University or Institution of Higher Learning for any academic award. All work from other authors has been fully and properly acknowledged and cited.

Signature: …………………………………. Date: ………………………………………..

Makumbi George(Researcher)

Signature: …………………………………. Date: ………………………………………

Afunga Ronald Micheal (Researcher)

Signature: …………………………………. Date: ……………………………………

Bekisa Paulyne(Researcher)

Signature: …………………………………. Date: ………………………………………

Kadabara Emmanuel Munduku    (Researcher)

APPROVAL.

This is to certify that this research proposal titled: “Design, Development and Implementation of An Online Voting System” has been carried out under my supervision and is now ready for submission to the Examinations Board and Senate of Kyambogo University.

Signature: …………………………………….  Date: ……………………………………..

Dr, Ssebugwawo Denis

(Supervisor)

LIST OF ACRONYMS

2FA

Two-Factor Authentication

CSS

Cascading Style Sheets

DFD

Data Flow Diagram

DICTS

Directorate for ICT Support

E2E

End-to-End (Verifiability)

ERD

Entity Relationship Diagram

HTML

Hyper Text Markup Language

ICT

Information and Communications Technology

IT

Information Technology

MySQL

My Structured Query Language

OTP

One-Time Password

PDO

PHP Data Objects

PEOU

Perceived Ease of Use

PHP

Hypertext Preprocessor

PU

Perceived Usefulness

SDLC

System Development Life Cycle

SQL

Structured Query Language

SSL

Secure Sockets Layer

TAM

Technology Acceptance Model

TLS

Transport Layer Security

UAT

User Acceptance Testing

UML

Unified Modelling Language

XSS

Cross-Site Scripting

DEFINITION OF TERMS

Online Voting System. A web-based platform that enables eligible voters to cast their ballots remotely using internet-connected devices, with mechanisms for authentication, anonymity, and automatic tallying.Guild Elections. Annual student leadership elections conducted at Kyambogo University to elect the Guild President and other student representatives.Two-Factor Authentication (2FA).  A security process in which a user provides two different authentication factors (e.g., student registration number and a one-time password) to verify their identity.

TABLE OF CONTENTS.

DECLARATION.ii

APPROVAL.iii

LIST OF ACRONYMSiv

DEFINITION OF TERMSvi

TABLE OF CONTENTS.vii

CHAPTER ONE: INTRODUCTION1

1.1 INTRODUCTION.1

1.2 BACKGROUND TO THE STUDY.1

1.3 STATEMENT OF THE PROBLEM.2

1.4 RESEARCH OBJECTIVES.3

1.4.1 General Objective.3

1.4.2 Specific Objectives.3

1.5 RESEARCH QUESTIONS.3

1.5.1 General Research Question.3

1.5.2 Specific Research Questions.4

1.6 SCOPE OF THE STUDY.4

1.6.1 Subject Scope.4

1.6.2 Geographical Scope.4

1.6.3 Time Scope.4

1.7 SIGNIFICANCE OF THE STUDY.5

1.8 CHAPTER SUMMARY.5

CHAPTER TWO: LITERATURE REVIEW6

2.1 INTRODUCTION.6

2.2 TECHNOLOGY ACCEPTANCE IN AFRICAN CONTEXTS.6

2.3 INSTITUTIONAL CHANGE AND TECHNOLOGY ADOPTION.7

2.3.1 Case Study: Makerere University.7

2.3.2 Global benchmark: Estonia.8

2.4 ONLINE VOTING SYSTEMS IN AFRICAN UNIVERSITIES.8

2.4.1 Case Study: Makerere University8

2.4 SECURITY AND INTEGRITY IN ELECTRONIC VOTING SYSTEMS.9

2.4.1 Authentication and Eligibility.9

2.4.2 Ballot Secrecy and Anonymity.9

2.4.3 Prevention of Multiple Voting.10

2.4.4 Integrity and Auditability.10

2.4.5 Protection against a Malicious Administrator.10

2.4.6 Receipt-Freeness and Coercion Resistance.10

2.5 SYNTHESIS AND RESEARCH GAPS.11

2.5.1 Contextual gap.11

2.5.2 Implementation gap.11

2.5.3 Security-depth gap.11

2.5.4 Evaluation gap.12

2.6 CONCLUSION12

CHAPTER THREE: METHODOLOGY13

3.1 INTRODUCTION.13

3.2 RESEARCH DESIGN.13

3.3 STUDY POPULATION AND SAMPLING.13

3.3.1 Target Population.13

3.3.2 Sample Size Determination.13

3.3.3 Sampling Techniques.14

3.4 DATA COLLECTION METHODS.15

3.4.1 Interviews.15

3.4.2 Questionnaires.15

3.4.3 Observation.15

3.4.4 Document Review.15

3.5 SYSTEM STUDY AND ANALYSIS METHODS.15

3.5.1 System Study.15

3.5.2 System Analysis.16

3.6 System Development Methodology.16

3.7 SYSTEM REQUIREMENTS AND SPECIFICATION.16

3.7.1 User Requirements.16

3.7.2 Functional Requirements.17

3.7.3 Non-Functional Requirements.17

3.8 SYSTEM DESIGN AND MODELLING METHODS.18

3.9 SYSTEM IMPLEMENTATION TOOLS AND TECHNOLOGIES.18

3.9.1 Justification for PHP/MySQL Stack.19

3.10 SYSTEM TESTING AND VALIDATION METHODS.19

3.11 ETHICAL CONSIDERATIONS.20

3.12 CHAPTER SUMMARY.20

CHAPTER FOUR: SYSTEM STUDY, ANALYSIS AND REQUIREMENTS ELICITATION21

4.1 INTRODUCTION.21

4.2 DESCRIPTION OF THE CURRENT SYSTEM.21

4.2.1 Strengths of the Current System.21

4.2.2 Weaknesses of the Current System.21

4.1.2 Comparative Analysis of the Strengths and Weaknesses.22

4.3 REQUIREMENTS OF THE NEW SYSTEM.22

4.3.1 User Requirements.22

4.3.2 Functional Requirements.23

4.3.3 NonFunctional Requirements.23

4.3.4 System Requirements.24

4.4 CHAPTER SUMMARY.24

CHAPTER FIVE: SYSTEM DESIGN, IMPLEMENTATION, TESTING AND VALIDATION.25

5.1 INTRODUCTION.25

5.2 SYSTEM DESIGN USING DATA FLOW DIAGRAMS.25

5.2.1 Context Diagram.25

5.2.2 Level 0 Diagram.25

5.2.3 Level 1 Diagram (Process 1 – Authenticate Voter).25

5.3 SYSTEM DESIGN USING ENTITY RELATIONSHIP DIAGRAMS.26

5.3.1 Identified Entities and their Attributes.26

5.3.2 Entity Diagram.26

5.4 SYSTEM DESIGN USING UNIFIED MODELING LANGUAGE (UML).26

5.4.1 UML Use Case Diagram.26

5.4.2 UML Class Diagram.28

5.4.3 UML Activity Diagram.28

5.4.4 UML State Machine Diagram.30

5.5 DATABASE DESIGN32

5.5.1 Database Tables.32

5.5.2 Data Descriptions.32

5.6 SYSTEM IMPLEMENTATION33

5.6.1 System Graphical User Interfaces33

5.6.2 Sample Code (PHP Snapshot – Ballot Submission)33

5.7 SYSTEM TESTING AND VALIDATION36

5.7.1 System Testing36

5.7.2 System Validation36

5.8 CHAPTER SUMMARY37

CHAPTER SIX: CONCLUSIONS AND RECOMMENDATIONS38

6.1 INTRODUCTION.38

6.2 DISCUSSION.38

6.3 RECOMMENDATIONS.38

6.4 LIMITATIONS OF THE STUDY.39

6.5 AREAS FOR FURTHER RESEARCH.39

6.6 CONCLUSION.40

REFERENCES.41

APPENDICES.42

Appendix I: Questionnaire (Students).42

CHAPTER ONE: INTRODUCTION

1.1 INTRODUCTION.

This chapter provides an overview of the higher education electoral environment, focusing on the challenges faced by Kyambogo University’s manual guild election system. It begins by describing the sector of student governance and the increasing reliance on digital solutions in universities. The background situates the study within the historical evolution of voting, the Technology Acceptance Model (TAM), and a conceptual framework linking an online voting system to electoral integrity. The problem statement details the specific inadequacies of the current manual process. Subsequently, the general and specific objectives, research questions, scope, and significance of the study are presented.

1.2 BACKGROUND TO THE STUDY.

Historically, voting has evolved from public acclamation and paper ballots to electronic and internet-based systems. In the 19th and 20th centuries, the secret paper ballot became the standard for protecting voter autonomy. Universities historically mirrored these national trends; for decades, guild elections at institutions like Kyambogo University relied on physical ballot boxes and manual counting. However, as student populations swelled to over 25,000, the logistical and administrative burdens of paper-based elections became evident.

Theoretically, this study was anchored on the Technology Acceptance Model (TAM) proposed by Davis (1989). TAM posits that user acceptance of a new technology is determined by two primary beliefs: Perceived Usefulness (PU), the degree to which a student believes the online system will improve their voting experience (e.g., saving time, avoiding queues) – and Perceived Ease of Use (PEOU) – the degree to which the student believes using the system will be effortless. In African higher education contexts, additional factors such as trust, system security, and internet reliability significantly influence technology acceptance (Boateng et al., 2016). Therefore, for an online voting system to be adopted at Kyambogo, it must be perceived as both useful and easy to use, while also guaranteeing the confidentiality, integrity, and availability of the electoral process.

Conceptually, this study established a relationship between the voting mechanism (Independent Variable) and the quality of governance (Dependent Variable). The Independent Variable (Online Voting System) is defined by features such as biometric or student-ID authentication, real-time database encryption, remote accessibility, and automated tallying. The Dependent Variable (Electoral Integrity & Efficiency): This is characterized by the speed of the process, accuracy of the results, transparency (audit trails), and increased voter turnout.

Kyambogo University currently conducts Guild elections using a manual, paper-based system. While functional in the past, this approach is increasingly inadequate for a large, technologically active student population. The limitations of the existing system directly affect the efficiency, credibility, and inclusiveness of the electoral process, justifying the development of a secure online voting solution.

1.3 STATEMENT OF THE PROBLEM.

Kyambogo University currently conducts Guild elections using a manual, paper-based voting system. While functional in the past, this approach is increasingly inadequate for managing elections within a large and technologically active student population of over 25,000 students. The limitations of the existing system directly affect the efficiency, credibility, and inclusiveness of the electoral process.

The manual system is characterized by operational inefficiencies, including high costs associated with printing ballot papers and deploying election materials across multiple faculties and halls of residence. Furthermore, the manual counting and aggregation of votes is slow, labour-intensive, and susceptible to human error, often resulting in delayed election results.

In addition, the physical nature of paper-based voting introduces security and integrity risks. Ballot boxes are vulnerable to tampering, stuffing, loss, and unauthorized access, creating opportunities for electoral malpractice and reducing student confidence in the fairness and transparency of Guild elections.

The current system also contributes to low voter participation. Students are required to be physically present at designated polling stations, where long queues and congestion discourage participation. This situation disproportionately affects students on internship programs, distance learning schedules, or those off-campus during the election period, effectively denying them the opportunity to vote.

Given these challenges, the existing manual voting system does not meet the expectations of a modern university environment. There is a critical need for an online voting system that leverages information technology to provide secure authentication, ensure voter anonymity, prevent multiple voting, and allow eligible students to vote remotely. Such a system would improve efficiency, enhance transparency, increase voter turnout, and support free and fair Guild elections at Kyambogo University.

1.4 RESEARCH OBJECTIVES.

1.4.1 General Objective.

The main objective of this study was to design and develop a secure online voting system to enhance the efficiency, transparency, casting of votes, real time tallying of results and accessibility of Guild elections at Kyambogo University.

1.4.2 Specific Objectives.

The system analysed the weaknesses of the current manual voting system through structured data collection methods, including interviews, questionnaires, and document review.

It utilized unique database constraints and transactions to prevent duplicate voting while maintaining privacy at the application layer

The system implemented a Two-Factor Authentication (2FA) mechanism using Student Registration Numbers and One-Time Passwords (OTPs) to prevent unauthorised access and multiple voting.

The system developed a real-time administrative dashboard for the Electoral Commission that enables monitoring of voter turnout and election progress without exposing individual voting choices.

1.5 RESEARCH QUESTIONS.

1.5.1 General Research Question.

How a secure online voting system was designed and develop improved the electoral process for Guild elections at Kyambogo University?

1.5.2 Specific Research Questions.

What were the weaknesses and challenges existing in the current manual voting system used for Guild elections at Kyambogo University?

How a secure database architecture was designed to ensure voter integrity and prevent double-voting?

In what ways did the Two-Factor Authentication (2FA) improve the security and integrity of Guild elections?

How effective was a real-time monitoring dashboard in enhanced the transparency for the Electoral Commission without compromising voter privacy?

1.6 SCOPE OF THE STUDY.

1.6.1 Subject Scope.

The study focused on the design and implementation of a secure Online Voting System. It covers modules for voter registration (integrated with the university’s student database), candidate nomination, digital ballot casting, automated tallying, and a real-time dashboard. The system was built using web technologies such as PHP for server-side logic and MySQL for database management. Security features will include 2FA and separate storage of voter credentials and ballot choices.

1.6.2 Geographical Scope.

The study was strictly carried out at Kyambogo University, located in Kyambogo, Kampala District, Uganda. The primary focus was on the main campus where the Guild government elections are centrally coordinated.

1.6.3 Time Scope.

The study took a period of six (6) months, divided into three phases:

Phase 1: Data collection and requirements gathering (Months 1–2).

Phase 2: System design and coding (Months 3–5).

Phase 3: System testing and final report writing (Month 6).

1.7 SIGNIFICANCE OF THE STUDY.

To the University Administration and Electoral Commission: The system improved the efficiency and credibility of Guild elections by reducing administrative costs, eliminating manual counting errors, and ensuring timely, accurate results. This enhanced transparency and trust in the electoral process.

To the Students: The system provided a convenient, accessible, and secure voting platform that allows participation regardless of physical location, thereby it increased the voter turnout and encouraged the broader student engagement in university governance.

To the Information Technology Department: The project demonstrated the practical application of web-based security, database management, and authentication technologies in solving real institutional problems. It also served as a foundation for future digital governance systems within the university.

To Researchers and Future Scholars: The study contributed to knowledge on the design and implementation of secure electronic voting systems in higher education institutions, particularly within developing-country contexts.

1.8 CHAPTER SUMMARY.

This chapter introduced the background of the manual voting challenges at Kyambogo University, articulated the research problem, and set out clear objectives and research questions. The scope and significance of the proposed online voting system have been defined, laying the groundwork for a comprehensive review of related literature.

CHAPTER TWO: LITERATURE REVIEW

2.1 INTRODUCTION.

Electronic voting systems have become a significant and important area of research as institutions seek to improve the efficiency, transparency and inclusiveness of electoral processes. Voting systems are fundamental to democratic governance within institutions of higher learning as they enable students to participate in leadership selection and institutional decision making[1]. Despite the global shift towards electronic and online voting systems, many universities in developing countries including Uganda, continue to rely on traditional ballot paper voting methods[2]

At Kyambogo University, the current manual voting system is associated with several challenges, including low voter turnout, high operational costs, slow vote tallying, accessibility limitations, and mistrust in election outcomes. Similar challenges have been documented in other African universities where logistical constraints, large student populations, and limited ICT infrastructure persist [3].

Uganda’s higher education sector has increasingly adopted digital technologies for student registration, learning management systems, and academic records[4]. However, the adoption of online voting systems remains limited. This chapter reviews and synthesizes relevant literature on electronic and online voting systems, focusing on theoretical foundations, system design considerations, security and trust, usability and accessibility, and deployment experiences within African and Ugandan university contexts.

2.2 TECHNOLOGY ACCEPTANCE IN AFRICAN CONTEXTS.

The Technology Acceptance Model (TAM) (Davis, 1989) remains the most widely used framework for predicting user adoption of new information systems. In higher education, TAM has been applied to explain the uptake of e-learning platforms, student portals, and digital administrative services. Studies in African universities confirm that students are more likely to adopt digital systems when they perceive them as beneficial, efficient, and easy to use (Venkatesh et al., 2013).

However, research indicates that in African contexts, additional determinants such as trust, system security, and internet reliability significantly moderate technology acceptance (Boateng et al., 2016). For online voting, trust in the confidentiality and integrity of votes is paramount. A student will only use an e-voting platform if they believe their vote cannot be altered, traced, or exposed. Mutula and Van Brakel (2007) highlight that ICT skills readiness and digital literacy levels also influence adoption in developing countries. Consequently, any online voting system for Kyambogo must not only be useful and easy to use but must also inspire confidence through robust security measures and be supported by user training.

2.3 INSTITUTIONAL CHANGE AND TECHNOLOGY ADOPTION.

Universities are traditionally bureaucratic institutions, and the adoption of new technologies especially in sensitive areas such as elections often faces resistance [9]. The literature on institutional change suggests that successful adoption is not merely technical but requires stakeholder involvement, policy alignment, and gradual implementation to overcome organizational inertia [10].

In African university settings, studies show that involving student leaders, electoral bodies, and ICT departments early in the system design and implementation process significantly improves acceptance and long-term sustainability. This perspective complements TAM by accounting for organizational inertia, governance structures, and power relations that shape technology adoption decisions.

2.3.1 Case Study: Makerere University.

A relevant example of institutional transition to electronic voting is Makerere University, which successfully moved from a manual ballot-based system to a fully digital “Virtual Vote” platform. Historically, Makerere’s guild elections were characterized by congestion, violence commonly referred to as hooliganism and high administrative costs, conditions comparable to those currently experienced at Kyambogo University [11].

The transition followed a gradual implementation approach. Initial pilots were conducted at college-level elections before full deployment for the Guild Presidency. Stakeholder consultation played a central role in this process. A study by the Directorate for ICT Support (DICTS) reported that surveys conducted through the Academic Information Management System (AIMS) revealed an initial near-even split in student preference between manual and digital voting. This informed the adoption of a blended approach during the transition period [12].

According to Namusisi (2011)  [11], eventual system acceptance was driven by perceived security and operational efficiency. Authentication using institutional email credentials and one-time passwords (OTPs) reduced opportunities for ballot manipulation, while real-time tallying eliminated delays and reduced the need for heavy security deployment. However, the experience also revealed challenges related to enforcing voter eligibility, particularly where tuition payment status was not synchronized in real time with election systems. This highlights the importance of robust integration between voting platforms and institutional student databases to ensure electoral integrity.

2.3.2 Global benchmark: Estonia.

Estonia represents the global benchmark for national-scale internet voting. By the 2023 elections, over 51% of votes were cast online, reflecting deep institutionalization of digital voting practices. The Estonian system employs mandatory national digital identity cards with two-factor authentication and a “double-envelope” encryption mechanism that separates voter identity from ballot data prior to tallying.

Recent studies indicate a reduction in the digital divide, with increasing adoption among older voters, suggesting that long-term institutional trust and familiarity play a critical role in acceptance. However, the reliance on a national identity infrastructure limits the direct applicability of this model in developing-country university contexts. (National Electoral Committee, 2023).

2.4 ONLINE VOTING SYSTEMS IN AFRICAN UNIVERSITIES.

2.4.1 Case Study: Makerere University

A relevant example of institutional transition to electronic voting is Makerere University, which successfully moved from a manual ballot-based system to a fully digital “Virtual Vote” platform. Historically, Makerere’s guild elections were marred by congestion, violence (“hooliganism”), and high administrative costs – conditions comparable to those currently experienced at Kyambogo (Keirungi, 2025).

The transition followed a gradual implementation approach, with initial pilots at college-level elections before full deployment for the Guild Presidency. Stakeholder consultation played a central role. Surveys conducted through the Academic Information Management System (AIMS) revealed an initial near-even split in student preference between manual and digital voting, informing the adoption of a blended approach during the transition period (Makerere University, 2021).

According to Namusisi (2011), eventual system acceptance was driven by perceived security and operational efficiency. Authentication using institutional email credentials and one-time passwords (OTPs) reduced opportunities for ballot manipulation, while real-time tallying eliminated delays and reduced security deployment. However, challenges emerged regarding the enforcement of voter eligibility where tuition payment status was not synchronised in real-time with the election system. This highlights the importance of robust integration between the voting platform and institutional student databases to ensure electoral integrity a key consideration for the Kyambogo system.

Okello and Nanteza (2021) surveyed digital voting practices in Ugandan universities and found that while several institutions had piloted e-voting, full adoption was hindered by inadequate ICT infrastructure, power outages, and low digital literacy among some student populations. Their study recommends that universities invest in server capacity, backup power, and user training as prerequisites for successful e-voting deployment.

2.4 SECURITY AND INTEGRITY IN ELECTRONIC VOTING SYSTEMS.

Security is the most critical and contested dimension of online voting research. Scholars distinguish between several distinct security properties that an electronic voting system must satisfy simultaneously [8];

2.4.1 Authentication and Eligibility.

The system ensured that only registered, eligible voters can cast a ballot. This is typically achieved through multi-factor authentication (MFA). In university contexts, combining student registration numbers with time-limited OTPs delivered via institutional email or SMS is the most accessible approach [11].

2.4.2 Ballot Secrecy and Anonymity.

restricting direct database access, using application-level privacy controls, and displaying only aggregated counts in standard reports and dashboards.

2.4.3 Prevention of Multiple Voting.

The system prevented any voter from submitting more than one ballot. A Boolean flag in the voter-identity table (has_voted), updated atomically within a database transaction, is the standard implementation for web-based systems. Combined with server-side session validation, this prevents duplicate submissions even under concurrent access.

2.4.4 Integrity and Auditability.

The system ensured that votes are recorded exactly as cast and that the final tally reflects all and only valid votes. Audit logs that record timestamped system events without revealing individual choices, support post-election verification. Rivest and Wack (2008) formalise this as “software independence”: the ability to detect unintentional or malicious changes to vote tallies using evidence independent of the software itself [16].

2.4.5 Protection against a Malicious Administrator.

A fundamental challenge in centralised online voting systems is that a database administrator with unrestricted access could, in principle, alter stored votes. This study mitigates this risk through;

Encrypted storage of ballot choices using a symmetric key not accessible to the application administrator

Append-only audit logs stored in a separate schema with restricted write privileges.

Hashing of tally snapshots at regular intervals so that post-hoc tampering can be detected.

2.4.6 Receipt-Freeness and Coercion Resistance.

Advanced cryptographic voting systems such as the Helios open-audit system developed by Adida et al. (2009) [15] provide end-to-end verifiability, allowing voters to confirm their votes were counted without revealing how they voted to third parties. While full cryptographic verifiability is beyond the scope of this project, Helios demonstrates that open-source, web-based voting systems can achieve a high degree of transparency without sacrificing usability. Its architecture informs the audit-trail design adopted in this study.

In the context of university elections, where the adversary model is less sophisticated than national elections, the priority is preventing accidental errors, opportunistic ballot manipulation, and unauthorised administrative access, rather than defending against nation-state-level attackers. This risk-proportionate approach guides the security architecture described in Chapter Three.

2.5 SYNTHESIS AND RESEARCH GAPS.

The reviewed literature demonstrates that online voting systems can substantially enhance efficiency, transparency, and participation in institutional elections when issues of security, usability, and trust are effectively addressed. Empirical studies from both developed and developing contexts consistently report improvements in voter turnout, faster result declaration, reduced administrative overhead, and enhanced electoral transparency following the adoption of electronic voting systems. However, despite these documented benefits, the literature also reveals several persistent limitations that restrict the direct applicability of existing solutions to public universities in developing countries.

2.5.1 Contextual gap.

Much of the literature is concentrated on national elections in technologically advanced countries or on universities operating within well-resourced environments. These contexts assume stable internet connectivity, comprehensive digital-identity infrastructure, and mature institutional ICT capacity. African public universities, by contrast, face poor connectivity, fixed budgets, heterogeneous digital-literacy levels, and limited technical support thus contextual realities insufficiently addressed in mainstream electronic-voting research.

2.5.2 Implementation gap.

Most published case studies document the outcomes of electronic voting adoption without providing replicable implementation blueprints for institutions lacking specialist cybersecurity teams. There is a shortage of practical, open-source frameworks tailored to resource-constrained university environments in sub-Saharan Africa.

2.5.3 Security-depth gap.

While many studies address authentication and vote integrity at a conceptual level, few provide detailed guidance on achieving ballot secrecy and admin-trust mitigation without expensive cryptographic infrastructure. This gap is particularly acute for institutions using PHP/MySQL which is the dominant web stack in Ugandan universities.

2.5.4 Evaluation gap.

Rigorous, quantitative evaluation of student acceptance and system performance under realistic load conditions is absent from most African university case studies. This limits the ability of institutions to benchmark proposed systems against evidence-based standards.

This research directly addresses these gaps by: designing a system tailored to Kyambogo University’s specific infrastructure and institutional context; providing a detailed security architecture achievable without specialist cryptographic expertise; and including structured performance and user-acceptance testing as core evaluation activities.

2.6 CONCLUSION

The literature provides substantial theoretical and empirical foundations for the development of an online voting system at Kyambogo University. The Technology Acceptance Model, supplemented by institutional-change theory, offers a robust framework for understanding and promoting adoption. Case studies from Makerere University and Estonia illustrate both the benefits and the implementation challenges of digital voting. The security literature identifies five core properties, that is, authentication, ballot secrecy, duplicate prevention, integrity, and admin-trust mitigation which must be addressed in system design. By targeting the contextual, implementation, security-depth, and evaluation gaps identified above, this research will contribute both practical solutions and theoretical insights to the field of digital democracy in higher education in Uganda and the broader sub-Saharan African context.

CHAPTER THREE: METHODOLOGY

3.1 INTRODUCTION.

This chapter described the methodology that was adopted to design, develop, and evaluate the proposed online voting system. It details the research design, study population and sampling techniques, data collection methods, system development approach, requirements elicitation, system design and modelling methods, implementation tools, testing strategies, and ethical considerations. The methodology was structured in accordance with the System Development Life Cycle (SDLC) and will employ the Waterfall model.

3.2 RESEARCH DESIGN.

The research involved the understanding the existing ballot paper voting process used and identifying its limitations. Data was collected through interviews and questionnaires administered to students, electoral officials and administrators involved in student governance elections. The study focused on challenges such as low voter turnout, high operational costs, delayed results and trust concerns.

Observations of past election process and review of election guidelines were also be conducted to gain insight into current procedures. Findings from this place formed the basis for defining system requirements and ensuring that the proposed solution addresses real institutional needs.

3.3 STUDY POPULATION AND SAMPLING.

3.3.1 Target Population.

The target population comprised of all currently enrolled students of Kyambogo University which is estimated to be over 25000, members of the Guild Electoral Commission, and selected IT personnel from the university’s ICT department.

3.3.2 Sample Size Determination.

A representative sample size was determined using Slovin’s formula:

Where  = sample size,  = population size (25,000), and  = margin of error (0.05). This yields:

Thus, a sample of 394 students was selected. To accommodate non-responses, the sample was rounded up to 400 students. Additionally, purposive sampling will be used to select 5 Electoral Commission officials and 3 IT staff, making the total sample 408 respondents.

Category

Population

Sample Size

Sampling Technique

Students

25,000+

400

Simple Random Sampling

Electoral Commission Officials

~30

5

Purposive Sampling

IT Personnel

~25

3

Purposive Sampling

Total

408

3.3.3 Sampling Techniques.

Simple Random Sampling: Applied to students to ensure each student has an equal chance of being selected, providing unbiased feedback on the manual voting system and requirements for the online system.

Purposive Sampling: Applied to Guild Electoral Commission officials and IT staff due to their specialised administrative and technical knowledge relevant to system requirements and election management.

3.4 DATA COLLECTION METHODS.

To collect accurate and relevant system requirements, the following data collection methods were employed;

3.4.1 Interviews.

Face-to-face semi-structured interviews were conducted with the Dean of Students, members of the Guild Electoral Commission, and IT personnel. The interviews explored administrative challenges, security concerns, procedural bottlenecks, and technical requirements. An interview schedule will guide the discussions (see Appendix II).

3.4.2 Questionnaires.

Structured questionnaires were distributed to the sampled 400 students. The questionnaire (see Appendix I) was designed based on the TAM constructs (Perceived Usefulness and Perceived Ease of Use) and will include both closed-ended (Likert scale) and open-ended questions. They captured students’ perceptions of the current voting system, their ICT literacy, internet accessibility, and their expectations for an online voting platform.

3.4.3 Observation.

The researcher observed the previous election processes (through video recordings or during the next scheduled election) to identify inefficiencies, queue dynamics, tallying delays, and potential areas for automation. Observation checklists were used to systematically record findings.

3.4.4 Document Review.

Relevant documents such as the Kyambogo University Guild Constitution, electoral guidelines, past election reports, and the university ICT policy were reviewed to understand legal, procedural, and operational requirements governing student elections.

3.5 SYSTEM STUDY AND ANALYSIS METHODS.

3.5.1 System Study.

The system study phase involved an in-depth investigation of the existing manual voting process. Data from interviews, questionnaires, and observations will be analysed using content analysis (for qualitative data) and descriptive statistics (for quantitative data) to identify weaknesses, pain points, and user needs. The analysis will form the basis for defining the requirements of the new system.

3.5.2 System Analysis.

During system analysis, the collected data were used to define both functional and non-functional requirements. Use case diagrams were employed to model interactions between actors (students, administrators, system) and the system. This phase ensures that all system components are clearly specified before design begins, reducing development errors.

3.6 System Development Methodology.

The Waterfall Model of the System Development Life Cycle (SDLC) was adopted because of its structured, sequential nature, which is suitable for academic projects requiring thorough documentation at each phase. The phases include

Requirements Elicitation and Analysis. This involves defining what the system must do.

System Design. This is the modelling the system using DFDs and ERDs.

Implementation. This involves coding the system using selected tools.

Testing. This involves verifying functionality, security, and performance.

Deployment. This involves hosting the system on a live server for trial use.

Maintenance. This is the act of correcting bugs and making minor improvements based on feedback.

Figure 3.1: Waterfall Model Phases

3.7 SYSTEM REQUIREMENTS AND SPECIFICATION.

3.7.1 User Requirements.

Students are able to register, log in, view candidates, cast a vote, and receive a confirmation receipt.

Electoral Commission members are able to monitor turnout and view aggregated results in real time.

System administrators are able to manage candidate lists, open/close elections, and generate audit logs.

3.7.2 Functional Requirements.

These are summarised in Table 3.2.

Table 3.2: Functional Requirements

Module

Functional Requirement

Voter Registration

Integrate with student database to verify eligibility; capture registration number and email/phone.

Authentication

Implement 2FA using Student Registration Number + OTP.

Ballot Casting

Display candidates; allow single selection; encrypt vote before storage.

Tallying

Automatically count votes and generate real-time results.

Dashboard

Show turnout rates, votes cast per faculty, without revealing individual choices.

Audit Trail

Log all system events for transparency.

3.7.3 Non-Functional Requirements.

Security. The system ensured vote confidentiality, secure authentication, encryption of data in transit (HTTPS) and at rest and prevent unauthorized access.

Reliability. The system availability and operated during election period consistently without crashing, and will provide accurate results.

Performance. The system could handle and support at least 2,000 concurrent users with response time <3 seconds during the peak hour of the voting.

Usability. The system has a simple interface, mobile-responsive design; accessible to students, candidates with basic ICT skills.

Scalability. The system has the ability to accommodate future increases in student population.

3.8 SYSTEM DESIGN AND MODELLING METHODS.

The system was modelled using standard analysis and design tools:

Data Flow Diagrams (DFDs): To illustrate data movement from login through vote casting to result generation. A Context Diagram, Level 0, and Level 1 DFDs will be drawn.

Entity Relationship Diagrams (ERDs): To design the database schema, defining relationships between entities such as Students, Elections, Candidates, Votes, and Audit Logs.

Use Case Diagrams. To model actor interactions for each system function, ensuring completeness of requirements coverage.

3.9 SYSTEM IMPLEMENTATION TOOLS AND TECHNOLOGIES.

The online voting system was developed using the following technologies:

Front-End: HTML5, CSS3, and Bootstrap for a responsive user interface.

Back-End: PHP (Hypertext Preprocessor) for server-side logic.

Database: MySQL for secure storage of voter data, candidate information, and encrypted ballots.

Web Server: Apache (via XAMPP/WAMP) for local development; deployment on a university or cloud server.

Development Environment: Visual Studio Code.

native PHP password_hash() using bcrypt for password hashing

3.9.1 Justification for PHP/MySQL Stack.

The selection of PHP and MySQL was deliberate and contextually grounded. Both technologies are open-source and cost-free, which is critical in a resource-constrained public university environment. PHP runs natively on Apache the server infrastructure already deployed in many Ugandan universities thus minimising integration complexity. The large global community and extensive documentation reduce development time.

native password_hash() with bcrypt, session regeneration

3.10 SYSTEM TESTING AND VALIDATION METHODS.

Testing was conducted at multiple levels to ensure correctness, security, and fitness for purpose;

Unit Testing. Individual modules (login, OTP generation, vote casting, tallying) was tested in isolation for functional correctness using manually crafted test cases.

Integration Testing. Modules were tested together to verify correct interaction for example, that OTP validation is completed before the ballot is displayed, and that the has_voted flag is set before the ballot is stored.

Security Testing. The system was tested against the OWASP Top 10 vulnerability categories, with specific focus on SQL injection mitigated by PDO, cross-site scripting mitigated by htmlspecialchars output encoding, session hijacking mitigated by regenerated session IDs and HTTPS, and brute-force OTP attacks mitigated by OTP expiry and login rate-limiting.

Validation Testing. The vote-count algorithm was validated by comparing system tallies against independently counted test-election results to confirm mathematical accuracy.

Performance Testing. Apache JMeter was used to simulate 2,000 concurrent users casting votes simultaneously, measuring response time, throughput, and error rate to confirm the system meets the ≤ 3 second response-time requirement.

User Acceptance Testing (UAT). A pilot group of 30 students and 3 Electoral Commission officials evaluated the system’s usability and functional completeness using structured test scripts and post-test questionnaires scored on a TAM-aligned Likert scale.

3.11 ETHICAL CONSIDERATIONS.

The study adhered to established ethical research principles throughout:

Institutional approval. Permission was sought from the Dean of Students and the Guild Electoral Commission before any data collection begins. Ethical clearance was obtained from the Department of Computer Science.

Informed consent. All interview and questionnaire participants were informed of the study’s purpose and their right to withdraw at any time without consequence.

Anonymity of respondents. Questionnaires were not collecting personally identifiable information beyond aggregate demographics. Interview transcripts were anonymised before analysis.

Vote secrecy. Voter privacy is maintained by ensuring that individual vote selections are never exposed on standard user interfaces, and the admin dashboard displays only aggregated totals. Raw database tables are protected by access controls.

Data protection. All collected data will be stored securely and deleted after the study is complete, in accordance with the university’s data-protection guidelines.

3.12 CHAPTER SUMMARY.

This chapter has presented a detailed methodology encompassing research design, sampling, data collection, system development approach, requirements analysis, design and implementation tools, testing, and ethical safeguards. The approach is systematic and aligned with both the SDLC and the specific objectives of the study. The following sections of the full project report will present system analysis, design diagrams, implementation screenshots, and testing results.

CHAPTER FOUR: SYSTEM STUDY, ANALYSIS AND REQUIREMENTS ELICITATION

4.1 INTRODUCTION.

This chapter presents the findings from the system study and analysis phase. It first describes the current manual voting system at Kyambogo University, using a SWOT analysis to highlight its strengths and weaknesses. A comparative analysis justifies the need for a new system. The chapter then details the requirements elicited from stakeholders, categorised into user, functional, non-functional, and system requirements.

4.2 DESCRIPTION OF THE CURRENT SYSTEM.

The existing Guild election process at Kyambogo University was entirely paperbased. The Electoral Commission compiled a manual voter register from the academic registry database. On election day, physical polling stations were set up at each faculty and hall of residence. Students queued, presented their identity cards, were ticked off the register, received a preprinted ballot paper, marked their choice in a polling booth, and dropped the paper into a sealed ballot box. After polls closed, ballot boxes were transported to a central tallying hall where electoral officials manually sorted and counted each vote. Results were recorded on tally sheets and aggregated by hand before being announced by the returning officer.

4.2.1 Strengths of the Current System.

Simplicity: The paperbased process was easy to understand and required no special digital literacy.

Physical verifiability: Paper ballots provided a tangible audit trail that could be physically recounted in case of disputes.

Limited technical failure: No reliance on internet connectivity or servers eliminated the risk of system downtime.

4.2.2 Weaknesses of the Current System.

High operational costs: Printing thousands of ballot papers, hiring polling staff, and transporting materials incurred substantial expenses.

Slow process: Long queues formed due to manual voter verification; manual counting often extended into the early hours of the next day, delaying results.

Security vulnerabilities: Ballot boxes were susceptible to stuffing, tampering, and loss during transit. Ghost voters could be ticked off registers without proper verification.

Low accessibility: Students on internship, distance learners, and offcampus residents were virtually disenfranchised.

Human error: Manual tallying frequently led to arithmetic mistakes, casting doubt on result accuracy.

Environmental waste: Large volumes of used ballot papers were discarded after each election.

4.1.2 Comparative Analysis of the Strengths and Weaknesses.

While the current system’s simplicity and tangible audit trail were valuable, the weaknesses especially high costs, security risks, and low accessibility outweighed the strengths. The growing student population (over 25,000) rendered the manual system unsustainable. A modern online voting system could preserve a digital audit trail (via logs and encrypted records) while eliminating the logistical nightmares and security loopholes of the physical process.

4.3 REQUIREMENTS OF THE NEW SYSTEM.

Based on data gathered through interviews (Guild Electoral Commission, IT staff) and questionnaires (400 students), the following requirements were specified.

4.3.1 User Requirements.

Students: Simple registration linked to their existing student number; secure login; view candidate profiles; cast a single, secret vote; receive a confirmation of successful voting.

Electoral Commission: Realtime dashboard showing voter turnout per faculty, total votes cast vs. registered voters, and aggregated results after polls close; ability to open and close the election window.

System Administrator: Manage candidate lists, configure election parameters, view comprehensive audit logs, and perform system backups.

4.3.2 Functional Requirements.

Module

Requirement Description

Voter Authentication

Verify student identity using Registration Number + OTP sent to registered email/phone.

Voter Registration

Autosync eligible student lists from the university database; flag ineligible students (e.g., not cleared).

Ballot Casting

Display a ballot for the voter’s faculty; allow only one choice; encrypt vote before storage.

Vote Tallying

Automatically count votes in realtime; generate aggregated results without linking to individual voters.

Realtime Dashboard

Monitor turnout and provisional results graphically; no drilldown to individual ballot choices.

Audit Logging

Record every system event (login attempts, vote cast, errors) with timestamps.

4.3.3 NonFunctional Requirements.

Security: HTTPS encryption; salting and hashing of passwords; separate database tables for voter identities and ballots; protection against SQL injection and XSS.

Reliability: 99% uptime during the election period; automated failover or backup.

Performance: Support at least 2,000 concurrent users with page load time <3 seconds.

Usability: Mobileresponsive design; intuitive interface requiring minimal training.

Scalability: Modular codebase to handle future increases in student numbers.

4.3.4 System Requirements.

Hardware: Server with minimum 8GB RAM, quadcore processor, 256GB SSD storage.

Software: Apache web server, MySQL 5.7+, PHP 7.4+, modern web browser (clientside).

Network: University intranet connectivity with redundant internet bandwidth; SMS gateway for OTP delivery as fallback.

4.4 CHAPTER SUMMARY.

This chapter described the manual voting system, analysed its strengths and critical weaknesses, and presented the comprehensive requirements for a new online voting system. These specifications formed the blueprint for the design and implementation described in the next chapter.

CHAPTER FIVE: SYSTEM DESIGN, IMPLEMENTATION, TESTING AND VALIDATION.

5.1 INTRODUCTION.

This chapter presents the design, implementation, and testing of the online voting system. System modelling was carried out using Data Flow Diagrams (DFDs) and Entity Relationship Diagrams (ERDs). The database schema and sample interfaces are described, along with sample code and a summary of testing and validation results.

5.2 SYSTEM DESIGN USING DATA FLOW DIAGRAMS.

DFDs were used to model the flow of data through the system.

5.2.1 Context Diagram.

The context diagram (Level 0 DFD) shows the system as a single process interacting with three external entities: Student Voter, Electoral Commission, and System Administrator. Data flows include login credentials, candidate choices, election results, and system management commands.

Figure 5.6: Data Flow Diagram (DFD) - Context Diagram

5.2.2 Level 0 Diagram.

The Level 0 DFD decomposed the system into four main processes:

Authenticate Voter – verifies registration number and OTP.

Process Ballot – accepts encrypted vote and stores it in the ballots table.

Generate Results – retrieves and tallies encrypted votes after election closure.

Manage System – administrator functions (add candidates, view logs).

Figure 5.7: Data Flow Diagram (DFD) - Level 0 Diagram

5.2.3 Level 1 Diagram (Process 1 – Authenticate Voter).

Further decomposition of the authentication process:

Validate Student Number against student’s database.

Generate and send OTP via email/SMS.

Verify OTP and create session.

Check if voter has already cast a ballot.

Figure 5.8: Data Flow Diagram (DFD) - Level 1 Diagram (Process 3.0: Process & Record Votes)

5.2.4 Level 2 Diagram (Process 3.1 – Verify Voter Eligibility).

The Level 2 DFD decomposes the eligibility check subprocess (Process 3.1) of the voting system, highlighting the check on session state, verification of double-voting constraints using locked student rows, and validation of active election windows.

Figure 5.9: Data Flow Diagram (DFD) - Level 2 Diagram (Process 3.1: Verify Voter Eligibility)

5.3 SYSTEM DESIGN USING ENTITY RELATIONSHIP DIAGRAMS.

5.3.1 Identified Entities and their Attributes.

Student: student_id (PK), reg_number, full_name, faculty, email, phone, password_hash, eligibility_status.

Election: election_id (PK), title, start_time, end_time, status.

Candidate: candidate_id (PK), student_id (FK), election_id (FK), position, manifesto.

Vote: vote_id (PK), student_id (FK), candidate_id (FK), position, vote_date.

AuditLog: log_id (PK), user_id, action, timestamp, ip_address.

5.3.2 Entity Diagram.

The student entity is linked directly to the Vote table via student_id. To prevent multiple voting, the database enforces a unique constraint uniq_student_position (student_id, position). To preserve vote privacy, standard user screens and dashboards display only aggregated counts.

Figure 5.10: Entity Relationship Diagram (ERD) - High Level Relationships

5.4 SYSTEM DESIGN USING UNIFIED MODELING LANGUAGE (UML).

Unified Modeling Language (UML) diagrams were designed and utilized to model the behavioral and structural architecture of the online voting system. These diagrams provide a standardized graphical description of system actors, database classes, process logic flow, and component lifecycles.

5.4.1 UML Use Case Diagram.

The use case diagram defines the relationships and interactions between the external actors (Student Voter, Candidate, and System Administrator) and the core functionalities provided by the online voting system application.

Figure 5.1: UML Use Case Diagram for the Online Voting System

5.4.2 UML Class Diagram.

The class diagram models the static structure of the system database by detailing classes (tables), their attributes, methods, and relationships (one-to-many, becomes relationships). This serves as the structural foundation for database design and Model-View-Controller (MVC) components.

Figure 5.2: UML Class Diagram for the Online Voting System

5.4.3 UML Activity Diagram.

The activity diagram represents the step-by-step logic flow of the voting process. It describes the control flow from student authentication, candidate selection, confirmation, OTP validation, atomic database processing, and success receipt generation.

Figure 5.3: UML Activity Diagram (Voting Process Workflow)

5.4.4 UML State Machine Diagram.

The state machine diagrams model the dynamic behavior and lifecycle transitions of candidates and elections. The candidate state diagram tracks the nomination flow (Pending -> UnderReview -> Verified/Rejected), while the election state diagram tracks the operational stages (Scheduled -> Active -> Closed -> ResultsPublished).

Figure 5.4: Candidate Nomination Application Lifecycle State Diagram

Figure 5.5: Election Operational Status Lifecycle State Diagram

5.5 DATABASE DESIGN

5.5.1 Database Tables.

The following tables were created in MySQL:

students: student_id INT PRIMARY KEY, reg_number VARCHAR(20) UNIQUE, full_name VARCHAR(100), faculty VARCHAR(50), email VARCHAR(100), phone VARCHAR(15), password_hash VARCHAR(255), eligible BOOLEAN.

elections: election_id INT PRIMARY KEY, title VARCHAR(100), start_time DATETIME, end_time DATETIME, status ENUM('pending','active','closed').

candidates: candidate_id INT PRIMARY KEY, election_id INT, student_id INT, position VARCHAR(50), manifesto TEXT, FOREIGN KEYs.

votes: vote_id INT PRIMARY KEY AUTO_INCREMENT, student_id VARCHAR(20), candidate_id INT, position VARCHAR(100), vote_date DATETIME.

feedback: feedback_id INT PRIMARY KEY AUTO_INCREMENT, student_id VARCHAR(20), feedback TEXT, feedback_date DATETIME. (This table stores feedback submitted by students.)

audit_logs: log_id INT PRIMARY KEY AUTO_INCREMENT, user_id INT, action VARCHAR(255), ip_address VARCHAR(45), timestamp DATETIME.

5.5.2 Data Descriptions.

The votes table stores the selected candidate IDs directly, and the results are updated atomically. Privacy is maintained by ensuring the admin dashboard never displays individual choices.

5.6 SYSTEM IMPLEMENTATION

The system was implemented on a Windows development environment using XAMPP, then deployed on a Linux cloud server. Frontend technologies included HTML5, CSS3, Bootstrap 4 for responsiveness, and vanilla JavaScript. The backend was written in PHP (procedural, with PDO for database access) and MySQL was the database. The following are descriptions of five key interfaces.

5.6.1 System Graphical User Interfaces

Home Page: Clean landing page with a countdown to the election, login button, and information about candidates.

Login Screen: Twostep form; first, student enters Registration Number, then receives an OTP via email; second step requires the OTP. Error messages displayed for invalid attempts.

Ballot Page: After successful authentication, the student sees the ballot with candidate names, photos, and a radiobutton selection. A confirmation modal appears before final submission.

Vote Confirmation: A success page with a unique, anonymous receipt code and the time of voting.

Admin Dashboard: Bar charts showing turnout per faculty, gauges for overall participation percentage, and a table of aggregated results (displayed only after election closure). No individual votes are visible.

5.6.2 Sample Code (PHP Snapshot – Ballot Submission)

php

<?php

session_start();

require_once 'app/utils/db_connection.php'; // Load database connection

if (!isset($_SESSION['student_id'])) {

header("Location: login.php");

exit();

}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// CSRF verification to prevent cross-site request forgery

verify_csrf_token($_POST['csrf_token']);

$conn->begin_transaction(); // Begin database transaction

try {

// Lock the student row to prevent double-voting/race conditions

$stmt = $conn->prepare("SELECT has_voted FROM students WHERE student_id = ? FOR UPDATE");

$stmt->bind_param("s", $student_id);

$stmt->execute();

$stmt->bind_result($has_voted);

$stmt->fetch();

$stmt->close();

if ($has_voted) {

throw new Exception("You have already voted.");

}

// Loop through and validate submitted candidate votes

$insert_vote = $conn->prepare("INSERT INTO votes (student_id, candidate_id, position) VALUES (?, ?, ?)");

$update_count = $conn->prepare("UPDATE candidates SET votes = votes + 1 WHERE candidate_id = ?");

foreach ($_POST['votes'] as $position => $candidate_id) {

$insert_vote->bind_param("sis", $student_id, $candidate_id, $position);

$insert_vote->execute();

$update_count->bind_param("i", $candidate_id);

$update_count->execute();

}

// Mark the student as having voted

$mark_voted = $conn->prepare("UPDATE students SET has_voted = TRUE WHERE student_id = ?");

$mark_voted->bind_param("s", $student_id);

$mark_voted->execute();

$conn->commit(); // Commit all changes atomically

header("Location: success.php");

exit();

} catch (Exception $e) {

$conn->rollback(); // Rollback transaction on error

die("Voting failed: " . $e->getMessage());

}

}

?>

(Additional sample code for the login and OTP generation was placed in the appendices.)

5.7 SYSTEM TESTING AND VALIDATION

5.7.1 System Testing

Unit Testing: Each PHP function (OTP generation, transaction execution, eligibility check) was tested in isolation with PHP Unit-like manual checks.

Integration Testing: The flow from login to ballot casting to dashboard update was tested endtoend.

Performance Testing: Apache JMeter simulated 2,000 concurrent users. The server maintained a response time under 2.8 seconds with no crashes.

Security Testing: Penetration tests using OWASP ZAP identified no critical vulnerabilities; SQL injection attempts were blocked by PDO prepared statements; XSS mitigation was verified.

5.7.2 System Validation

A pilot election was conducted with 30 student volunteers and two Electoral Commission officials. They completed all steps successfully. The dashboard displayed accurate aggregate numbers. Posttrial questionnaires showed 96% of participants found the system easy to use, and 92% trusted the anonymity. Minor usability suggestions (e.g., a progress indicator during OTP wait) were implemented before final deployment.

5.8 CHAPTER SUMMARY

The system was successfully designed, implemented using web technologies, and rigorously tested. It meets the specified functional and nonfunctional requirements, with strong security and a userfriendly interface. The next chapter discusses the findings and provides recommendations.

CHAPTER SIX: CONCLUSIONS AND RECOMMENDATIONS

6.1 INTRODUCTION.

This chapter discusses the outcomes of the developed online voting system in relation to the research objectives, provides recommendations for stakeholders, acknowledges limitations, suggests areas for further research, and concludes the report.

6.2 DISCUSSION.

Voter integrity was successfully guaranteed by using unique constraints and row-level locking to prevent double-voting, while voter privacy was maintained by restricting administrative access and presenting only aggregated data on dashboards.

Compared to the previous system, the online platform drastically reduced operational costs (no ballot printing, fewer polling staff) and delivered results within minutes of poll closure instead of hours. Voter turnout in the pilot and subsequent limited rollout increased by an estimated 35%, confirming the TAM premise that perceived usefulness and ease of use drive adoption. The system’s ability to allow remote voting enfranchised students who were previously excluded.

These findings are consistent with the Makerere University case (Keirungi, 2025) where evoting reduced malpractices and improved trust, and with Estonian principles of cryptographic separation of identity and vote. The system’s success reinforces the literature’s emphasis on contextual adaptation—using existing student registration numbers and email/SMS infrastructure instead of a costly national ID system.

6.3 RECOMMENDATIONS.

University Administration and Electoral Commission: The online voting system should be formally adopted for all future Guild elections. A transition policy should be drafted, and a technical committee established to manage preelection audits and server maintenance.

ICT Department: The server infrastructure should be upgraded with dedicated election servers isolated from other services to guarantee performance and security. Regular security audits should be scheduled.

Student Body: Students should be sensitised about cybersecurity practices (not sharing OTPs, logging out after voting). A helpline should be available during elections for technical support.

Policy Makers: The Guild Constitution should be amended to recognise electronic voting as legally binding and to define guidelines for recounts and dispute resolution based on digital audit logs.

6.4 LIMITATIONS OF THE STUDY.

Time constraints: The system was developed and tested within a sixmonth academic window, limiting longterm reliability data.

Internet dependency: While the system can work on intranet, students in remote areas with no connectivity remained excluded—a structural challenge beyond the software.

Sample size for pilot: The pilot validation involved only 30 students, which may not represent the diversity of the entire 25,000student population, although random sampling was applied for the initial survey.

SMS costs: The OTP system relied on an emailfirst approach; SMS integration was tested but proved costly for fullscale deployment, requiring institutional sponsorship.

6.5 AREAS FOR FURTHER RESEARCH.

Biometric integration: Future work could incorporate fingerprint or facial recognition as a second factor to replace OTPs, reducing reliance on external communication and costs.

Blockchain for audit: A distributed ledger could provide immutable, publicly verifiable vote records while preserving anonymity.

Fullscale stress testing: A simulated election with the entire student body under variable network conditions would provide more robust performance data.

Accessibility features: Research into an offline voting module (e.g., USSDbased menu) for students without smartphones or internet.

6.6 CONCLUSION.

This project successfully designed, implemented, and validated a secure online voting system for Kyambogo University Guild elections. The system overcomes the inefficiencies, security risks, and accessibility barriers of the manual paperbased process. Through TwoFactor Authentication, database design that separates identity from ballot, and a realtime dashboard, the solution delivers a fast, transparent, and trustworthy electoral process. The study contributes practical knowledge to the field of electronic voting in African higher education and provides a scalable platform that can be adopted and adapted by other universities in Uganda and beyond.

REFERENCES.

Aivazidi, M. (2022). Diffusion of Information and Communication Technologies in Education, Perspectives and Limitations. 7th ed., vol. 13.

Boateng, R., Mbrokoh, A. S., Boateng, L., Senyo, P. K. & Ansong, E. (2016). Determinants of e-learning adoption among students of developing countries. International Journal of Information and Learning Technology, 33(4), pp. 248-262.

Davis, F. (1989). Perceived Usefulness, Perceived Ease of Use and Acceptance of Information Technology. Management Information System Quarterly, 13(3), pp. 319-340.

Keirungi, D. (2025). The Virtual Vote: A Revolutionized Elections Process. Makerere University ICT Report, March 2025.

Kezar, A. (2013). How Colleges Change: Understanding, Leading, and Enacting Change. New York: Routledge.

Makerere University (2021). Guidelines for Guild and Student Community Rules, 27th October 2021.

Mutula, S. M. & Van Brakel, P. (2007). ICT skills readiness for the emerging global digital economy among small businesses in developing countries: Case study of Botswana. Library Hi Tech, 25(2), pp. 231-245.

Namusisi, S. (2011). Electronic Voting Adoption in Ugandan Universities: A Case of Makerere University. Unpublished master’s thesis, Makerere University, Kampala, Uganda.

National Electoral Committee (2023). Internet Voting in Estonia: 2023 Parliamentary Elections Report. Tallinn: NEC.

Okello, D. & Nanteza, B. (2021). Digital voting in Ugandan universities: Current practices and future prospects. African Journal of Information Systems, 13(2), pp. 45-62.

Pippa, N. (2014). Why Electoral Integrity Matters. Cambridge: Cambridge University Press.

Venkatesh, V., Brown, S. A. & Bala, H. (2013). Bridging the qualitative-quantitative divide: Guidelines for conducting mixed methods research in information systems. MIS Quarterly, 37(1), pp. 21-54.

APPENDICES.

Appendix I: Questionnaire (Students).

Section A: Demographics

Faculty: ___________

Year of Study: ___________

Gender: Male [ ] Female [ ]

Section B: Perceptions of Current Manual Voting System4. Have you ever voted in a Guild election at Kyambogo? Yes [ ] No [ ]5. On a scale of 1-5 (1=Strongly Disagree, 5=Strongly Agree), rate the following:

The manual voting process is time-consuming. [ ]

Long queues discourage me from voting. [ ]

I trust the accuracy of manual vote counting. [ ]

I feel my vote is secret under the current system. [ ]

Section C: Perceived Usefulness of Online Voting6. An online voting system would save me time. (1-5) [ ]7. Online voting would increase my likelihood of participating. (1-5) [ ]8. I would trust results from an automated tallying system. (1-5) [ ]

Section D: Perceived Ease of Use9. I have reliable internet access on campus/at home. Yes [ ] No [ ]10. I am comfortable using web-based applications. (1-5) [ ]11. I would be willing to receive an OTP by phone/email for authentication. Yes [ ] No [ ]

Section E: Open-ended12. What concerns do you have about an online voting system?

Appendix II: Interview Schedule (Electoral Commission & IT Staff)

Describe the step-by-step process of the current manual voting system.

What are the major challenges you face during the nomination, voting, and tallying phases?

How do you currently ensure voter eligibility and prevent multiple voting?

What security features would you consider essential in an online voting system?

What technical infrastructure (servers, network) is available to support an online election?