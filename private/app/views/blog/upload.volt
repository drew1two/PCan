<?php 
    if (isset($upfiles))
    {
        echo '<table class="table table-condensed"><tbody>';
        foreach($upfiles as $fup)
        {
            $linkpath = $fup->path . $fup->name;
            echo '<tr><td>' . $this->tag->linkTo(array($linkpath,$linkpath, 'target' => '_blank'));
            echo '</td><td>' . $fup->date_upload;  
             echo '</td><td>' . $fup->mime_type;  
             echo '</td><td>' . $fup->getSizeStr(); 
             echo '</td><td><button type="button" onclick="del_file('
                    . $fup->id . ',' . $fup->blog_id . ')">DEL</button>'; 
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    if (isset ($replylist))
    {
        foreach($replylist as $reply)
        {
            echo '<p>' . $reply . '</p>';
        }
    }
?>

