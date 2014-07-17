
<?php echo $this->getContent(); ?>

<div class="container">
    <style type="text/css">

        h1,h2 {
            text-align:center;
        }
        p {  
            margin-left: 3em;
            margin-right:3em;
        }
    </style>
    <h2>{{ blog.title }}</h2>
    <span  >{{ blog.article }}</span>
</div>
<?php
if (!is_null($this->view->user_id) ) {
    echo $this->tag->javascriptInclude("js/tinymce/tinymce.min.js");
?>
<script type='text/javascript'>
    function newEditor(selector)
    {
        var options = {
    content_css: '/css/elyxir.css',
    theme_advanced_font_sizes: '10px,12px,13px,14px,16px,20px',
    font_size_style_values: '10px,12px,13px,14px,16px,20px',
    height: 200,
    theme: 'modern',
    menubar: false,
    plugins: [
        'advlist autolink lists link charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars fullscreen',
        'media nonbreaking save table contextmenu directionality',
        'template paste textcolor colorpicker textpattern'
    ],
    toolbar1: 'insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | print preview fullscreen',
    };
        
        options['selector'] = selector;
        tinymce.init(options);
    }

var nextid = 1;
function sonicMe(btn)
{
    btn.style.display = 'none';
    var nc=document.getElementById('newComment');
    nc.style.display = 'block';
    document.getElementById('cancelBtn').style.display = 'inline-block';
    newEditor('#comment');
}
function hideMe(btn)
{
    btn.style.display = 'none';
    document.getElementById('commentBtn').style.display = 'inline-block';
    document.getElementById('newComment').style.display = 'none';
}

function cloneEditor(divid, mydiv,rid)
{
    // from template
    var cid = nextid+1;
    nextid = cid;
    var edtform = document.getElementById(divid);
    var clone = edtform.cloneNode(true); // a cloned form, but same id
    clone.id = clone.id+cid;
    var jelm = $(clone);
    
    var fn = function(){
        if (this.id.length > 0)
        {
            this.id=this.id+cid;
        }
    };
    
    jelm.find(':input').each(fn);  
    jelm.find('#titleLabel').each(fn); 
    mydiv.appendChild(clone);
    clone.style.display = 'block'; 
    var btn = document.getElementById('cancelBtn'+cid);
    btn.style.display = 'inline-block';
    btn = document.getElementById('titleLabel'+cid);
    btn.setAttribute('for','title'+cid);
    btn = document.getElementById('reply_to_id'+cid);
    btn.value=rid;
    newEditor('#comment'+cid);
}
function replyTo(rid)
{
    var id='cmt-'+rid;
    var mydiv = document.getElementById(id);
    cloneEditor('newComment',mydiv,rid);
}
function editComment(rid)
{
    var id='cmt-'+rid;
    var mydiv = document.getElementById(id);
    // must get formable content for existing values
    var req={
        type:"POST",
        url:"/comment/fetch",
        success:function(response)
        {
            
            var ed='#comment'+nextid;
            //tinymce.activeEditor.setContent(response.getResponseHeader());
            var ch = $.parseHTML(response);
            mydiv.appendChild(ch[0]);
            cloneEditor('editComment',mydiv,rid);
            return false;
        },
        error:function(xhr, ajaxOptions, thrownError)
        {
            alert(xhr.status);
            alert(thrownError);
            return false;
        }
    };
    
    req['data'] = {id:rid};
    $.ajax(req);
    //cloneEditor(mydiv,rid);
       
}
</script>
<div class="container">
    <button id='commentBtn' class='btn' onclick='return sonicMe(this);'>Add Comment</button>
</div>
<div id='newComment' class='container' style='display:none;'>
    <form action='/comment/new' method='post'>
        <div class='row'>
            <label id='titleLabel' for='title'>Title for new comment</label>
            <?php echo $form->render('title') ?>
        </div>
        <div class='row'>
            <?php echo $form->render('comment') ?>
            <table class='table' width='50%'><tr>
                <td width='10%'></td>
                <td><button id='cancelBtn' class='btn' type='button' style='display:none;' onclick='return hideMe(this);'>Cancel</button></td>
                <td><?php echo $this->tag->submitButton(array('Save Comment', 'class' => 'btn btn-default')) ?></td>
            </tr></table>"
        </div>
        <?php 
            echo $form->render('id');
            echo $form->render('blog_id');
            echo $form->render('user_id');
            echo $form->render('head_id');
            echo $form->render('reply_to_id');
        ?>
    </form>
 </div>

<?php foreach ($page->items as $comment) { 

    if ($comment->reply_to_id) {
        $hdr_class = "class='col-md-8 col-md-offset-1'"; 
        $cmt_class = "class='col-md-8 col-md-offset-1 comment'";
    }
    else {
        $hdr_class = "class='col-md-9'"; 
        $cmt_class = "class='col-md-9 comment'";
    }   
?>
<div class='container-fluid' id=<?php echo "'cmt-" . $comment->id . "'"; ?> >
    <?php if ($comment->reply_to_id > 0) { ?>
        <div class='reply-border'>
    <?php } else { ?>
        <div class='cmt-border'>     
    <?php } ?>
    <div class='row'>
        <div <?php echo $hdr_class; ?> >
        <?php echo substr($comment->date_comment, 0, 10) . " | " . $comment->author_name . " | " . $comment->title; ?>
        <div class='pull-right'>
        <button class="btn btn-xs" 
            onclick="return replyTo(<?php echo $comment->id;?>);">Reply to</button>
        <?php if ($this->view->user_id == $comment->user_id) { ?>
        <button class="btn btn-xs" 
            onclick="return editComment(<?php echo $comment->id;?>);">Edit</button>
        <?php } ?>
        </div>
        </div>
    </div>
    <div class='row'>
        <div <?php echo $cmt_class; ?> >
        <?php echo $comment->comment; ?>
        </div>
    </div>
    </div>
</div>

<?php } ?>
</div>
<?php }  ?>




