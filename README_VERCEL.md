# Food Hunt - Vercel Deployment Guide

## Overview
यह guide आपको Food Hunt application को Vercel पर deploy करने में मदद करेगी। Vercel PHP को natively support नहीं करता है, इसलिए हमने application को serverless functions के साथ optimize किया है।

## Prerequisites
- Vercel account
- GitHub account
- MySQL database (external service जैसे PlanetScale, Railway, या AWS RDS)
- Node.js (local development के लिए)

## Step 1: Database Setup

### 1.1 External MySQL Database
Vercel पर PHP के लिए आपको external MySQL database की जरूरत होगी:

**Options:**
- **PlanetScale** (Recommended)
- **Railway**
- **AWS RDS**
- **DigitalOcean Managed Database**

### 1.2 Database Configuration
अपनी database में `localhost.sql` file import करें और credentials note करें।

## Step 2: Project Setup

### 2.1 Install Vercel CLI
```bash
npm i -g vercel
```

### 2.2 Project Structure
आपका project अब इस structure को follow करता है:
```
dbfood/
├── api/
│   └── index.php          # Serverless API functions
├── index.html             # Frontend (HTML/CSS/JS)
├── mobile.css             # Mobile responsive styles
├── vercel.json            # Vercel configuration
├── package.json           # Node.js dependencies
└── README_VERCEL.md       # यह file
```

## Step 3: Environment Variables

### 3.1 Set Environment Variables
Vercel dashboard में जाकर ये environment variables set करें:

1. Vercel dashboard खोलें
2. अपना project select करें
3. Settings → Environment Variables जाएं
4. ये variables add करें:

```
DB_HOST = your_database_host
DB_USER = your_database_user
DB_PASS = your_database_password
DB_NAME = your_database_name
APP_URL = https://your-app.vercel.app
```

## Step 4: Deployment Methods

### Method 1: GitHub Integration (Recommended)

#### 4.1 Push to GitHub
```bash
git init
git add .
git commit -m "Initial commit for Vercel deployment"
git branch -M main
git remote add origin https://github.com/yourusername/food-hunt.git
git push -u origin main
```

#### 4.2 Connect to Vercel
1. Vercel dashboard में "Add New Project" click करें
2. अपना GitHub repository import करें
3. Framework के लिए "Other" select करें
4. Build settings automatically detect हो जाएंगे
5. "Deploy" button click करें

### Method 2: Vercel CLI

#### 4.3 Deploy with CLI
```bash
# Project directory में जाएं
cd dbfood

# Login to Vercel
vercel login

# Deploy project
vercel

# Production deploy के लिए
vercel --prod
```

## Step 5: API Endpoints

आपके application में ये API endpoints available होंगे:

```
GET  /api/foods      - सभी foods की list
GET  /api/vendors    - सभी vendors की list  
POST /api/orders     - Order place करना
POST /api/login      - User login
POST /api/register   - User registration
GET  /api/search     - Food/vendor search
```

## Step 6: Frontend Integration

### 6.1 API Calls
Frontend JavaScript में API calls इस तरह करें:

```javascript
// Example: Foods fetch करना
async function getFoods() {
    const response = await fetch('/api/foods');
    const foods = await response.json();
    return foods;
}

// Example: Login करना
async function login(email, password) {
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
    });
    return await response.json();
}
```

## Step 7: Testing

### 7.1 Local Testing
```bash
# Install dependencies
npm install

# Start local server
npm run dev
```

### 7.2 Production Testing
Deploy के बाद ये test करें:
- API endpoints working हैं
- Database connection successful है
- Mobile responsiveness working है
- All features properly function कर रहे हैं

## Step 8: Custom Domain (Optional)

### 8.1 Domain Setup
1. Vercel dashboard में जाएं
2. Settings → Domains जाएं
3. अपना domain add करें
4. DNS settings update करें

## Step 9: Monitoring

### 9.1 Vercel Analytics
Vercel automatically analytics provide करता है:
- Page views
- Visitors
- Performance metrics
- Error tracking

### 9.2 Logs
```bash
# Vercel logs check करने के लिए
vercel logs
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
```
Solution: Environment variables check करें और database credentials verify करें
```

#### 2. CORS Error
```
Solution: API में CORS headers properly set हैं यह check करें
```

#### 3. Build Error
```
Solution: package.json और vercel.json configuration check करें
```

#### 4. 404 Error
```
Solution: API routes properly configured हैं यह verify करें
```

### Debugging Tips

#### 1. Local Testing
```bash
# Local environment variables set करें
export DB_HOST="localhost"
export DB_USER="root"
export DB_PASS=""
export DB_NAME="dbfood"
```

#### 2. API Testing
```bash
# curl से API test करें
curl -X GET https://your-app.vercel.app/api/foods
```

#### 3. Browser Console
Browser developer tools में console check करें JavaScript errors के लिए।

## Limitations

### Vercel Specific
- PHP execution time limit: 10 seconds (free tier)
- Database connections must be external
- File uploads limited
- Background jobs not supported

### Workarounds
- Long-running tasks के लिए cron jobs use करें
- File storage के लिए cloud storage use करें
- Email sending के लिए third-party services use करें

## Cost Considerations

### Vercel Pricing
- **Hobby**: Free (100GB bandwidth, 100 function invocations/day)
- **Pro**: $20/month (Unlimited bandwidth, more function invocations)
- **Enterprise**: Custom pricing

### Database Costs
- **PlanetScale**: Free tier available
- **Railway**: $5/month starting
- **AWS RDS**: Pay-as-you-go

## Migration from Traditional Hosting

### Key Differences
1. **Static Frontend**: HTML/CSS/JS files static होते हैं
2. **Serverless API**: PHP functions serverless होते हैं
3. **External Database**: Database external होना चाहिए
4. **No File System**: File system access limited है

### Migration Steps
1. Frontend को HTML/CSS/JS में convert करें
2. PHP logic को API endpoints में move करें
3. Database calls को API calls में convert करें
4. File uploads को cloud storage में move करें

## Support

### Resources
- [Vercel Documentation](https://vercel.com/docs)
- [Vercel PHP Runtime](https://github.com/vercel/vercel/tree/main/packages/php)
- [PlanetScale Documentation](https://planetscale.com/docs)

### Community
- Vercel Discord Server
- Stack Overflow
- GitHub Issues

## Conclusion

Food Hunt application को Vercel पर successfully deploy करने के लिए:
1. External MySQL database setup करें
2. Environment variables configure करें
3. GitHub repository से deploy करें
4. सभी functionality test करें
5. Monitor और optimize करते रहें

यह setup आपको modern, scalable, और fast hosting provide करेगा जो mobile-friendly भी है।
