# 🐳 Docker Setup Guide - Homify

## 📋 Overview

This guide provides complete Docker setup for the Homify Laravel application with multi-stage builds, development environment, and production deployment.

## 🏗️ Files Created

- `Dockerfile` - Production multi-stage build
- `Dockerfile.dev` - Development with hot reload
- `docker-compose.yml` - Complete development stack
- `docker/entrypoint.sh` - Production entrypoint script
- `docker/entrypoint.dev.sh` - Development entrypoint script
- `.dockerignore` - Optimized build context

## 🚀 Quick Start

### Production Build
```bash
# Build and run production container
docker build -t homify-app .
docker run -p 8000:80 --env-file .env homify-app
```

### Development Environment
```bash
# Start complete development stack
docker-compose up -d

# Access services:
# - App: http://localhost:8000
# - Mailpit: http://localhost:8025
# - MySQL: localhost:3306
# - Redis: localhost:6379
```

## 🔧 Environment Configuration

Create a `.env` file with these Docker-specific settings:

```env
APP_NAME=Homify
APP_ENV=production
APP_KEY=base64:l9OFOy1Mx97sddaMxOgd8OPAP2hsVaxKgEP7Jv4FhN0=
APP_DEBUG=false
APP_URL=http://localhost:8000
APP_TIMEZONE=Africa/Casablanca

# Database (Docker MySQL)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=homify
DB_USERNAME=homify_user
DB_PASSWORD=homify_password

# Cache & Session (Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379

# Mail (Mailpit for testing)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="hello@homify.com"
MAIL_FROM_NAME="${APP_NAME}"

# Stripe (add your keys)
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

## 🏭 Production Deployment

### Using Docker Compose
```bash
# Build and start production services
docker-compose up -d app mysql redis

# Check logs
docker-compose logs -f app
```

### Using Docker Swarm
```bash
# Initialize swarm
docker swarm init

# Deploy stack
docker stack deploy -c docker-compose.yml homify
```

### Using Kubernetes
```bash
# Generate Kubernetes manifests
kompose convert -f docker-compose.yml

# Apply to cluster
kubectl apply -f .
```

## 🛠️ Development Commands

```bash
# Start development environment
docker-compose up -d dev

# Run migrations
docker-compose exec dev php artisan migrate

# Seed database
docker-compose exec dev php artisan db:seed

# Install new packages
docker-compose exec dev composer require package/name
docker-compose exec dev npm install package-name

# Run tests
docker-compose exec dev php artisan test

# Clear caches
docker-compose exec dev php artisan cache:clear
docker-compose exec dev php artisan config:clear
docker-compose exec dev php artisan route:clear
docker-compose exec dev php artisan view:clear
```

## 🔍 Service Details

### Application Container
- **Base**: PHP 8.2 + Apache
- **Extensions**: PDO MySQL, ZIP, Intl, GD
- **Port**: 8000 (production), 8001 (development)
- **Features**: Multi-stage build, optimized assets

### MySQL Database
- **Version**: MySQL 8.0
- **Port**: 3306
- **Database**: homify
- **Credentials**: homify_user / homify_password
- **Persistence**: Docker volume

### Redis Cache
- **Version**: Redis 7 Alpine
- **Port**: 6379
- **Usage**: Sessions, cache, queues
- **Persistence**: Docker volume

### Mailpit (Email Testing)
- **SMTP Port**: 1025
- **Web UI**: http://localhost:8025
- **Purpose**: Email testing and debugging

## 🔐 Security Considerations

1. **Change default passwords** in production
2. **Use secrets management** for sensitive data
3. **Enable SSL/TLS** for production deployments
4. **Regular security updates** for base images
5. **Network isolation** with Docker networks

## 📊 Monitoring & Logs

```bash
# View application logs
docker-compose logs -f app

# View all service logs
docker-compose logs -f

# Check container status
docker-compose ps

# Monitor resource usage
docker stats
```

## 🚨 Troubleshooting

### Common Issues

1. **Composer Install Errors**
   ```
   Could not open input file: artisan
   ```
   **Solution**: The Dockerfile has been fixed to copy necessary Laravel files before running composer install. Use the updated Dockerfile.

2. **Permission Errors**
   ```bash
   sudo chown -R $USER:$USER storage bootstrap/cache
   ```

3. **Database Connection Issues**
   ```bash
   # Check if MySQL is ready
   docker-compose exec mysql mysql -u homify_user -p homify
   ```

4. **Asset Build Issues**
   ```bash
   # Rebuild assets
   docker-compose exec dev npm run build
   ```

5. **Cache Issues**
   ```bash
   # Clear all caches
   docker-compose exec app php artisan optimize:clear
   ```

6. **PHP Deprecation Warnings**
   ```
   Deprecation Notice: optional(): Implicitly marking parameter $callback as nullable
   ```
   **Solution**: These are Laravel framework warnings and don't affect functionality. They'll be resolved in future Laravel updates.

## 🔄 CI/CD Integration

### GitHub Actions Example
```yaml
name: Build and Deploy
on:
  push:
    branches: [main]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Build Docker image
        run: docker build -t homify-app .
      - name: Deploy
        run: docker-compose up -d
```

## 📈 Performance Optimization

1. **Multi-stage builds** reduce image size
2. **Layer caching** for faster builds
3. **Redis caching** for better performance
4. **Asset optimization** with Vite
5. **Database indexing** for queries

## 🎯 Next Steps

1. Set up your `.env` file with proper credentials
2. Add your Stripe keys for payment processing
3. Configure your domain and SSL certificates
4. Set up monitoring and logging
5. Implement CI/CD pipeline
6. Configure backup strategies

---

**Need help?** Check the troubleshooting section or create an issue in the repository.
