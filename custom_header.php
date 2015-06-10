<?php

   if (!strstr($_SERVER["PHP_SELF"], 'login.php') && !strstr($_SERVER["PHP_SELF"], 'forgotpass.php')) {
	    $nav = '<nav class="navbar navbar-inverse">
				  <div class="container-fluid">
				  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">';
        if($session->logged_in) {
        $nav .= '<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1"><ul class="nav navbar-nav">
					<li class="dropdown">
					  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Menu <span class="caret"></span></a>
					  <ul class="dropdown-menu" role="menu">';
		
		$nav .= '		<li><a href="useredit.php">  ' . USER_PANEL . '</a></li>';
		
		if($session->isAdmin()) {
		
		$nav .= '		<li><a href="admin/admin.php">  ' . ADMIN_PANEL . '</a></li>';
		
		}
		
		$nav .=	'		<li><a href="process.php">  ' . LOGOUT . '</a></li>
						
					  </ul>
					</li>
				  </ul>';
			
			
		$nav .= '<p class="navbar-text navbar-right">' . LOGGED_IN_AS . '  <strong>' . $session->username . '</strong></p>';
        $nav .= '</div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
  </nav>'; 

		echo $nav;
        } else {
        echo '<a href="login.php">  ' . LOGIN . '</a>&nbsp;&nbsp;';
        }
    }
?>