<div class="page-header">
    <div class="col-md-2">
        <h4 class="page-title">
            Total de Resultados
        </h4>
    </div>
    <div class="col-md-10">
        <?php if($total > $limit): ?>
            <nav aria-label="Page navigation" class="pager-grid">
                <ul class="pagination">
                    <?php if($currentPage != 1): ?>
                        <li>
                            <a href="<?php echo $this->route('notebook', ['letter' => $letter])."?page=1"; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php foreach(range(1, $pages) as $page): ?>
                        <li class="<?php echo $page == $currentPage ?'active':''; ?>">
                            <a href="<?php echo $this->route('notebook', ['letter' => $letter])."?page={$page}"; ?>">
                                <?php echo $page; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if( $currentPage != $pages ): ?>
                        <li>
                            <a href="<?php echo $this->route('notebook', ['letter' => $letter])."?page={$pages}"; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
        <div class="pager-grid">
            <?php echo "{$currentPage} / {$pages} - "; ?>
            <span class="badge"><?php echo $total; ?></span>
        </div>
    </div>
    <div class="clear"></div>
</div>