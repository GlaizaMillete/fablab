import os
import platform
import subprocess
import shutil
import sys
import PyInstaller.__main__

def check_requirements():
    """Check if PyInstaller is installed, install if needed."""
    try:
        PyInstaller.__main__.run([
            'billing.py',
            '--onefile',
            '--name=FabLabJobOrder',
            '--windowed',
        ])
        print("PyInstaller build finished.")
    except Exception as e:
        print("Error during PyInstaller build:", e)
    except ImportError:
        print("Installing PyInstaller...")
        subprocess.check_call([sys.executable, "-m", "pip3", "install", "PyInstaller"])
        print("PyInstaller installed successfully.")

def create_executable():
    """Create the executable using PyInstaller."""
    print("Creating executable with PyInstaller...")
    
    # Ensure all requirements are installed
    requirements = [
        "reportlab",
        "PyPDF2",
        "tkcalendar"
    ]
    
    for req in requirements:
        try:
            __import__(req.split(">=")[0].strip())
            print(f"{req} is already installed.")
        except ImportError:
            print(f"Installing {req}...")
            subprocess.check_call([sys.executable, "-m", "pip", "install", req])
            print(f"{req} installed successfully.")
    
    # Create spec file with proper configuration
    spec_content = """
# -*- mode: python ; coding: utf-8 -*-

block_cipher = None

a = Analysis(
    ['billing.py'],
    pathex=[],
    binaries=[],
    datas=[('template.pdf', '.'), ('overlay.pdf', '.')],
    hiddenimports=['tkcalendar', 'babel.numbers'],
    hookspath=[],
    hooksconfig={},
    runtime_hooks=[],
    excludes=[],
    win_no_prefer_redirects=False,
    win_private_assemblies=False,
    cipher=block_cipher,
    noarchive=False,
)
pyz = PYZ(a.pure, a.zipped_data, cipher=block_cipher)

exe = EXE(
    pyz,
    a.scripts,
    a.binaries,
    a.zipfiles,
    a.datas,
    [],
    name='FabLabJobOrder',
    debug=False,
    bootloader_ignore_signals=False,
    strip=False,
    upx=True,
    upx_exclude=[],
    runtime_tmpdir=None,
    console=False,
    disable_windowed_traceback=False,
    argv_emulation=False,
    target_arch=None,
    codesign_identity=None,
    entitlements_file=None,
    icon='NONE',
)
"""
    
    with open("fablab_joborder.spec", "w") as f:
        f.write(spec_content)
    
    # Build the executable using the spec file
    subprocess.check_call([sys.executable, "-m", "PyInstaller", "fablab_joborder.spec", "--clean"])
    
    print("\nExecutable created successfully!")
    print(f"You can find it in the 'dist' folder as 'FabLabJobOrder{'.exe' if platform.system() == 'Windows' else ''}'")

if __name__ == "__main__":
    check_requirements()
    create_executable()