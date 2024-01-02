<?php
include '../connect.php';
session_start();

if (isset($_POST['insert_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);        
        $image_filename = uniqid() . '.' . $image_extension;
        $image_path = $image_filename;
        $target_dir = 'uploads/';
        $target_file = $target_dir . $image_filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $messageError[] = 'Failed To Upload Image';
        }
    }
    $sql = "INSERT INTO posts (admin_id, title, content, category_id, image, status) VALUES (:admin_id, :title, :content, :category_id, :image, :status)";
    $stmt = $conn->prepare($sql);
    $admin_id = $_SESSION['admin_id'];
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':image', $image_path, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $messageSuccess[] = 'Post created successfully.';
    } else {
        $messageError[] = 'Error creating post.';
    }
}
 


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
    <title>Add Posts</title>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <?php include('../message/message.php'); ?>
    <section class="form-container d-flex flex-column align-items-center">
    <h4 class="heading">Add new Post</h4>
        <form action="" method="post" enctype="multipart/form-data" style="width:500px;">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Create Title" id="title" name="title" required>
            </div>
            <div class="form-group">
                <textarea class="form-control" id="content" name="content" placeholder="Enter Content" rows="4"
                    required></textarea>
            </div>
            <div class="form-group">
                <label for="category_id">Choose Category</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">-</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo $category['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="status">Choose Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="">-</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <div class="row">
                <div class="col text-right">
                    <button type="submit" name="insert_post" class="btn btn-primary">Create Post</button>
                </div>
            </div>
        </form>
    </section>

</body>

</html>