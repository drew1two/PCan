<?php use Phalcon\Tag; ?>

<?php echo $this->getContent(); ?>

<div style="margin-left:100px;">
    <br/>
<?php 
    $ubase = "read/index?orderby=";  
    $link = $ubase.$orderby;  
    echo " " . $this->tag->linkTo($link, 'First');
    echo " | " . $this->tag->linkTo($link.'&page=' . $page->before, 'Previous');
    echo " | " .  $this->tag->linkTo($link.'&page=' . $page->next, 'Next');
    echo " | " .  $this->tag->linkTo($link. '&page=' . $page->last, 'Last');
    echo " | " .  $page->current, "/", $page->last;
?>
</div>
<div class='container' >
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th style="width:65%;"><?php echo $this->tag->linkTo($ubase.$orderalt['title'], 'Title') ?></th>
            <th style="width:20%;"><?php echo $this->tag->linkTo($ubase.$orderalt['author'], 'Author') ?></th>
            <th style="width:15%;"><?php echo $this->tag->linkTo($ubase.$orderalt['date'], 'Date') ?></th> 
         </tr>
    </thead>

    <tbody>
    <?php foreach ($page->items as $blog) { ?>
        <tr>
            <td style="text-align:left"><?php echo $this->tag->linkTo(array("article/" . $blog->title_clean, $blog->title)); ?></td>
            <td><?php echo $blog->author_name; ?></td>
            <td><?php echo substr($blog->date_published,0,10); ?></td>
        </tr>
    <?php } ?>
    </tbody>

</table>
</div>