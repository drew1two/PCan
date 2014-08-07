<?php

use Phalcon\Tag; ?>

<?php echo $this->getContent(); ?>
<table class="table table-responsive">
    <tr>
        <td><?php echo "page " . $page->current . " of " . $page->last ?></td>
        <td><?php echo $this->tag->linkTo(array("blog/index", "First", 'class'=>"btn")) ?></td>
        <td><?php echo $this->tag->linkTo(array("blog/index?page=" . $page->before, "Previous", 'class'=>"btn")) ?></td>
        <td><?php echo $this->tag->linkTo(array("blog/index?page=" . $page->next, "Next", 'class'=>"btn")) ?></td>
        <td><?php echo $this->tag->linkTo(array("blog/index?page=" . $page->last, "Last", 'class'=>"btn")) ?></td>


        <td>
            <?php echo $this->tag->linkTo(array("blog/new", "New Article ")); ?>
        </td>
    <tr>
</table>

<table class="table table-bordered table-striped" align="center">

    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Published</th>
            <th>Feature</th>
            <th>Enable</th>
            <th>Comment</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($page->items as $blog) { ?>
            <tr>
                <td><?php echo $this->tag->linkTo(array("blog/comment/" . $blog->id, $blog->title)); ?></td>
                <td><?php if ($isEditor || ($blog->author_id == $user_id)) {
                      echo $this->tag->linkTo(array("blog/edit/" . $blog->id, $blog->author_name));
                 } else { 
                      echo $blog->author_name;
                 } ?></td>
                
                <td><?php echo $blog->date_published ?></td>
                <td><?php
                    if ($blog->featured == 1) {
                        echo $this->tag->image("img/tick16.png");
                    }
                    ?></td>
                <td><?php
                    if ($blog->enabled == 1) {
                        echo $this->tag->image("img/tick16.png");
                    }
                    ?></td>
                <td><?php
                    if ($blog->comments == 1) {
                        echo $this->tag->image("img/tick16.png");
                    }
                    ?></td>
             
            </tr>
        <?php } ?>
    </tbody>

</table>


