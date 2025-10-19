# Tile Order Management System

Modern REST/SOAP API for managing tile orders with real-time price scraping, statistics, and full-text search capabilities.

## Features

- ✅ **4 Required Endpoints** - Price scraping, statistics, SOAP service, order retrieval
- ✅ **Docker Compose** - One-command deployment
- ✅ **Pure OOP PHP 8.2** - Modern, typed, PSR-4 compliant
- ✅ **Database** - Original + improved normalized schemas with analysis
- ✅ **Manticore Search** - Full-text search integration
- ✅ **Security** - Input validation, CORS, XSS protection
- ✅ **Tests** - PHPUnit test suite
- ✅ **Documentation** - Swagger UI + interactive SOAP tester

## Quick Start

```bash
# Start all services
make install

# Or manually
docker-compose up -d
docker-compose exec php composer install
```

Access: **http://localhost:8080**

## API Endpoints

### 1. Get Tile Price
```bash
GET /api/price?factory=cobsa&collection=manual&article=manu7530bcbm-manualbaltic7-5x30
```

### 2. Order Statistics
```bash
GET /api/orders/statistics?page=1&per_page=10&group_by=month
```

### 3. SOAP Service 
```bash
POST /api/orders/new
WSDL: http://localhost:8080/api/orders/new?wsdl
Tester: http://localhost:8080/soap-test.html
```

### 4. Get Order
```bash
GET /api/orders?id=1
```

### Bonus: Search Orders
```bash
GET /api/orders/search?q=john&page=1
```

## Documentation

- **Swagger UI:** http://localhost:8080/swagger
- **Database Analysis:** [DATABASE_ANALYSIS.md](DATABASE_ANALYSIS.md)
- **API Root:** http://localhost:8080

## Project Structure

```
.
├── docker/                 # Docker configurations
│   ├── mysql/init/        # DB schemas (original + improved)
│   ├── nginx/             # Nginx config
│   ├── php/               # PHP Dockerfile
│   └── manticore/         # Search config
├── src/
│   ├── Config/            # Database connection
│   ├── Controller/        # API controllers
│   ├── Model/             # Data models
│   ├── Repository/        # Data access layer
│   └── Service/           # Business logic
├── tests/                 # PHPUnit tests
├── public/                # Web root
│   ├── index.php         # Main router
│   ├── swagger.html      # API docs
│   └── soap-test.html    # SOAP tester
├── docker-compose.yml
├── Makefile
└── README.md
```

## Make Commands

```bash
make build      # Build containers
make up         # Start services
make down       # Stop services
make restart    # Restart
make test       # Run tests
make logs       # View logs
make shell      # PHP container shell
make mysql      # MySQL CLI
```

## Technology Stack

- **PHP 8.2-FPM** - Modern PHP with OPcache
- **MySQL 8.0** - Relational database
- **Nginx** - High-performance web server
- **Manticore Search** - Full-text search
- **Docker** - Containerization

## Database

### Issues in Original Schema
1. Wrong data types (VARCHAR for dates/prices)
2. No indexes
3. Not normalized
4. No constraints
5. Missing timestamps

### Improvements Made
- Proper types (TIMESTAMP, DECIMAL)
- Normalized (customers, orders, order_items)
- Indexes on key fields
- Foreign keys & constraints
- Auto timestamps

Full analysis: [DATABASE_ANALYSIS.md](DATABASE_ANALYSIS.md)

## Security Features

- Input validation & sanitization
- SQL injection protection (PDO prepared statements)
- XSS protection headers
- CORS configuration
- Error hiding in production
- Type-safe code

## Testing

```bash
# Run all tests
make test

# Or
docker-compose exec php vendor/bin/phpunit

# Run specific test
docker-compose exec php vendor/bin/phpunit tests/OrderRepositoryTest.php
```

## Environment Variables

Configure in `.env`:

```env
# Application
APP_ENV=development

# Ports
NGINX_PORT=8080
DB_EXTERNAL_PORT=3306

# Database
DB_HOST=mysql
DB_PORT=3306
DB_NAME=tile_app
DB_USER=root
DB_PASS=root

# Manticore
MANTICORE_HOST=manticore
MANTICORE_PORT=9306
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Change default passwords
3. Configure HTTPS
4. Set up proper CORS origins
5. Enable PHP OPcache
6. Configure log rotation
7. Set up monitoring

## Troubleshooting

### Services not starting
```bash
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Port conflicts
Edit `.env` and change port numbers, then `make restart`

### Database connection failed
```bash
docker-compose logs mysql
docker-compose restart mysql
```

## Architecture

- **Repository Pattern** - Data access abstraction
- **Service Layer** - Business logic separation
- **PSR-4 Autoloading** - Standard PHP structure
- **Type Safety** - Full type hints
- **Error Handling** - Comprehensive exception handling

## Performance

- Database indexes on frequently queried fields
- Lazy loading for optional services
- PDO with prepared statements
- OPcache enabled in production
- Efficient Docker multi-stage builds

## License

MIT

## Author

Test Task Implementation
