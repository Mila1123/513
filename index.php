<?php
// index.php - 首页
require_once 'config.php';
require_once 'header.php';
?>

<style>
    .hero-section {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2072&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        text-align: center;
        padding: 100px 20px;
        margin: -20px -20px 40px -20px;
        border-radius: 0;
    }
    
    .hero-section h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        font-weight: bold;
    }
    
    .hero-section p {
        font-size: 1.2rem;
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .cta-button {
        background-color: #e74c3c;
        color: white;
        padding: 15px 30px;
        font-size: 1.1rem;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
    }
    
    .cta-button:hover {
        background-color: #c0392b;
    }
    
    .section-title {
        text-align: center;
        font-size: 2.5rem;
        margin: 60px 0 30px 0;
        color: #2c3e50;
    }
    
    .section-subtitle {
        text-align: center;
        font-size: 1.1rem;
        color: #7f8c8d;
        margin-bottom: 50px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
        margin: 50px 0;
    }
    
    .feature-item {
        text-align: center;
        padding: 30px;
    }
    
    .feature-number {
        font-size: 3rem;
        font-weight: bold;
        color: #e74c3c;
        margin-bottom: 20px;
    }
    
    .feature-title {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .feature-description {
        color: #7f8c8d;
        line-height: 1.6;
    }
    
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin: 50px 0;
    }
    
    .testimonial-card {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .testimonial-text {
        font-style: italic;
        margin-bottom: 20px;
        color: #555;
    }
    
    .testimonial-author {
        font-weight: bold;
        color: #2c3e50;
    }
    
    .benefits-section {
        background: #f8f9fa;
        padding: 60px 20px;
        margin: 60px -20px;
        text-align: center;
    }
    
    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin: 50px 0;
    }
    
    .benefit-item h3 {
        color: #2c3e50;
        margin-bottom: 15px;
    }
    
    .benefit-item p {
        color: #7f8c8d;
    }
    
    .newsletter-section {
        background: #2c3e50;
        color: white;
        padding: 60px 20px;
        margin: 60px -20px;
        text-align: center;
    }
    
    .newsletter-section h2 {
        margin-bottom: 20px;
    }
    
    .newsletter-section p {
        margin-bottom: 30px;
        font-size: 1.1rem;
    }
    
    .divider {
        height: 2px;
        background: #ddd;
        margin: 40px 0;
    }
</style>

<!-- Hero Section -->
<div class="hero-section">
    <h1>WELCOME TO<br>Burger Haven</h1>
    <p>Discover premium burgers, fresh ingredients, and unforgettable flavors. Made with love, served with passion.</p>
    <a href="product.php" class="cta-button">ORDER NOW</a>
</div>

<!-- Featured Burgers Section -->
<h2 class="section-title">OUR DELICIOUS</h2>
<h2 class="section-title">Featured Burgers</h2>
<p class="section-subtitle">Savor a variety of gourmet burgers, crafted to perfection and bursting with flavor.</p>

<div class="features-grid">
    <div class="feature-item">
        <div class="feature-number">01</div>
        <h3 class="feature-title">Premium Classic Burgers</h3>
        <p class="feature-description">Savor the taste of our signature classic burgers made with high-quality ingredients and cooked to perfection.</p>
    </div>
    
    <div class="feature-item">
        <div class="feature-number">02</div>
        <h3 class="feature-title">Gourmet Specialty Burgers</h3>
        <p class="feature-description">Delight in our unique gourmet burgers, loaded with exciting flavors and lavish ingredients that push culinary boundaries.</p>
    </div>
    
    <div class="feature-item">
        <div class="feature-number">03</div>
        <h3 class="feature-title">Veggie and Vegan Options</h3>
        <p class="feature-description">Enjoy our flavorful veggie and vegan burgers, crafted to cater to plant-based diets without compromising taste.</p>
    </div>
</div>

<div style="text-align: center; margin: 40px 0;">
    <a href="product.php" class="btn">LEARN MORE</a>
</div>

<!-- Testimonials Section -->
<h2 class="section-title">What Our Customers Say</h2>

<div class="testimonials-grid">
    <div class="testimonial-card">
        <p class="testimonial-text">"The best burgers in town! Juicy, flavorful, and always fresh. The combo deals are amazing value. I'm a regular customer now!"</p>
        <p class="testimonial-author">MARK JOHNSON</p>
    </div>
    
    <div class="testimonial-card">
        <p class="testimonial-text">"Perfect for family dinners! My kids love the kids' meals and the quality is consistently excellent. Great service every time."</p>
        <p class="testimonial-author">JAMES CARTER</p>
    </div>
    
    <div class="testimonial-card">
        <p class="testimonial-text">"Fast delivery and the burgers arrived hot and delicious. The bacon cheeseburger is my absolute favorite. Highly recommended!"</p>
        <p class="testimonial-author">EMILY SMITH</p>
    </div>
    
    <div class="testimonial-card">
        <p class="testimonial-text">"Every time I order from Burger Haven, I'm impressed! The flavors are spectacular, and the service is always friendly."</p>
        <p class="testimonial-author">SAMANTHA LEE</p>
    </div>
</div>

<!-- Benefits Section -->
<div class="benefits-section">
    <h2 class="section-title">Discover the Burger Haven Advantage</h2>
    <p class="section-subtitle">Learn what sets us apart in the burger world and why you should indulge with us.</p>
    
    <div class="divider"></div>
    
    <h2 style="color: #2c3e50; margin-bottom: 30px;">WHY CHOOSE US</h2>
    
    <div class="benefits-grid">
        <div class="benefit-item">
            <h3>Locally Sourced Ingredients</h3>
            <p>We pride ourselves on using fresh, locally sourced ingredients in all our burgers to ensure flavor and quality.</p>
        </div>
        
        <div class="benefit-item">
            <h3>Rapid Delivery Service</h3>
            <p>Enjoy your favorite burgers delivered fast, hot, and ready to eat. Your satisfaction is our priority.</p>
        </div>
        
        <div class="benefit-item">
            <h3>Exceptional Customer Experience</h3>
            <p>We focus on providing a top-notch dining experience that keeps our customers coming back for more.</p>
        </div>
    </div>
</div>

<!-- Newsletter Section -->
<div class="newsletter-section">
    <h2>JOIN US TODAY</h2>
    <p>Don't Miss Out on Deliciousness!<br>Subscribe now for exclusive offers, updates, and delicious burger news delivered straight to your inbox.</p>
    <a href="contact.php" class="cta-button">ORDER NOW</a>
</div>

<?php require_once 'footer.php'; ?>