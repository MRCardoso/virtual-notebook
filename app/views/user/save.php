<?php $this->container([
    "url" => "",
    "module" => "Meus Dados",
    "route" => $this->route('myData'),
    "reqMethod" => "PATCH",
    "model" => $model
], '_form'); ?>

    <div class="form-group">
        <label class="control-label col-md-3">
            Status
        </label>
        <div class="col-md-6">
            <?php foreach([1,0] as $statues): ?>
                <label class="radio-inline">
                    <input type="radio" name="status" value="<?php echo $statues; ?>" <?php echo $model->status  == $statues ? 'checked': '' ?>>
                    <?php echo $statues ? "Ativo" : "Inativo"; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Nome</label>
        <div class="col-md-8">
            <input type="text" name="name" value="<?php echo $model->name; ?>" class="form-control" placeholder="Nome">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3">E-mail</label>
        <div class="col-md-8">
            <input type="text" name="email" value="<?php echo $model->email; ?>" class="form-control" placeholder="E-mail">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Usuário</label>
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" name="username" value="<?php echo $model->username; ?>" class="form-control" placeholder="Usuário">
            </div>
        </div>
    </div>

    <div class="divider-grey" id="btn-box-password">
        Alterar Senha
        <i class="fa <?php echo $request->input('password') != '' ? 'fa-angle-down' : 'fa-angle-right'; ?>"></i>
    </div>

    <div id="box-password" <?php echo $request->input('password') != '' ? '' : 'style="display:none"'; ?>>
        <div class="form-group">
            <label class="control-label col-md-3">
                Senha
            </label>
            <div class="col-md-8">
                <div class="input-group">
                    <span role="button" class="input-group-addon ico-password"><i class="fa fa-eye-slash"></i></span>
                    <input type="password" name="password" class="form-control" <?php echo $request->input('password') != '' ? '' : 'disabled="disabled"'; ?> placeholder="Senha">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-md-3">
                Nova Senha
            </label>
            <div class="col-md-8">
                <div class="input-group">
                    <span role="button" class="input-group-addon ico-password"><i class="fa fa-eye-slash"></i></span>
                    <input type="password" name="new_password" class="form-control" <?php echo $request->input('password') != '' ? '' : 'disabled="disabled"'; ?>  placeholder="Nova Senha">
                </div>
            </div>
        </div>
    </div>
<?php $this->closeContainer(); ?>