<?php
include_once("functions.php");
include_once("header.php");
?>

<!-- Register Title -->
<div class="title_div">
    <h1 class="main_title">
        Register
    </h1>
</div>

<!-- Signup Error Message -->
<?php if (isset($_GET["error"])) { ?>
    <div id="signup_error">
        <b>Error:</b> <?php echo $_GET["error"]; ?>
        <br>Please try again.
    </div>
<?php } ?>

<!-- Signup Form -->
<form method="post" enctype="multipart/form-data" action="backend_register.php">
    <!-- User Data Information -->
    <div class="paragraph_div">
        <h4>New User Data</h4>
        All the following fields are required.
    </div>

    <!-- User Data Selection -->
    <div class="paragraph_div">
        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" class="log_field" name="username" maxlength="16">

        <!-- Password -->
        <br>
        <label for="password">Password:</label>
        <input type="password" class="log_field" name="password">

        <!-- Reenter Password -->
        <br>
        <label for="password2">Re-Enter Password:</label>
        <input type="password" class="log_field" name="password2">
    </div>

    <!-- Avatar Info -->
    <div class="paragraph_div">
        <h4>Avatar</h4>
        An avatar is not required, but you may upload one if you want to customize your account.
        It must not be larger than 100KiB. Will be displayed as a 50x50px image.
    </div>

    <!-- Avatar Selection -->
    <div class="paragraph_div">
        <label for="avatarfile">Choose a file:</label>
        <input type="file" name="avatarfile">
    </div>


    <div class="paragraph_div">
        <input type="submit" class="log_field" value="Create Account">
    </div>
</form>

<!-- Footer -->
<?php
include_once("footer.php");
?>