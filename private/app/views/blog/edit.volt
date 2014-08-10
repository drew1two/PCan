
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


<?php echo $this->tag->form(array("blog/edit/" . $id, 'id' => 'myform')); ?>

<table class="table table-striped">
    <tr>        
        <td class='leftCell'><?php echo $this->tag->linkTo(array("blog/comment/" . $id, "Comments", 'target' => '_blank')) ?></td>
        <td class='centerCell'><?php echo "Updated " . $blog->date_updated . " &nbsp;" . $this->tag->submitButton('Save') ?></td>
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
            <label for="title_clean">Unique URL</label>
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
    <?php if ($isApprover) { ?>
        <tr>
            <td align="right"><label for="enabled">Enabled</label></td>
            <td align="left"><?php echo $this->tag->checkField(array("enabled", "value" => $blog->enabled)); ?></td>
        </tr>
        <tr>
            <td align="right"><label for="featured">Featured</label></td>
            <td align="left"><?php echo $this->tag->checkField(array("featured", "value" => $blog->featured)); ?></td>
        </tr>
        <tr>
            <td align="right"><label for="comments">Comments</label></td>
            <td align="left"><?php echo $this->tag->checkField(array("comments", "value" => $blog->comments)); ?></td>
        </tr>
        <?php
    } else {
        $tickImage = $this->tag->image("img/tick16.png");
        $crossImage = $this->tag->image("img/cross16.png");
        ?>
        <tr>
            <td colspan='2'>Values below can be altered by another Editor.</td>
        </tr>
        <tr>
            <td align="right"><label for="enabled">Enabled</label></td>
            <td align="left"><?php
                if ($blog->enabled == 1) {
                    echo $tickImage;
                } else {
                    echo $crossImage;
                }
                ?></td>
        </tr>
        <tr>
            <td align="right"><label for="featured">Featured</label></td>
            <td align="left"><?php
                if ($blog->featured == 1) {
                    echo $tickImage;
                } else {
                    echo $crossImage;
                }
                ?></td>
        </tr>
        <tr>
            <td align="right"><label for="comments">Comments</label></td>
            <td align="left"><?php
                if ($blog->comments == 1) {
                    echo $tickImage;
                } else {
                    echo $crossImage;
                }
                ?></td>
        </tr>
<?php } ?>
</table>

<div>
    <table class='table table-striped'>
        <thead>
            <tr ><th class='centerCell' colspan='2'>Metatags (for search engines)</th></tr>
        </thead>
        <tbody>
            <?php
            foreach ($metatags as $meta) {
                // generate a name using a prefix.
                $label = $meta->meta_name;
                $name = 'metatag-' . $meta->id;
                $value = $meta->content;
                $setup = array($name, 'value' => $meta->content, 'size' => 60, 'cols' => 60, 'maxlength' => $meta->data_limit);

                if ($meta->data_limit <= 80) {
                    $input = $this->tag->textField($setup);
                } else {
                    $input = $this->tag->textArea($setup);
                }
                ?>
                <tr>
                    <td class='rightCell'><label for='{{ name }}'> {{ label }}</label>
                    <td class='leftCell'>{{ input }}</td>
                </tr> 
<?php } ?>
        </tbody>
    </table>
</div>
</form>
<div class="progress">
    <div class="up_bar"></div >
    <div class="up_percent">0%</div >          
</div>

<form id='upfile' action="/blog/upload" method="post" enctype="multipart/form-data">
    <h4>Uploaded Files</h4>
    <div id="up_status">{% include "blog/upload.volt" %}</div>
    <input type="hidden" name="blogid" value="{{ id }}" />
    <table class='table'>
        <tr>
            <td class='rightCell'><label>File</label></td>
            <td class='leftCell'><input type="file" name="files[]" multiple></td>
        </tr>
        <tr>
            <td class='rightCell'><label>Destination</label></td>
            <td class='leftCell'>
                <select name='up_dest'>
                    <option value='image'>image</option>
                    <option value='file'>file</option>
                </select></td>
        <tr/>
        <tr><td class='rightCell'><input type="submit" value="upload" class='btn-danger'></td></tr>
    </table>
</form>
<script>
    function del_file(fileid,bid)
    {
        var status = $('#up_status');
        var delInfo = {
            id : fileid,
            blogid : bid
        };
        $.ajax({
            type: "POST",
            url: "/blog/deleteFile",
            dataType: "json",
            complete: function(xhr) {
                status.html(xhr.responseText);
                status.style.display = 'block';
                document.refresh;
            },
            data : delInfo
        });
    }
</script>
<script src="/js/jquery/jquery.form.js" type="text/javascript" ></script>
<script>
    (function() {

        var bar = $('.up_bar');
        var percent = $('.up_percent');
        var status = $('#up_status');

        $('#upfile').ajaxForm({
            beforeSend: function() {
                status.empty();
                var percentVal = '0%';
                bar.width(percentVal)
                percent.html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal)
                percent.html(percentVal);
            },
            success: function() {
                var percentVal = '100%';
                bar.width(percentVal)
                percent.html(percentVal);
            },
            complete: function(xhr) {
                status.html(xhr.responseText);
                status.style.display = 'block';
                document.refresh;
            }
        });
    })();
</script>
<script>

