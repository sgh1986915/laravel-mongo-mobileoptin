<nav class="navbar navbar-default">
    <div class="container">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">mobile<span>optin</span></a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="{{ url('/admin/users') }}">Users</a></li>
                <li><a href="{{ url('/admin/templates') }}">Templates</a></li>
                <li><a href="{{ URL::route('admin.domains') }}">Domains</a></li>
                <li><a href="{{ URL::route('admin.templates.groups.list') }}">Template Group</a></li>
                <li><a href="{{ url('/admin/modules') }}">Modules</a></li>
                <li><a href="{{ url('/admin/package') }}">Packages</a></li>
                <li><a href="{{ url('/admin/user_content') }}">Pop-Up</a></li>
                <li><a href="{{ URL::route('admin.faq.categories') }}">FAQ</a></li>
                <li><a href="{{ URL::route('admin.expert_traffic.categories') }}">TEA</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">

                <li><a href="{{ url('/') }}">Exit Admin</a></li>



            </ul>

        </div>
    </div>
</nav>