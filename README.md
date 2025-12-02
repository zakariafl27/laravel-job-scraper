# Moroccan Job Scraper

Automated job scraper for Moroccan job boards with FREE WhatsApp notifications using Baileys API.

![GitHub stars](https://img.shields.io/github/stars/zakariafl27/laravel-job-scraper-morocco?style=social)
![GitHub forks](https://img.shields.io/github/forks/zakariafl27/laravel-job-scraper-morocco?style=social)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-blue.svg)](https://www.postgresql.org/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## Features

- **Multi-Source Scraping** - Rekrute.com, Emploi.ma, M-job.ma
- **FREE WhatsApp Notifications** - Using Baileys (no paid API)
- **Smart Job Alerts** - Keyword, location, and job type filtering
- **Beautiful Dashboard** - Modern Tailwind CSS interface
- **Real-time Updates** - Auto-refresh every 30 seconds
- **RESTful API** - Complete JSON API endpoints
- **Queue System** - Asynchronous job processing
- **PostgreSQL Database** - Robust database with ILIKE search

## Quick Start

### Prerequisites

- PHP 8.3+
- PostgreSQL 15+
- Composer
- Node.js (for WhatsApp)

### Installation

```bash
# Clone repository
git clone https://github.com/zakariafl27/laravel-job-scraper-morocco.git
cd laravel-job-scraper-morocco

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database
# Edit .env file with your database credentials

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

Visit: **http://localhost:8000**

## Usage

### Create Job Alert

**Via Dashboard:**
1. Open http://localhost:8000
2. Fill the form (name, email, WhatsApp, keyword)
3. Click "Create Alert"
4. Receive WhatsApp notifications automatically

**Via API:**
```bash
curl -X POST http://localhost:8000/api/v1/alerts \
  -H "Content-Type: application/json" \
  -d '{
    "user_name": "John Doe",
    "user_email": "john@example.com",
    "user_phone": "+212600000000",
    "keyword": "Laravel Developer",
    "location": "Casablanca",
    "job_types": ["CDI"],
    "sources": ["rekrute", "emploi", "mjob"]
  }'
```

### Scrape Jobs

```bash
# Scrape for all active alerts
php artisan job:score

# Process queue
php artisan queue:work
```

### Automated Scraping

Add to crontab for automatic job scraping:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Tech Stack

<table>
  <tr>
    <td align="center"><b>Backend</b></td>
    <td>Laravel 12, PHP 8.3</td>
  </tr>
  <tr>
    <td align="center"><b>Database</b></td>
    <td>PostgreSQL 15</td>
  </tr>
  <tr>
    <td align="center"><b>Frontend</b></td>
    <td>Tailwind CSS, Alpine.js</td>
  </tr>
  <tr>
    <td align="center"><b>Queue</b></td>
    <td>Laravel Queue (Database driver)</td>
  </tr>
  <tr>
    <td align="center"><b>WhatsApp</b></td>
    <td>Baileys API (Node.js)</td>
  </tr>
</table>

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/jobs` | List all jobs (paginated) |
| `GET` | `/api/v1/jobs/{id}` | Get job details |
| `GET` | `/api/v1/jobs/statistics` | Get dashboard statistics |
| `GET` | `/api/v1/alerts` | List all alerts |
| `POST` | `/api/v1/alerts` | Create new alert |
| `PUT` | `/api/v1/alerts/{id}` | Update alert |
| `DELETE` | `/api/v1/alerts/{id}` | Delete alert |

### Example Response

```json
{
  "total_alerts": 5,
  "active_alerts": 3,
  "total_jobs": 152,
  "new_today": 12,
  "notifications_sent": 45,
  "jobs_by_source": {
    "rekrute": 87,
    "emploi": 42,
    "mjob": 23
  }
}
```

## Project Structure

```
laravel-job-scraper-morocco/
├── app/
│   ├── Console/Commands/      # Artisan commands (job:score, etc.)
│   ├── Http/Controllers/Api/  # API controllers
│   ├── Jobs/                  # Queue jobs
│   ├── Models/                # Eloquent models (Job, JobAlert)
│   └── Services/              # Business logic & scrapers
├── database/
│   └── migrations/            # Database schema
├── resources/
│   └── views/                 # Blade templates (dashboard)
├── routes/
│   └── api.php               # API routes
└── README.md
```

## Roadmap

- [ ] Add more job sources (LinkedIn, Indeed Morocco)
- [ ] Email notifications support
- [ ] Advanced filtering options
- [ ] Job application tracking
- [ ] Mobile app (React Native)
- [ ] Chrome extension

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Issues

Found a bug? Have a feature request? Please open an [issue](https://github.com/zakariafl27/laravel-job-scraper-morocco/issues).

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Author

**Zakaria FLAFI**
- GitHub: [@zakariafl27](https://github.com/zakariafl27)
- LinkedIn: [Zakaria FLAFI](https://www.linkedin.com/in/zakaria-flafi-410706333/)

## Acknowledgments

- [Laravel Framework](https://laravel.com) - The PHP framework for web artisans
- [Baileys WhatsApp Library](https://github.com/WhiskeySockets/Baileys) - WhatsApp Web API
- [Tailwind CSS](https://tailwindcss.com) - A utility-first CSS framework
- Moroccan Job Boards - For providing public job listings

## Support

If you find this project helpful, please consider:
- Starring the repository
- Reporting bugs
- Suggesting new features
- Contributing to the code

---

<div align="center">

**Made with Love in Morocco**

If you find this project useful, please give it a star!

</div>
