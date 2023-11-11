<?php
include_once("header.php");
?>

<!-- Recent Topics Title -->
<div class="title_div">
    <h1 class="main_title">Create New Category</h1>
</div>

<!-- General Message -->
<?php if (isset($_GET["message"])) { ?>
    <div id="notification_message">
        <b>Information:</b> <?php echo $_GET["message"]; ?>
    </div>
<?php } ?>

<!-- Error Message -->
<?php if (isset($_GET["error"])) { ?>
    <div id="signup_error">
        <b>Error:</b> <?php echo $_GET["error"]; ?>
        <br>Please try again.
    </div>
<?php } ?>

<!-- Categories Area -->
<div class="paragraph_div">
    This page is meant for bulletin board administrators to create new categories.
    An administrator account is required to make any changes.
</div>

<!-- Category Creation Form -->
<form method="post" enctype="multipart/form-data" action="backend_create_category.php">
    <!-- Category Data -->
    <div class="paragraph_div">
        <!-- Username -->
        <label for="cat_name">Category Name:</label>
        <input type="text" class="log_field" name="cat_name" maxlength="64">
    </div>

    <!-- Category Icon -->
    <div class="paragraph_div">
        <h4>Icon</h4>
        An icon is not required, but you may upload one if you want to customize the category.
        It must not be larger than 100KiB. Will be displayed as a 16x16px image.
    </div>

    <!-- Avatar Selection -->
    <div class="paragraph_div">
        <label for="iconfile">Choose a file:</label>
        <input type="file" name="iconfile">
    </div>

    <!-- Admin Authentication -->
    <div class="paragraph_div">
        <!-- Username -->
        <label for="username">Admin Username:</label>
        <input type="text" class="log_field" name="username" maxlength="16">

        <!-- Password -->
        <br>
        <label for="password">Admin Password:</label>
        <input type="password" class="log_field" name="password">
    </div>

    <div class="paragraph_div">
        <input type="submit" class="log_field" value="Create Category">
    </div>
</form>

<!-- Footer -->
<?php
include_once("footer.php");
?>