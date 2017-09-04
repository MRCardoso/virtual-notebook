<section class="content-form signin-form" id="signin-form">
    <div class="row pane-gray">
        <div class="col-md-3">
            <a href="<?php echo $this->baseUrl("signin"); ?>" style="padding-top: 9px; display:block">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
            </a>
        </div>
        <div class="col-md-9">
            <h4>Restaurar Senha</h4>
        </div>
    </div>

    <?php $this->partial('/errors.form', ['errors' => $model->getErrors() ], FALSE); ?>

    <form action="<?php echo $this->baseUrl("reset/{$token}");?>" method="post" class="form-horizontal">
        <input type="hidden" name="_METHOD" value="PATCH"/>
        <div class="form-group">
            <div class="col-md-12">
                <div class="input-group">
                    <span role="button" class="input-group-addon ico-password"><i class="fa fa-eye-slash"></i></span>
                    <input type="password" name="password" class="form-control input-lg item-form" placeholder="Senha">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <div class="input-group">
                    <span role="button" class="input-group-addon ico-password"><i class="fa fa-eye-slash"></i></span>
                    <input type="password" name="confirmation" class="form-control input-lg item-form" placeholder="Confirmação de Senha">
                </div>
            </div>
        </div>
        <div class="button-group">
            <input type="submit" value="Restaurar Senha" style="width: 100%" class="btn btn-blue-inverse">
        </div>
    </form>
</section>