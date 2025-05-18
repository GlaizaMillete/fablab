@echo off
echo Checking Python installation...
python --version >nul 2>&1
if errorlevel 1 (
    echo Python is not installed! Please install Python from https://www.python.org/downloads/
    echo Make sure to check "Add Python to PATH" during installation
    pause
    exit
)

echo Installing required packages...
pip install -r requirements.txt

echo Creating executable...
pyinstaller --onefile --windowed --add-data "request.pdf;." servicereq.py

echo.
echo Installation complete! You can find the executable in the 'dist' folder.
echo.
pause 