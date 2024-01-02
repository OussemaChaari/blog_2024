<?php
include '../connect.php'; // Include your database connection file

if (isset($_GET['postId'])) {
    $postId = intval($_GET['postId']);
    $sql = "SELECT * FROM posts WHERE id = :postId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($post);
    } else {
        echo json_encode(['error' => 'Error fetching post content!']);
    }
} else {
    echo json_encode(['error' => 'Invalid request!']);
}
?>