<?php
// list.php - 联系页面（原 contact.php）
require_once 'config.php';
require_once 'header.php';

// 确保 $pdo_customer 已在 config.php 中定义
if (!isset($pdo_customer)) {
    die('Database connection not configured in config.php');
}
?>

<style>
    .action-buttons {
        margin-bottom: 10px;
        white-space: nowrap;
    }
    
    .action-buttons a {
        margin-right: 5px;
        text-decoration: none;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 0.8rem;
        display: inline-block;
        color: white;
    }
    
    .btn-edit { background-color: #3498db; }
    .btn-copy { background-color: #27ae60; }
    .btn-delete { background-color: #e74c3c; }
    
    .table-info {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>

<h1>Contact Us</h1>
<p>Have questions or feedback? Reach out to us using the form below.</p>

<?php
try {
    $tableName = 'wp_fc_subscribers';
    
    // 检查表是否存在 id 字段
    $stmt = $pdo_customer->query("DESCRIBE $tableName");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('id', $columns)) {
        echo '<p style="color: red;">Error: Table must have an "id" column as primary key.</p>';
        require_once 'footer.php';
        exit;
    }

    echo '<div class="table-info">';
    echo '<p>Using table: <strong>' . htmlspecialchars($tableName) . '</strong></p>';
    echo '<p>Available columns: ' . implode(', ', array_map('htmlspecialchars', $columns)) . '</p>';
    
    $countStmt = $pdo_customer->query("SELECT COUNT(*) as total FROM $tableName");
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo '<p>Total records: <strong>' . $totalRecords . '</strong></p>';
    echo '</div>';

    // 定义要显示的列（排除 contact_owner）
    $possibleColumns = ['first_name', 'last_name', 'email', 'facebook', 'phone', 'user_email', 'display_name'];
    $displayColumns = array_filter($possibleColumns, fn($col) => in_array($col, $columns));

    if (empty($displayColumns)) {
        $displayColumns = array_slice($columns, 0, 4);
        // 确保不包含 id 在显示列中（除非用户明确需要）
        $displayColumns = array_filter($displayColumns, fn($col) => $col !== 'id');
    }

    $selectCols = implode(', ', array_merge(['id'], $displayColumns));
    $stmt = $pdo_customer->query("SELECT $selectCols FROM $tableName");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($contacts) {
        echo '<table>';
        echo '<thead><tr>';
        // ❌ 删除了 <th>Actions</th>
        foreach ($displayColumns as $col) {
            echo '<th>' . htmlspecialchars($col) . '</th>';
        }
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($contacts as $contact) {
            $rowId = (int)($contact['id'] ?? 0);
            echo '<tr data-id="' . $rowId . '">';

            // ❌ 完全删除操作列 <td class="action-buttons">...</td>

            foreach ($displayColumns as $col) {
                $value = $contact[$col] ?? '';
                if ($value === null || $value === '') {
                    $value = '<span style="color:#999;">NULL</span>';
                }
                echo '<td>' . htmlspecialchars($value) . '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '<div class="subscriber-count">Total contacts displayed: ' . count($contacts) . '</div>';
    } else {
        echo '<p>No contacts found.</p>';
    }

} catch (PDOException $e) {
    echo '<p style="color: red;">Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Delete
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            if (!id || !confirm('Are you sure you want to delete this contact?')) return;

            try {
                const res = await fetch('delete_contact.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + encodeURIComponent(id)
                });
                const result = await res.json();
                if (result.success) {
                    alert('Deleted successfully!');
                    this.closest('tr').remove();
                } else {
                    alert('Error: ' + (result.message || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Network error. Please try again.');
            }
        });
    });

    // Copy row
    document.querySelectorAll('.btn-copy').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const row = this.closest('tr');
            const cells = row.querySelectorAll('td:not(.action-buttons)');
            const text = Array.from(cells).map(c => c.textContent.trim()).join('\t');
            navigator.clipboard.writeText(text)
                .then(() => alert('Row copied!'))
                .catch(() => alert('Copy failed.'));
        });
    });

    // Edit: redirect
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            window.location.href = 'edit_contact.php?id=' + id;
        });
    });
});
</script>

<?php require_once 'footer.php'; ?>