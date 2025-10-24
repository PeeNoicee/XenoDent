# XenoDent AI - Render Blueprint Deployment Guide

## Overview
Deploy your Laravel + Flask application using Render's Blueprint feature with the `render.yaml` configuration file.

## Pre-Deployment Checklist

### 1. Generate Laravel Application Key
Run this command locally to get your APP_KEY:
```bash
php artisan key:generate --show
```
**Save this key** - you'll need it for the deployment.

### 2. Verify render.yaml Configuration
Your `render.yaml` file is configured with:
- ✅ Laravel web service (`xenodent-web`)
- ✅ Flask AI service (`xenodent-flask`)
- ✅ PostgreSQL database (`xenodent-db`)
- ✅ Environment variables setup
- ✅ Service communication configured

### 3. API Key Security
The Flask app now uses the `ROBOFLOW_API_KEY` environment variable. Update this in production:
- Current: `E6WARDv3iZ4kV75PfaR5`
- Replace with your production API key

## Deployment Steps

### Step 1: Push to GitHub
1. Ensure all changes are committed to your GitHub repository
2. Verify `render.yaml` is in the root directory
3. Push your latest changes

### Step 2: Create Blueprint on Render
1. Go to [render.com](https://render.com)
2. Click **"New"** → **"Blueprint"**
3. Connect your GitHub account if not already connected
4. Select your XenoDent repository
5. Render will automatically detect `render.yaml`

### Step 3: Configure APP_KEY
**Important**: The `render.yaml` has `generateValue: true` for APP_KEY, but you may want to use your own:

1. After deployment starts, go to the `xenodent-web` service
2. Navigate to **Environment** tab
3. Update `APP_KEY` with the key you generated earlier
4. Click **Save Changes**

### Step 4: Monitor Deployment
Watch the deployment logs for each service:
- **xenodent-web**: Laravel build and start
- **xenodent-flask**: Flask AI service
- **xenodent-db**: Database creation

### Step 5: Verify Services
Once deployed, check:
1. **Laravel service**: Should show your application
2. **Flask service**: Should respond to health checks
3. **Database**: Migrations should run automatically

## Service Architecture

```
┌─────────────────┐    ┌─────────────────┐
│   Laravel Web   │───▶│   Flask AI      │
│   (Main App)    │    │   (X-ray API)   │
└─────────┬───────┘    └─────────────────┘
          │
          ▼
┌─────────────────┐
│   PostgreSQL    │
│   (Database)    │
└─────────────────┘
```

## Environment Variables

### Laravel Service (xenodent-web):
- `APP_KEY`: Auto-generated or your custom key
- `APP_URL`: Auto-set to service URL
- `DB_*`: Auto-connected to database
- `FLASK_BACKEND_URL`: Auto-set to Flask service URL

### Flask Service (xenodent-flask):
- `FLASK_ENV`: production
- `PORT`: 5000
- `ROBOFLOW_API_KEY`: Your API key

## Post-Deployment Configuration

### 1. Custom Domain (Optional)
1. Go to `xenodent-web` service settings
2. Add your custom domain
3. Update `APP_URL` environment variable

### 2. Database Seeding (If needed)
Access the Laravel service shell:
```bash
php artisan db:seed
```

### 3. File Storage (Production)
For file uploads, consider configuring AWS S3:
```bash
# Add to Laravel environment variables
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
```

## Troubleshooting

### Common Issues:

**Build Failures:**
- Check PHP/Python versions in build logs
- Verify all dependencies are in composer.json/requirements.txt

**Database Connection:**
- Environment variables are auto-configured
- Check database service status

**Service Communication:**
- Flask URL is auto-configured in Laravel
- Check both services are running

**Asset Compilation:**
- Ensure Node.js dependencies install correctly
- Check Vite build process in logs

### Debug Commands:
```bash
# Laravel service shell
php artisan config:clear
php artisan cache:clear
php artisan migrate:status
php artisan tinker
```

## Monitoring & Scaling

### Logs:
- View real-time logs in Render dashboard
- Laravel logs go to stderr (optimized for Render)
- Flask logs available in service dashboard

### Performance:
- Start with Starter plans ($7 each)
- Monitor resource usage
- Scale up plans as needed

### Health Checks:
- Render automatically monitors service health
- Configure custom health check endpoints if needed

## Cost Breakdown
- **Laravel Service**: $7/month (Starter)
- **Flask AI Service**: $7/month (Starter)
- **PostgreSQL Database**: $7/month (Starter)
- **Total**: $21/month

## Security Best Practices
1. ✅ API keys in environment variables
2. ✅ Database credentials auto-managed
3. ✅ SSL certificates (free with Render)
4. ✅ Production environment settings
5. 🔄 Update Roboflow API key for production

## Support Resources
- [Render Blueprint Documentation](https://render.com/docs/blueprint-spec)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Flask Production Deployment](https://flask.palletsprojects.com/en/2.0.x/deploying/)

## Next Steps After Deployment
1. Test X-ray analysis functionality
2. Configure any additional integrations
3. Set up monitoring and alerts
4. Plan for backup strategies
5. Consider CDN for static assets
