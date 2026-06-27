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
