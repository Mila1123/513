<?php
$page = 'about';
require_once 'header.php';
?>

<main class="container">
    <!-- Hero Banner -->
    <div style="background-color: #d35400; color: white; padding: 60px; border-radius: 12px; margin: 30px auto; max-width: 1200px; text-align: center;">
        <h1 style="margin: 0; font-size: 2.5rem;">About Burger Haven</h1>
        <p style="margin-top: 15px; font-size: 1.2rem; opacity: 0.9;">
            Innovation breaks through taste first, burger experts are here!
        </p>
    </div>

    <!-- Content Section -->
    <section style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; border-radius: 8px;">

        <h2 style="color: #2c3e50; margin-top: 0;">Our Story</h2>
        <p>
            We are a professional brand with over 30 years of history, mainly focusing on traditional hamburgers and fried chicken.
            The taste is mainly crispy and full, and the meat patties have a fragrant aroma after frying. 
            Paired with vegetables and sauce, they are even more delicious.
        </p>
        

        <h2 style="color: #2c3e50;">Our Mission</h2>
        <p><strong>🎯 Our Vision</strong></p>
        <p>To become the leading provider of personalized burgers that celebrate life's most precious moments.</p>

        <p><strong>❤️ Our Values</strong></p>
        <p>Quality craftsmanship, personalized service, and creating meals that hold emotional significance.</p>

        <p><strong>⚡ Our Promise</strong></p>
        <p>Each burger is made with the utmost care and attention to detail, ensuring it becomes a cherished memory.</p>

        <h2 style="color: #2c3e50;">Our Crafting Process</h2>
        <ol>
            <li><strong>Consultation</strong><br>
                We discuss your vision and the story you want to tell through your meal.</li>
            <li><strong>Design</strong><br>
                Our chefs create a custom menu for your approval.</li>
            <li><strong>Crafting</strong><br>
                Skilled cooks bring your order to life with precision and care.</li>
            <li><strong>Delivery</strong><br>
                Your finished meal is carefully packaged and delivered to you.</li>
        </ol>

        <h2 style="color: #2c3e50;">Meet Our Founder</h2>
        <p><strong>Mila</strong></p>
        <p>Founder & Lead Chef</p>
        <p>
            With over 5 years of experience in culinary art and a passion for storytelling through food, 
            Mila founded Burger Haven to help people preserve their most cherished memories in delicious, 
            wearable form.
        </p>
        <p>"Every bite we create carries a story. It's not just food – it's a memory you can carry with you always."</p>
    </section>

    <!-- 高德地图嵌入 -->
<div style="max-width: 1200px; margin: 30px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
    <h2 style="color: #2c3e50;">Location</h2>
    <div style="overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <iframe 
            src="https://uri.amap.com/marker?position=153.4057,-28.0123&name=Burger%27D%20Benowa" 
            width="100%" 
            height="400" 
            frameborder="0" 
            scrolling="no">
        </iframe>
    </div>
    <p style="text-align: center; margin-top: 10px; font-size: 0.95rem; color: #666;">
        Burger'D Benowa, Benowa QLD 4217, Australia
    </p>
</div>
</main>

<?php require_once 'footer.php'; ?>