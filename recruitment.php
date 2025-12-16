<?php
// Include header (automatically starts session and connects to database)
require_once 'header.php';

// ========== Config ==========
$host = 'localhost';
$dbname = '47_110_70_30';
$username = '47_110_70_30';
$password = 'twPhCr21zd';

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 岗位选项（可改为从数据库读取）
$positions = [
    'Customer Service Representative',
    'Frontend Developer',
    'Backend Developer',
    'Marketing Specialist',
    'Graphic Designer',
    'Other'
];

$message = '';
$name = $email = $position = $messageText = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($position) || empty($messageText)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 注意：现在插入 position 字段
            $stmt = $pdo->prepare("INSERT INTO feedback (name, email, position, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $position, $messageText]);

            // Handle file uploads
            if (!empty($_FILES['files']['name'][0])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                $maxFileSize = 5 * 1024 * 1024;

                foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['files']['error'][$key] !== UPLOAD_ERR_OK) continue;

                    $fileType = $_FILES['files']['type'][$key];
                    $fileSize = $_FILES['files']['size'][$key];
                    $fileName = basename($_FILES['files']['name'][$key]);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $newFileName = uniqid('fb_', true) . '.' . $fileExt;
                    $targetPath = $uploadDir . $newFileName;

                    if (!in_array($fileType, $allowedTypes)) {
                        $message .= "File {$fileName} is not an allowed type.<br>";
                        continue;
                    }
                    if ($fileSize > $maxFileSize) {
                        $message .= "File {$fileName} exceeds the size limit (maximum 5MB).<br>";
                        continue;
                    }

                    if (!move_uploaded_file($tmpName, $targetPath)) {
                        $message .= "Failed to upload file {$fileName}.<br>";
                    }
                }
            }

            $message = "Application submitted successfully!";
            // Clear form
            $name = $email = $position = $messageText = '';

        } catch (PDOException $e) {
            $message = "Database error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
?>

<h2>Join Our Team – Apply Now</h2>

<?php if ($message): ?>
    <div class="message" style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-left: 4px solid #28a745;">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" style="max-width: 600px;">
    <div class="form-group" style="margin-bottom: 15px;">
        <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required
               style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email Address *</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required
               style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
    </div>

    <!-- 新增：岗位选择 -->
    <div class="form-group" style="margin-bottom: 15px;">
        <label for="position" style="display: block; margin-bottom: 5px; font-weight: bold;">Position Applying For *</label>
        <select id="position" name="position" required
                style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">-- Select a position --</option>
            <?php foreach ($positions as $pos): ?>
                <option value="<?= htmlspecialchars($pos, ENT_QUOTES, 'UTF-8') ?>"
                    <?= ($position === $pos) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pos, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label for="message" style="display: block; margin-bottom: 5px; font-weight: bold;">Cover Letter / Message *</label>
        <textarea id="message" name="message" rows="6" required
                  style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;"><?= htmlspecialchars($messageText, ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label for="files" style="display: block; margin-bottom: 5px; font-weight: bold;">Upload Resume & Portfolio (JPG, PNG, PDF)</label>
        <input type="file" id="files" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf">
        <small style="color: #666;">Multiple files allowed. Max 5MB per file.</small>
    </div>

    <button type="submit" class="btn" style="background-color: #28a745;">Submit Application</button>
</form>

<?php
include 'footer.php';
?>