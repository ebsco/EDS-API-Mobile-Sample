<?php if (isset($_SESSION['login'])) {
    header("Location: ./");
} ?>
<div id="toptabcontent" class="clearfix">
    <div class="loginform">
        <h2>Login</h2>
        <?php if ($fail == 'y') { ?>
            <div class="loginfailed">Invalid login -- please try again</div>
        <?php } ?>
        <form data-ajax="false" action="auth.php" method="post">
            <label><b>Username:</b> </label><input tyep="text" name="userId" value=""/>
            <label><b>Password:</b> </label><input type="password" name="password" value=""/>
            <input data-theme="b" type="submit" value="Login"/>
            <?php if ($path == "PDF") { ?>
                <div>
                    <input type="hidden" value="<?php echo $path ?>" name="path"/>
                    <input type="hidden" value="<?php echo $db ?>" name="db"/>
                    <input type="hidden" value="<?php echo $an ?>" name="an"/>
                </div>
            <?php } ?>
            <?php if ($path == "results") { ?>
                <div>
                    <input type="hidden" value="<?php echo $path ?>" name="path"/>
                    <input type="hidden" value="<?php echo $query ?>" name="query"/>
                    <input type="hidden" value="<?php echo $fieldCode ?>" name="fieldcode"/>
                </div>
            <?php } ?>
            <?php if ($path == "record" || $path == "HTML") { ?>
                <div>
                    <input type="hidden" value="<?php echo $path ?>" name="path"/>
                    <input type="hidden" value="<?php echo $db ?>" name="db"/>
                    <input type="hidden" value="<?php echo $an ?>" name="an"/>
                    <input type="hidden" value="<?php echo $highlight ?>" name="highlight"/>
                    <input type="hidden" value="<?php echo $resultId ?>" name="resultId"/>
                    <input type="hidden" value="<?php echo $recordCount ?>" name="recordCount"/>
                    <input type="hidden" value="<?php echo $query ?>" name="query"/>
                    <input type="hidden" value="<?php echo $fieldCode ?>" name="fieldcode"/>
                </div>
            <?php } ?>
        </form>
    </div>

</div>

