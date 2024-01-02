
    <?php
    if (!empty($messageError)) {
        foreach ($messageError as $message) {
            echo '<script>toastr.error("' . $message . '");</script>';
        }
    }
    ?>
    <?php
    if (!empty($messageSuccess)) {
        foreach ($messageSuccess as $message) {
            echo '<script>toastr.success("' . $message . '");</script>';
        }
    }
    ?>
