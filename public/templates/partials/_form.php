<?php $this->partial('/partials._header', compact('url', 'module', 'action'), FALSE); ?>

<div class="content">
    
    <?php $this->partial('/errors.form', ['errors' => $model->getErrors() ], FALSE); ?>

    <form action="<?php echo $route; ?>" class="form-horizontal" method="post">

        <?php if(!empty($model->id)): ?>
            <input type="hidden" name="_METHOD" value="<?php echo isset($reqMethod) ? $reqMethod : 'PUT' ; ?>"/>
        <?php endif; ?>

        <?php echo $content; ?>
    
        <div class="button-group">
            <a href="<?php echo isset($_SESSION['previousURL']) ? $_SESSION['previousURL'] : $this->route($url); ?>" class="btn btn-default">Back</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>