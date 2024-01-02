<?php 
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:index.php');
}
$select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
$admin_name = $fetch_profile['name'];

?>
<header class="header">
    <nav class="nav_header">
        <span class="profile"><?= $admin_name; ?></span>
        <a href="dashboard.php"><i class="fas fa-home"></i> <span>home</span></a>
        <a href="add_posts.php"><i class="fas fa-pen"></i> <span>Add posts</span></a>
        <a href="view_posts.php"><i class="fas fa-eye"></i> <span>View posts</span></a>
        <a href="add_category.php"><i class="fas fa-pen"></i> <span>Add category</span></a>
        <a href="view_category.php"><i class="fas fa-eye"></i> <span>View category</span></a>
        <a href="admin_accounts.php"><i class="fas fa-user"></i> <span>Accounts</span></a>
        <a href="logout.php" onclick="return confirm('logout from the website?');"><i
                class="fas fa-right-from-bracket"></i><span>logout</span></a>
    </nav>
</header>