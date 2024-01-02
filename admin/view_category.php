<?php
include '../connect.php';
session_start();
//delete category function
if (isset($_POST['delete'])) {
    $categoryId = $_POST['category_input_id'];
    $sql = "DELETE FROM categories WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $messageSuccess[] = 'Category deleted successfully.';
        $perPage = 5;
        $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $start = ($currentPage - 1) * $perPage;
        $sqlCount = "SELECT COUNT(*) as count FROM categories";
        $stmtCount = $conn->prepare($sqlCount);
        if ($stmtCount->execute()) {
            $resultCount = $stmtCount->fetch(PDO::FETCH_ASSOC);
            if (is_array($resultCount) && isset($resultCount['count'])) {
                $totalCategories = $resultCount['count'];
                $totalPages = ceil($totalCategories / $perPage);
                if ($totalCategories > 0 && $currentPage > $totalPages) {
                    header('Location: view_category.php?page=' . ($totalPages));
                    exit();
                }
            }
        }
    } else {
        $messageError[] = 'Error deleting category.';
    }
}
//edit
if (isset($_POST['edit'])) {
    $categoryId = $_POST['category_input_id'];
    $newCategoryName = $_POST['category'];

    // Update the category name in the database
    $sql = "UPDATE categories SET name = :newCategoryName WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':newCategoryName', $newCategoryName, PDO::PARAM_STR);
    $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $messageSuccess[] = 'Category updated successfully.';
    } else {
        $messageError[] = 'Error updating category.';
    }
}
//fetch category
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
    <title>Show Category</title>
</head>

<body>
    <?php include('sidebar.php'); ?>
    <?php include('../message/message.php'); ?>


    <section class="d-flex flex-column align-items-center" style="width:800px;">
    <h4 class="heading">Listes Des categories</h4>

        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name Category</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Set the number of items per page
                $perPage = 5;
                $totalCategories = count($categories);
                $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $start = ($currentPage - 1) * $perPage;
                $end = $start + $perPage;
                $categoriesToShow = array_slice($categories, $start, $perPage);

                foreach ($categoriesToShow as $category): ?>
                    <tr>
                        <th scope="row">
                            <?php echo $category['id']; ?>
                        </th>
                        <td>
                            <?php echo $category['name']; ?>
                        </td>
                        <td>
                            <button class="btn btn-primary edit" data-toggle="modal"
                                data-target="#ModalEdit<?php echo $category['id']; ?>"
                                data-categoryid="<?php echo $category['id']; ?>"
                                data-categoryname="<?php echo $category['name']; ?>">
                                <i class="fa fa-edit"></i>
                            </button>
                            <div class="modal" id="ModalEdit<?php echo $category['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Category!</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="post">
                                                <input type="hidden" name="category_input_id"
                                                    id="category_input_id_<?php echo $category['id']; ?>" value="">
                                                <div class="form-group">
                                                    <label for="category_edit">Category Name:</label>
                                                    <input type="text" name="category" class="form-control"
                                                        id="category_edit_<?php echo $category['id']; ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col text-right">
                                                        <button type="submit" name="edit"
                                                            class="btn btn-success">Edit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-danger delete" data-toggle="modal"
                                data-categoryid="<?php echo $category['id']; ?>"
                                data-target="#ModalDelete<?php echo $category['id']; ?>"><i
                                    class="fa fa-trash"></i></button>
                            <div class="modal" id="ModalDelete<?php echo $category['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Confirmation!</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this category?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="" method="post">
                                                <input type="hidden" name="category_input_id"
                                                    id="category_input_id_<?php echo $category['id']; ?>" value="">
                                                <button type="submit" name="delete" class="btn btn-success">Confirm</button>
                                            </form>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination">
                <?php
                $totalPages = ceil($totalCategories / $perPage);
                // Show "Previous" button
                if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif;
                // Show ellipsis if there are more than 3 pages
                if ($totalPages > 3 && $currentPage > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif;
                for ($i = max(1, $currentPage - 1); $i <= min($currentPage + 1, $totalPages); $i++): ?>
                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor;
                if ($totalPages > 3 && $currentPage < $totalPages - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif;
                // Show "Next" button
                if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </section>
    <script>
        $(document).ready(function () {
            $(".delete").click(function () {
                var categoryId = $(this).data('categoryid');
                $('#category_input_id_' + categoryId).val(categoryId);
            });
            $(".edit").click(function () {
                var categoryId = $(this).data('categoryid');
                var categoryName = $(this).data('categoryname');
                $('#category_input_id_' + categoryId).val(categoryId);
                $('#category_edit_' + categoryId).val(categoryName);
            });
        });

    </script>
</body>

</html>