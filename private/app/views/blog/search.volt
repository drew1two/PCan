
<?php echo $this->getContent() ?>

<div align="right">
    <?php echo $this->tag->linkTo(array("blog/new", "Create blog")) ?>
</div>

<?php echo $this->tag->form(array("blog/search", "autocomplete" => "off")) ?>

<div align="center">
    <h1>Search blog</h1>
</div>

<table>
    <tr>
        <td align="right">
            <label for="id">Id</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("id", "type" => "number")) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="title">Title</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("title", "size" => 30)) ?>
        </td>
    </tr> 
    <tr>
        <td align="right">
            <label for="article">Article</label>
        </td>
        <td align="left">
                <?php echo $this->tag->textField(array("article", "type" => "date")) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="title_clean">Title Of Clean</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("title_clean", "size" => 30)) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="author_id">Author</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("author_id", "type" => "number")) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="date_published">Date Of Published</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("date_published", "size" => 30)) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="featured">Featured</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("featured", "size" => 30)) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="enabled">Enabled</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("enabled", "size" => 30)) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="comments_enabled">Comments Of Enabled</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("comments_enabled", "size" => 30)) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="views">Views</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("views", "type" => "number")) ?>
        </td>
    </tr>

    <tr>
        <td></td>
        <td><?php echo $this->tag->submitButton("Search") ?></td>
    </tr>
</table>

</form>
