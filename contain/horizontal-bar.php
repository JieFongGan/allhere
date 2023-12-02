<header>
    <div class="directory-tag">
        <p>
            <?php echo $pathtitle; ?>
        </p>
    </div>

    <div class="social-icons">
        <div class="social-icon">
                <p style="float: left;
                margin-right: 8px;
                margin-top: 8px;">Welcome Back, <?php echo $username ?>
                </p>
            <img src="../img/user-profile.png" alt="Social Icon" id="social-icon">
            <ul class="dropdown">
                <li><a href="../dashboard/profile.php">Profile</a></li>
                <li><a href="../dashboard/chpassword.php">Change Password</a></li>
                <li><a href="../logout.php">log out</a></li>
            </ul>
        </div>
    </div>
</header>