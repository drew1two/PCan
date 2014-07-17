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

<?php echo $this->tag->form("blog/new") ?>

<table width="100%">
    <tr>
        <td align="left"><?php echo $this->tag->linkTo(array("blog", "Go Back")) ?></td>
        <td align="right">{{submit_button("Save")}}</td>
    <tr>
</table>



<div align="center">
    <h2>New Article</h2>
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

</table>

<div class="container-fluid">
  {{ text_area('article') }}
</div>
</form>


