<?php
include '../connect.php';
session_start();
if (!isset($_SESSION['user_email'])) {
    header('location:index.php');
    exit();
}
$email = $_SESSION['user_email'];
// select name of user
$sql = "SELECT name,id FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('location:index.php');
    exit();
}
$name = $user['name'];
$id_user = $user['id'];
// select published posts
$sql = "SELECT * FROM posts WHERE status = 'published'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$publishedPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['comment'])) {
    $post_id = $_POST['publishedPostId'];
    $comment = $_POST['commentaire'];
    $date = date("Y-m-d H:i:s");
    $sql = "INSERT INTO comments (post_id, user_id, user_name, comment, date) VALUES (:post_id, :user_id, :user_name, :comment, :date)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':user_name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $messageSuccess[] = 'Comment added successfully.';
    } else {
        $messageError[] = 'Error adding comment.';
    }
}




if (isset($_POST['update_comment'])) {
    $commentId = $_POST['id_comment'];
    $updatedComment = $_POST['commentaire'];
    $updateQuery = "UPDATE comments SET comment = :updated_comment WHERE id = :comment_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':updated_comment', $updatedComment, PDO::PARAM_STR);
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $messageSuccess[] = 'Comment Updated successfully.';
    } else {
        $messageError[] = 'Error Updating comment.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>User dashboard</title>
</head>

<body>
    <?php include('../message/message.php'); ?>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Oussema Blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link">Welcome
                        <?= htmlspecialchars($name); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" onclick="return confirm('Logout from the website?');">
                        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-2">
        <div class="row">
            <?php $count = 0; ?>
            <?php foreach ($publishedPosts as $publishedPost): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <img class="card-img-top" src="../admin/uploads/<?= $publishedPost['image']; ?>"
                            style="width:100%;height:150px;">
                        <div class="card-body">
                            <h4 class="card-title">
                                <?= $publishedPost['title']; ?>
                            </h4>
                            <p class="card-text">
                                <?= $publishedPost['content']; ?>
                            </p>
                            <form action="" method="post">
                                <input type="hidden" name="publishedPostId" value="<?= $publishedPost['id']; ?>">
                                <textarea name="commentaire" class="form-control" id="commentaire" rows="2"
                                    required></textarea>
                                <button type="submit" name="comment" class="btn btn-success mt-2 float-right">Add
                                    commentaire</button>
                            </form>
                        </div>
                        <?php
                        $postId = $publishedPost['id'];
                        $sql = "SELECT * FROM comments WHERE post_id = :postId";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
                        $stmt->execute();
                        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment bg-light text-dark d-flex align-items-center justify-content-between p-2">
                                <div>
                                    <form action="" method="post" class="d-flex align-items-center">
                                        <input type="hidden" name="id_comment" value="<?= $comment['id']; ?>">
                                        <input type="text" name="commentaire" class="form-control commentaire" disabled
                                            value="<?= htmlspecialchars($comment['comment']); ?>" />
                                        <i class='far fa-comment ml-2 edit_comment'></i>
                                        <button type="submit" class="update_comment"
                                            name="update_comment"
                                            style="border: none;background: transparent;display:none;"><i
                                                class="fa fa-edit ml-2"></i></button>
                                    </form>
                                </div>
                                <div>
                                    <?= htmlspecialchars($comment['user_name']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php
                $count++;
                if ($count % 2 == 0) {
                    echo '</div><div class="row">';
                }
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>
<script>
    $(document).ready(function () {
        $('.edit_comment').click(function () {
            // Toggle visibility of the update button
            $(this).siblings('.update_comment').toggle();
            const commentaireInput = $(this).siblings('.commentaire');
            const isDisabled = commentaireInput.prop('disabled');
            commentaireInput.prop('disabled', !isDisabled);
        });
    });
</script>

</html>