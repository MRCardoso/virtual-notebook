<div class="btn-row">
    <div class="breadcrumb">
        <div class="col-md-8">
            <?php foreach(range('A','Z') as $letter) : ?>
                <a href="<?php echo $this->route('notebook', ['letter' => $letter]); ?>" 
                    data-id="<?php echo $letter; ?>" class="storage-letter btn btn-xs btn-blue <?php echo in_array($letter, $letters) ? '' : 'disabled'; ?>">
                    <?php echo $letter; ?>
                </a>
            <?php endforeach; ?>
        </div>    
        <div class="col-md-4">
            <form action="<?php echo $this->route('notebook', ['letter' => $request->query('letter')]); ?>" method="GET">
                <!-- /input-group -->
                <div class="input-group">
                    <input type="text" name="query" value="<?php echo $request->query('query'); ?>" class="form-control" placeholder="Pressione 'Enter' para filtrar...">
                    <span class="input-group-btn">
                        <a href="<?php echo $this->route('person.create'); ?>" class="btn btn-blue" 
                            data-toggle="tooltip" data-placement="top" title="Criar contato">
                            Novo
                            <i class="fa fa-plus"></i>
                        </a>
                    </span>
                </div>
            </form>
        </div>
        <div class="clear"></div>
    </div>
</div>

<?php $this->pager($people['links']); ?>

<?php foreach($people['data'] as $person): ?>
    <div class="col-md-6">
        <?php $this->partial('_item',[ 'person' => $person ]); ?>
    </div>
<?php endforeach; ?>