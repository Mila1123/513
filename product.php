<?php
// product.php - 产品页面
require_once 'config.php';
require_once 'header.php';

// 检查用户是否登录
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$customer_id = $logged_in ? $_SESSION['customer_id'] : null;

// 处理添加到购物车
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!$logged_in) {
        echo "<script>alert('Please login first to add items to cart!'); window.location.href='login.php';</script>";
        exit();
    }
    
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;
    
    try {
        // 从JSON文件获取产品信息
        $json_data = file_get_contents('products.json');
        $products = json_decode($json_data, true);
        
        if (isset($products[$product_id])) {
            $product = $products[$product_id];
            
            // 检查是否已在购物车
            $check_stmt = $pdo_product->prepare("SELECT id, quantity FROM cart_items WHERE customer_id = ? AND product_id = ?");
            $check_stmt->execute([$customer_id, $product_id]);
            $existing_item = $check_stmt->fetch();
            
            if ($existing_item) {
                // 更新数量
                $update_stmt = $pdo_product->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE id = ?");
                $update_stmt->execute([$quantity, $existing_item['id']]);
            } else {
                // 新增到购物车
                $insert_stmt = $pdo_product->prepare("
                    INSERT INTO cart_items (customer_id, product_id, product_name, product_price, quantity) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                // 提取价格数字
                $price = floatval(preg_replace('/[^\d.]/', '', $product['price']));
                
                $insert_stmt->execute([
                    $customer_id, 
                    $product_id, 
                    $product['name'], 
                    $price, 
                    $quantity
                ]);
            }
            
            echo "<script>alert('Product added to cart successfully!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error adding to cart: " . addslashes($e->getMessage()) . "');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error processing product: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// 产品页面特定样式
?>
<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }
    
    .product-card {
        border: 1px solid #e0e0e0;
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        background: white;
    }
    
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    
    .product-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3));
    }
    
    .product-category-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.9);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .product-info {
        padding: 20px;
    }
    
    .product-name {
        font-size: 1.3rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
        line-height: 1.3;
    }
    
    .product-price {
        font-size: 1.5rem;
        font-weight: bold;
        color: #e74c3c;
        margin: 12px 0;
    }
    
    .product-stock {
        color: #27ae60;
        font-weight: 500;
        margin-bottom: 12px;
        font-size: 0.9rem;
    }
    
    .product-description {
        color: #7f8c8d;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 15px;
        height: 60px;
        overflow: hidden;
    }
    
    .add-to-cart {
        display: inline-block;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s;
        text-align: center;
        width: 100%;
        border: none;
        cursor: pointer;
    }
    
    .add-to-cart:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }
    
    .category-filter {
        text-align: center;
        margin: 30px 0;
    }
    
    .category-btn {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        padding: 10px 20px;
        margin: 0 8px;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        color: #6c757d;
    }
    
    .category-btn:hover, .category-btn.active {
        background: #3498db;
        color: white;
        border-color: #3498db;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
    }
    
    .page-title {
        text-align: center;
        font-size: 2.8rem;
        color: #2c3e50;
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .page-subtitle {
        text-align: center;
        color: #7f8c8d;
        font-size: 1.1rem;
        margin-bottom: 40px;
    }
    
    .no-products {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
        font-size: 1.1rem;
    }
    
    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .fallback-icon {
        font-size: 4rem;
        color: rgba(255,255,255,0.8);
        z-index: 1;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .quantity-selector label {
        margin-right: 10px;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .quantity-selector select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>

<h1 class="page-title">Our Delicious Products</h1>
<p class="page-subtitle">Discover our mouth-watering selection of burgers, sides, and drinks</p>

<div class="category-filter">
    <button class="category-btn active" onclick="filterProducts('all')">All Products</button>
    <button class="category-btn" onclick="filterProducts('Burger')">🍔 Burgers</button>
    <button class="category-btn" onclick="filterProducts('Side Dish')">🍟 Side Dishes</button>
    <button class="category-btn" onclick="filterProducts('Dessert')">🍦 Desserts</button>
    <button class="category-btn" onclick="filterProducts('Drink')">🥤 Drinks</button>
</div>

<?php
try {
    // 从JSON文件获取产品数据
    $json_data = file_get_contents('products.json');
    $products = json_decode($json_data, true);
    
    if (!$products) {
        echo '<div class="no-products">';
        echo '<p>No products available at the moment. Please check back later.</p>';
        echo '</div>';
    } else {
        echo '<div class="product-grid" id="productGrid">';
        
        $index = 0;
        foreach ($products as $product) {
            // 设置默认图标作为备用
            $fallback_icon = '🍔';
            if ($product['category'] == 'Side Dish') $fallback_icon = '🍟';
            if ($product['category'] == 'Dessert') $fallback_icon = '🍦';
            if ($product['category'] == 'Drink') $fallback_icon = '🥤';
            
            echo '<div class="product-card" data-category="' . htmlspecialchars($product['category']) . '">';
            
            // 产品图片
            echo '<div class="product-image" style="background-image: url(\'' . htmlspecialchars($product['image']) . '\')">';
            echo '<span class="fallback-icon">' . $fallback_icon . '</span>';
            echo '<span class="product-category-badge">' . htmlspecialchars($product['category']) . '</span>';
            echo '</div>';
            
            echo '<div class="product-info">';
            echo '<h3 class="product-name">' . htmlspecialchars($product['name']) . '</h3>';
            
            echo '<div class="product-meta">';
            echo '<div class="product-price">' . htmlspecialchars($product['price']) . '</div>';
            echo '<div class="product-stock">' . htmlspecialchars($product['stock']) . '</div>';
            echo '</div>';
            
            echo '<p class="product-description">' . htmlspecialchars($product['description']) . '</p>';
            
            // 添加到购物车表单
            echo '<form method="POST" action="">';
            echo '<input type="hidden" name="product_id" value="' . $index . '">';
            echo '<div class="quantity-selector">';
            echo '<label for="quantity_' . $index . '">Qty:</label>';
            echo '<select name="quantity" id="quantity_' . $index . '">';
            for ($i = 1; $i <= 10; $i++) {
                echo '<option value="' . $i . '">' . $i . '</option>';
            }
            echo '</select>';
            echo '</div>';
            echo '<button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>';
            echo '</form>';
            
            echo '</div>';
            echo '</div>';
            
            $index++;
        }
        
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<div class="no-products">';
    echo '<p>Error loading products: ' . $e->getMessage() . '</p>';
    echo '</div>';
}
?>

<script>
function filterProducts(category) {
    const products = document.querySelectorAll('.product-card');
    const buttons = document.querySelectorAll('.category-btn');
    
    // 更新按钮状态
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.includes(category) || (category === 'all' && btn.textContent.includes('All'))) {
            btn.classList.add('active');
        }
    });
    
    // 过滤产品
    products.forEach(product => {
        if (category === 'all' || product.getAttribute('data-category') === category) {
            product.style.display = 'block';
            setTimeout(() => {
                product.style.opacity = '1';
            }, 50);
        } else {
            product.style.display = 'none';
        }
    });
}

// 图片加载失败时显示备用图标
document.addEventListener('DOMContentLoaded', function() {
    const productImages = document.querySelectorAll('.product-image');
    productImages.forEach(image => {
        const img = new Image();
        const imageUrl = image.style.backgroundImage.replace(/url\(['"]?(.*?)['"]?\)/i, '$1');
        img.src = imageUrl;
        img.onerror = function() {
            image.style.backgroundImage = 'none';
            image.querySelector('.fallback-icon').style.display = 'block';
        };
    });
});
</script>

<?php require_once 'footer.php'; ?>