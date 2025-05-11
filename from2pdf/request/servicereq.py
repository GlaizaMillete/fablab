import tkinter as tk
from tkinter import ttk, StringVar, IntVar, BooleanVar
from tkcalendar import DateEntry
from reportlab.pdfgen import canvas as reportlab_canvas
from reportlab.lib.pagesizes import letter
from PyPDF2 import PdfReader, PdfWriter
from reportlab.lib.colors import black
import os
import platform
import datetime

def fill_pdf(data):
    output_filename = "filled_request_form.pdf"
    overlay_filename = "request_overlay.pdf"

    c = reportlab_canvas.Canvas(overlay_filename, pagesize=letter)
    c.setFont("Helvetica", 10)
    c.setFillColor(black)

    # Date requested
    c.drawString(210, 800, data["date_requested"])
    
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
root.geometry("800x900")

# Create a scrollable frame
main_frame = tk.Frame(root)
main_frame.pack(fill=tk.BOTH, expand=1)

# Create canvas with scrollbar
my_canvas = tk.Canvas(main_frame)
scrollbar = ttk.Scrollbar(main_frame, orient="vertical", command=my_canvas.yview)
scrollable_frame = ttk.Frame(my_canvas)

scrollable_frame.bind(
    "<Configure>",
    lambda e: my_canvas.configure(scrollregion=my_canvas.bbox("all"))
)

my_canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
my_canvas.configure(yscrollcommand=scrollbar.set)

my_canvas.pack(side="left", fill="both", expand=True)
scrollbar.pack(side="right", fill="y")

# Title Label
title_label = ttk.Label(scrollable_frame, text="FAB LAB BICOL - Client Profile and Service Request Form", font=("Helvetica", 14, "bold"))
title_label.grid(row=0, column=0,sticky="w",padx="200", pady=10)

# Date requested
ttk.Label(scrollable_frame, text="Date Requested:").grid(row=1, column=0, sticky="w", padx=10, pady=5)
date_requested = DateEntry(scrollable_frame, width=20, background='darkblue', foreground='white', borderwidth=2, date_pattern='mm/dd/yyyy')
date_requested.grid(row=1, column=0, sticky="w", padx=200, pady=5)
date_requested.set_date(datetime.datetime.now())

# Consent checkbox
ttk.Label(scrollable_frame, text="Consent:").grid(row=2, column=0, sticky="w", padx=10, pady=5)
consent_frame = ttk.Frame(scrollable_frame)
consent_frame.grid(row=2, column=0, columnspan=3, sticky="w", padx=200, pady=5)
consent_var = BooleanVar(value=True)
consent_check = ttk.Checkbutton(consent_frame, text="I agree to the Data Privacy Act of 2012", variable=consent_var)
consent_check.grid(row=0, column=0, sticky="w")

# Personal Information section
ttk.Label(scrollable_frame, text="Personal Information", font=("Helvetica", 12, "bold")).grid(row=3, column=0, columnspan=4, sticky="w", padx=10, pady=5)

# Name
ttk.Label(scrollable_frame, text="Name:").grid(row=4, column=0, sticky="w", padx=10, pady=5)
name_entry = ttk.Entry(scrollable_frame, width=40)
name_entry.grid(row=4, column=0, columnspan=3, sticky="w", padx=200, pady=5)

# Address
ttk.Label(scrollable_frame, text="Address:").grid(row=5, column=0, sticky="w", padx=10, pady=5)
address_entry = ttk.Entry(scrollable_frame, width=40)
address_entry.grid(row=5, column=0, columnspan=3, sticky="w", padx=200, pady=5)

# Contact Number
ttk.Label(scrollable_frame, text="Contact No.:").grid(row=6, column=0, sticky="w", padx=10, pady=5)
contact_entry = ttk.Entry(scrollable_frame, width=40)
contact_entry.grid(row=6, column=0, columnspan=3, sticky="w", padx=200, pady=5)

# Gender
ttk.Label(scrollable_frame, text="Gender:").grid(row=7, column=0, sticky="w", padx=10, pady=5)
gender_var = StringVar(value="Male")
gender_frame = ttk.Frame(scrollable_frame)
gender_frame.grid(row=7, column=0, sticky="w", padx=200, pady=5)
ttk.Radiobutton(gender_frame, text="Male", variable=gender_var, value="Male").grid(row=0, column=0, sticky="w")
ttk.Radiobutton(gender_frame, text="Female", variable=gender_var, value="Female").grid(row=0, column=1, sticky="w")
ttk.Radiobutton(gender_frame, text="Prefer not to say", variable=gender_var, value="Prefer not to say").grid(row=0, column=2, sticky="w")

# Age
ttk.Label(scrollable_frame, text="Age:").grid(row=7, column=0, sticky="w", padx=460, pady=5)
age_entry = ttk.Entry(scrollable_frame, width=10)
age_entry.grid(row=7, column=0, sticky="w", padx=500, pady=5)

# Work/Position/Designation
ttk.Label(scrollable_frame, text="Work/Position/Designation:").grid(row=8, column=0, sticky="w", padx=10, pady=5)
position_var = StringVar(value="Student")
position_frame = ttk.Frame(scrollable_frame)
position_frame.grid(row=8, column=0, columnspan=2, sticky="w", padx=200, pady=5)

# Add trace to position_var to enable/disable other field
position_var.trace_add("write", lambda *args: toggle_position_other())

ttk.Radiobutton(position_frame, text="Student", variable=position_var, value="Student").grid(row=0, column=0, sticky="w")
ttk.Radiobutton(position_frame, text="MSME/Entrepreneur", variable=position_var, value="MSME/Entrepreneur").grid(row=0, column=1, sticky="w")
ttk.Radiobutton(position_frame, text="Teacher", variable=position_var, value="Teacher").grid(row=0, column=2, sticky="w")
ttk.Radiobutton(position_frame, text="Hobbyist", variable=position_var, value="Hobbyist").grid(row=0, column=2, padx=80, sticky="w")
ttk.Radiobutton(position_frame, text="Other", variable=position_var, value="Other").grid(row=1, column=0, sticky="w")

# Other position specification
ttk.Label(position_frame, text="(please specify):").grid(row=1, column=1, sticky="w")
position_other_entry = ttk.Entry(position_frame, width=20, state="disabled")
position_other_entry.grid(row=1, column=2, sticky="w")

# Company/Affiliated with
ttk.Label(scrollable_frame, text="Company/Affiliated with:").grid(row=9, column=0, sticky="w", padx=10, pady=5)
company_entry = ttk.Entry(scrollable_frame, width=40)
company_entry.grid(row=9, column=0, columnspan=2, sticky="w", padx=200, pady=5)

# Service Requested section
ttk.Label(scrollable_frame, text="Service Requested", font=("Helvetica", 12, "bold")).grid(row=10, column=0, columnspan=4, sticky="w", padx=10, pady=10)

# Service types
service_frame = ttk.Frame(scrollable_frame)
service_frame.grid(row=11, column=0, columnspan=4, sticky="w", padx=10, pady=5)

training_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Training/Tour/Orientation", variable=training_var).grid(row=0, column=0, sticky="w")

product_design_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Product/Design/Consultation", variable=product_design_var).grid(row=1, column=0, sticky="w")

equipment_var = BooleanVar()
ttk.Checkbutton(service_frame, text="Equipment (Select equipment below)", variable=equipment_var).grid(row=2, column=0, sticky="w")

# Equipment section
ttk.Label(scrollable_frame, text="Equipment", font=("Helvetica", 10)).grid(row=12, column=0, columnspan=4, sticky="w", padx=20, pady=5)

equipment_frame = ttk.Frame(scrollable_frame)
equipment_frame.grid(row=13, column=0, columnspan=4, sticky="w", padx=30, pady=5)

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
ttk.Checkbutton(equipment_frame, text="Embroidery Machine (One head)", variable=embroidery_one_var).grid(row=0, column=1, sticky="w", padx=20)

embroidery_four_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Embroidery Machine (Four Heads)", variable=embroidery_four_var).grid(row=1, column=1, sticky="w", padx=20)

flatbed_cutter_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Flatbed Cutter", variable=flatbed_cutter_var).grid(row=2, column=1, sticky="w", padx=20)

vacuum_forming_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Vacuum Forming", variable=vacuum_forming_var).grid(row=3, column=1, sticky="w", padx=20)

water_jet_var = BooleanVar()
ttk.Checkbutton(equipment_frame, text="Water Jet Machine", variable=water_jet_var).grid(row=4, column=1, sticky="w", padx=20)

# Hand tools with trigger for entry field
hand_tools_var = BooleanVar()
hand_tools_frame = ttk.Frame(equipment_frame)
hand_tools_frame.grid(row=5, column=1, sticky="w", padx=20)
ttk.Checkbutton(hand_tools_frame, text="Hand Tools (please specify):", variable=hand_tools_var, 
               command=toggle_hand_tools).grid(row=0, column=0, sticky="w")
hand_tools_entry = ttk.Entry(hand_tools_frame, width=25, state="disabled")
hand_tools_entry.grid(row=0, column=1, sticky="w")

# Other equipment with trigger for entry field
other_equipment_var = BooleanVar()
other_equipment_frame = ttk.Frame(equipment_frame)
other_equipment_frame.grid(row=6, column=1, sticky="w", padx=20)
ttk.Checkbutton(other_equipment_frame, text="Other (please specify):", variable=other_equipment_var,
               command=toggle_other_equipment).grid(row=0, column=0, sticky="w")
other_equipment_entry = ttk.Entry(other_equipment_frame, width=25, state="disabled")
other_equipment_entry.grid(row=0, column=1, sticky="w")

# Consultation mode
ttk.Label(scrollable_frame, text="Other details:", font=("Helvetica", 10)).grid(row=14, column=0, sticky="w", padx=10, pady=10)
ttk.Label(scrollable_frame, text="If consultation, what mode of meeting do you prefer?").grid(row=15, column=0, sticky="w", padx=30, pady=5)

consultation_frame = ttk.Frame(scrollable_frame)
consultation_frame.grid(row=15, column=0, columnspan=3, sticky="w", padx=400, pady=5)

consultation_virtual_var = BooleanVar()
ttk.Checkbutton(consultation_frame, text="Virtual", variable=consultation_virtual_var).grid(row=0, column=0, sticky="w")

consultation_face_var = BooleanVar()
ttk.Checkbutton(consultation_frame, text="Face to Face", variable=consultation_face_var).grid(row=0, column=1, sticky="w")

# Schedule information
ttk.Label(scrollable_frame, text="Please specify your schedule:").grid(row=16, column=0, sticky="w", padx=30, pady=5)
schedule_entry = ttk.Entry(scrollable_frame, width=40)
schedule_entry.grid(row=16, column=0, columnspan=3, sticky="w", padx=400, pady=5)

ttk.Label(scrollable_frame, text="If equipment utilization, please specify your schedule:").grid(row=17, column=0, sticky="w", padx=30, pady=5)
equipment_schedule_entry = ttk.Entry(scrollable_frame, width=40)
equipment_schedule_entry.grid(row=17, column=0, columnspan=3, sticky="w", padx=400, pady=5)

# Work description
ttk.Label(scrollable_frame, text="Describe the work requested:").grid(row=18, column=0, sticky="w", padx=30, pady=5)
work_description_text = tk.Text(scrollable_frame, width=40, height=5)
work_description_text.grid(row=18, column=0, columnspan=3, sticky="w", padx=400, pady=5)

# Signature section
ttk.Label(scrollable_frame, text="Signature Information", font=("Helvetica", 12, "bold")).grid(row=19, column=0, columnspan=4, sticky="w", padx=10, pady=10)

ttk.Label(scrollable_frame, text="Date:").grid(row=20, column=0, sticky="w", padx=10, pady=5)
signature_date = DateEntry(scrollable_frame, width=20, background='darkblue', foreground='white', borderwidth=2, date_pattern='mm/dd/yyyy')
signature_date.grid(row=20, column=0, sticky="w", padx=200, pady=5)
signature_date.set_date(datetime.datetime.now())

ttk.Label(scrollable_frame, text="Client Name:").grid(row=21, column=0, sticky="w", padx=10, pady=5)
client_name_entry = ttk.Entry(scrollable_frame, width=35)
client_name_entry.grid(row=21, column=0, columnspan=2, sticky="w", padx=200, pady=5)

# Submit button
submit_button = ttk.Button(scrollable_frame, text="Generate PDF", command=on_submit)
submit_button.grid(row=22, column=0, columnspan=1,sticky="w", padx=350, pady=20)

# Result label
result_label = ttk.Label(scrollable_frame, text="")
result_label.grid(row=23, column=0, columnspan=1, sticky="w", padx=320, pady=5)

# Note about template
# note_label = ttk.Label(scrollable_frame, text="Note: This application requires 'request.pdf' to be in the same directory.", font=("Helvetica", 8))
# note_label.grid(row=24, column=0, sticky="w",padx=280, pady=10)

# Bind mouse wheel to canvas for scrolling
def _on_mousewheel(event):
    my_canvas.yview_scroll(int(-1*(event.delta/120)), "units")

my_canvas.bind_all("<MouseWheel>", _on_mousewheel)

root.mainloop()