from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter
from PyPDF2 import PdfReader, PdfWriter
import os

def create_grid_overlay(input_pdf="request.pdf"):
    # Get the dimensions from the input PDF
    reader = PdfReader(input_pdf)
    page = reader.pages[0]
    mediabox = page.mediabox
    page_width = float(mediabox.width)
    page_height = float(mediabox.height)
    
    print(f"PDF dimensions: Width={page_width}, Height={page_height}")
    
    # Create grid overlay
    overlay_path = "grid_overlay.pdf"
    c = canvas.Canvas(overlay_path, pagesize=(page_width, page_height))
    c.setFont("Helvetica", 6)
    c.setStrokeColorRGB(0.8, 0.8, 0.8)  # Light gray lines

    # Draw vertical grid lines and labels (every 10 units instead of 20)
    for x in range(0, int(page_width) + 1, 10):
        c.line(x, 0, x, page_height)
        # Bottom labels - moved up slightly to be more visible
        c.drawString(x + 1, 5, str(x))
        # Top labels - moved down slightly to be more visible
        c.drawString(x + 1, page_height - 10, str(x))

    # Draw horizontal grid lines and labels (every 10 units instead of 20)
    for y in range(0, int(page_height) + 1, 10):
        c.line(0, y, page_width, y)
        # Left labels - moved right slightly to be more visible
        c.drawString(2, y + 1, str(y))
        # Right labels - moved left slightly to be more visible
        c.drawString(page_width - 20, y + 1, str(y))
    
    # Add color-coded form field highlights to help visualize important areas
    # Use very light colors with low opacity
    
    # Header area
    c.setFillColorRGB(0.9, 0.9, 1.0, 0.1)  # Very light blue, low opacity
    c.rect(0, page_height - 250, page_width, 250, fill=True, stroke=False)
    
    # Personal info section
    c.setFillColorRGB(0.9, 1.0, 0.9, 0.1)  # Very light green, low opacity
    c.rect(0, page_height - 450, page_width, 200, fill=True, stroke=False)
    
    # Service requested section
    c.setFillColorRGB(1.0, 0.9, 0.9, 0.1)  # Very light red, low opacity
    c.rect(0, page_height - 600, page_width, 150, fill=True, stroke=False)
    
    # Equipment section
    c.setFillColorRGB(1.0, 1.0, 0.9, 0.1)  # Very light yellow, low opacity
    c.rect(0, page_height - 750, page_width, 150, fill=True, stroke=False)
    
    # Add reference points for common form fields based on servicereq.py
    c.setFillColorRGB(1, 0, 0, 0.3)  # Red, semi-transparent
    
    # Key coordinates from servicereq.py (adding small markers)
    key_points = [
        (210, 800, "Date Requested"),  # Consent checkbox
        (135, 740, "Consent"),  # Consent checkbox
        (200, 595, "Name"),
        (200, 580, "Address"),
        (200, 565, "Contact"),
        (310, 550, "Male"),  # Gender checkboxes
        (310, 535, "Female"),
        (310, 520, "Prefer not"),
        (355, 550, "Age"),
        (170, 495, "Student"),  # Position checkboxes
        (170, 480, "MSME"),
        (170, 465, "Teacher"),
        (170, 450, "Hobbyist"),
        (170, 435, "Other position"),
        (450, 495, "Company"),
        (290, 405, "Training"),  # Service Requested checkboxes
        (290, 390, "Product"),
        (290, 375, "Equipment"),
        (410, 360, "3D Printer"),  # Equipment checkboxes
        (410, 165, "Other equip")
    ]
    
    # Draw small circles at key coordinates
    c.setFillColorRGB(1, 0, 0, 0.5)  # Red with 50% opacity
    for x, y, label in key_points:
        c.circle(x, y, 2, fill=True)
        c.setFillColorRGB(0, 0, 0, 1)  # Black for text
        c.drawString(x + 3, y - 3, label)
        c.setFillColorRGB(1, 0, 0, 0.5)  # Reset to red for next circle
    
    c.save()
    return overlay_path, page_width, page_height

def merge_with_template(input_pdf="request.pdf", output_pdf="request_with_grid.pdf"):
    overlay_path, width, height = create_grid_overlay(input_pdf)
    
    try:
        base = PdfReader(input_pdf)
        overlay = PdfReader(overlay_path)
        writer = PdfWriter()

        # Merge each page (in case of multi-page documents)
        for i in range(len(base.pages)):
            page = base.pages[i]
            
            # Only apply overlay to the first page if there's only one overlay page
            if i < len(overlay.pages):
                page.merge_page(overlay.pages[i])
                
            writer.add_page(page)

        # Write the output PDF
        with open(output_pdf, "wb") as f:
            writer.write(f)
            
        print(f"✅ Created '{output_pdf}' with grid overlay")
        print(f"   PDF dimensions: Width={width}, Height={height}")
        
        # Clean up the temporary overlay file
        if os.path.exists(overlay_path):
            os.remove(overlay_path)
            
        return True
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        return False

if __name__ == "__main__":
    merge_with_template()