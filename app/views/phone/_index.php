<div class="item-header">
    <div class="col-md-2"><strong>Telefone</strong></div>
    <div class="col-md-10 text-right">
        <a href="<?php echo $this->route('person.{personId}.phones.create', ['personId' => $person['id']]); ?>"
            class="btn btn-blue btn-xs" data-toggle="tooltip" data-placement="top" title="Adicionar">
            Novo
            <i class="fa fa-plus"></i>
        </a>
    </div>
    <div class="clear"></div>
</div>
<?php if(count($person['phones']) > 0): ?>
    <table class="table table-condensed table-hover table-bordered table-center">
        <tr>
            <th>Número</th>
            <th>Principal</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php foreach($person['phones'] as $phone) : ?>
            <tr>
                <td><?php echo "({$phone['areaCode']}) - {$phone['number']}" ?></td>
                <td>
                    <?php if($phone['principal'] == 1): ?>
                        <i class="fa fa-check"></i>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $this->partial('/partials.labels._type',["value" => $phone['type']], false); ?>
                </td>
                <td>
                    <?php $this->partial('/partials.labels._status',["value" => $phone['status']], false); ?>
                </td>
                <td>
                    <div class="text-right">
                        <?php $this->partial('/partials._actions',[
                            "module" => "person.{personId}.phones",
                            "data" => ['id' => $phone['id'],'personId'=>$person['id'] ]
                        ], false); ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>