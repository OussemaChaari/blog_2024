<?php
include '../connect.php';
// fetch post 
if (isset($_GET['postId'])) {
    $postId = intval($_GET['postId']);   
    $sql = "SELECT posts.*, admin.name AS admin_name, categories.name AS category_name 
            FROM posts 
            INNER JOIN admin ON posts.admin_id = admin.id 
            INNER JOIN categories ON posts.category_id = categories.id 
            WHERE posts.id = :postId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        echo '<img src="uploads/' . $post['image'] . '" alt="Post Image" style="width:100%;height:250px;">';
        echo '<h6>' . $post['title'] . '</h6>';
        echo '<p>' . $post['content'] . '</p>';
        echo '<p>Admin Name: ' . $post['admin_name'] . '</p>';
        echo '<p>Category Name: ' . $post['category_name'] . '</p>';
        echo '<p>Status: ';
        if ($post['status'] == "published") {
            echo '<span class="badge badge-success">Published</span>';
        } else {
            echo '<span class="badge badge-secondary">Draft</span>';
        }
        echo '</p>';
    } else {
        $messageError[] = 'Error fetching post content!';
    }
}
?>