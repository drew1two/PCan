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
            <th style="width:65%;">Value</th>
            <th style="width:20%;">Attribute</th>
            <th style="width:15%;">Content</th>   
         </tr>
    </thead>

    <tbody>
    <?php foreach ($page->items as $meta) { ?>
        <tr>
            <td style="text-align:left"><?php echo $this->tag->linkTo(array("meta/edit?id=" . $meta->id, $meta->attr_value)); ?></td>
            <td><?php echo $meta->attr_name; ?></td>
            <td><?php echo $meta->content_type; ?></td>
        </tr>
    <?php } ?>
    </tbody>

</table>
</div>