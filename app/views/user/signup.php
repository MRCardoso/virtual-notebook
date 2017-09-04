<section class="content-form signin-form" id="signin-form">
    <ol class="breadcrumb" class="text-center">
        <li>Criar Conta</li>
    </ol>
    <form action="<?php echo $this->route('signup'); ?>" method="post" class="form-horizontal">
        
        <?php $this->partial('/errors.form', ['errors' => $model->getErrors() ], FALSE); ?>
        
        <div class="form-group">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" name="name" value="<?php echo $model->name; ?>" class="form-control input-lg item-form" placeholder="Nome">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" name="email" data-id="email" value="<?php echo $model->email; ?>" class="form-control input-lg item-form" placeholder="E-mail">
                </div>
            </div>
        </div>

        <div class="divider"><hr></div>

        <div class="form-group">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" name="username" data-id="username" value="<?php echo $model->username; ?>" class="form-control input-lg item-form" placeholder="Usuário">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <div class="input-group">
                    <span role="button" class="input-group-addon ico-password"><i class="fa fa-eye-slash"></i></span>
                    <input type="password" name="password" class="form-control input-lg item-form" placeholder="Senha">
                </div>
            </div>
        </div>
        <div class="button-group">
            <input type="submit" value="Criar" style="width: 100%" class="btn btn-blue-inverse">
        </div>
    </form>
</section>