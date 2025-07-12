# GrapeVault 🍷

**India's First Wine Selling Platform**

*Preserving the Essence of Fine Wines, So You Can Relish Every Drop, Every Time.*

## Overview

GrapeVault is a comprehensive wine e-commerce platform built with PHP, featuring user authentication, admin management, AWS integration, and health-focused wine recommendations. The platform promotes ethical wine consumption while providing detailed information about wine's health benefits.

## Features

### 🛒 E-commerce Core
- Product catalog with wine categories (Red, White, Sparkling, Rosé)
- Shopping cart and wishlist functionality
- Order management and checkout system
- User profile and address management

### 👤 User Management
- User registration and authentication
- KYC (Know Your Customer) verification
- Face verification system
- Admin panel with role-based access

### 🏥 Health Benefits Section
- Educational content on wine's health benefits
- Categories for different health conditions
- Responsible drinking awareness

### ☁️ AWS Integration
- **AWS Secrets Manager** for secure credential storage
- **AWS S3** for file storage
- **AWS Lex** chatbot integration
- **AWS Cognito** for authentication

### 🤖 AI Features
- Interactive chatbot for customer support
- Smart product recommendations

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL with PDO
- **Frontend**: HTML5, CSS3, JavaScript
- **Cloud**: AWS (S3, Secrets Manager, Lex, Cognito)

- **Dependencies**: Composer packages

## Installation

### Prerequisites
- XAMPP/WAMP server
- PHP 7.4 or higher
- MySQL 5.7+
- Composer
- AWS Account

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd grape/main
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   - Create MySQL database named `shop_db`
   - Import the database schema (if available)

4. **AWS Configuration**
   - Set up AWS Secrets Manager with database credentials
   - Configure S3 bucket named `grapevault`
   - Set up Lex chatbot
   - Configure Cognito identity pool

5. **Environment Configuration**
   - Update `config.php` with your AWS credentials
   - Configure database connection parameters

6. **File Permissions**
   ```bash
   chmod 755 uploaded_files/
   chmod 755 uploaded_img/
   ```

## Configuration

### AWS Secrets Manager
Store database credentials in AWS Secrets Manager with the following structure:
```json
{
  "db_host": "your-host",
  "db_name": "shop_db",
  "db_user": "your-username",
  "db_pass": "your-password"
}
```

### Required Environment Variables
- `AWS_KEY`: AWS Access Key ID
- `AWS_SECRET`: AWS Secret Access Key
- `AWS_REGION`: us-east-1 (default)

## Project Structure

```
grape/main/
├── css/                    # Stylesheets
├── js/                     # JavaScript files
├── images/                 # Static images
├── uploaded_files/         # User uploaded documents
├── uploaded_img/          # User uploaded images
├── vendor/                # Composer dependencies
├── config.php             # Database and AWS configuration
├── home.php               # Main homepage
├── admin_page.php         # Admin dashboard
├── login.php              # User authentication
├── shop.php               # Product catalog
└── ...                    # Other PHP files
```

## Key Features Explained

### Age Verification
- Mandatory age verification popup
- Session-based verification with timeout
- Responsible drinking messaging

### Admin Panel
- Order management and approval
- Product inventory management
- User and admin account management
- KYC request processing
- Analytics dashboard

### Security Features
- PDO prepared statements for SQL injection prevention
- Session management
- File upload validation
- AWS Secrets Manager for credential security

## Usage

### For Users
1. Register/Login to the platform
2. Complete KYC verification
3. Browse wine categories
4. Add products to cart/wishlist
5. Complete checkout process
6. Track orders

### For Admins
1. Access admin panel at `/admin_page.php`
2. Manage products and orders
3. Process KYC requests
4. Monitor platform analytics

## API Integrations

### AWS Lex Chatbot
- Bot ID: `QEIHQHA1FP`
- Alias: `TSTALIASID`
- Locale: `en_US`
- Region: `us-east-1`



## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Security Considerations

- Never commit AWS credentials to version control
- Use environment variables for sensitive data
- Regularly update dependencies
- Implement proper input validation
- Use HTTPS in production

## License

This project is proprietary software. All rights reserved.

## Support

For technical support or questions, please contact the development team.

---

**Disclaimer**: This platform promotes responsible wine consumption. Please drink responsibly and only if you are of legal drinking age in your jurisdiction.