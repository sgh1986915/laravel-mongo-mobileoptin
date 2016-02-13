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
           		@if(Auth::guest() || Auth::user()->hasRole( 'advertiser' ) || Auth::user()->hasRole( 'admin' ) )
                <li><a href="{{ url('/') }}">Home</a></li>
                @endif
                @if (!Auth::guest())
                <li><a href="{{ url('/campaigns') }}">Campaigns</a></li>
                @if(Auth::user()->hasRole( 'advertiser' ) || Auth::user()->hasRole( 'admin' ))
                <li><a href="{{ url('/domains') }}">Domains</a></li>
                <li><a href="{{ url('/admin/users') }}">Users</a></li>
                <li><a href="{{ route('support.with.expert_categories.first') }}" id=''>Traffic Expert Academy</a></li>
                @endif
                @endif
                
            </ul>

            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                <li><a href="{{ url('/auth/login') }}">Login</a></li>
                {{--<li><a href="{{ url('/auth/register') }}">Register</a></li>--}}
                @else
                @if(Auth::user()->hasRole( 'admin' ))
                <li><a href="{{ url('/admin') }}">Admin</a></li>
                @endif
                @if(Auth::getSession()->get( 'current_admin_user', null ) != null)
                <li><a href="{{ route('reconnect_as_admin',['id'=> Auth::getSession()->get( 'current_admin_user' )])  }}">Exit</a></li>
                @endif
                
                @if(Auth::user()->hasRole( 'advertiser' ) || Auth::user()->hasRole( 'admin' ))
                <?php
				//  && Auth::user()->hasModule('messages')
                $user_id = Auth::id();
                $messcount = \Sercul\Messages\MessagesRead::where('user_id', $user_id)->where('status', 0)->count();
                ?>
                <li><a href="{{ url('/messages') }}">Messages
                @if($messcount)
                <span id='messcount' style="color:red;font-weight: bold;">({{$messcount}})</span>
                @endif
                </a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Support
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ route('support.with.faq_categories.first') }}" id=''>FAQ & Training</a></li>
                        <li><a href="{{ url('/support') }}"  id='contact_top'>Contact Us</a></li>
                    </ul>
                </li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }}
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                         <li><a href="{{ url('/integrations') }}">Integrations</a></li>
                        <li><a href="{{ url('/profile') }}">Profile</a></li>
                        @if(Auth::getSession()->get( 'current_admin_user', null ) != null)
		                <li><a href="{{ route('reconnect_as_admin',['id'=> Auth::getSession()->get( 'current_admin_user' )])  }}">Exit</a></li>
		                @else
                        <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                   		@endif
                    </ul>
                </li>
                @endif
            </ul>

        </div>
    </div>
</nav>