<?php
include '../connect.php';
session_start();

if (isset($_POST['delete'])) {
    $postId = $_POST['post_id'];
    $imagePath = fetchImagePath($conn, $postId);
    $sql = "DELETE FROM posts WHERE id = :postId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        if (!empty($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $messageSuccess[] = 'Post deleted successfully!';
    } else {
        $messageError[] = 'Error deleting post.';
    }
}
if (isset($_POST['update_post'])) {
    $postId = $_POST['postId'];
    $editTitle = $_POST['editTitle'];
    $editContent = $_POST['editContent'];
    $editCategory = $_POST['editCategory'];
    $editStatus = $_POST['editStatus'];
    $editImage = '';
    if (isset($_FILES['editImage']) && $_FILES['editImage']['error'] == 0) {
        $image_extension = pathinfo($_FILES['editImage']['name'], PATHINFO_EXTENSION);
        $editImage = uniqid() . '.' . $image_extension;
        $target_dir = 'uploads/';
        $target_file = $target_dir . $editImage;

        if (move_uploaded_file($_FILES['editImage']['tmp_name'], $target_file)) {
            $oldImagePath = fetchImagePath($conn, $postId);
            if ($oldImagePath && $oldImagePath !== 'uploads/' . $editImage && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        } else {
            $messageError[] = 'Failed To Upload Image';
        }
    }
    $sql = "UPDATE posts SET title = :editTitle, content = :editContent, category_id = :editCategory, image = :editImage, status = :editStatus, admin_id = :adminId WHERE id = :postId";
    $stmtup = $conn->prepare($sql);
    $admin_id = $_SESSION['admin_id'];
    $stmtup->bindParam(':adminId', $admin_id, PDO::PARAM_INT);
    $stmtup->bindParam(':editTitle', $editTitle, PDO::PARAM_STR);
    $stmtup->bindParam(':editContent', $editContent, PDO::PARAM_STR);
    $stmtup->bindParam(':editCategory', $editCategory, PDO::PARAM_INT);
    $stmtup->bindParam(':editImage', $editImage, PDO::PARAM_STR);
    $stmtup->bindParam(':editStatus', $editStatus, PDO::PARAM_STR);
    $stmtup->bindParam(':postId', $postId, PDO::PARAM_INT);

    if ($stmtup->execute()) {
        $messageSuccess[] = 'Post updated successfully.';
    } else {
        $messageError[] = 'Error updating post.';
    }
}
// Function to fetch the image path based on post ID
function fetchImagePath($conn, $postId)
{
    $sql = "SELECT image FROM posts WHERE id = :postId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return isset($result['image']) ? 'uploads/' . $result['image'] : '';
}
$sqlCount = "SELECT COUNT(*) as total FROM posts";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute();
$totalPosts = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

$sql = "SELECT * FROM posts WHERE 1=1";
$params = array();

// Category filter
$categoryFilter = isset($_GET['categoryFilter']) ? $_GET['categoryFilter'] : '';
if (!empty($categoryFilter)) {
    $sql .= " AND category_id = :categoryFilter";
    $params[':categoryFilter'] = $categoryFilter;
}

// Title filter
$searchPost = isset($_GET['searchPost']) ? $_GET['searchPost'] : '';
if (!empty($searchPost)) {
    $sql .= " AND title LIKE :searchPost";
    $params[':searchPost'] = '%' . $searchPost . '%';
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);

// Fetch posts
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// select all category
$sql = "SELECT * FROM categories";
$stmt = $conn->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../includes/include.php'); ?>
    <title>Show Posts</title>
</head>

<body>
    <?php include('sidebar.php'); ?>
    <?php include('../message/message.php'); ?>
    <section class="" style="float:right!important;">
        <h4 class="heading">Listes Des Posts</h4>
            <form action="" method="get" class="form-inline mb-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="searchPost" name="searchPost" placeholder="Search By title Post">
                </div>
                <div class="form-group ml-3">
                    <select class="form-control" id="categoryFilter" name="categoryFilter">
                        <option value="" selected>Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($categoryFilter == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-search"></i></button>
            </form>
        <div class="d-flex" style="	flex-wrap: wrap!important;width:1000px!important;">
           <?php if(empty($posts)){ 
            echo '<div class="alert alert-warning" style="width:100%;" role="alert">';
                if ($totalPosts == 0) {
                    echo 'No posts available in the database.';
                } else {
                    echo 'No posts found with the given filters.';
                }
                echo '</div>';
             }else{ ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-2 mr-2">
                    <img class="card-img-top" src="uploads/<?= $post['image']; ?>" style="width:300px;height:150px;">
                    <div class="card-body">
                        <h4 class="card-title">
                            <?= $post['title']; ?>
                        </h4>
                        <p class="card-text">
                            <?= $post['content']; ?>
                        </p>
                        <div class="float-right">
                            <a href="" class="btn btn-primary view" data-toggle="modal"
                                data-target="#ModalView<?php echo $post['id']; ?>" data-id="<?php echo $post['id']; ?>"><i
                                    class="fas fa-eye"></i></a>
                            <div class="modal" id="ModalView<?php echo $post['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                                        <div class="modal-body" id="post-content-container<?php echo $post['id']; ?>">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a class="btn btn-success edit" data-toggle="modal"
                                data-target="#editModal<?php echo $post['id']; ?>" data-id="<?php echo $post['id']; ?>">
                                <i class="fa fa-edit"></i>
                            </a>
                            <div class="modal" id="editModal<?php echo $post['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Post</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Your edit form goes here -->
                                            <form action="" method="post" id="editForm<?php echo $post['id']; ?>"
                                                enctype="multipart/form-data">
                                                <input type="hidden" name="postId" value="<?php echo $post['id']; ?>">
                                                <div class="form-group">
                                                    <input type="text" class="form-control"
                                                        id="editTitle<?php echo $post['id']; ?>" name="editTitle"
                                                        value="<?php echo $post['title']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <textarea class="form-control"
                                                        id="editContent<?php echo $post['id']; ?>" name="editContent"
                                                        placeholder="Enter Content" rows="4"
                                                        required><?php echo $post['content']; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <select class="form-control" id="editCategory<?php echo $post['id']; ?>"
                                                        name="editCategory" required>
                                                        <option value="">-</option>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?php echo $category['id']; ?>" <?php echo ($post['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                                <?php echo $category['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <input type="file" class="form-control-file"
                                                        id="editImage<?php echo $post['id']; ?>" name="editImage"
                                                        accept="image/*">
                                                </div>
                                                <img id="editImagePreview<?php echo $post['id']; ?>" class="mb-2"
                                                    src="uploads/<?php echo $post['image']; ?>"
                                                    alt="<?php echo $post['image']; ?>" style="width:250px; height:100px;">
                                                <div class="form-group">
                                                    <select class="form-control" id="editStatus<?php echo $post['id']; ?>"
                                                        name="editStatus" required>
                                                        <option value="">-</option>
                                                        <option value="draft" <?php echo ($post['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                                        <option value="published" <?php echo ($post['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col text-right">
                                                        <button type="submit" name="update_post"
                                                            class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a class="btn btn-danger delete" data-toggle="modal"
                                data-target="#ModalDelete<?php echo $post['id']; ?>" data-id="<?php echo $post['id']; ?>">
                                <i class="fa fa-trash"></i>
                            </a>
                            <div class="modal" id="ModalDelete<?php echo $post['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Confirmation!</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this post?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="" method="post">
                                                <input type="hidden" name="post_id"
                                                    id="post_input_id_<?php echo $post['id']; ?>" value="">
                                                <button type="submit" name="delete" class="btn btn-success">Confirm</button>
                                            </form>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;} ?>
        </div>
    </section>
    <script>
        $(document).ready(function () {
            $(".view").click(function () {
                var postId = $(this).data('id');
                var contentContainer = $("#post-content-container" + postId);
                $.ajax({
                    type: "GET",
                    url: "fetch_post_content.php",
                    data: { postId: postId },
                    success: function (response) {
                        contentContainer.html(response);
                    },
                    error: function () {
                        console.log("Error fetching post content!");
                    }
                });
            });
            $(".delete").click(function () {
                var postId = $(this).data('id');
                $('#post_input_id_' + postId).val(postId);
            });
            $(".edit").click(function () {
                var postId = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "fetch_post.php",
                    data: { postId: postId },
                    success: function (response) {
                        // Parse the response and populate the form fields
                        var post = JSON.parse(response);
                        $("#editTitle" + post.id).val(post.title);
                        $("#editContent" + post.id).val(post.content);
                        $("#editCategory" + post.id).val(post.category_id);
                        $("#editImage" + post.id).val(post.image);
                        $("#editImagePreview" + post.id).attr("src", "uploads/" + post.image);
                        $("#editStatus" + post.id).val(post.status);
                    },
                    error: function () {
                        console.log("Error fetching post content!");
                    }
                });
            });

        });
    </script>
</body>

</html>