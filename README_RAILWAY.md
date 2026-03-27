# Food Hunt - Railway Deployment Guide

## Why Railway?
✅ **Full PHP Support** - Native PHP support  
✅ **Built-in Database** - MySQL included  
✅ **Easy Setup** - One-click deployment  
✅ **Free Tier** - $5 credit every month  
✅ **Custom Domain** - Free SSL certificate  

## Step 1: Railway Setup

### 1.1 Create Account
1. [railway.app](https://railway.app) पर जाएं
2. "Login with GitHub" click करें
3. Email confirm करें

### 1.2 New Project
1. Dashboard में "New Project" click करें
2. "Deploy from GitHub repo" select करें
3. `food-hunt-vercel` repository choose करें
4. "Deploy Now" click करें

## Step 2: Configure Project

### 2.1 Build Settings
Railway automatically detect करेगा, अगर नहीं तो:
```
Build Command: php -v
Start Command: php -S 0.0.0.0:$PORT -t .
```

### 2.2 Environment Variables
Project settings में जाकर ये variables add करें:
```
DB_HOST = localhost
DB_USER = root
DB_PASS = ""
DB_NAME = railway
APP_URL = https://your-app-name.railway.app
```

## Step 3: Database Setup

### 3.1 Add MySQL Database
1. Project में "New" → "Database" click करें
2. "MySQL" select करें
3. Database name दें (जैसे `food-hunt-db`)
4. "Add Database" click करें

### 3.2 Database Connection
Database create होने के बाद:
1. Database पर click करें
2. "Connect" tab खोलें
3. Connection details copy करें
4. Environment variables update करें:

```
DB_HOST = containers-us-west-XXX.railway.app
DB_USER = root
DB_PASS = your_database_password
DB_NAME = railway
```

### 3.3 Import Database Schema
1. Railway database में "Query" tab खोलें
2. `localhost.sql` file का content copy करें
3. Paste करके "Execute" click करें

## Step 4: Deploy

### 4.1 Automatic Deploy
Railway automatically deploy करेगा:
- Code changes को detect करेगा
- Build करेगा
- Deploy करेगा

### 4.2 Manual Deploy
अगर manual deploy करना हो:
1. Project में "Deployments" tab खोलें
2. "New Deployment" click करें
3. Branch select करें (main)
4. "Deploy Now" click करें

## Step 5: Access Application

### 5.1 Application URL
Deploy होने के बाद:
- URL: `https://your-app-name.railway.app`
- Automatic SSL certificate
- Mobile responsive design

### 5.2 Test Application
1. Homepage open करें
2. Food listing check करें
3. Search functionality test करें
4. Login/register test करें

## Step 6: Custom Domain (Optional)

### 6.1 Add Domain
1. Project settings में "Settings" जाएं
2. "Domains" tab खोलें
3. "Add Domain" click करें
4. अपना domain enter करें

### 6.2 DNS Configuration
Domain provider में DNS records add करें:
```
Type: CNAME
Name: @
Value: proxy.railway.app
```

## Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
```
Solution:
- Check PHP syntax errors
- Verify database connection
- Check file permissions
```

#### 2. Database Connection Failed
```
Solution:
- Verify environment variables
- Check database is running
- Test connection manually
```

#### 3. White Screen
```
Solution:
- Enable PHP error reporting
- Check .htaccess rules
- Verify file paths
```

### Debug Mode Enable करें
`connection.php` में ये code add करें:
```php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Performance Optimization

### 1. Enable OPcache
PHP settings में:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
```

### 2. Database Optimization
- Add indexes to frequently queried columns
- Use prepared statements
- Optimize queries

### 3. Caching
- Implement Redis caching
- Use browser caching
- Compress static assets

## Monitoring

### 1. Railway Dashboard
- Real-time logs
- Performance metrics
- Error tracking

### 2. Custom Monitoring
```php
// Add to your PHP files
error_log("User action: " . $_SERVER['REQUEST_URI']);
```

## Scaling

### 1. Vertical Scaling
- Upgrade to larger instance
- More RAM and CPU

### 2. Horizontal Scaling
- Multiple instances
- Load balancing

## Security

### 1. Environment Variables
- Never commit secrets
- Use Railway environment variables
- Rotate passwords regularly

### 2. HTTPS
- Automatic SSL certificate
- Force HTTPS redirect
- Security headers

## Backup Strategy

### 1. Database Backup
```bash
# Manual backup
mysqldump -h host -u user -p database > backup.sql
```

### 2. File Backup
- Git repository backup
- Asset files backup

## Cost Management

### 1. Free Tier Usage
- $5 credit monthly
- Monitor usage
- Optimize resources

### 2. Paid Plans
- Hobby: $5/month
- Pro: $20/month
- Custom: Enterprise

## Support Resources

### 1. Railway Documentation
- [docs.railway.app](https://docs.railway.app)
- GitHub discussions
- Community Discord

### 2. PHP Resources
- PHP.net documentation
- Stack Overflow
- Community forums

## Migration from Local

### 1. Database Migration
```sql
-- Export local database
mysqldump -u root -p dbfood > local_backup.sql

-- Import to Railway
mysql -h host -u user -p railway < local_backup.sql
```

### 2. File Migration
- Upload via Git
- Use Railway CLI
- Direct file upload

## Best Practices

### 1. Code Quality
- Use version control
- Code reviews
- Automated testing

### 2. Performance
- Optimize images
- Minimize HTTP requests
- Use CDN

### 3. Security
- Input validation
- SQL injection prevention
- XSS protection

## Conclusion

Railway पर Food Hunt application deploy करना बहुत easy है:
1. GitHub से connect करें
2. Database add करें
3. Environment variables set करें
4. Deploy करें

आपका application minutes में live हो जाएगा!
