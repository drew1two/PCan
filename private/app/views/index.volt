<!DOCTYPE html>
<html>
    <!-- index.volt -->
    <head>
        <?php if(!isset($title))
        {   
            $title = "ParraCAN?";  
        }
        echo "<title>$title</title>";
        ?>
        <link href="/bootstrap-3.2/css/bootstrap.min.css" rel="stylesheet" />
        <link href="/bootstrap-3.2/css/bootstrap-theme.min.css" rel="stylesheet" />
        <!-- JQuery must be first -->
        <script src="/js/jquery/jquery-2.1.1.min.js" type="text/javascript" ></script>
        <script src="/bootstrap-3.2/js/bootstrap.min.js" type="text/javascript" ></script>
        <meta charset="UTF-8"/>
        {{ stylesheet_link("/css/elyxir.css", false) }}
        <?php 
            if (isset($meta)) {
                foreach($meta as $mtag)
                {
                    echo "<meta $mtag->attr_name='$mtag->attr_value' $mtag->content_type='$mtag->content' />" . PHP_EOL;
                }
            }
        ?>
    </head>
    <body>

        {{ content() }}

    </body>
</html>