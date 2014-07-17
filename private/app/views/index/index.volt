{{ stylesheet_link("css/elyxir.css", false) }}

<div class="container">
    <div class="row">
        <div id="blogs-menu" class = "col-xs-4 col-md-3" style="background-color:#FAEBDA;">
            <span style="font-size:1.2em;">Recent Articles</span>
            <ul class="list-unstyled">
                <?php foreach ($recent as $blog) { ?>
                    <li class="Nav"><?php echo $this->tag->linkTo(array("read/article/" . $blog->id, "&bull; ".$blog->title)); ?></li>
                <?php } ?>
            </ul>
        </div>
        <div id="blogs-featured" class="col-xs-8 col-md-6">
            <?php foreach ($feature as $blog) { ?>
                <h2><?php echo $blog->title ?></h2> 
                <div>   
                <?php echo $blog->article;
                      echo $this->tag->linkTo("read/article/" . $blog->id, "Read more ...");
                ?>
                </div> 
                <hr>
            <?php } ?>
        </div>  
    </div>
</div>