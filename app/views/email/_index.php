<div class="item-header">
    <div class="col-md-2"><strong>E-mail</strong></div>
    <div class="col-md-10 text-right">
        <a href="<?php echo $this->route('person.{personId}.emails.create', ['personId' => $person['id']]); ?>"
            class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" title="Adicionar">
            Novo
            <i class="fa fa-plus"></i>
        </a>
    </div>
    <div class="clear"></div>
</div>
<?php if(count($person['emails']) > 0): ?>
    <table class="table table-condensed table-hover table-bordered table-center">
        <tr>
            <th>E-mail</th>
            <th>Principal</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php foreach($person['emails'] as $email) : ?>
            <tr>
                <td><?php echo $email['email'] ?></td>
                <td>
                    <?php if($email['principal'] == 1): ?>
                        <i class="fa fa-check"></i>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $this->partial('/partials.labels._type',["value" => $email['type']], false); ?>
                </td>
                <td>
                    <?php $this->partial('/partials.labels._status',["value" => $email['status']], false); ?>
                </td>
                <td>
                    <div class="text-right">
                        <?php $this->partial('/partials._actions',[
                            "module" => "person.{personId}.emails",
                            "data" => ['id' => $email['id'],'personId'=>$person['id'] ]
                        ], false); ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>