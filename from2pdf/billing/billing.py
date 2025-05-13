import tkinter as tk
from reportlab.pdfgen import canvas
from tkcalendar import DateEntry
from reportlab.lib.pagesizes import letter
from PyPDF2 import PdfReader, PdfWriter
import os
import platform

print("Current working directory:", os.getcwd())
print("Looking for template.pdf...")

def fill_pdf(data):
    output_filename = "filled_job_order.pdf"
    overlay_filename = "overlay.pdf"

    c = canvas.Canvas(overlay_filename, pagesize=letter)
    c.setFont("Helvetica", 10)

    c.drawString(140, 650, data["Client Name"])
    c.drawString(140, 638, data["Address"])
    c.drawString(140, 626, data["Contact Number"])
    c.drawString(420, 661, data["Number"])
    c.drawString(400, 650, data["Date"])
    c.drawString(380, 560, data["Service Description"])
    c.drawString(100, 500, data["Item"])
    c.drawString(256, 500, data["Unit"])
    c.drawString(310, 500, data["Rate"])
    c.drawString(410, 500, data["Total"])
    c.drawString(410, 452, data["OvaTotal"])

    checkmark = "✓" 
    if data["is_student"]:
        c.drawString(131, 581, checkmark)

    if data["is_msme"]:
        c.drawString(131, 566, checkmark)

    if data["is_other"]:
        c.drawString(131, 551, checkmark)  
        c.drawString(180, 540, data["other_text"]) 

    
    c.save()

    base = PdfReader("template.pdf")
    overlay = PdfReader(overlay_filename)
    writer = PdfWriter()

    page = base.pages[0]
    page.merge_page(overlay.pages[0])
    writer.add_page(page)

    with open(output_filename, "wb") as f_out:
        writer.write(f_out)

    if platform.system() == "Windows":
        os.startfile(output_filename)
    elif platform.system() == "Darwin":
        os.system(f"open {output_filename}")
    else:
        os.system(f"xdg-open {output_filename}")

def on_submit():
    form_data = {label: entries[label].get() for label in labels}

    # Checkbox values
    form_data["is_student"] = is_student.get()
    form_data["is_msme"] = is_msme.get()
    form_data["is_other"] = is_other.get()
    form_data["other_text"] = other_entry.get() if is_other.get() else ""

    fill_pdf(form_data)
    result_label.config(text="PDF generated and opened.")

# GUI
root = tk.Tk()
root.title("Job Order Form / Billing")
root.geometry("800x900")

labels = [
    "Client Name", "Address", "Contact Number", "Number", "Date",
    "Service Description","Item", "Unit", "Rate", "Total", "OvaTotal"
]
entries = {}
is_student = tk.IntVar()
is_msme = tk.IntVar()
is_other = tk.IntVar()

# Build GUI elements
for i, label in enumerate(labels):
    if label == "Service Description":
        # Checkboxes for client type
        tk.Label(root, text="Client Type:").pack(pady=(15, 0))
        tk.Checkbutton(root, text="Student", variable=is_student).pack(anchor="w", padx=30)
        tk.Checkbutton(root, text="MSME", variable=is_msme).pack(anchor="w", padx=30)

        def toggle_other_field():
            if is_other.get():
                other_entry.config(state='normal')
            else:
                other_entry.delete(0, 'end')
                other_entry.config(state='disabled')

        tk.Checkbutton(root, text="Others", variable=is_other, command=toggle_other_field).pack(anchor="w", padx=30)
        other_entry = tk.Entry(root, width=30, state='disabled')
        other_entry.pack(pady=(5, 0), padx=30, anchor="w")

    # Regular input field
    tk.Label(root, text=label).pack(pady=(10, 0))
    if label == "Date":
        entry = DateEntry(root, width=37, background='darkblue', foreground='white', borderwidth=2, date_pattern='mm/dd/yyyy')
    else:
        entry = tk.Entry(root, width=40)

    entry.pack()
    entries[label] = entry

tk.Button(root, text="Generate PDF", command=on_submit).pack(pady=20)
result_label = tk.Label(root, text="")
result_label.pack()

root.mainloop()
