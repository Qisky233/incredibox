<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<div class="container privacy-page">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-title">Privacy Policy</h1>
            <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
        </div>
    </div>

    <div class="row privacy-section">
        <div class="col-md-12">
            <h2><i class="fa fa-shield-alt"></i> Information We Collect</h2>
            <p>When you use CloudArcade, we may collect certain information automatically, including:</p>
            <ul>
                <li>Device information (browser type, IP address)</li>
                <li>Gameplay data (scores, preferences)</li>
                <li>Cookies and similar tracking technologies</li>
            </ul>
        </div>
    </div>

    <div class="row privacy-section">
        <div class="col-md-12">
            <h2><i class="fa fa-cookie-bite"></i> Use of Cookies</h2>
            <p>We use cookies to:</p>
            <ul>
                <li>Remember your preferences</li>
                <li>Analyze site traffic</li>
                <li>Improve user experience</li>
            </ul>
            <p>You can disable cookies in your browser settings, but some features may not work properly.</p>
        </div>
    </div>

    <div class="row privacy-section">
        <div class="col-md-12">
            <h2><i class="fa fa-lock"></i> Data Security</h2>
            <p>We implement appropriate security measures to protect your personal information. However, no internet transmission is 100% secure.</p>
        </div>
    </div>

    <div class="row privacy-section">
        <div class="col-md-12">
            <h2><i class="fa fa-exchange-alt"></i> Third-Party Services</h2>
            <p>Some games on our site may use third-party services that have their own privacy policies. We recommend reviewing these policies before playing.</p>
        </div>
    </div>
</div>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>

<style>
.privacy-page {
    padding: 30px 0;
    color: #333;
    max-width: 800px;
    margin: 0 auto;
}

.page-title {
    text-align: center;
    margin-bottom: 10px;
    color: #2c3e50;
    font-weight: 700;
}

.last-updated {
    text-align: center;
    color: #7f8c8d;
    margin-bottom: 40px;
}

.privacy-section {
    margin-bottom: 40px;
}

.privacy-section h2 {
    color: #3498db;
    margin-bottom: 15px;
    font-size: 22px;
}

.privacy-section h2 i {
    margin-right: 10px;
}

.privacy-section p {
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 15px;
}

.privacy-section ul {
    margin-bottom: 15px;
    padding-left: 20px;
}

.privacy-section li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.contact-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin-top: 15px;
}

@media (max-width: 768px) {
    .privacy-page {
        padding: 20px 15px;
    }
    
    .privacy-section h2 {
        font-size: 20px;
    }
}
</style>