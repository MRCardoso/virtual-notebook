<?php $this->container([
    "url" => "notebook",
    "module" => "Pessoa",
    "route" => $route,
    "model" => $model
], '_form'); ?>

    <div class="form-group">
        <label class="control-label col-md-3">
            Status
        </label>
        <div class="col-md-6">
            <?php foreach([1,0] as $statues): ?>
                <label class="radio-inline">
                    <input type="radio" name="status" value="<?php echo $statues; ?>" <?php echo $model->status == $statues ? 'checked': '' ?>>
                    <?php echo $statues ? "Ativo" : "Inativo"; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">
            Sexo
        </label>
        <div class="col-md-6">
            <?php foreach(['M','F'] as $options): ?>
                <label class="radio-inline">
                    <input type="radio" name="sex" value="<?php echo $options; ?>" <?php echo $model->sex  == $options ? 'checked': '' ?>>
                    <i class="fa fa-<?php echo $options == 'M' ? "male" : "female"; ?>"></i>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3">Nome</label>
        <div class="col-md-6">
            <input type="text" name="name" value="<?php echo $model->name; ?>" class="form-control" placeholder="Nome">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Sobre Nome</label>
        <div class="col-md-6">
            <input type="text" name="lastName" value="<?php echo $model->lastName; ?>" class="form-control" placeholder="Sobre Nome">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Apelido</label>
        <div class="col-md-6">
            <input type="text" name="nickname" value="<?php echo $model->nickname; ?>" class="form-control" placeholder="Apelido">
        </div>
    </div>
<?php $this->closeContainer(); ?>