module.exports = {
  apps: [{
    name: 'laravel-queue',
    script: 'artisan',
    interpreter: 'php',
    args: 'queue:work --sleep=3 --tries=3 --max-time=3600',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '500M',
    env: {
      APP_ENV: 'local'
    }
  }]
};
