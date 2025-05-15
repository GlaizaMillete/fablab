import tkinter as tk
from tkinter import ttk, StringVar, IntVar, BooleanVar
from tkcalendar import DateEntry
from reportlab.pdfgen import canvas as reportlab_canvas
from reportlab.lib.pagesizes import legal
from PyPDF2 import PdfReader, PdfWriter
from reportlab.lib.colors import black
import os
import platform
import datetime

def fill_pdf(data):
    output_filename = "filled_request_form.pdf"
    overlay_filename = "request_overlay.pdf"

    c = reportlab_canvas.Canvas(overlay_filename, pagesize=legal)
    c.setFont("Helvetica", 10)
    c.setFillColor(black)

    # Date requested
    c.drawString(210, 795, data["date_requested"])
    
    # Consent checkbox
    if data["consent"]:
        c.drawString(134, 740, "✓")
    
    # Personal Information
    c.drawString(205, 703, data["name"])
    c.drawString(205, 691, data["address"])
    c.drawString(205, 679, data["contact_no"])
    
    # Gender checkboxes
    if data["gender"] == "Male":
        c.drawString(206, 666, "✓")
    elif data["gender"] == "Female":
        c.drawString(206, 652, "✓")
    elif data["gender"] == "Prefer not to say":
        c.drawString(206, 640, "✓")
    
    c.drawString(380, 650, data["age"])
    
    # Work/Position/Designation checkboxes
    if data["position"] == "Student":
        c.drawString(206, 620, "✓")
    elif data["position"] == "MSME/Entrepreneur":
        c.drawString(206, 600, "✓")
    elif data["position"] == "Teacher":
        c.drawString(206, 580, "✓")
    elif data["position"] == "Hobbyist":
        c.drawString(206, 560, "✓")
    elif data["position"] == "Other":
        c.drawString(206, 540, "✓")
        c.drawString(240, 532, data["position_other"])
    
    c.drawString(360, 610, data["company"])
    
    # Service Requested checkboxes
    services = {
        "training": (206, 520),
        "product_design": (206, 509),
        "equipment": (206, 497)
    }
    
    for service, coords in services.items():
        if data[service]:
            c.drawString(coords[0], coords[1], "✓")
    
    # Equipment checkboxes
    equipment = {
        "3d_printer": (219, 482),
        "3d_scanner": (219, 469),
        "laser_cutting": (219, 456),
        "print_cut": (219, 444),
        "cnc_big": (219, 432),
        "cnc_small": (219, 420),
        "vinyl_cutter": (219, 407),
        "embroidery_one": (219, 395),
        "embroidery_four": (219, 383),
        "flatbed_cutter": (219, 371),
        "vacuum_forming": (219, 358),
        "water_jet": (219, 346),
        "hand_tools": (219, 334),
        "other_equipment": (219, 322)
    }
    
    for eq, coords in equipment.items():
        if eq in data and data[eq]:
            c.drawString(coords[0], coords[1], "✓")
    
    # Hand tools specification
    if data["hand_tools"]:
        c.drawString(370, 334, data["hand_tools_specify"])
    
    # Other equipment specification
    if data["other_equipment"]:
        c.drawString(370, 322, data["other_equipment_specify"])
    
    # Consultation mode
    if data["consultation_virtual"]:
        c.drawString(340, 310, "✓")
    if data["consultation_face"]:
        c.drawString(340, 298, "✓")
    
    # Schedule information
    c.drawString(370, 272, data["schedule"])
    c.drawString(340, 250, data["equipment_schedule"])
    c.drawString(220, 210, data["work_description"])
    
    # Client signature info
    c.drawString(100, 160, data["signature_date"])
    c.drawString(210, 160, data["client_name"])
    
    c.save()

    try:
        # Merge with template PDF
        base = PdfReader("request.pdf")
        overlay = PdfReader(overlay_filename)
        writer = PdfWriter()

        page = base.pages[0]
        page.merge_page(overlay.pages[0])
        writer.add_page(page)

        with open(output_filename, "wb") as f_out:
            writer.write(f_out)

        # Open generated PDF
        if platform.system() == "Windows":
            os.startfile(output_filename)
        elif platform.system() == "Darwin":
            os.system(f"open {output_filename}")
        else:
            os.system(f"xdg-open {output_filename}")
        
        return True, "PDF generated successfully!"
    except FileNotFoundError:
        return False, "Error: request.pdf not found. Please make sure it's in the same directory."
    except Exception as e:
        return False, f"Error: {str(e)}"

def on_submit():
    # Collect all form data
    form_data = {
        "date_requested": date_requested.get(),
        "consent": consent_var.get(),
        "name": name_entry.get(),
        "address": address_entry.get(),
        "contact_no": contact_entry.get(),
        "gender": gender_var.get(),
        "age": age_entry.get(),
        "position": position_var.get(),
        "position_other": position_other_entry.get(),
        "company": company_entry.get(),
        "training": training_var.get(),
        "product_design": product_design_var.get(),
        "equipment": equipment_var.get(),
        "3d_printer": printer_3d_var.get(),
        "3d_scanner": scanner_3d_var.get(),
        "laser_cutting": laser_cutting_var.get(),
        "print_cut": print_cut_var.get(),
        "cnc_big": cnc_big_var.get(),
        "cnc_small": cnc_small_var.get(),
        "vinyl_cutter": vinyl_cutter_var.get(),
        "embroidery_one": embroidery_one_var.get(),
        "embroidery_four": embroidery_four_var.get(),
        "flatbed_cutter": flatbed_cutter_var.get(),
        "vacuum_forming": vacuum_forming_var.get(),
        "water_jet": water_jet_var.get(),
        "hand_tools": hand_tools_var.get(),
        "hand_tools_specify": hand_tools_entry.get(),
        "other_equipment": other_equipment_var.get(),
        "other_equipment_specify": other_equipment_entry.get(),
        "consultation_virtual": consultation_virtual_var.get(),
        "consultation_face": consultation_face_var.get(),
        "schedule": schedule_entry.get(),
        "equipment_schedule": equipment_schedule_entry.get(),
        "work_description": work_description_text.get("1.0", "end-1c"),
        "signature_date": signature_date.get(),
        "client_name": client_name_entry.get()
    }

    # Generate and open the PDF
    success, message = fill_pdf(form_data)
    if success:
        result_label.config(text=message, foreground="green")
    else:
        result_label.config(text=message, foreground="red")

# Toggle functions for dependent fields
def toggle_position_other():
    if position_var.get() == "Other":
        position_other_entry.config(state="normal")
    else:
        position_other_entry.delete(0, 'end')
        position_other_entry.config(state="disabled")

def toggle_hand_tools():
    if hand_tools_var.get():
        hand_tools_entry.config(state="normal")
    else:
        hand_tools_entry.delete(0, 'end')
        hand_tools_entry.config(state="disabled")

def toggle_other_equipment():
    if other_equipment_var.get():
        other_equipment_entry.config(state="normal")
    else:
        other_equipment_entry.delete(0, 'end')
        other_equipment_entry.config(state="disabled")

# Create the main window
root = tk.Tk()
root.title("FAB LAB BICOL - Client Profile and Service Request Form")
root.geometry("1000x1000")
root.configure(bg="#f0f0f0")

# Configure font styles
title_font = ("Arial", 16, "bold")
header_font = ("Arial", 12, "bold")
normal_font = ("Arial", 10)

# Create main frame with padding
main_frame = ttk.Frame(root, padding="20 20 20 20")
main_frame.pack(fill=tk.BOTH, expand=True)

# Create a notebook for tabbed interface
notebook = ttk.Notebook(main_frame)
notebook.pack(fill=tk.BOTH, expand=True, pady=10)

# First tab - Client Information
client_frame = ttk.Frame(notebook, padding="10")
notebook.add(client_frame, text="Client Information")

# Second tab - Service Details
service_frame = ttk.Frame(notebook, padding="10")
notebook.add(service_frame, text="Service Details")

# Client Information Tab
# Title
title_label = ttk.Label(client_frame, text="Client Information", font=title_font)
title_label.grid(row=0, column=0, columnspan=2, sticky="w", pady=(0, 20))

# Date requested
ttk.Label(client_frame, text="Date Requested:", font=normal_font).grid(row=1, column=0, sticky="w", padx=(0, 10), pady=5)
date_requested = DateEntry(client_frame, width=20, background='darkblue', foreground='white', borderwidth=2, date_pattern='mm/dd/yyyy')
date_requested.grid(row=1, column=1, sticky="w", pady=5)
date_requested.set_date(datetime.datetime.now())

# Consent checkbox
consent_var = BooleanVar(value=True)
consent_check = ttk.Checkbutton(client_frame, text="I agree to the Data Privacy Act of 2012", variable=consent_var)
consent_check.grid(row=2, column=0, columnspan=2, sticky="w", pady=10)

# Personal Information section
section_label = ttk.Label(client_frame, text="Personal Information", font=header_font)
section_label.grid(row=3, column=0, columnspan=2, sticky="w", pady=(10, 5))

# Name
ttk.Label(client_frame, text="Name:", font=normal_font).grid(row=4, column=0, sticky="w", padx=(0, 10), pady=5)
name_entry = ttk.Entry(client_frame, width=40)
name_entry.grid(row=4, column=1, sticky="ew", pady=5)

# Address
ttk.Label(client_frame, text="Address:", font=normal_font).grid(row=5, column=0, sticky="w", padx=(0, 10), pady=5)
address_entry = ttk.Entry(client_frame, width=40)
address_entry.grid(row=5, column=1, sticky="ew", pady=5)

# Contact Number
ttk.Label(client_frame, text="Contact No.:", font=normal_font).grid(row=6, column=0, sticky="w", padx=(0, 10), pady=5)
contact_entry = ttk.Entry(client_frame, width=40)
contact_entry.grid(row=6, column=1, sticky="ew", pady=5)

# Gender and Age
gender_frame = ttk.Frame(client_frame)
gender_frame.grid(row=7, column=0, columnspan=2, sticky="w", pady=5)

ttk.Label(gender_frame, text="Gender:", font=normal_font).grid(row=0, column=0, sticky="w", padx=(0, 10))
gender_var = StringVar(value="Male")
ttk.Radiobutton(gender_frame, text="Male", variable=gender_var, value="Male").grid(row=0, column=1, sticky="w", padx=(0, 10))
ttk.Radiobutton(gender_frame, text="Female", variable=gender_var, value="Female").grid(row=0, column=2, sticky="w", padx=(0, 10))
ttk.Radiobutton(gender_frame, text="Prefer not to say", variable=gender_var, value="Prefer not to say").grid(row=0, column=3, sticky="w")

ttk.Label(gender_frame, text="Age:", font=normal_font).grid(row=0, column=4, sticky="w", padx=(20, 5))
age_entry = ttk.Entry(gender_frame, width=10)
age_entry.grid(row=0, column=5, sticky="w")

# Position
position_frame = ttk.LabelFrame(client_frame, text="Work/Position/Designation", padding=10)
position_frame.grid(row=8, column=0, columnspan=2, sticky="ew", pady=10)

position_var = StringVar(value="Student")
position_var.trace_add("write", lambda *args: toggle_position_other())

ttk.Radiobutton(position_frame, text="Student", variable=position_var, value="Student").grid(row=0, column=0, sticky="w")
ttk.Radiobutton(position_frame, text="MSME/Entrepreneur", variable=position_var, value="MSME/Entrepreneur").grid(row=0, column=1, sticky="w")
ttk.Radiobutton(position_frame, text="Teacher", variable=position_var, value="Teacher").grid(row=1, column=0, sticky="w")
ttk.Radiobutton(position_frame, text="Hobbyist", variable=position_var, value="Hobbyist").grid(row=1, column=1, sticky="w")
ttk.Radiobutton(position_frame, text="Other", variable=position_var, value="Other").grid(row=2, column=0, sticky="w")
position_other_entry = ttk.Entry(position_frame, width=20, state="disabled")
position_other_entry.grid(row=2, column=1, sticky="w")

# Company
ttk.Label(client_frame, text="Company/Affiliated with:", font=normal_font).grid(row=9, column=0, sticky="w", padx=(0, 10), pady=5)
company_entry = ttk.Entry(client_frame, width=40)
company_entry.grid(row=9, column=1, sticky="ew", pady=5)

# Service Details Tab
# Title
title_label = ttk.Label(service_frame, text="Service Details", font=title_font)
title_label.grid(row=0, column=0, columnspan=2, sticky="w", pady=(0, 20))

# Service Requested
section_label = ttk.Label(service_frame, text="Service Requested", font=header_font)
section_label.grid(row=1, column=0, columnspan=2, sticky="w", pady=(10, 5))

training_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Training/Tour/Orientation", variable=training_var).grid(row=2, column=0, sticky="w", pady=5)

product_design_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Product/Design/Consultation", variable=product_design_var).grid(row=3, column=0, sticky="w", pady=5)

equipment_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Equipment (Select equipment below)", variable=equipment_var).grid(row=4, column=0, sticky="w", pady=5)

# Equipment section
equipment_frame = ttk.LabelFrame(service_frame, text="Equipment", padding=10)
equipment_frame.grid(row=5, column=0, columnspan=2, sticky="ew", pady=10)

# First column of equipment
printer_3d_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="3D Printer", variable=printer_3d_var).grid(row=0, column=0, sticky="w")

scanner_3d_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="3D Scanner", variable=scanner_3d_var).grid(row=1, column=0, sticky="w")

laser_cutting_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Laser Cutting Machine", variable=laser_cutting_var).grid(row=2, column=0, sticky="w")

print_cut_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Print and Cut Machine", variable=print_cut_var).grid(row=3, column=0, sticky="w")

cnc_big_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="CNC Machine (Big)", variable=cnc_big_var).grid(row=4, column=0, sticky="w")

cnc_small_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="CNC Machine (Small)", variable=cnc_small_var).grid(row=5, column=0, sticky="w")

vinyl_cutter_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Vinyl Cutter", variable=vinyl_cutter_var).grid(row=6, column=0, sticky="w")

# Second column of equipment
embroidery_one_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Embroidery Machine (One head)", variable=embroidery_one_var).grid(row=0, column=1, sticky="w")

embroidery_four_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Embroidery Machine (Four Heads)", variable=embroidery_four_var).grid(row=1, column=1, sticky="w")

flatbed_cutter_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Flatbed Cutter", variable=flatbed_cutter_var).grid(row=2, column=1, sticky="w")

vacuum_forming_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Vacuum Forming", variable=vacuum_forming_var).grid(row=3, column=1, sticky="w")

water_jet_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Water Jet Machine", variable=water_jet_var).grid(row=4, column=1, sticky="w")

# Hand tools with trigger for entry field
hand_tools_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Hand Tools (please specify):", variable=hand_tools_var, 
               command=toggle_hand_tools).grid(row=5, column=1, sticky="w")
hand_tools_entry = ttk.Entry(equipment_frame, width=25, state="disabled")
hand_tools_entry.grid(row=6, column=1, sticky="w")

# Other equipment with trigger for entry field
other_equipment_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Other (please specify):", variable=other_equipment_var,
               command=toggle_other_equipment).grid(row=7, column=1, sticky="w")
other_equipment_entry = ttk.Entry(equipment_frame, width=25, state="disabled")
other_equipment_entry.grid(row=8, column=1, sticky="w")

# Consultation mode
section_label = ttk.Label(service_frame, text="Other Details", font=header_font)
section_label.grid(row=6, column=0, columnspan=2, sticky="w", pady=(10, 5))

ttk.Label(service_frame, text="If consultation, what mode of meeting do you prefer?", font=normal_font).grid(row=7, column=0, sticky="w", pady=5)

consultation_virtual_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Virtual", variable=consultation_virtual_var).grid(row=7, column=1, sticky="w")

consultation_face_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Face to Face", variable=consultation_face_var).grid(row=8, column=1, sticky="w")

# Schedule information
ttk.Label(service_frame, text="Please specify your schedule:", font=normal_font).grid(row=9, column=0, sticky="w", pady=5)
schedule_entry = ttk.Entry(service_frame, width=40)
schedule_entry.grid(row=9, column=1, sticky="ew", pady=5)

ttk.Label(service_frame, text="If equipment utilization, please specify your schedule:", font=normal_font).grid(row=10, column=0, sticky="w", pady=5)
equipment_schedule_entry = ttk.Entry(service_frame, width=40)
equipment_schedule_entry.grid(row=10, column=1, sticky="ew", pady=5)

# Work description
ttk.Label(service_frame, text="Describe the work requested:", font=normal_font).grid(row=11, column=0, sticky="w", pady=5)
work_description_text = tk.Text(service_frame, width=40, height=5)
work_description_text.grid(row=11, column=1, sticky="ew", pady=5)

# Signature section
section_label = ttk.Label(service_frame, text="Signature Information", font=header_font)
section_label.grid(row=12, column=0, columnspan=2, sticky="w", pady=(10, 5))

ttk.Label(service_frame, text="Date:", font=normal_font).grid(row=13, column=0, sticky="w", pady=5)
signature_date = DateEntry(service_frame, width=20, background='darkblue', foreground='white', borderwidth=2, date_pattern='mm/dd/yyyy')
signature_date.grid(row=13, column=1, sticky="w", pady=5)
signature_date.set_date(datetime.datetime.now())

ttk.Label(service_frame, text="Client Name:", font=normal_font).grid(row=14, column=0, sticky="w", pady=5)
client_name_entry = ttk.Entry(service_frame, width=40)
client_name_entry.grid(row=14, column=1, sticky="ew", pady=5)

# Configure grid weights
client_frame.columnconfigure(1, weight=1)
service_frame.columnconfigure(1, weight=1)

# Submit button and status
button_frame = ttk.Frame(main_frame)
button_frame.pack(fill=tk.X, pady=10)

submit_button = ttk.Button(button_frame, text="Generate PDF", command=on_submit, style="Primary.TButton")
submit_button.pack(side=tk.RIGHT, padx=5)

result_label = ttk.Label(main_frame, text="", font=normal_font)
result_label.pack(fill=tk.X, pady=5)

# Configure button styles
style = ttk.Style()
style.configure("Primary.TButton", background="#0066cc", foreground="white")
style.configure("Secondary.TButton", background="#e0e0e0")

root.mainloop()