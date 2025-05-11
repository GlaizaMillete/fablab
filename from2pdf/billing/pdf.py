from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter
from PyPDF2 import PdfReader, PdfWriter

def create_grid_overlay():
    overlay_path = "grid_overlay.pdf"
    c = canvas.Canvas(overlay_path, pagesize=letter)

    c.setFont("Helvetica", 6)  # smaller labels

    # Vertical lines and labels
    for x in range(0, 612, 20):  # 612 is width of letter
        c.setStrokeColorRGB(0.8, 0.8, 0.8)
        c.line(x, 0, x, 792)  # 792 is height of letter
        c.setStrokeColorRGB(0, 0, 0)
        c.drawString(x + 1, 2, str(x))

    # Horizontal lines and labels
    for y in range(0, 792, 20):
        c.setStrokeColorRGB(0.8, 0.8, 0.8)
        c.line(0, y, 612, y)
        c.setStrokeColorRGB(0, 0, 0)
        c.drawString(2, y + 1, str(y))

    c.save()
    return overlay_path

def merge_with_template():
    overlay_path = create_grid_overlay()
    base = PdfReader("template.pdf")
    overlay = PdfReader(overlay_path)
    writer = PdfWriter()

    page = base.pages[0]
    page.merge_page(overlay.pages[0])
    writer.add_page(page)

    with open("template_with_grid.pdf", "wb") as f:
        writer.write(f)

create_grid_overlay()
merge_with_template()
print("Created 'template_with_grid.pdf' with fine grid overlay.")
