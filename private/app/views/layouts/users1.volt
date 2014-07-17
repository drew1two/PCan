<!-- layouts/users.volt -->
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
<div>
    <ul class="nav navbar-nav Nav" >
             {%- set menus = [
              'Home': 'index/index',
              'Public': 'read/index',
              'Blog' : 'blog/index',
              'Contact': 'contact/index',
              'About': 'about/index'
            ] -%}

<?php $cname = $this->dispatcher->getControllerName(); ?>
{%- for key, value in menus %}
  <?php  if (0===strpos($value,$cname)) { ?> 
<li class="active Nav">{{ link_to(value, key) }}</li>
  <?php } else { ?>
<li class="Nav">{{ link_to(value, key) }}</li>
  <?php } ?>
{%- endfor -%}
       <li class="dropdown Nav">
        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown">
            {{auth.getName()}}<b class="caret"></b>
        </a>
        <ul class="dropdown-menu Nav">  
            <li class='Nav'>{{ link_to('myaccount/edit', 'My Profile') }}</li>
            <li class='divider Nav'></li>
            <li class='Nav'>{{ link_to('session/logout', 'Logout') }}</li>
        </ul>
        </li>   
    </ul>
</div>
</nav>
{{ content() }}
<div class="container">
<footer class="footer"><hr/>
  {{ link_to("privacy", "Privacy Policy") }} |
  {{ link_to("terms", "Terms") }} |
 Using Phalcon.
</footer>
</div>