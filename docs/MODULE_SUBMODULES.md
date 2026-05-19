# Laravel modules as Git submodules

Each `Modules/{Name}` package and `pos_desktop/` live in their own GitHub repo under [Zeebroo-Team](https://github.com/Zeebroo-Team). The main app ([zeebroo-portal](https://github.com/Zeebroo-Team/zeebroo-portal)) references them as **git submodules**.

## Repo naming

| Path | Submodule repo |
|------|----------------|
| `Modules/Pos` | `Zeebroo-Team/zeebroo-module-pos` |
| `Modules/Product` | `Zeebroo-Team/zeebroo-module-product` |
| … | `Zeebroo-Team/zeebroo-module-{name}` (lowercase) |
| `pos_desktop` | `Zeebroo-Team/zeebroo-pos-desktop` |

## First-time setup (maintainers)

1. Log in to GitHub CLI:
   ```bash
   gh auth login
   ```

2. Publish modules and install submodules:
   ```bash
   chmod +x scripts/git-submodules/*.sh
   ./scripts/git-submodules/setup-all-submodules.sh
   ```

   Or one module at a time:
   ```bash
   gh repo create Zeebroo-Team/zeebroo-module-pos --public
   ./scripts/git-submodules/publish-module.sh Pos
   ./scripts/git-submodules/install-module-submodule.sh Pos
   ```

3. Push the main repo:
   ```bash
   git push origin main
   ```

## Clone the app with all modules

```bash
git clone --recurse-submodules git@github.com:Zeebroo-Team/zeebroo-portal.git
cd zeebroo-portal
composer install
```

If you already cloned without submodules:

```bash
git submodule update --init --recursive
```

## Update a module to latest

```bash
cd Modules/Pos
git pull origin main
cd ../..
git add Modules/Pos
git commit -m "Bump Pos submodule"
```

## Laravel / Composer

Submodule paths stay at `Modules/*`. `composer.json` merge-plugin still loads `Modules/*/composer.json` after `composer install`.

Run migrations as usual:

```bash
php artisan migrate
```

## Desktop POS client

```bash
cd pos_desktop
cmake -B build -DCMAKE_PREFIX_PATH="$(qtpaths6 --install-prefix 2>/dev/null || echo /opt/homebrew/opt/qt)"
cmake --build build
```
