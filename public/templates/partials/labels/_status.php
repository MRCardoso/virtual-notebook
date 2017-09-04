<?php if($value == 1) : ?>
    <span class="label label-success" data-toggle="tooltip" data-placement="top" title="Ativo">
        <i class="fa fa-plus"></i>
    </span>
<?php elseif($value == 0) : ?>
    <span class="label label-danger" data-toggle="tooltip" data-placement="top" title="Inativo">
        <i class="fa fa-minus"></i>
    </span>
<?php endif; ?>