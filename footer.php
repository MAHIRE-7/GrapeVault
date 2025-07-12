<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrapeVault | Wine Selling Platform</title>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400|Consolas&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <footer>
        <div id="containerFooter">
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Extra Links</h3>
                <ul>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                    <li><a href="download.php">Download</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Info</h3>
                <p><i class="fas fa-phone"></i> +91-85300 58107</p>
                <p><i class="fas fa-phone"></i> +91-85300 61182</p>
                <p><i class="fas fa-envelope"></i> hr@grapev.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Pune, India - 410 507</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#" class="icon fb"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="icon tw"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="icon ig"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="icon li"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div id="credit">
            &copy; 2025 GrapeVault | Developed by <span>Dynamic Developers: DYP-G5</span> | All rights reserved.
        </div>
    </footer>
</body>
</html>

<style>
body {
    margin: 0;
    font-family: 'Consolas', monospace;
    background-color: #f8f8f8;
}

footer {
    background: linear-gradient(135deg,rgb(163, 69, 75), #16213e, #0f3460);
    color: white;
    padding: 50px 0;
    box-shadow: 0px -5px 15px rgba(0, 0, 0, 0.3);
    position: relative;
    font-family: 'Consolas', monospace;
}

#containerFooter {
    width: 90%;
    max-width: 1200px;
    margin: auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    text-align: left;
}

.footer-section {
    flex: 1;
    color:rgb(249, 248, 247);
    font-size: 16px;
    min-width: 200px;
    margin-bottom: 20px;
}
footer {
    background: linear-gradient(135deg,rgb(50, 18, 42), rgb(50, 18, 42),rgb(50, 18, 42));
}
.footer-section h3, #credit {
    color: #ffcc00;
}
.footer-section a:hover {
    color: #ff5733;
}


.footer-section h3 {
    font-size: 20px;
    margin-bottom: 15px;
    text-transform: uppercase;
    color:rgb(249, 248, 247);
    font-weight: 600;
    position: relative;
}

.footer-section h3::after {
    content: '';
    width: 60px;
    height: 4px;
    background: #f8c471;
    display: block;
    margin-top: 6px;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section li {
    margin-bottom: 12px;
}
.footer-section p {
    margin-bottom: 10px;
    color:rgb(249, 248, 247);
    
}
.footer-section a {
    margin-bottom: 10px;
    color:rgb(249, 248, 247);
    
}

.footer-section a {
    color:rgb(249, 248, 247);
    text-decoration: none;
    transition: color 0.3s ease;
    font-weight: 300;
}

.footer-section a:hover {
    color: #f8c471;
    text-decoration: underline;
}

.social-icons {
    display: flex;
    gap: 15px;
    color:rgb(249, 248, 247);
    margin-top: 15px;
}

.icon {
    color:rgb(249, 248, 247);
    font-size: 22px;
    transition: transform 0.3s ease, color 0.3s ease;
}

.icon:hover {
    transform: scale(1.3);
}

.fb:hover { color: #3b5998; }
.tw:hover { color: #1DA1F2; }
.ig:hover { color: #E1306C; }
.li:hover { color: #0077B5; }

#credit {
    width: 100%;
    text-align: center;
    background-color: rgba(226, 44, 44, 0.1);
    padding: 15px 0;
    font-size: 20px;
    font-weight: 400;
    color: #f8c471;
}

#credit span {
    color: #f39c12;
    font-weight: bold;
}
</style>
