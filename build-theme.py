#!/usr/bin/env python3

"""
Builds the theme from the source files.
"""

import shutil
from datetime import datetime
from pathlib import Path
from tempfile import TemporaryDirectory

from rich import print


def main():
    print("[bold green]Building theme...[/bold green]")

    with TemporaryDirectory() as temp_dir:
        print("Copying source files...")
        print(f"Temporary directory: {temp_dir}")

        print(f'Copying "theme"...')
        shutil.copytree("theme", Path(temp_dir) / "theme")

        print(f'Copying "vendor"...')
        shutil.copytree("vendor", Path(temp_dir) / "theme" / "vendor")

        print("Creating theme.zip...")
        filename = f'theme-{datetime.now().strftime("%Y-%m-%d_%H-%M-%S")}'
        shutil.make_archive(
            base_name=filename, format="zip", root_dir=temp_dir, base_dir="."
        ),

    print("[bold green]Theme built successfully![/bold green]")


if __name__ == "__main__":
    main()
