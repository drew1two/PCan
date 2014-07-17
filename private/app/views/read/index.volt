<?php use Phalcon\Tag; ?>

<?php echo $this->getContent(); ?>

<div style="margin-left:100px;">
    <br/>
<?php echo " " . $this->tag->linkTo("read/index", "First") ?></td>
<?php echo " | " . $this->tag->linkTo("read/index?page=" . $page->before, "Previous") ?>
<?php echo " | " .  $this->tag->linkTo("read/index?page=" . $page->next, "Next") ?>
<?php echo " | " .  $this->tag->linkTo("read/index?page=" . $page->last, "Last") ?>
<?php echo " | " .  $page->current, "/", $page->last ?>
</div>
<div class='container' >
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="width:65%;">Title</th>
            <th style="width:20%;">Author</th>
            <th style="width:15%;">Date</th>
            
         </tr>
    </thead>

    <tbody>
    <?php foreach ($page->items as $blog) { ?>
        <tr>
            <td style="text-align:left"><?php echo $this->tag->linkTo(array("read/article/" . $blog->id, $blog->title)); ?></td>
            <td><?php echo $blog->author_name; ?></td>
            <td><?php echo substr($blog->date_published,0,10); ?></td>
        </tr>
    <?php } ?>
    </tbody>

</table>
</div>