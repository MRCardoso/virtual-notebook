<div class="text-right">
    <a href="<?php echo $this->route("{$module}.update.{id}", $data) ?>"
       class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="top" title="Editar">
        <i class="fa fa-pencil"></i>
    </a>
    <a href="#" class="remove-link btn btn-danger btn-xs"
       data-url="<?php echo $this->route("{$module}.remove.{id}", $data) ?>" data-toggle="modal" data-target=".modal-remove">
        <i class="fa fa-remove" data-toggle="tooltip" data-placement="top" title="Remover"></i>
    </a>
</div>