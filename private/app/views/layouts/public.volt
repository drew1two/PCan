<nav class="navbar navbar-default" role="navigation" >
<div class="navbar-header">
       <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#do-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
       </button> 
</div>
<a href="/" ><img src="/img/logo.png" class="img"/></a>
<span style="color:darkorange;font-size:28px;vertical-align:middle;">Parramatta Climate Action Network</span>
<div id="do-collapse-1">
    <ul class="nav navbar-nav Nav">
            {%- set menus = [
              'Home': 'index/index',
              'Articles': 'read/index',
              'Contact': 'contact/index',
              'About': 'about/index'
              
            ] -%}

<?php $cname = $this->dispatcher->getControllerName(); ?>
{%- for key, value in menus %}
  <?php  if (0===strpos($value,$cname)) { ?> 
<li class='active Nav'>{{ link_to(value, key) }}</li>
  <?php } else { ?>
<li class='Nav'>{{ link_to(value, key) }}</li>
  <?php } ?>
{%- endfor -%}
    <li class='Nav'>{{ link_to('session/login', 'Login') }}</li>
    <li class='Nav'>{{ link_to('session/signup', 'Signup') }}</li>
    </ul>
</div>
</nav>

{{ content() }}

<div class="container">
 <footer class="footer">
  {{ link_to("privacy", "Privacy Policy") }} |
  {{ link_to("terms", "Terms") }} |
 Using Phalcon.
</footer>   
</div>




