<?php
require_once 'includes/bootstrap.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        $redirect = isset($_GET['redirect']) ? sanitize_input($_GET['redirect']) : 'index.php';
        header("Location: " . $redirect);
        exit;
    } else {
        $error = "Invalid email or password. Please verify your credentials.";
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="auth-split-wrapper">
        <!-- Left: Brand Showcase & Guarantees -->
        <div class="auth-brand-side">
            <div>
                <div class="auth-brand-badge">⚡ Official Retailer</div>
                <h2 class="auth-brand-title">Your Flagship Gear, One Seamless Hub</h2>
                <p class="auth-brand-desc">Sign in to access your saved delivery addresses, track live courier dispatches, and unlock member pricing on authentic accessories.</p>

                <div class="auth-features">
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">✓</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">100% Genuine Inventory</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Direct sourcing from Apple, Samsung, Spigen & dbrand</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">⚡</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">Same-Day Express Dispatch</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Orders dispatched within 24 hours with SMS tracking</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">🛡️</div>
                        <div>
                            <strong style="display: block; color: #fff; margin-bottom: 2px;">7-Day Easy Replacements</strong>
                            <span style="color: #94a3b8; font-size: 13px;">Zero-hassle replacement on any fitting or transit issue</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Review Snippet -->
            <div class="auth-quote-card">
                "Ordered the Spigen Rugged Armor and 45W GaN charger. Package arrived in 2 days in mint condition. Superb experience."
                <span class="auth-quote-author">— Vikram Patel, Ahmedabad (Verified Buyer)</span>
            </div>
        </div>

        <!-- Right: Modern Sign-In Form -->
        <div class="auth-form-side">
            <div class="auth-form-header">
                <h1 class="auth-form-title">Sign In to MobileGear</h1>
                <p class="auth-form-subtitle">Enter your registered email to continue to your account.</p>
            </div>

            <!-- Demo Credentials Helper -->
            <div class="demo-creds-chip">
                <div>
                    <span style="font-weight: 700; color: var(--dark); font-size: 12px; display: block;">TEST ACCOUNT AVAILABLE</span>
                    <span style="font-size: 12px; color: var(--text-muted);">admin@mobilegear.com &bull; password</span>
                </div>
                <button type="button" class="demo-creds-btn" onclick="fillTestAccount()">Auto-Fill</button>
            </div>

            <?php if(isset($error)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: var(--radius-md); border: 1px solid #fecaca; font-weight: 500; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="loginForm">
                <!-- Email Field with Icon -->
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="email" id="loginEmail" name="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email">
                    </div>
                </div>

                <!-- Password Field with Icon and Eye Toggle -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <label>Password</label>
                        <a href="javascript:void(0)" onclick="alert('For this demonstration, you can use the test admin account or register a new customer account.')" style="font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600;">Forgot password?</a>
                    </div>
                    <div class="input-with-icon">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" radius="2" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('loginPassword', this)" title="Show or hide password">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                    <input type="checkbox" id="rememberMe" checked style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;">
                    <label for="rememberMe" style="font-size: 13px; color: var(--text-muted); cursor: pointer; margin-bottom: 0;">Remember this device for 30 days</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 13px; font-size: 15px;">
                    <span>Sign In to Account</span>
                    <span>&rarr;</span>
                </button>
            </form>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-light); text-align: center;">
                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px;">
                    New customer at MobileGear?
                </p>
                <a href="register.php" class="btn btn-secondary btn-block" style="font-size: 14px; padding: 11px;">
                    Create Your Customer Account
                </a>
            </div>

            <div style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 20px; font-size: 12px; color: var(--text-light);">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Bank-grade 256-Bit SSL Encrypted Connection</span>
            </div>
        </div>
    </div>
</div>

<script>
function fillTestAccount() {
    document.getElementById('loginEmail').value = 'admin@mobilegear.com';
    document.getElementById('loginPassword').value = 'password';
}

function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    } else {
        input.type = 'password';
        btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
