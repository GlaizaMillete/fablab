#!/bin/bash

echo "==================================="
echo "FabLab Bicol Job Order System Setup"
echo "==================================="
echo

echo "Checking for Python installation..."
if ! command -v python3 &> /dev/null; then
    echo "Python 3 is not installed or not in PATH."
    echo "Please install Python 3.6 or higher before continuing."
    echo "On Ubuntu/Debian: sudo apt-get install python3 python3-pip"
    echo "On macOS with Homebrew: brew install python3"
    exit 1
fi

echo "Python found. Installing required packages..."
python3 -m pip install --upgrade pip
python3 -m pip install reportlab PyPDF2 tkcalendar

# First, try to uninstall pathlib if it exists (it's incompatible with PyInstaller)
echo "Checking for incompatible packages..."
if python3 -m pip list | grep -q "pathlib"; then
    echo "Found incompatible package 'pathlib'. Attempting to uninstall..."
    
    # For conda environments
    if command -v conda &> /dev/null; then
        conda remove -y pathlib || echo "Failed to remove with conda. Trying pip..."
    fi
    
    # Try with pip as fallback
    python3 -m pip uninstall -y pathlib || echo "Warning: Failed to uninstall pathlib. The executable build may fail."
fi

echo
echo "Creating executable..."
python3 create_executable.py

# Check if executable was created successfully
if [ -f "dist/FabLabJobOrder" ] || [ -f "dist/FabLabJobOrder.exe" ]; then
    HAS_EXECUTABLE=true
    echo "Executable created successfully!"
else
    HAS_EXECUTABLE=false
    echo "Warning: Executable creation failed. Creating a fallback launcher script instead."
    
    # Create launcher script
    cat > FabLabJobOrder.sh << 'EOF'
#!/bin/bash
# FabLab Bicol Job Order System Launcher
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$DIR"
python3 billing.py
EOF
    chmod +x FabLabJobOrder.sh
    echo "Created launcher script FabLabJobOrder.sh"
fi

echo
echo "Creating distribution package..."
mkdir -p FabLabJobOrder
if [ "$HAS_EXECUTABLE" = true ]; then
    # Copy the executable
    if [ -f "dist/FabLabJobOrder" ]; then
        cp dist/FabLabJobOrder FabLabJobOrder/
    elif [ -f "dist/FabLabJobOrder.exe" ]; then
        cp dist/FabLabJobOrder.exe FabLabJobOrder/
    fi
else
    # Copy all necessary files for running from source
    cp FabLabJobOrder.sh FabLabJobOrder/
    cp billing.py FabLabJobOrder/
fi

# Always copy these files
cp template.pdf FabLabJobOrder/
cp overlay.pdf FabLabJobOrder/
cp README.md FabLabJobOrder/README.txt

echo
echo "Creating ZIP archive..."
if command -v zip &> /dev/null; then
    zip -r FabLabJobOrder.zip FabLabJobOrder
else
    echo "Warning: 'zip' command not found. ZIP archive not created."
    echo "Please install zip or manually create a ZIP archive of the FabLabJobOrder folder."
fi

echo
echo "========================================================"
echo "Setup complete!"
echo
echo "The executable package is available in:"
echo "- Folder: FabLabJobOrder"
echo "- ZIP file: FabLabJobOrder.zip (if zip was installed)"
echo

if [ "$HAS_EXECUTABLE" = false ]; then
    echo "Note: The package contains a launcher script instead of an executable."
    echo "To run the application, use:"
    echo "  - On Mac/Linux: ./FabLabJobOrder.sh"
fi
echo "========================================================"
echo

echo "Press Enter to continue..."
read