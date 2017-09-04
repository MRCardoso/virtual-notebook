<div class="navbar navbar-blue">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="icon-auth" href="<?php echo $this->baseUrl() ?>">
                <?php if(auth() != NULL): ?>
                    <img src="http://www.gravatar.com/avatar/<?php echo md5(auth('email'));?>" />
                <?php else: ?>
                    <img src="<?php echo $this->baseUrl('public/favicon.png') ?>" width="30">
                <?php endif; ?>
            </a>
        </div>
        <div class="collapse navbar-collapse">
            <?php if( auth() != NULL ): ?>
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?php echo $this->baseUrl() ?>">Home</a></li>
                    <li><a href="<?php echo $this->baseUrl('notebook') ?>">Agenda</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">                            
                            <?php echo auth('username'); ?>                            
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo $this->baseUrl('myData'); ?>">
                                    <i class="fa fa-user"></i>
                                    Meus Dados
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo $this->baseUrl('signout'); ?>">
                                    <i class="fa fa-sign-out"></i>
                                    Sair
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="nav navbar-nav navbar-right" ng-if="!auth">
                    <li>
                        <a href="<?php echo $this->baseUrl("signup"); ?>" accesskey="S">
                            Criar Conta
                            <span class="fa fa-user-o"></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->baseUrl("signin"); ?>" accesskey="I">
                            Acessar
                            <span class="fa fa-sign-in"></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div><!--/.nav-collapse -->
    </div>
</div>