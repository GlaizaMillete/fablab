import tkinter as tk
from tkinter import ttk, font
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
    c.drawString(340, 570, data["Service Description"])
    
    # Handle multiple item rows
    y_offset = 500
    for idx, item_row in enumerate(data["items"]):
        # Only draw the first 4 items to avoid going off the page
        if idx < 4:
            row_y = y_offset - (idx * 12)
            c.drawString(100, row_y, item_row["Item"])
            c.drawString(256, row_y, item_row["Unit"])
            c.drawString(310, row_y, item_row["Rate"])
            c.drawString(410, row_y, item_row["Total"])
    
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

class JobOrderApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Fab Lab Bicol - Job Order Form")
        self.root.geometry("1050x800")
        self.root.configure(bg="#f0f0f0")
        
        # # Set app icon
        # try:
        #     self.root.iconbitmap("fab_lab_icon.ico")
        # except:
        #     pass 
        
        # Configure font styles
        self.title_font = font.Font(family="Arial", size=16, weight="bold")
        self.header_font = font.Font(family="Arial", size=12, weight="bold")
        self.normal_font = font.Font(family="Arial", size=10)
        
        # Variables
        self.entries = {}
        self.is_student = tk.IntVar()
        self.is_msme = tk.IntVar()
        self.is_other = tk.IntVar()
        
        # For managing multiple item rows
        self.item_rows = []
        
        # Create main frame with padding
        self.main_frame = ttk.Frame(root, padding="20 20 20 20")
        self.main_frame.pack(fill=tk.BOTH, expand=True)
        
        # Create a notebook for tabbed interface
        self.notebook = ttk.Notebook(self.main_frame)
        self.notebook.pack(fill=tk.BOTH, expand=True, pady=10)
        
        # First tab - Client Information
        self.client_frame = ttk.Frame(self.notebook, padding="10")
        self.notebook.add(self.client_frame, text="Client Information")
        
        # Second tab - Service Details
        self.service_frame = ttk.Frame(self.notebook, padding="10")
        self.notebook.add(self.service_frame, text="Service Details")
        
        # Setup the UI components
        self.setup_client_info()
        self.setup_service_details()
        self.setup_buttons()
        
        # Status bar at the bottom
        self.status_frame = ttk.Frame(self.main_frame, relief=tk.SUNKEN)
        self.status_frame.pack(fill=tk.X, side=tk.BOTTOM, pady=(10, 0))
        self.status_label = ttk.Label(self.status_frame, text="Ready", anchor=tk.W)
        self.status_label.pack(fill=tk.X, padx=5, pady=2)
    
    def setup_client_info(self):
        # Title
        title_label = ttk.Label(self.client_frame, text="Client Information", font=self.title_font)
        title_label.grid(row=0, column=0, columnspan=2, sticky="w", pady=(0, 20))
        
        # Client information fields
        client_fields = [
            ("Client Name", "Client Name"), 
            ("Address", "Address"), 
            ("Contact Number", "Contact Number"),
            ("Job Order Number", "Number"),
            ("Date", "Date")
        ]
        
        for i, (label_text, key) in enumerate(client_fields):
            row = i + 1
            ttk.Label(self.client_frame, text=label_text, font=self.normal_font).grid(
                row=row, column=0, sticky="w", padx=(0, 10), pady=(10, 5)
            )
            
            if key == "Date":
                entry = DateEntry(
                    self.client_frame, 
                    width=20,
                    background='darkblue',
                    foreground='white',
                    borderwidth=2,
                    date_pattern='mm/dd/yyyy'
                )
            else:
                entry = ttk.Entry(self.client_frame, width=40)
            
            entry.grid(row=row, column=1, sticky="ew", pady=(10, 5))
            self.entries[key] = entry
        
        # Client type frame with border
        client_type_frame = ttk.LabelFrame(self.client_frame, text="Client Type", padding="10")
        client_type_frame.grid(row=6, column=0, columnspan=2, sticky="ew", pady=20)
        
        # Client type options
        ttk.Checkbutton(client_type_frame, text="Student", variable=self.is_student).grid(
            row=0, column=0, sticky="w", padx=(0, 20)
        )
        ttk.Checkbutton(client_type_frame, text="MSME", variable=self.is_msme).grid(
            row=0, column=1, sticky="w", padx=(0, 20)
        )
        
        # Others option with text field
        other_check = ttk.Checkbutton(
            client_type_frame, 
            text="Others, specify:", 
            variable=self.is_other,
            command=self.toggle_other_field
        )
        other_check.grid(row=0, column=2, sticky="w")
        
        self.other_entry = ttk.Entry(client_type_frame, width=20, state='disabled')
        self.other_entry.grid(row=0, column=3, sticky="w", padx=(5, 0))
        
        # Configure grid
        self.client_frame.columnconfigure(1, weight=1)
    
    def setup_service_details(self):
        # Title
        title_label = ttk.Label(self.service_frame, text="Service Details", font=self.title_font)
        title_label.grid(row=0, column=0, columnspan=5, sticky="w", pady=(0, 20))
        
        # Service description
        ttk.Label(self.service_frame, text="Service Description", font=self.normal_font).grid(
            row=1, column=0, sticky="w", pady=(10, 5)
        )
        service_desc = ttk.Entry(self.service_frame, width=50)
        service_desc.grid(row=1, column=1, columnspan=4, sticky="ew", pady=(10, 5))
        self.entries["Service Description"] = service_desc
        
        # Create a frame for the item table
        self.item_frame = ttk.LabelFrame(self.service_frame, text="Item Details", padding="10")
        self.item_frame.grid(row=2, column=0, columnspan=5, sticky="ew", pady=20)
        
        # Headers
        headers = ["Item", "Unit", "Rate", "Total", ""]  # Added an empty header for delete button column
        for i, header in enumerate(headers):
            ttk.Label(self.item_frame, text=header, font=self.header_font).grid(
                row=0, column=i, padx=10, pady=(0, 10)
            )
        
        # Add initial item row
        self.add_item_row()
        
        # Add Row button
        ttk.Button(
            self.item_frame,
            text="+ Add Row",
            command=self.add_item_row
        ).grid(row=len(self.item_rows) + 1, column=0, sticky="w", padx=10, pady=10)
        
        # Overall total row
        ttk.Label(self.item_frame, text="Overall Total:", font=self.header_font).grid(
            row=len(self.item_rows) + 2, column=2, sticky="e", padx=10, pady=20
        )
        total_entry = ttk.Entry(self.item_frame, width=20)
        total_entry.grid(row=len(self.item_rows) + 2, column=3, padx=10, pady=20)
        self.entries["OvaTotal"] = total_entry
        
        # Configure grid
        self.service_frame.columnconfigure(1, weight=1)
        self.service_frame.columnconfigure(2, weight=1)
        self.service_frame.columnconfigure(3, weight=1)
    
    def add_item_row(self):
        row_index = len(self.item_rows) + 1  # +1 because row 0 is for headers
        row_data = {}
        
        # Create entries for this row
        for col, field in enumerate(["Item", "Unit", "Rate", "Total"]):
            entry = ttk.Entry(self.item_frame, width=20)
            entry.grid(row=row_index, column=col, padx=10, pady=5)
            
            # Add validation and event binding to recalculate total when Rate or Total changes
            if field == "Total":
                entry.bind("<FocusOut>", lambda e, idx=len(self.item_rows): self.calculate_overall_total())
                entry.bind("<Return>", lambda e, idx=len(self.item_rows): self.calculate_overall_total())
            
            row_data[field] = entry
        
        # Add delete button for all rows except the first one
        if row_index > 1:
            delete_btn = ttk.Button(
                self.item_frame,
                text="X",
                width=2,
                command=lambda idx=row_index-1: self.delete_item_row(idx)
            )
            delete_btn.grid(row=row_index, column=4, padx=5, pady=5)
            row_data["delete_btn"] = delete_btn
        
        self.item_rows.append(row_data)
        
        # Reposition the Add Row button
        for widget in self.item_frame.grid_slaves():
            if int(widget.grid_info()["row"]) == len(self.item_rows) and widget.grid_info()["column"] == "0" and isinstance(widget, ttk.Button):
                widget.grid_forget()
        
        # Update Add Row button position
        ttk.Button(
            self.item_frame,
            text="+ Add Row",
            command=self.add_item_row
        ).grid(row=len(self.item_rows) + 1, column=0, sticky="w", padx=10, pady=10)
        
        # Update Overall Total row position
        for widget in self.item_frame.grid_slaves():
            if int(widget.grid_info()["row"]) == len(self.item_rows) + 1 and widget.grid_info()["column"] in ["2", "3"]:
                widget.grid_forget()
        
        ttk.Label(self.item_frame, text="Overall Total:", font=self.header_font).grid(
            row=len(self.item_rows) + 2, column=2, sticky="e", padx=10, pady=20
        )
        
        if "OvaTotal" in self.entries:
            self.entries["OvaTotal"].grid(row=len(self.item_rows) + 2, column=3, padx=10, pady=20)
        else:
            total_entry = ttk.Entry(self.item_frame, width=20)
            total_entry.grid(row=len(self.item_rows) + 2, column=3, padx=10, pady=20)
            self.entries["OvaTotal"] = total_entry
        
        # Limit the number of rows to 4
        if len(self.item_rows) >= 4:
            for widget in self.item_frame.grid_slaves():
                if int(widget.grid_info()["row"]) == len(self.item_rows) + 1 and widget.grid_info()["column"] == "0" and isinstance(widget, ttk.Button):
                    widget.config(state='disabled')
                    self.status_label.config(text="Maximum rows reached (4)")
    
    def delete_item_row(self, index):
        # Remove the widgets from the grid
        for field in self.item_rows[index]:
            if not isinstance(self.item_rows[index][field], str):  # Only remove widgets
                self.item_rows[index][field].grid_forget()
        
        # Remove the row data from our list
        self.item_rows.pop(index)
        
        # Re-grid all rows after the deleted one
        self.redraw_item_rows()
        
        # Enable Add Row button if we're below the maximum
        if len(self.item_rows) < 4:
            for widget in self.item_frame.grid_slaves():
                if int(widget.grid_info()["row"]) == len(self.item_rows) + 1 and widget.grid_info()["column"] == "0" and isinstance(widget, ttk.Button):
                    widget.config(state='normal')
                    self.status_label.config(text="Row deleted")
        
        # Recalculate the overall total
        self.calculate_overall_total()
    
    def redraw_item_rows(self):
        # Clear the grid of all item rows
        for widget in self.item_frame.grid_slaves():
            if int(widget.grid_info()["row"]) > 0 and int(widget.grid_info()["row"]) <= len(self.item_rows) + 2:
                widget.grid_forget()
        
        # Redraw each row
        for i, row_data in enumerate(self.item_rows):
            row_index = i + 1  # +1 because row 0 is for headers
            
            # Redraw the entries
            for col, field in enumerate(["Item", "Unit", "Rate", "Total"]):
                row_data[field].grid(row=row_index, column=col, padx=10, pady=5)
                
                # Rebind the event for Total field
                if field == "Total":
                    row_data[field].bind("<FocusOut>", lambda e, idx=i: self.calculate_overall_total())
                    row_data[field].bind("<Return>", lambda e, idx=i: self.calculate_overall_total())
            
            # Redraw the delete button (if not the first row)
            if i > 0 and "delete_btn" in row_data:
                row_data["delete_btn"].grid(row=row_index, column=4, padx=5, pady=5)
            # Add a delete button if this isn't the first row and it doesn't have one
            elif i > 0 and "delete_btn" not in row_data:
                delete_btn = ttk.Button(
                    self.item_frame,
                    text="X",
                    width=2,
                    command=lambda idx=i: self.delete_item_row(idx)
                )
                delete_btn.grid(row=row_index, column=4, padx=5, pady=5)
                row_data["delete_btn"] = delete_btn
        
        # Redraw the "Add Row" button
        ttk.Button(
            self.item_frame,
            text="+ Add Row",
            command=self.add_item_row,
            state='normal' if len(self.item_rows) < 4 else 'disabled'
        ).grid(row=len(self.item_rows) + 1, column=0, sticky="w", padx=10, pady=10)
        
        # Redraw the "Overall Total" label and entry
        ttk.Label(self.item_frame, text="Overall Total:", font=self.header_font).grid(
            row=len(self.item_rows) + 2, column=2, sticky="e", padx=10, pady=20
        )
        self.entries["OvaTotal"].grid(row=len(self.item_rows) + 2, column=3, padx=10, pady=20)
        
        # Recalculate the overall total
        self.calculate_overall_total()
    
    def calculate_overall_total(self):
        """Calculate the sum of all Total fields and update Overall Total"""
        try:
            total_sum = 0.0
            
            # Loop through each row and add up the Total values
            for row in self.item_rows:
                total_entry = row["Total"]
                total_text = total_entry.get().strip()
                
                # Skip empty entries
                if total_text:
                    try:
                        # Try to convert to float and add to sum
                        total_value = float(total_text)
                        total_sum += total_value
                    except ValueError:
                        # If conversion fails, show error message
                        self.status_label.config(text="Invalid number in Total field")
                        return
            
            # Format the sum to 2 decimal places
            formatted_sum = f"{total_sum:.2f}"
            
            # Update the Overall Total field
            self.entries["OvaTotal"].delete(0, tk.END)
            self.entries["OvaTotal"].insert(0, formatted_sum)
            
            self.status_label.config(text=f"Total calculated: {formatted_sum}")
            
        except Exception as e:
            self.status_label.config(text=f"Error calculating total: {str(e)}")
    
    def setup_buttons(self):
        button_frame = ttk.Frame(self.main_frame)
        button_frame.pack(fill=tk.X, pady=20)
        
        # Add buttons with better styling
        ttk.Button(
            button_frame, 
            text="Clear Form", 
            command=self.clear_form,
            style="Secondary.TButton"
        ).pack(side=tk.LEFT, padx=5)
        
        ttk.Button(
            button_frame, 
            text="Calculate Total", 
            command=self.calculate_overall_total,
            style="Secondary.TButton"
        ).pack(side=tk.LEFT, padx=5)
        
        ttk.Button(
            button_frame, 
            text="Generate PDF", 
            command=self.generate_pdf,
            style="Primary.TButton"
        ).pack(side=tk.RIGHT, padx=5)
        
        # Configure button styles
        style = ttk.Style()
        style.configure("Primary.TButton", background="#0066cc", foreground="white")
        style.configure("Secondary.TButton", background="#e0e0e0")
    
    def toggle_other_field(self):
        if self.is_other.get():
            self.other_entry.config(state='normal')
        else:
            self.other_entry.delete(0, 'end')
            self.other_entry.config(state='disabled')
    
    def clear_form(self):
        # Clear all entry fields
        for entry in self.entries.values():
            entry.delete(0, 'end')
        
        # Clear item rows
        for row in self.item_rows:
            for entry in row.values():
                if hasattr(entry, 'delete'):  # Only clear Entry widgets
                    entry.delete(0, 'end')
        
        # Reset checkboxes
        self.is_student.set(0)
        self.is_msme.set(0)
        self.is_other.set(0)
        self.other_entry.config(state='disabled')
        self.other_entry.delete(0, 'end')
        
        self.status_label.config(text="Form cleared")
    
    def generate_pdf(self):
        self.status_label.config(text="Generating PDF...")
        
        # Recalculate the total to ensure it's up to date
        self.calculate_overall_total()
        
        # Collect form data
        form_data = {label: self.entries[label].get() for label in self.entries}
        
        # Collect item rows data
        form_data["items"] = []
        for row in self.item_rows:
            item_data = {field: row[field].get() for field in ["Item", "Unit", "Rate", "Total"]}
            form_data["items"].append(item_data)
        
        # Checkbox values
        form_data["is_student"] = self.is_student.get()
        form_data["is_msme"] = self.is_msme.get()
        form_data["is_other"] = self.is_other.get()
        form_data["other_text"] = self.other_entry.get() if self.is_other.get() else ""
        
        # Generate PDF
        fill_pdf(form_data)
        self.status_label.config(text="PDF generated and opened.")


if __name__ == "__main__":
    root = tk.Tk()
    app = JobOrderApp(root)
    root.mainloop()