<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<div class="container about-page">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-title">About CloudArcade</h1>
        </div>
    </div>

    <div class="row about-section">
        <div class="col-md-6">
            <h2><i class="fa fa-gamepad"></i> Our Mission</h2>
            <p>CloudArcade is committed to providing players with the best quality and most fun online gaming experience. We carefully select a variety of games to ensure that you find the best entertainment for you.</p>
            <p>Whether you are a casual gamer or a hardcore gamer, you will find fun here. Our game library is constantly updated to bring you the latest and hottest gaming experiences.</p>
        </div>
    </div>

    <div class="row about-section">
        <div class="col-md-6 order-md-2">
            <h2><i class="fa fa-users"></i> Our team</h2>
            <p>CloudArcade was founded by a group of developers who love games, understand what players need, and strive to build the best online gaming platform.</p>
            <p>Our team is constantly optimizing the performance of the website to ensure that you can enjoy the game smoothly, without downloading, just click and play.</p>
        </div>
    </div>

    <div class="row about-section">
        <div class="col-md-12">
            <h2><i class="fa fa-envelope"></i> Contact us</h2>
            <div class="contact-info">
                <p><strong>Mail:</strong> <a href="mailto:contact@cloudarcade.aluo18.top">contact@cloudarcade.aluo18.top</a></p>
                <p><strong>Feedback and Suggestions:</strong> We value every user's opinion and welcome your valuable suggestions.</p>
                <p><strong>Game Submission:</strong> To submit your game, please contact us via email.</p>
            </div>
        </div>
    </div>
</div>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>

<style>
.about-page {
    color: #333;
}

.page-title {
    text-align: center;
    color: #2c3e50;
    font-weight: 700;
}

.about-section {
    margin-bottom: 50px;
    align-items: center;
}

.about-section h2 {
    color: #3498db;
}

.about-section h2 i {
    margin-right: 10px;
}

.about-section p {
    font-size: 16px;
    line-height: 1.8;
}

.contact-info {
    background: #f8f9fa;
    border-radius: 5px;
}

.contact-info p {
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .about-section {
        text-align: left;
    }
    
    .contact-info{
        padding: 16px;
    }
}
</style>