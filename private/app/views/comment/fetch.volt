<div id='editComment' class='container' style='display:none;'>
    <form action='/comment/update' method='post'>
        <div class='row'>
            <label id='titleLabel' for='title'>Title</label>
            <?php echo $form->render('title') ?>
        </div>
        <div class='row'>
            <?php echo $form->render('comment') ?>
            <table class='table' width='50%'><tr>
                <td width='10%'></td>
                <td><button id='cancelBtn' class='btn' type='button' style='display:none;' onclick='return hideMe(this);'>Cancel</button></td>
                <td><?php echo $this->tag->submitButton(array('Save Edit', 'class' => 'btn btn-default')) ?></td>
            </tr></table>"
        </div>
        <?php 
            echo $form->render('id');
            echo $form->render('blog_id');
            echo $form->render('user_id');
            echo $form->render('head_id');
            echo $form->render('reply_to_id');
        ?>
    </form>
 </div>
