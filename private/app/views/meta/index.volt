<?php use Phalcon\Tag; ?>

<?php echo $this->getContent(); ?>

<?php echo " " . $this->tag->linkTo(array('meta/new', 'New', 'class' => 'btn btn-success')) ?>

<div style="margin-left:100px;">
    <br/>
<?php echo " " . $this->tag->linkTo("meta/index", "First") ?>
<?php echo " | " . $this->tag->linkTo("meta/index?page=" . $page->before, "Previous") ?>
<?php echo " | " .  $this->tag->linkTo("meta/index?page=" . $page->next, "Next") ?>
<?php echo " | " .  $this->tag->linkTo("meta/index?page=" . $page->last, "Last") ?>
<?php echo " | " .  $page->current, "/", $page->last ?>

</div>
<div class='container' >
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="width:20%;">Name</th>
            <th style="width:65%;">Template</th>
            <th style="width:15%;">Size Limit</th>
            <th style="width:15%;">Auto</th> 
         </tr>
    </thead>

    <tbody>
    <?php foreach ($page->items as $meta) { ?>
        <tr>
            <td style="text-align:left"><?php echo $this->tag->linkTo(array("meta/edit?id=" . $meta->id, $meta->meta_name)); ?></td>
            <td><?php echo htmlentities($meta->template); ?></td>
            <td><?php echo $meta->data_limit; ?></td>
            <td><?php 
                $howset = isset($meta->auto_filled) && $meta->auto_filled==1 ? "AUTO" : "";
                echo $howset;
                ?></td>
        </tr>
    <?php } ?>
    </tbody>

</table>
</div>