<div class="admin-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><i class="fas fa-user-cog"></i> Admin Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['admin_username']; ?>!</p>
        </div>
        <div style="color: #ff9800; font-size: 14px;">
            <i class="fas fa-clock"></i> <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>
</div>