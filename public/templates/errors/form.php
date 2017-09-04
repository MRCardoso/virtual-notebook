<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php foreach ($errors as $field => $error) : ?>
            <?php foreach ($error as $err) : ?>
            <p>
                <i class="fa fa-info-circle"></i>
               <?php echo $err; ?>
            </p>
            <?php endforeach ?>
        <?php endforeach ?>
    </div>
<?php endif; ?>