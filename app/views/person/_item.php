<div class="panel panel-<?php echo $person["sex"] == 'F' ? 'danger': 'info'?>">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-<?php echo $person['sex'] == 'F' ? 'female': 'male'; ?>"></i>
            <?php echo $person['name']; ?>
            <div class="pull-right">
                <?php $this->partial('/partials._actions',[
                    "module" => "person",
                    "data" => ["id" => $person['id']]
                ], false); ?>
            </div>
        </h3>
    </div>
    <div class="panel-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active">
                <a href="#phone-<?php echo $person['id']; ?>" aria-controls="phone-<?php echo $person['id']; ?>" role="tab" data-toggle="tab">
                    <i class="fa fa-phone"></i>
                    Telefone
                    <i class="badge"><?php echo count($person['phones']); ?></i>
                </a>
            </li>
            <li role="presentation">
                <a href="#email-<?php echo $person['id']; ?>" aria-controls="email-<?php echo $person['id']; ?>" role="tab" data-toggle="tab">
                    <i class="fa fa-envelope-o"></i>
                    E-mail
                    <i class="badge"><?php echo count($person['emails']); ?></i>
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="phone-<?php echo $person['id']; ?>">
                <?php $this->partial('/phone._index',[ 'person' => $person ]); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="email-<?php echo $person['id']; ?>">
                <?php $this->partial('/email._index',[ 'person' => $person ]); ?>
            </div>
        </div>
    </div>
</div>