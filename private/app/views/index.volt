<!DOCTYPE html>
<html>
    <!-- index.volt -->
    <head>
        <meta charset="UTF-8"/>
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
        
        {{ stylesheet_link("/css/elyxir.css", false) }}
        <?php 
            if (isset($metaloaf) && count($metaloaf) > 0) {
                
                foreach($metaloaf as $mtag)
                {
                    echo $mtag . PHP_EOL;
                }
            }
        ?>
    </head>
    <body>

        {{ content() }}

    </body>
</html>