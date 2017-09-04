<?php $this->container([
    "url" => "notebook", 
    "module" => "Phone",
    "model" => $model,
    "route" => $route,
], '_form'); ?>

    <div class="form-group">
        <label class="control-label col-md-3">
            É Principal
        </label>
        <div class="col-md-6">
            <?php foreach([1,0] as $options): ?>
                <label class="radio-inline">
                    <input type="radio" name="principal" value="<?php echo $options; ?>" <?php echo $model->principal == $options ? 'checked': '' ?>>
                    <?php echo $options ? "Sim" : "Não"; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">
            Status
        </label>
        <div class="col-md-6">
            <?php foreach([1,0] as $options): ?>
                <label class="radio-inline">
                    <input type="radio" name="status" value="<?php echo $options; ?>" <?php echo $model->status == $options ? 'checked': '' ?>>
                    <?php echo $options ? "Ativo" : "Inativo"; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3">Tipo</label>
        <div class="col-md-6">
            <?php foreach(['personal', 'commercial'] as $options): ?>
                <label class="radio-inline">
                    <input type="radio" name="type" value="<?php echo $options; ?>" <?php echo $model->type == $options ? 'checked': '' ?>>
                    <?php echo $options == 'personal' ? "Pessoal" : "Comercial"; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Ordem</label>
        <div class="col-md-6">
            <input type="number" name="order" value="<?php echo $model->order; ?>" class="form-control" placeholder="Ordem">
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3">Numero</label>
        <div class="col-md-2">
            <input type="text" name="areaCode" value="<?php echo $model->areaCode; ?>" class="form-control" placeholder="DDD" maxlength="2">
        </div>
        <div class="col-md-4">
            <input type="text" name="number" value="<?php echo $model->number; ?>" class="form-control" placeholder="Número" maxlength="9">
        </div>
    </div>
<?php $this->closeContainer(); ?>