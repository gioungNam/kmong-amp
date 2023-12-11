<?php
	require_once 'template/header.php';
?>

<div class="container mt-4">
<?php if (isset($_SESSION['user_id'])) : ?>
    <div class="row justify-content-start" style="max-width: 400px">
        <div class="col-md-6">      
                <div class="card">
                    <div class="card-body">
                        <p class="card-text"><b><?php echo $_SESSION['nickname'] ?></b>님. 어서오세요!</p>
                        <?php if (isset($_SESSION['profile_picture'])) : ?>
                            <img src="<?= $_SESSION['profile_picture'] ?>" class="card-img-top" alt="Profile Picture">
                        <?php endif; ?>
                    </div>
                </div>
        </div>
    </div>
    <?php else : require_once 'auth/login_form.php' ?>
    <?php endif; ?>   
</div>

</body>
</html>

