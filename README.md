# ZKBio Zlink to CS form 48 (Philippines Government DTR) Project

## Overview
This project is a **Daily Time Record (DTR)** system designed to import and generate employee attendance in PDF form. The project includes a database and configuration settings to customize the application.

## Features
- ZKBio Zlink employees punches to CS form 48 DTR
- Database included for easy setup
- Configurable password hash for security

## Setup Instructions

1. **Clone the Repository**  
   Clone this project to your local machine.

2. **Database Setup**  
   The database file is located in the `database` folder. Import the database into your preferred database management system.

3. **Environment Configuration**  
   - Locate the `.env` file in the project directory.
   - To change the password, modify the `pass_hash` value in the `.env` file.

   Example:
   ```env
   PASS_HASH=your_new_hashed_password
   ```

4. **Download Punch Records**  
   - Go to the ZKBio link: [https://zlink.minervaiot.com](https://zlink.minervaiot.com).
   - Navigate to **Applications > Time Attendance > Reports > Total Punches > Export Data**.
   - Transform the exported data into a CSV file.
   - Ensure the CSV file has only the following headers in the first row:
     ```
     Person ID, Person Name, Date, Punch Records
     ```

5. **Install LibreOffice**  
   - The project requires `soffice` from LibreOffice to convert DOCX files to PDF.
   - Download and install LibreOffice from [https://www.libreoffice.org/download/](https://www.libreoffice.org/download/).
   - Ensure the `soffice` executable is located at:
     ```
     C:\Program Files\LibreOffice\program\soffice.exe
     ```

6. **Run the Project**  
   Use your local development environment (e.g., Laragon) to serve the project.

## Notes
- Ensure your development environment is properly configured to support PHP and MySQL.
- If you encounter issues, double-check the `.env` file for correct configuration.

## Folder Structure
- `database/` - Contains the database file for the project.
- `public/` - Publicly accessible files.
- `src/` - Source code for the application.