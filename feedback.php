<?php
// Include header (automatically starts session and connects to database)
require_once 'header.php';

// ========== Database config ==========
$host = 'localhost';
$dbname = '47_110_70_30';
$username = '47_110_70_30';
$password = 'twPhCr21zd';

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$message = '';
$name = $email = $messageText = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($messageText)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 注意：表名含连字符，必须用反引号 `` 包裹！
            $stmt = $pdo->prepare("INSERT INTO `feedback-form` (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $messageText]);

            // Handle file uploads (optional: you may want to link files to this record later)
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

            $message = "Feedback submitted successfully!";
            $name = $email = $messageText = '';

        } catch (PDOException $e) {
            error_log("Feedback DB Error: " . $e->getMessage());
            $message = "Sorry, there was an error saving your feedback. Please try again later.";
        }
    }
}
?>

<!-- Page-specific content -->
<h2>Customer Support Feedback</h2>

<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>" 
         style="margin: 15px 0; padding: 12px; border-radius: 4px; background: #f8f9fa; color: #333; border-left: 4px solid <?= strpos($message, 'successfully') !== false ? '#28a745' : '#dc3545' ?>;">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" style="max-width: 600px; margin-top: 20px;">
    <div class="form-group" style="margin-bottom: 15px;">
        <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold;">Name *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required
               style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email *</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required
               style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;">
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label for="message" style="display: block; margin-bottom: 5px; font-weight: bold;">Message *</label>
        <textarea id="message" name="message" rows="5" required
                  style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px;"><?= htmlspecialchars($messageText, ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label for="files" style="display: block; margin-bottom: 5px; font-weight: bold;">Upload Attachments (JPG, PNG, GIF, PDF)</label>
        <input type="file" id="files" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf">
        <small style="color: #666;">Multiple files allowed. Max 5MB per file.</small>
    </div>

    <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
        Submit Feedback
    </button>
</form>

<?php
include 'footer.php';
?>