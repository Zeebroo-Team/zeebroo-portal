# Socibiz POS Desktop

Qt 6 desktop point-of-sale client for **Socibiz**. It uses the same REST API as the web Online POS (`Modules/Pos`).

## Features

- Sign in with Sanctum API token (`POST /api/v1/pos/auth/token`)
- Business picker (`GET /api/v1/pos/businesses`)
- Three-panel UI aligned with the web terminal: **Current sale**, **Product catalog**, **Checkout**
- Category filters, name search, SKU scan
- Multi-batch stock layer picker when required
- Cash / card / credit checkout with numpad (`POST /api/v1/pos/online/checkout`)
- Receipt dialog after sale with **thermal printer** support (80mm paper preset)

## Requirements

- **Qt 6** (Core, Gui, Widgets, Network, Multimedia, PrintSupport)
- **CMake** 3.21+
- C++17 compiler
- Running **Socibiz** Laravel app with POS API enabled (`laravel/sanctum`, POS module routes)

## Configuration

Copy `config.example.json` to your app config directory, or place `config.json` next to the executable:

```json
{
  "api_base_url": "https://your-app.test/api/v1/pos",
  "device_name": "pos-desktop-1"
}
```

On macOS the writable path is typically:

`~/Library/Application Support/Socibiz/PosDesktop/config.json`

You can also set the API URL on the sign-in screen.

## Build

```bash
cd pos_desktop
cmake -B build -DCMAKE_PREFIX_PATH="$(qtpaths6 --install-prefix 2>/dev/null || echo /opt/homebrew/opt/qt)"
cmake --build build
./build/SocibizPosDesktop
```

Homebrew (macOS):

```bash
brew install qt cmake
```

## API reference

- Interactive docs: `GET /api/v1/pos/docs`
- Markdown guide: `Modules/Pos/docs/API.md`

## Project layout

```
pos_desktop/
  CMakeLists.txt
  src/
    main.cpp
    core/          ApiClient, Cart, Models, Config
    ui/            LoginDialog, MainWindow, LayerPickerDialog
  resources/
    styles.qss     Warm POS theme
```

## Notes

- Send `X-Business-Id` on every authenticated request (handled automatically after login).
- Cash and card payments require a deposit account (from POS settings / bootstrap `accounts`).
- When `requires_layer_pick` is true on a product, the app prompts for a stock batch before adding to cart.
