<!-- layouts/private.volt -->
<nav class="navbar navbar-default" role="navigation" >
    <div class="container-fluid">
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
        <div id="do-collapse-1" class="collapse navbar-collapse">
            <ul class="nav navbar-nav Nav" >
                {%- set menus = [
                'Users': 'users/list',
                'Profiles': 'profiles/index',
                'Permissions': 'permissions/index',
                'Meta' : 'meta/index',
                'Edit':'blog/index',
                'Read':'read/index'
                ] -%}
                <?php $cname = $this->dispatcher->getControllerName(); ?>
                {%- for key, value in menus %}
                <?php if (0 === strpos($value, $cname)) { ?> 
                    <li class="active Nav">{{ link_to(value, key) }}</li>
                <?php } else { ?>
                    <li class='Nav'>{{ link_to(value, key) }}</li>
                <?php } ?>
                {%- endfor -%}
                <li class="dropdown Nav">
                    <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown">
                        {{auth.getName()}}<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li class='Nav'>{{ link_to('blog/new', 'New Article') }}</li>
                        <li class='Nav'>{{ link_to('myaccount/edit', 'My Account') }}</li>
                        <li class='divider Nav'></li>
                        <li class='Nav'>{{ link_to('session/logout', 'Logout') }}</li>
                    </ul>
                </li>   

            </ul>
        </div>
    </div>
</nav>
{{ content() }}
<footer class="footer">
    {{ link_to("privacy", "Privacy Policy") }} |
    {{ link_to("terms", "Terms") }} |
    Using Phalcon.
</footer>