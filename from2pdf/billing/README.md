FabLab Bicol Job Order System
Overview
This application creates job orders for the FabLab Bicol facility. It allows staff to enter client information, service details, and generate PDF job order forms.
Features

Client information entry
Service and item details with automatic cost calculation
PDF generation with template overlay
Support for different client types (Student, MSME, Others)

Installation Instructions
Option 1: Run the Executable (No Python Required)

Extract all files from the ZIP archive
Double-click the FabLabJobOrder.exe file (Windows) or FabLabJobOrder (Mac/Linux)
The application will launch directly

Option 2: Run from Source Code (Python Required)

Ensure you have Python 3.6+ installed
Install required packages: pip install reportlab PyPDF2 tkcalendar
Extract all files from the ZIP archive
Run python billing.py from the command line

Usage Instructions

Enter client information in the "Client Information" tab
Select client type (Student, MSME, or Other)
Enter service description and item details in the "Service Details" tab
Click "Calculate Total" to update the overall total
Click "Generate PDF" to create and open the filled job order form
Click "Clear Form" to reset all fields

Files Included

FabLabJobOrder.exe (or FabLabJobOrder on Mac/Linux): The executable program
template.pdf: The base job order template
README.txt: This file

System Requirements

Windows 7 or later / macOS 10.14 or later / Linux with GTK support
512MB RAM minimum (1GB recommended)
100MB free disk space

Important Notes

PDF generation relies on having read/write access to the application folder
PDF viewing requires a default PDF viewer to be installed on your system

Support
For assistance, please contact the FabLab Bicol IT department or Rey Gabriel L. Lietral.
https://www.facebook.com/mcflurrryyycoffeecaramel