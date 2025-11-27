# Moroccan Job Scraper

Automated job scraper for Moroccan employment websites with FREE WhatsApp notifications.

## Features

- Scrapes jobs from Rekrute.com, Emploi.ma, and M-job.ma
- FREE unlimited WhatsApp notifications (using Baileys)
- Filter by keyword, location, and job type
- REST API for integration
- Web dashboard

## Quick Start

```bash
# 1. Install
git clone https://github.com/YOUR-USERNAME/moroccan-job-scraper.git
cd moroccan-job-scraper
composer install

# 2. Setup
cp .env.example .env
php artisan key:generate
# Edit .env with your database credentials
php artisan migrate

# 3. Start WhatsApp service
cd whatsapp-service
npm install
npm start
# Scan QR code with your phone

# 4. Start queue worker
php artisan queue:work &

# 5. Create alert
php artisan job:create-alert "Developer" "Casablanca" "you@email.com" "+212600000000"
```

## Requirements

- PHP 8.1+
- Composer
- Node.js 16+
- MySQL/PostgreSQL

## Cost

FREE - Uses Baileys (WhatsApp Web API) instead of paid providers ($0 vs $25-99/month)

## Tech Stack

- Laravel 10.x
- Node.js (Baileys for WhatsApp)
- MySQL/PostgreSQL
- Tailwind CSS

## License

MIT License - See [LICENSE](LICENSE)

## Contact

- Issues: [GitHub Issues](https://github.com/zakariafl27/moroccan-job-scraper/issues)
- Email: flafizakaria27@gmail.com
