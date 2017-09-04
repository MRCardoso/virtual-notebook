<section class="content-form signin-form" id="signin-form">
    
    <?php $this->partial('/errors.form', ['errors' => $model->getErrors() ], FALSE); ?>

    <div class="row">
        <div class="col-md-3">
            <a href="<?php echo $this->route('signin'); ?>" style="padding-top: 9px; display:block">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
            </a>
        </div>
        <div class="col-md-9">
            <h4>Esqueci a senha</h4>
        </div>
    </div>
    <form action="<?php echo $this->route('forgot'); ?>" method="post" class="form-horizontal">
        <div class="form-group">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <input type="text" name="email" data-id="email" class="form-control input-lg item-form" placeholder="E-mail">
                </div>
            </div>
        </div>

        <div class="button-group">
            <input type="submit" value="Enviar Token" style="width: 100%" class="btn btn-blue-inverse">
        </div>
    </form>
</section>