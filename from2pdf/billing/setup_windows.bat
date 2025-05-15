@echo off
echo ===================================
echo FabLab Bicol Job Order System Setup
echo ===================================
echo.

echo Checking for Python installation...
python --version > nul 2>&1
if %errorlevel% neq 0 (
    echo Python is not installed or not in PATH.
    echo Please install Python 3.6 or higher before continuing.
    echo You can download Python from https://www.python.org/downloads/
    pause
    exit /b 1
)

echo Python found. Installing required packages...
python -m pip install --upgrade pip
python -m pip install reportlab PyPDF2 tkcalendar pyinstaller

echo.
echo Checking for incompatible packages...
python -c "import pkg_resources; print('pathlib' in [pkg.key for pkg in pkg_resources.working_set])" > temp.txt
set /p HAS_PATHLIB=<temp.txt
del temp.txt

if "%HAS_PATHLIB%"=="True" (
    echo Found incompatible package 'pathlib'. Attempting to uninstall...
    
    REM Try with pip
    python -m pip uninstall -y pathlib
    
    echo Warning: The 'pathlib' package may cause issues with PyInstaller.
    echo If executable creation fails, please manually uninstall it:
    echo   - For pip: pip uninstall pathlib
    echo   - For conda: conda remove pathlib
)

echo.
echo Creating executable...
python create_executable.py

REM Check if executable was created successfully
if exist "dist\FabLabJobOrder.exe" (
    set HAS_EXECUTABLE=true
) else (
    set HAS_EXECUTABLE=false
    echo Warning: Executable creation failed. Creating a fallback launcher script instead.
    
    REM Create launcher batch file
    echo @echo off > FabLabJobOrder.bat
    echo REM FabLab Bicol Job Order System Launcher >> FabLabJobOrder.bat
    echo cd /d "%%~dp0" >> FabLabJobOrder.bat
    echo python billing.py >> FabLabJobOrder.bat
    echo pause >> FabLabJobOrder.bat
    
    echo Created launcher script FabLabJobOrder.bat
)

echo.
echo Creating distribution package...
if not exist FabLabJobOrder mkdir FabLabJobOrder

if "%HAS_EXECUTABLE%"=="true" (
    copy dist\FabLabJobOrder.exe FabLabJobOrder\
) else (
    copy FabLabJobOrder.bat FabLabJobOrder\
    copy billing.py FabLabJobOrder\
)

copy template.pdf FabLabJobOrder\
copy overlay.pdf FabLabJobOrder\
copy README.md FabLabJobOrder\README.txt

echo.
echo Creating ZIP archive...
powershell Compress-Archive -Path FabLabJobOrder -DestinationPath FabLabJobOrder.zip -Force

echo.
echo ========================================================
echo Setup complete! 
echo.
echo The executable package is available in:
echo - Folder: FabLabJobOrder
echo - ZIP file: FabLabJobOrder.zip
echo.

if "%HAS_EXECUTABLE%"=="false" (
    echo Note: The package contains a launcher script instead of an executable.
    echo To run the application, double-click on FabLabJobOrder.bat
)
echo ========================================================
echo.

pause