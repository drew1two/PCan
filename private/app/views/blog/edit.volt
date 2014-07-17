
<?php echo $this->getContent(); ?>
<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">


{{ javascript_include("js/tinymce/tinymce.min.js") }}

<script type="text/javascript">
tinymce.init({
    selector: "#article",
    height: 400,
    fontsize_formats: "8pt 9pt 10pt 11pt 12pt 18pt 24pt 36pt",
    theme: "modern",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker textpattern"
    ],
    toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    toolbar2: "print preview media fullscreen | forecolor backcolor emoticons | fontselect fontsizeselect ",
    image_advtab: true,
});
   
</script>


<?php echo $this->tag->form(array("blog/edit/" . $id, 'id'=>'myform')); ?>

<table class="table table-striped">
    <tr>
        <td align="left"><?php echo $this->tag->linkTo(array("blog/index", "Index")) ?></td>
        <td align="center"><?php echo $this->tag->linkTo(array("blog/comment/" . $id, "Comments")) ?></td>
        <td align="right">{{submit_button("Save")}}</td>
    <tr>
</table>

<?php echo $this->tag->hiddenField(array("id", "value" => $id)) ?>

<table class="table table-striped">
    <tr>
        <td align="right">
            <label for="title">Title</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("title", "size" => 50)) ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="title_clean">Title Of Clean</label>
        </td>
        <td align="left">
            <?php echo $this->tag->textField(array("title_clean", "size" => 50)) ?>
        </td>
    </tr>
</table>

<label class="col-lg-1" for="article">Article Text</label> {{ text_area('article') }}
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
        <td align="left"><?php if ($isApprover) {  
            echo $this->tag->checkField(array("featured", "value" => $blog->featured));
            } else {
                  if ($blog->featured == 1) {
                        echo $this->tag->image("img/tick16.png");
                   }
            } ?>
        </td>    
    </tr>
    
    <tr>
        <td align="right">
            <label for="enabled">Enabled</label>
        </td>
        <td align="left"><?php if ($isApprover) {  
            echo $this->tag->checkField(array("enabled", "value" => $blog->enabled));
            } else {
                  if ($blog->enabled == 1) {
                        echo $this->tag->image("img/tick16.png");
                   }
             } ?>
        </td>
    </tr>
    <tr>
        <td align="right">
            <label for="comments_enabled">Comments enabled</label>
        </td>
        <td align="left"><?php if ($isApprover) {  
            echo $this->tag->checkField(array("comments_enabled", "value" => $blog->comments_enabled));
            } else {
                   if ($blog->comments_enabled == 1) {
                        echo $this->tag->image("img/tick16.png");
                   } 
            } ?>
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

</form>
