module.exports = {
  apps: [
    {
      name: 'whatsapp-service',
      script: './whatsapp-service/server.js',
      instances: 1,
      autorestart: true,
      max_memory_restart: '500M'
    },
    {
      name: 'laravel-queue',
      script: './artisan',
      interpreter: 'php',
      args: 'queue:work --sleep=3 --tries=3',
      instances: 1,
      autorestart: true
    },
    {
      name: 'laravel-scheduler',
      script: './artisan',
      interpreter: 'php',
      args: 'schedule:work',
      instances: 1,
      autorestart: true
    },
    {
      name: 'laravel-server',
      script: './artisan',
      interpreter: 'php',
      args: 'serve --port=8000',
      instances: 1,
      autorestart: true
    }
  ]
};
