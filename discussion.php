<?php
// discussion.php - 讨论区页面

// 启动会话（如果 config.php 中未启动）
session_start();

// 引入配置文件（仅包含数据库连接等，不应有输出）
require_once 'config.php';

// 检查用户是否登录 —— 必须在任何输出前完成！
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'];

// 处理新帖子提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_title'])) {
    $title = trim($_POST['post_title']);
    $content = trim($_POST['post_content']);

    if (!empty($title) && !empty($content)) {
        try {
            $stmt = $pdo_product->prepare("INSERT INTO discussion_posts (customer_id, customer_name, title, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$customer_id, $customer_name, $title, $content]);
            $success_message = "Your post has been submitted successfully!";
        } catch (PDOException $e) {
            $error_message = "Error submitting post: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in both the title and content.";
    }
}

// 获取所有帖子
try {
    $posts_stmt = $pdo_product->prepare("SELECT * FROM discussion_posts ORDER BY created_at DESC");
    $posts_stmt->execute();
    $posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error loading posts: " . $e->getMessage();
    $posts = [];
}

// 如果没有帖子，插入20条样本数据
if (empty($posts)) {
    $sample_titles = [
        "Best burger I've ever had!",
        "Delivery was super fast",
        "Suggestion: Add more vegan options",
        "The spicy chicken burger is 🔥",
        "Question about loyalty program?",
        "Love the new packaging!",
        "Golden Fries are a must-try",
        "Website is easy to use",
        "Complaint: My order was missing an item",
        "Praise for your friendly staff",
        "Can you add a breakfast menu?",
        "The Vanilla Milkshake is perfect",
        "How fresh are the ingredients?",
        "Great value for money",
        "My kids love the kids' meals",
        "Suggestion: Offer combo deals online",
        "Lead Cola hits the spot!",
        "First time ordering, very impressed",
        "Request: Gluten-free buns?",
        "Consistently delicious every time!"
    ];

    $sample_contents = [
        "Seriously, the Classic Beef Burger is on another level. The special sauce makes it!",
        "Ordered at noon, got it by 12:45. Amazing speed!",
        "I'm trying to eat more plant-based. Would love to see 2-3 more veggie burger choices.",
        "Not too spicy, just right! And the chicken is so juicy.",
        "Do you have a points system or rewards for frequent orders?",
        "It keeps everything hot and doesn't leak. Big plus!",
        "They are crispy on the outside and fluffy inside. Perfect with any burger.",
        "Adding items to cart and checking out was a breeze. Great UX!",
        "My Cheese Bacon Burger didn't come with bacon. Please look into this.",
        "The delivery person was so polite and even wished me a good day!",
        "Would be awesome to grab a breakfast burger in the morning!",
        "So creamy and not too sweet. Hits the spot after a meal.",
        "Just curious about your sourcing. Do you use local produce?",
        "For the quality of food, the prices are very reasonable.",
        "The mini burgers and fries are their favorite. Makes dinner easy!",
        "It would be great to bundle a burger, fries, and drink for a lower price on the website.",
        "It's the perfect classic cola taste. Refreshing!",
        "Will definitely be a regular customer from now on. Thanks!",
        "My partner has celiac disease. Gluten-free options would be a game-changer.",
        "Whether I order the Classic or the Veggie, it's always top-notch."
    ];

    // 从 FluentCRM 表中获取一个示例用户ID和姓名用于填充
    $user_stmt = $pdo_customer->query("SELECT id, CONCAT(first_name, ' ', last_name) as full_name FROM wpot_fc_subscribers LIMIT 1");
    $sample_user = $user_stmt->fetch();
    if ($sample_user) {
        $sample_id = $sample_user['id'];
        $sample_name = $sample_user['full_name'];
    } else {
        $sample_id = 1;
        $sample_name = "Sample User";
    }

    try {
        $pdo_product->beginTransaction();
        $insert_stmt = $pdo_product->prepare("INSERT INTO discussion_posts (customer_id, customer_name, title, content, created_at) VALUES (?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))");
        
        for ($i = 0; $i < 20; $i++) {
            $days_ago = $i;
            $insert_stmt->execute([$sample_id, $sample_name, $sample_titles[$i], $sample_contents[$i], $days_ago]);
        }
        $pdo_product->commit();
        
        // 重新获取帖子
        $posts_stmt->execute();
        $posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $pdo_product->rollBack();
        $error_message = "Error creating sample data: " . $e->getMessage();
    }
}
?>

<?php require_once 'header.php'; ?>

<style>
.discussion-container { max-width: 900px; margin: 0 auto; }
.page-header { text-align: center; margin-bottom: 30px; }
.post-form { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 40px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50; }
.form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
.form-group textarea { height: 120px; resize: vertical; }
.btn-submit-post { background: #3498db; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
.btn-submit-post:hover { background: #2980b9; }
.posts-list { margin-top: 20px; }
.post-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 25px; }
.post-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
.post-title { font-size: 1.4rem; font-weight: bold; color: #2c3e50; margin: 0; }
.post-meta { color: #7f8c8d; font-size: 0.9rem; }
.post-content { line-height: 1.6; color: #333; }
.alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="discussion-container">
    <div class="page-header">
        <h1>Discussion Forum</h1>
        <p>Share your thoughts, feedback, and opinions with our community!</p>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <!-- Post Creation Form -->
    <div class="post-form">
        <h2>Create a New Post</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="post_title">Title</label>
                <input type="text" id="post_title" name="post_title" required>
            </div>
            <div class="form-group">
                <label for="post_content">Your Message</label>
                <textarea id="post_content" name="post_content" required></textarea>
            </div>
            <button type="submit" class="btn-submit-post">Submit Post</button>
        </form>
    </div>

    <!-- Display All Posts -->
    <div class="posts-list">
        <h2>Recent Discussions</h2>
        <?php if (empty($posts)): ?>
            <p>No posts yet. Be the first to start a discussion!</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="post-header">
                        <h3 class="post-title"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="post-meta">
                            By <?php echo htmlspecialchars($post['customer_name'], ENT_QUOTES, 'UTF-8'); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                        </div>
                    </div>
                    <div class="post-content">
                        <?php echo nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>