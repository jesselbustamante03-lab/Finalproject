Group 7 BaryoTap: Barangay Service System

Baryo Tap is a centralized digital barangay service platform designed to help residents of Barangay Mantalongon, Dalaguete, Cebu access essential community services quickly and conveniently. It integrates core barangay functions into one online system, allowing users to:
Report community issues (roads, lights, garbage, incidents)
Request barangay documents (Indigency, Clearance, Residency, etc.)
View verified daily vegetable prices
Access emergency contacts
Receive barangay news, alerts, and updates
---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
🚀 Getting Started

To run the Baryo Tap application, you need a local server environment such as XAMPP, with PHP and MySQL enabled.
*Prerequisites:
    -Web Server: XAMPP (or any server running Apache + PHP)
    -Database: MySQL / MariaDB
    -Directory Setup: Place the system folder inside your server root directory (e.g., htdocs/Finalproject/)

Step-by-Step Usage Guide
  1. Server Initialization

      Open XAMPP Control Panel      
      Click Start for:
      -Apache
      -MySQL

Confirm both modules are running (green indicator)

  2. Accessing the System
    Open your preferred browser (Chrome, Edge, Firefox).
    In the URL bar, enter:    
    http://localhost/Finalproject/landing.php   
    This loads the official landing page of the system.

3. Authentication Flow
| Step                | Interface          | Action / Required Input                                                                          |
| --------------------| -------------------| -------------------------------------------------------------------------------------------------|
| **Landing Page**    | N/A                | Explore the page, then click **Get Started**,**Sign In**, **Sign In**, or **Sign Up**            |
| **Sign Up**         | Registration Form  | Fill in all required information. Form validates email, password strength, and required fields.  |
| **Password Policy** | —                  | Must be 8+ characters with uppercase, number, and special character. Example: `Cheryl@123`       |
| **Sign In**         | Login Form         | Enter registered **Email** and **Password**                                                      |
| **Forgot Password** | Modal Steps        | 1. Verify Email → 2. Enter Verification Code → 3. Set New Password                               |

4. Home Dashboard Interface

When the user logs in successfully, the dashboard displays personalized content:
*Personal Greeting

*Quick Access Buttons
    -Report Issue
    -Request Document
    -Vegetable Prices
    -Emergency Contacts
    -Profile

5. Module Interfaces (CRUD Operations)
Below is how each module functions inside the system:

| Module                 | Action Type        | Features & Description                                                                                                                                             |
| ---------------------- | ------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Report Issue**       | Add, Update Status | Submit concerns such as road damage, waste issues, or streetlight problems. Upload photos and describe the issue. Track status (Pending → In Progress → Resolved). |
| **Request Document**   | Add Request        | Request documents such as **Indigency**, **Residency**, **Clearance**, etc. Receive notifications once the document is **For Pickup**.                             |
| **Vegetable Prices**   | View, Admin Update | Shows official vegetable prices collected from Mantalongon market vendors. Includes daily updates, highest price highlights, and monthly analytics.                |
| **Emergency Contacts** | View               | Lists verified contacts: CEBECO, PNP, BFP, MDRRMO, Tourism Office, etc. Click-to-call features available.                                                          |
| **Notifications**      | Auto-generated     | Users receive alerts when reports are updated, documents are ready, or new announcements are posted.                                                               |

6. Profile Interface

The Profile page allows users to:
    -Update Personal Information
    -Change Password via verification modal
    -Upload / Change Profile Picture
    -Manage Account (validate)

7. Logout
Click the Logout button at the bottom of the sidebar to securely end the session and return to the Login page.

👥 Meet the Team (Group 7)
| Name                      | Primary Role                            |
| ------------------------- | ----------------------------------------|
| **Geoman, Cheryl Jane**   | (Leader / Front-end / DatabaseDesigner) |
| **Revillas, Kharen**      | (Front-end Developer)                   |
| **Bustamante, Jessel**    | (Back-end Developer)                    |
| **Calvo, Marc**           | (Database Designer)                     |
| **Cariñoza, Jenisel Ann** | (Back-end Developer)                    |

