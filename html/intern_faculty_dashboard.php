<?php

require_once "../php/database.php";


// -------------------------
// Session & auth placeholder
// -------------------------
session_start();

$user = $_SESSION['user'];

// -------------------------
// Helper functions
// -------------------------
function get_count(PDO $pdo, string $table, string $where = '1=1', array $params = []) {
    $sql = "SELECT COUNT(*) AS cnt FROM {$table} WHERE {$where}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function get_recent_activities(PDO $pdo, int $limit = 8) {
    // Example table 'activities' with columns: id, user_name, action, created_at
    $sql = "SELECT user_name, action, created_at FROM activities ORDER BY created_at DESC LIMIT :lim";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_pending_interns(PDO $pdo, int $limit = 10) {
    // Example table 'interns' with 'status' column (pending, approved, rejected)
    $sql = "SELECT id, full_name, email, applied_at FROM interns WHERE status = 'pending' ORDER BY applied_at DESC LIMIT :lim";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// -------------------------
// Handle simple POST actions (approve intern, add announcement)
// -------------------------
$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'approve_intern' && $user['role'] !== 'intern') {
        $intern_id = (int)($_POST['intern_id'] ?? 0);
        $sql = "UPDATE interns SET status = 'approved', approved_at = NOW(), approved_by = :by WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':by' => $user['id'], ':id' => $intern_id]);
        $flash = 'Intern approved successfully.';
    }

    if (isset($_POST['action']) && $_POST['action'] === 'add_announcement' && $user['role'] !== 'intern') {
        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        if ($title !== '' && $body !== '') {
            $sql = "INSERT INTO announcements (title, body, created_by, created_at) VALUES (:t, :b, :c, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':t' => $title, ':b' => $body, ':c' => $user['id']]);
            $flash = 'Announcement posted.';
        } else {
            $flash = 'Title and body cannot be empty.';
        }
    }
}

// -------------------------
// Fetch dashboard data
// -------------------------
$interns_total = get_count($pdo, 'interns');
$interns_pending = get_count($pdo, 'interns', "status = :s", [':s' => 'pending']);
$faculty_total = get_count($pdo, 'faculty');
$announcements_total = get_count($pdo, 'announcements');
$recent_activities = get_recent_activities($pdo, 8);
$pending_interns = get_pending_interns($pdo, 6);

// -------------------------
// HTML below
// -------------------------
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Intern & Faculty Dashboard</title>
    <style>
        :root{--accent:#2b6cb0;--muted:#666}
        body{font-family:Inter,Segoe UI,Arial;margin:0;background:#f4f6f8;color:#222}
        .container{max-width:1100px;margin:28px auto;padding:18px}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
        .brand{display:flex;align-items:center;gap:12px}
        .brand h1{margin:0;font-size:20px}
        .card{background:#fff;border-radius:10px;padding:14px;box-shadow:0 6px 18px rgba(20,20,50,0.06)}
        .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
        .widget{padding:14px}
        .big{font-size:20px;font-weight:700}
        .muted{color:var(--muted);font-size:13px}
        .main{display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-top:12px}
        .list{list-style:none;padding:0;margin:0}
        .list li{padding:10px;border-bottom:1px solid #eee}
        .btn{display:inline-block;padding:8px 12px;border-radius:8px;background:var(--accent);color:#fff;text-decoration:none}
        form.inline{display:flex;gap:8px;align-items:center}
        @media (max-width:900px){.grid{grid-template-columns:repeat(2,1fr)}.main{grid-template-columns:1fr}}
        @media (max-width:520px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="brand">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="6" fill="#2b6cb0"/><path d="M6 12h12M12 6v12" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div>
                <h1>Intern & Faculty Dashboard</h1>
                <div class="muted">Welcome, <?php echo htmlspecialchars($user['name']); ?> — Role: <?php echo htmlspecialchars($user['role']); ?></div>
            </div>
        </div>
        <div>
            <a class="btn" href="#" onclick="alert('Log out placeholder. Implement logout.php to destroy session.');return false;">Logout</a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="card" style="margin-bottom:12px;border-left:4px solid #22c55e;padding-left:12px;"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>

    <div class="grid">
        <div class="card widget">
            <div class="muted">Total Interns</div>
            <div class="big"><?php echo $interns_total; ?></div>
            <div class="muted">Pending: <?php echo $interns_pending; ?></div>
        </div>

        <div class="card widget">
            <div class="muted">Total Faculty</div>
            <div class="big"><?php echo $faculty_total; ?></div>
        </div>

        <div class="card widget">
            <div class="muted">Announcements</div>
            <div class="big"><?php echo $announcements_total; ?></div>
            <div class="muted">Post a new announcement below</div>
        </div>

        <div class="card widget">
            <div class="muted">Quick Actions</div>
            <div style="margin-top:8px">
                <form class="inline" method="post">
                    <input type="hidden" name="action" value="add_announcement">
                    <input name="title" placeholder="Short title" required style="padding:8px;border-radius:6px;border:1px solid #ddd">
                    <button class="btn" type="submit">Post</button>
                </form>
            </div>
        </div>
    </div>

    <div class="main">
        <div>
            <div class="card" style="margin-bottom:12px">
                <h3 style="margin-top:0">Pending Intern Applications</h3>
                <ul class="list">
                    <?php if (count($pending_interns) === 0): ?>
                        <li class="muted">No pending intern applications.</li>
                    <?php endif; ?>
                    <?php foreach ($pending_interns as $intern): ?>
                        <li>
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <div>
                                    <strong><?php echo htmlspecialchars($intern['full_name']); ?></strong>
                                    <div class="muted" style="font-size:12px"><?php echo htmlspecialchars($intern['email']); ?> — Applied: <?php echo htmlspecialchars($intern['applied_at']); ?></div>
                                </div>
                                <div>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="action" value="approve_intern">
                                        <input type="hidden" name="intern_id" value="<?php echo (int)$intern['id']; ?>">
                                        <button class="btn" type="submit">Approve</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card">
                <h3 style="margin-top:0">Recent Activity</h3>
                <ul class="list">
                    <?php if (count($recent_activities) === 0): ?>
                        <li class="muted">No recent activity.</li>
                    <?php endif; ?>
                    <?php foreach ($recent_activities as $act): ?>
                        <li>
                            <div style="display:flex;justify-content:space-between">
                                <div>
                                    <strong><?php echo htmlspecialchars($act['user_name']); ?></strong>
                                    <div class="muted" style="font-size:13px"><?php echo htmlspecialchars($act['action']); ?></div>
                                </div>
                                <div class="muted" style="font-size:12px"><?php echo htmlspecialchars($act['created_at']); ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <aside>
            <div class="card" style="margin-bottom:12px">
                <h3 style="margin-top:0">Announcements</h3>
                <div class="muted" style="font-size:13px">Latest announcements will show here.</div>
                <div style="margin-top:10px">
                    <?php
                    // show 5 most recent announcements
                    $stmt = $pdo->query("SELECT title, body, created_at FROM announcements ORDER BY created_at DESC LIMIT 5");
                    $anns = $stmt->fetchAll();
                    if (count($anns) === 0) {
                        echo '<div class="muted">No announcements yet.</div>';
                    } else {
                        echo '<ul class="list">';
                        foreach ($anns as $a) {
                            echo '<li><strong>' . htmlspecialchars($a['title']) . '</strong><div class="muted" style="font-size:12px">' . htmlspecialchars(substr($a['body'],0,80)) . '...</div><div class="muted" style="font-size:11px">' . htmlspecialchars($a['created_at']) . '</div></li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-top:0">Profile</h3>
                <div><strong><?php echo htmlspecialchars($user['name']); ?></strong></div>
                <div class="muted">Role: <?php echo htmlspecialchars($user['role']); ?></div>
                <div style="margin-top:10px"><a href="#" class="btn" onclick="alert('Edit profile placeholder');return false;">Edit Profile</a></div>
            </div>
        </aside>
    </div>

    <footer style="margin-top:18px;text-align:center;color:#888;font-size:13px">&copy; <?php echo date('Y'); ?> Intern Faculty Dashboard — Demo</footer>
</div>

</body>
</html>
