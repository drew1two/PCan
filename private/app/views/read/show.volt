{{ stylesheet_link("js/jQuery-TE_v.1.4.0/jquery-te-1.4.0.css") }}
{{ javascript_include("js/jQuery-TE_v.1.4.0/jquery-te-1.4.0.min.js") }}
{{ stylesheet_link("css/elyxir.css", false) }}
    <style type="text/css">
        body {
            font-size : 100%;
            line-height:20px;
        }
        h1,h2 {
            text-align:center;
        }
        p {  
             font: 20px Arial,  san-serif;
             margin-left: 3em;
             margin-right:3em;
         }
    </style>

<?php echo $this->getContent(); ?>

<?php echo $this->tag->form("blog/edit/" . $id) ?>

<table width="100%">
    <tr>
        <td align="left"><?php echo $this->tag->linkTo(array("blog", "Back")) ?></td>
        <td align="right"><?php echo $this->tag->submitButton("Save") ?></td>
    <tr>
</table>

<div align="center">
    <h1>Edit blog</h1>
</div>

<table>
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
            <label for="title_clean">Title Of Clean</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("title_clean", "size" => 30)) ?>
        </td>
    </tr>
</table>

<label for="article">Article</label> {{ text_area('article', 'class':'jqte-edit', 'style':'width:100%;') }}
<table>

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
            {{ check_field('featured', 'value' : 'N') }}
            <!--<?php echo $this->tag->textField(array("featured", "size" => 30)) ?>-->
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

</table>
<script>
	$('.jqte-edit').jqte();
</script>
</form>
