# Module Folder Name

Display the technical name of each module in the PrestaShop module listing for quick identification during development and administration.

## Overview

When managing multiple modules in PrestaShop, identifying the technical folder name requires accessing module metadata or file system. This module adds the technical name directly beneath each module title in the management interface, eliminating the need for additional lookups.

## Features

- **Display Technical Names** — Shows module folder names (technical identifiers) in the module listing
- **Customizable Selectors** — Configure CSS selectors to adapt to different PrestaShop versions or custom layouts
- **Non-Intrusive** — Uses JavaScript injection via hook; no database modifications required
- **Zero Dependencies** — Works out of the box with PrestaShop 9

## Requirements

- PrestaShop 9.0.0 or higher
- PHP 8.1+

## Installation

1. Download the module from the release or clone the repository
2. Upload the `modules_slug` folder to `/modules/` in your PrestaShop installation
3. Navigate to **Modules > Module Manager** in the PrestaShop back-office
4. Search for "Module Folder Name" and click **Install**
5. (Optional) Click **Configure** to customize CSS selectors

## Configuration

The module provides two configurable CSS selectors to locate elements within the module listing:

- **Card Selector** — Element containing `data-tech-name` attribute (default: `.module-item[data-tech-name]`)
- **Name Selector** — Element where the technical name is inserted (default: `h3.module-name-list`)

To restore defaults, click the **Restore Default Values** button on the configuration page.

### When to Customize

Modify selectors if:
- PrestaShop updates the module listing HTML structure
- Your installation uses custom templates or CSS classes
- You need to target a specific layout variation

## How It Works

The module injects CSS and JavaScript into the PrestaShop back-office header when viewing module pages. A `MutationObserver` monitors DOM changes and appends the technical name beneath each module title in a small monospace font.

The technical name is extracted from the existing `data-tech-name` HTML attribute already present in module cards — no additional data collection occurs.

## Support & Issues

For issues or feature requests, contact the author or submit a bug report.

## Author

**cartorse**

---

**Version:** 1.0.0  
**License:** Proprietary
